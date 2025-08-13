<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TokenIntrospectionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token no proporcionado.'], 401);
        }

        try {
            // Llamamos a API 1 para validar token y obtener datos del usuario
            $response = Http::withToken($token)
                            ->get('http://127.0.0.1:9000/api/v1/usuarios/me');

            if ($response->successful()) {
                $userData = $response->json()['data'];

                // Creamos un objeto usuario básico sin Spatie ni roles
                $user = new class {
                    public $id;
                    public $usuario;
                    public $email;
                };

                $user->id = $userData['id'] ?? null;
                $user->usuario = $userData['usuario'] ?? null;
                $user->email = $userData['email'] ?? null;

                // Establecemos el usuario en el guardia por defecto
                Auth::setUser($user);

                return $next($request);
            }

            return response()->json(['message' => 'Token inválido o usuario no encontrado.'], 401);

        } catch (\Exception $e) {
            // Opcional: log para debug
            // \Log::error('Error en token introspection: ' . $e->getMessage());

            return response()->json(['message' => 'Error al validar el token.'], 500);
        }
    }
}
