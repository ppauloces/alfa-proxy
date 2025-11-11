<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se o usuário está autenticado e se o cargo é 'admin'
        if (Auth::check() && in_array(Auth::user()->cargo, ['admin', 'super'])) {
            return $next($request);
        }        

        // Se não for admin, redireciona para uma página de erro ou página inicial
        return redirect('/dashboard')->with('error', 'Você não tem permissão para acessar esta página.');
    }
}
