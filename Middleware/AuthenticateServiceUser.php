<?php

namespace App\Http\Middleware;

use App\Models\Service;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateServiceUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if($user) {
            // Usuario logueado correctamente
            if(get_class($user) == User::class) {
                return $next($request);
            }

            // La petición se realizó desde un servicio
            if(get_class($user) == Service::class) {
                // La petición se realiza a nombre de un usuario
                if($request->header('UserId')) {
                    $user = User::findOrFail($request->header('UserId'));

                    $request->setUserResolver(function () use ($user) {
                        return $user;
                    });
                // La petición no envió usuario
                } else {
                    $request->setUserResolver(function () {
                        return null;
                    });

                }
            }
        // No se a definido un usuario pero si existe un token de acceso
        // lo cual puede significar que la ruta accedida no tenga el middleware auth:sanctum
        // lo que hace que no se autentique el usuario
        } elseif($request->bearerToken()) {
            $token = PersonalAccessToken::findToken($request->bearerToken());
            // El token es de un servicio
            if($token->tokenable_type == Service::class) {
                // La petición se realiza a nombre de un usuario
                if($request->header('UserId')) {
                    $user = User::find($request->header('UserId'));

                    $request->setUserResolver(function () use ($user) {
                        return $user;
                    });
                }
            // Existe un token de usuario
            } elseif($token->tokenable_type == User::class) {
                $user = User::find($token->tokenable_id);

                $request->setUserResolver(function () use ($user) {
                    return $user;
                });
            }
        }

        return $next($request);
    }
}
