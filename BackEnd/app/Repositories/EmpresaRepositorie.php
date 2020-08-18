<?php

namespace App\Repositories;

use App\Models\Empresa;
use App\Models\Payment;
use App\Models\Permissions;
use App\User;

class EmpresaRepositorie
{
    function __construct()
    {
        $this->model = new Empresa();
    }

    public function listAll($params)
    {
        $query = $this->model;

        $query = $query->get();

        return $query;
    }

    public function getById($id)
    {
        $query = $this->model->find($id);
        return $query;
    }

    public function create($params)
    {
        $verifica = $this->verficaCNPJ($params['cnpj']);

        if (count($verifica) > 0) {
            return array('erro' => "Já existe um cadastro com esse CNPJ!");
        }

        $registro = $this->model->create($params);
        $registro->save();

        if ($registro->id <= 0) {
            return array('erro' => "Falha ao cadastrar!");
        }

        Payment::create(['empresa_id' => $registro->id, 'forma' => "Dinheiro", 'more' => 1]);

        return $registro;
    }

    public function update($params, $id)
    {
        $verifica = $this->verficaCNPJ($params['cnpj'], $id);

        if (count($verifica) > 0) {
            return array('erro' => "Já existe um cadastro com esse CNPJ!");
        }

        $registro = $this->model->find($id);
        $registro->fill($params);

        if (!$registro->save()) {
            return array('erro' => "Falha ao atualizar!");
        }

        return $registro;
    }

    public function delete($id)
    {
        $registro = $this->model->find($id);
        $resp = $registro->delete();

        if (!$resp) {
            return array('erro' => "Falha ao deletar!");
        }

        return $resp;
    }

    //usuarios
    public function getUsers($params)
    {
        $query = User::with('nivel')->where('empresa_id', $params['empresa_id']);

        $query = $query->get();

        return $query;
    }

    public function getByIdUser($id)
    {
        $query = User::find($id);
        return $query;
    }

    public function createUser($params)
    {
        $verifica = $this->verficaEmail($params['email']);

        if (count($verifica) > 0) {
            return array('erro' => "Já existe um cadastro com esse EMAIL!");
        }

        $params['password'] = bcrypt($params['password']);
        $registro = User::create($params);
        $registro->save();

        if ($registro->id <= 0) {
            return array('erro' => "Falha ao cadastrar!");
        }

        $listPermissions = Permissions::where('empresa_id', $params['empresa_id'])->get();
        if (count($listPermissions) <= 0) {
            $permissions = [
                'empresa_id' => $params['empresa_id'], 'descricao' => 'Administrador', 'users' => 1, 'create_user' => 1, 'edit_user' => 1, 'delete_user' => 1,
                'nivel_user' => 1, 'products' => 1, 'create_product' => 1, 'edit_product' => 1, 'delete_product' => 1, 'clients' => 1, 'create_client' => 1,
                'delete_client' => 1, 'vendas' => 1, 'create_venda' => 1, 'edit_venda' => 1, 'delete_venda' => 1, 'cancel_venda' => 1, 'finalizar_venda' => 1,
                'desconto_venda' => 1, 'nfe' => 1, 'create_nfe' => 1, 'edit_nfe' => 1, 'delete_nfe' => 1, 'nfce' => 1, 'create_nfce' => 1, 'edit_nfce' => 1,
                'delete_nfce' => 1, 'emitentes' => 1, 'create_emitente' => 1, 'edit_emitente' => 1, 'delete_emitente' => 1, 'caixa' => 1, 'contas_receber' => 1,
                'contas_receber_create' => 1, 'contas_receber_edit' => 1, 'contas_receber_delete' => 1, 'contas_receber_payment' => 1, 'contas_pagar' => 1,
                'contas_pagar_create' => 1, 'contas_pagar_edit' => 1, 'contas_pagar_delete' => 1, 'contas_pagar_payment' => 1, 'create_caixa' => 1,
                'edit_caixa' => 1, 'delete_caixa' => 1, 'edit_client' => 1,
            ];

            $result = Permissions::create($permissions);
            $result->save();

            $registro->permissions = $result->id;
            $registro->save();
        } else {
            $registro->permissions = $listPermissions[0]->id;
            $registro->save();
        }

        return $registro;
    }

    public function updateUser($params, $id)
    {
        $verifica = $this->verficaEmail($params['email'], $id);

        if (count($verifica) > 0) {
            return array('erro' => "Já existe um cadastro com esse EMAIL!");
        }

        if (isset($params['password']) && !empty($params['password'])) {
            $params['password'] = bcrypt($params['password']);
        }

        $registro = User::find($id);
        $registro->fill($params);

        if (!$registro->save()) {
            return array('erro' => "Falha ao atualizar!");
        }

        return $registro;
    }

    public function deleteUser($id)
    {
        $registro = User::find($id);
        $resp = $registro->delete();

        if (!$resp) {
            return array('erro' => "Falha ao deletar!");
        }

        return $resp;
    }

    private function verficaCNPJ($cnpj, $id = 0)
    {
        $query = $this->model->where('cnpj', $cnpj);

        if ($id > 0) {
            $query = $query->where('id', '!=', $id);
        }

        $query = $query->get();

        return $query;
    }

    private function verficaEmail($email, $id = 0)
    {
        $query = User::where('email', $email);

        if ($id > 0) {
            $query = $query->where('id', '!=', $id);
        }

        $query = $query->get();

        return $query;
    }
}
