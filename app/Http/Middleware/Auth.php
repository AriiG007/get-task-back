<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Exception;
use Illuminate\Support\Facades\Log;
class Auth
{

    public function handle(Request $request, Closure $next, $permission): Response
    {
        $token = JWTAuth::getToken();
        if (!$token) {
            return response()->json(['error' => 'Token not provided'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $user = JWTAuth::parseToken()->authenticate();

            Log::info('User authenticated: ', [
                "prequest" => $permission,
                'haspermission' => $user->permissions->where('permission', $permission)->count(),
                'permissions' => $user->permissions()->pluck('permission')->toArray(),
            ]);

            if (!$user->permissions->where('permission', $permission)->count()) {

                return response()->json(['success'=> false, 'code'=> 'unahutorized', 'error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
            }

        } catch (Exception $e) {

            if ($e instanceof TokenInvalidException) {
                return response()->json(['success'=> false, 'code'=> 'invalid_token', 'error' => 'Invalid Token'], Response::HTTP_UNAUTHORIZED);

            } else if ($e instanceof TokenExpiredException) {
                return response()->json(['success'=> false, 'code'=> 'token_expired', 'error' => 'Token expired'], Response::HTTP_UNAUTHORIZED);

            } else {
                return response()->json(['success'=> false, 'code'=> 'unahutorized', 'error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
            }
        }

        return $next($request);

    }
}
