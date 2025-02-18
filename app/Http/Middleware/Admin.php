<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Maneja la solicitud.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/'); // Redirige si no está autenticado
        }

        // Verifica si el usuario tiene permisos de administrador
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acceso denegado. No eres administrador.');
        }

        return $next($request);
    }
}
