<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate; // Puedes descomentar esto si usas Gates/Policies
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Auth\TokenGuard; // Importamos nuestro guardia personalizado

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // ========================================================================
        // ¡AQUÍ ESTÁ LA LÓGICA CLAVE!
        // Registramos nuestro "driver" de autenticación personalizado.
        // ========================================================================
        Auth::extend('token-introspection', function ($app, $name, array $config) {
            // Cada vez que Laravel necesite usar el guardia 'token-introspection',
            // creará una nueva instancia de nuestro TokenGuard.
            return new TokenGuard($app['request']);
        });
    }
}