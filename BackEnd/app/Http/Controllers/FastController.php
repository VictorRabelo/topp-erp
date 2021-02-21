<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Repositories\FastRepositorie;
use App\Repositories\UserRepositorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FastController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(FastRepositorie $repository)
    {
        $this->repo = $repository;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        $token = $this->guard()->attempt($credentials);

        if (!$token) {
            return response()->json(['message' => 'Usuário ou senha inválida'], 404);
        }

        return $this->me();
        // return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = $this->guard()->user();

        $id_permissions = (int) $user['permissions'];
        $repoPerm = new UserRepositorie();
        $permissions = $repoPerm->getSinglePermissions($id_permissions);

        // $user['_permissions'] = $permissions;

        $empresa_id = (int) $user['empresa_id'];
        $registro = DB::table('empresas')->where('id', $empresa_id)->first();

        //verifica vencimento
        $hoje = new \DateTime(date('Y-m-d'));
        $licenca = new \DateTime($registro->licenca);

        $dateInterval = $hoje->diff($licenca);

        $registro->dias = ($hoje > $licenca) ? $dateInterval->days : $dateInterval->days * -1;

        // $user['registro'] = $registro;

        // print_r($dateInterval);

        return response()->json(['user' => $user, 'permissions' => $permissions, 'empresa' => $registro]);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'accessToken' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    public function guard()
    {
        return Auth::guard('api');
    }

    public function pushCarga(Request $request, int $id)
    {
        $params = $request->all();
        $resp = $this->repo->pushCarga($params, $id);

        return response()->json($resp);
    }
}
