<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Macro para la API de Autenticación y Usuarios (API 1)
        Http::macro('authApi', function () {
            return Http::withOptions([
                'base_uri' => env('AUTH_API_URL', 'http://127.0.0.1:9000'),
            ])->acceptJson();
        });
    }
}