<?php

namespace App\Repositories;

use App\Models\EstoqueEntrada;
use App\Models\EstoqueSaida;
use App\Models\Product;
// use Hashids\Hashids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductRepositorie
{
    function __construct()
    {
        $this->model = new Product();
        $this->user = Auth::guard('api')->user();
    }

    public function list($params)
    {
        $query = $this->model->where('empresa_id', $this->user->empresa_id)->limit(100)->orderBy('created_at', 'desc');

        if (isset($params['termo']) && !empty($params['termo'])) {
            $query = $query->where(function ($subquery) use ($params) {
                $subquery->orWhere('codigo_barras', 'like', '%' . $params['termo'] . '%')
                    ->orWhere('referencia', 'like', '%' . $params['termo'] . '%')
                    ->orWhere('descricao', 'like', '%' . $params['termo'] . '%');
            });
        }

        $query = $query->get();

        $query = $this->parse_dados($query);

        return $query;
    }
    public function parse_dados($list)
    {
        $dados = [];
        foreach ($list as $item) {
            if (!empty($item->foto)) {
                $item->foto_url = $this->set_foto($item->foto);
            }

            array_push($dados, $item);
        }

        // for ($i = 0; $i < count($list); $i++) {
        //     print_r($list);
        //     if (!empty($list[$i]->foto)) {
        //         $list->foto_url = $this->set_foto($list->foto);
        //     }
        // }

        return $dados;
    }

    public function novo($dados)
    {
        // print_r($post);
        if (isset($dados['foto']) && isset($dados['photo_name'])) {
            $dados['foto'] = $this->parse_foto($dados);
        }

        $dados['empresa_id'] = $this->user->empresa_id;
        $dados = $this->model->create($dados);
        // print_r($dados);
        $produto_id = (isset($dados->id)) ? $dados->id : 0;

        if ($produto_id > 0 && $dados['estoque'] > 0) {
            $this->entra_estoque($produto_id, $dados['preco'], $dados['estoque']);
        }

        return $dados;
    }

    public function getSingle(int $id)
    {
        $dados = $this->model->find($id);

        if (!empty($dados->foto)) {
            $dados->foto_atual = $dados->foto;
            $dados->foto_url = $this->set_foto($dados->foto);
        }

        return $dados;
    }

    public function editar($data, int $id)
    {
        if (isset($data['foto']) && isset($data['photo_name'])) {
            $data['foto'] = $this->parse_foto($data);
        }

        $model = $this->model->find($id);
        $model->fill($data);

        return $model->save();
    }

    public function delete(int $id)
    {
        $dados = $this->model->find($id);
        return $dados->delete();
    }

    //movimento do estoque
    private function entra_estoque(int $produto_id, $valor, $quantidade, $nota = null)
    {
        $entrada = new EstoqueEntrada();
        $entrada->produto_id = $produto_id;
        $entrada->valor_unitario = $valor;
        $entrada->quantidade = $quantidade;
        $entrada->nota = $nota;
        return $entrada->save();
    }

    public function estoque_mov($data)
    {
        if ($data['tipo'] == 1) {
            $model = new EstoqueEntrada();
        } else {
            $model = new EstoqueSaida();
        }

        $model->produto_id = $data['produto_id'];
        $model->valor_unitario = $data['valor_unitario'];
        $model->quantidade = $data['quantidade'];
        // $model->nota = $nota;
        $resp = $model->save();

        if ($resp) {
            $produto = $this->model->find($data['produto_id']);

            if ($data['tipo'] == 1) {
                $produto->estoque += $data['quantidade'];
            } else {
                $produto->estoque -= $data['quantidade'];
            }

            $produto->save();
        }

        return $resp;
    }


    //variations
    public function variationList($params)
    {
        # code...
    }


    // utilidades
    private function parse_foto($data)
    {
        if (isset($data['foto']) && isset($data['photo_name'])) {

            $this->_deletePhotoIfExists($data);

            $content = base64_decode($data['foto'][0]);
            $file = fopen('php://temp', 'r+');
            fwrite($file, $content);
            $photo_name = md5(
                uniqid(
                    microtime(),
                    true
                )
            ) . '.' . pathinfo($data['photo_name'], PATHINFO_EXTENSION);

            Storage::disk('public')
                ->put(md5($this->user->empresa_id) . "/fotos/produtos/" . $photo_name, $file);
            return $photo_name;
        }
    }
    private function _deletePhotoIfExists(array $data): void
    {
        if (array_key_exists('foto_atual', $data) && $data['foto_atual'] != null) {
            Storage::disk('public')
                ->delete(md5($this->user->empresa_id) . "/fotos/produtos/" . $data['foto_atual']);
        }
    }
    private function set_foto($foto)
    {
        return Storage::url(md5($this->user->empresa_id) . "/fotos/produtos/" . $foto);
    }
}
