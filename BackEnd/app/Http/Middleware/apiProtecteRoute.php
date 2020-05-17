<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class apiProtecteRoute extends BaseMiddleware
{
   public function handle($request, Closure $next)
   {
      try {

         $user = JWTAuth::parseToken()->authenticate();
         
      } catch (\Exception $e) {
         if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
            return response()->json(['message' => 'Token Inválido!'], 401);
         } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
            return response()->json(['message' => 'Token Expirado!'], 401);
         } else {
            return response()->json(['message' => 'Token não existe!'], 401);
         }
      }

      return $next($request);
   }
}
