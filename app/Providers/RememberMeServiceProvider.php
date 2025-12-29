<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class RememberMeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configurar cookie "Remember Me" para durar conforme .env (padrão: 1 ano = 525600 minutos)
        $duration = (int) env('REMEMBER_ME_DURATION', 525600);

        // O contrato Guard não expõe sessão; precisamos configurar no guard concreto (SessionGuard)
        $guard = Auth::guard('web');

        if (method_exists($guard, 'setRememberDuration')) {
            $guard->setRememberDuration($duration);
        }
    }
}
