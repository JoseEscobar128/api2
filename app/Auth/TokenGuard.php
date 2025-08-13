<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // <-- ¡ESTA ES LA LÍNEA QUE FALTABA!

class TokenGuard implements Guard
{
    protected $user;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function check()
    {
        return !is_null($this->user());
    }

    public function guest()
    {
        return !$this->check();
    }

    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $token = $this->request->bearerToken();

        if (!$token) {
            return null;
        }

        // Ahora PHP sabe qué es "Http" y esta llamada funcionará
        $response = Http::withToken($token)
            ->acceptJson()
            ->get(env('AUTH_API_URL') . '/api/v1/usuarios/me');

        // Puedes dejar este dd() por ahora para confirmar que funciona
        // dd($response->json());

        if ($response->failed()) {
            return null;
        }

        $this->user = (object) $response->json('data');

        return $this->user;
    }

    public function id()
    {
        if ($user = $this->user()) {
            return $user->id;
        }
    }

    public function validate(array $credentials = []) { return false; }
    public function setUser($user) { $this->user = $user; }
    public function hasUser() { return ! is_null($this->user); }
}