<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RegistroCompletoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario está autenticado
        if (Auth::check()) {
            $user = Auth::user();

            // Permitir acceso solo si el usuario ha completado el registro
            if (!$user->confirmado) {
                // Definir las rutas permitidas sin completar el registro
                $rutasPermitidas = [
                    route('landing'),  // Ruta de la página de inicio
                    route('pago.index'), // Página de pago
                    route('pago.procesar') // Procesar pago
                ];

                // Permitir acceso si la ruta actual está en la lista de rutas permitidas
                if (!in_array($request->url(), $rutasPermitidas)) {
                    return redirect()->route('pago.index')->with('warning', 'Debes completar tu registro para acceder.');
                }
            }
        }

        return $next($request);
    }
}
