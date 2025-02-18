<?php
namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Asistencia;

class AsistenciaController extends Controller {
    public function index()
    {
        // Obtener solo los usuarios confirmados (email verificado)
        $usuarios = User::whereNotNull('email_verified_at')->with('asistencias.evento')->get();

        return view('admin.asistencias.index', compact('usuarios'));
    }
    public function index_api()
    {
        // Obtener solo los usuarios confirmados (email verificado) con sus asistencias y eventos
        $usuarios = User::whereNotNull('email_verified_at')
            ->with('asistencias.evento')
            ->get();

        return response()->json($usuarios, 200);
    }
    public function show_api($id) {
        $eventos = Asistencia::where('usuario_id', $id)->get();
        return response()->json($eventos, 200);
    }

}
