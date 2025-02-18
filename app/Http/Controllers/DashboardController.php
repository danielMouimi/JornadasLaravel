<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pago;
use App\Models\Evento;

class DashboardController extends Controller
{
    /**
     * Muestra el panel de usuario con información relevante.
     */
    public function index()
    {
        $user = Auth::user(); // Obtiene el usuario autenticado

        // Obtener los pagos realizados por el usuario
        $pagos = Pago::where('usuario_id', $user->id)->latest()->take(5)->get();

        // Obtener los eventos a los que el usuario está registrado
        $eventos = Evento::whereHas('asistencias', function ($query) use ($user) {
            $query->where('usuario_id', $user->id);
        })->latest()->take(5)->get();

        return view('dashboard', compact('user', 'pagos', 'eventos'));
    }

    public function index_api()
    {
        $user = Auth::user(); // Obtiene el usuario autenticado

        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        try {
            // Obtener los pagos del usuario (últimos 5)
            $pagos = Pago::where('usuario_id', $user->id)
                ->latest()
                ->take(5)
                ->get();

            // Obtener los eventos a los que está registrado
            $eventos = Evento::whereHas('asistencias', function ($query) use ($user) {
                $query->where('usuario_id', $user->id);
            })
                ->latest()
                ->take(5)
                ->get();

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'pagos' => $pagos,
                'eventos' => $eventos
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno en el servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
