<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;
use App\Models\Ponente;
use Illuminate\Support\Facades\Validator;
use App\Models\Asistencia;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use function Laravel\Prompts\alert;

class EventoController extends Controller
{
    /**
     * Muestra la lista de eventos.
     */
    public function index()
    {
        $eventos = Evento::with('ponente')->orderBy('fecha','asc')->paginate(10);
        return view('eventos.index', compact('eventos'));
    }

    public function adminIndex() {
        $eventos = Evento::orderBy('fecha', 'desc')->paginate(10);
        return view('admin.eventos.index', compact('eventos'));
    }

    /**
     * Muestra el formulario para crear un nuevo evento.
     */
    public function create()
    {
        $ponentes = Ponente::all();
        return view('eventos.create', compact('ponentes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|in:conferencia,taller',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'ponente_id' => 'required|exists:ponentes,id',
            'capacidad_maxima' => 'required|integer|min:1',
        ]);

        $horaInicio = Carbon::parse($request->hora_inicio);
        $horaFin = Carbon::parse($request->hora_fin);

        // Validar superposición de eventos del mismo tipo
        $eventoExiste = Evento::where('tipo', $request->tipo)
            ->where('fecha', $request->fecha)
            ->where(function ($query) use ($horaInicio, $horaFin) {
                $query->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                    ->orWhereBetween('hora_fin', [$horaInicio, $horaFin]);
            })
            ->exists();

        if ($eventoExiste) {
            return redirect()->back()->withErrors(['error' => 'Ya existe un evento del mismo tipo en este horario.']);
        }

        Evento::create([
            'titulo' => $request->titulo,
            'tipo' => $request->tipo,
            'fecha' => $request->fecha,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'ponente_id' => $request->ponente_id,
            'capacidad_maxima' => $request->capacidad_maxima,
        ]);

        return redirect()->route('admin.eventos.index')->with('success', 'Evento creado correctamente.');
    }

    /**
     * Muestra un evento específico.
     */
    public function show(Evento $evento)
    {
        return view('eventos.show', compact('evento'));
    }

    /**
     * Muestra el formulario para editar un evento.
     */
    public function edit(Evento $evento)
    {
        $ponentes = Ponente::all();
        return view('eventos.edit', compact('evento', 'ponentes'));
    }

    /**
     * Actualiza un evento en la base de datos.
     */
    public function update(Request $request, Evento $evento)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|in:conferencia,taller',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'ponente_id' => 'required|exists:ponentes,id',
            'capacidad_maxima' => 'required|integer|min:1',
        ]);

        $horaInicio = Carbon::parse($request->hora_inicio);
        $horaFin = Carbon::parse($request->hora_fin);

        // Validar superposición de eventos del mismo tipo, excluyendo el actual evento
        $eventoExiste = Evento::where('id', '!=', $evento->id)
            ->where('tipo', $request->tipo)
            ->where('fecha', $request->fecha)
            ->where(function ($query) use ($horaInicio, $horaFin) {
                $query->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                    ->orWhereBetween('hora_fin', [$horaInicio, $horaFin]);
            })
            ->exists();

        if ($eventoExiste) {
            return redirect()->back()->withErrors(['error' => 'Ya existe un evento del mismo tipo en este horario.']);
        }

        $evento->update([
            'titulo' => $request->titulo,
            'tipo' => $request->tipo,
            'fecha' => $request->fecha,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'ponente_id' => $request->ponente_id,
            'capacidad_maxima' => $request->capacidad_maxima,
        ]);

        return redirect()->route('admin.eventos.index')->with('success', 'Evento actualizado correctamente.');
    }


    public function inscribirse(Evento $evento)
    {
        $user = Auth::user();

        // Comprobar si el usuario ya está inscrito en el evento
        if ($evento->asistencias()->where('usuario_id', $user->id)->exists()) {
            return redirect()->back()->with('error', 'Ya estás inscrito en este evento.');
        }

        // Contar las inscripciones del usuario
        $conferencias = $user->asistencias()->whereHas('evento', function ($query) {
            $query->where('tipo', 'conferencia');
        })->count();

        $talleres = $user->asistencias()->whereHas('evento', function ($query) {
            $query->where('tipo', 'taller');
        })->count();

        // Validar que no supere los límites
        if ($evento->tipo === 'conferencia' && $conferencias >= 5) {
            return redirect()->back()->with('error', 'No puedes inscribirte a más de 5 conferencias.');
        }

        if ($evento->tipo === 'taller' && $talleres >= 4) {
            return redirect()->back()->with('error', 'No puedes inscribirte a más de 4 talleres.');
        }

        // Comprobar el aforo disponible
        $inscritos = $evento->asistencias()->count();
        if ($inscritos >= $evento->capacidad_maxima) {
            return redirect()->back()->with('error', 'El evento ha alcanzado su capacidad máxima.');
        }

        // Registrar la asistencia
        Asistencia::create([
            'usuario_id' => $user->id,
            'evento_id' => $evento->id,
        ]);

        return redirect()->back()->with('success', 'Inscripción realizada con éxito.');
    }

    public function desapuntarse($id)
    {
        $user = Auth::user();

        // Buscar la asistencia del usuario en el evento
        $asistencia = Asistencia::where('usuario_id', $user->id)
            ->where('evento_id', $id)
            ->first();

        if ($asistencia) {
            $asistencia->delete();
            return redirect()->back()->with('success', 'Te has desapuntado del evento correctamente.');
        }

        return redirect()->back()->with('error', 'No estás inscrito en este evento.');
    }

    /**
     * Elimina un evento.
     */
    public function destroy(Evento $evento)
    {
        $evento->delete();
        return redirect()->route('eventos.index')->with('success', 'Evento eliminado.');
    }




// Métodos de la API en EventoController

    public function index_api()
    {
        // Cargar eventos con los ponentes relacionados y ordenarlos por fecha
        $eventos = Evento::all();

        // Devolver los eventos como respuesta JSON
        return response()->json($eventos);
    }

    public function show_api($id)
    {
        $evento = Evento::with('ponente')->find($id);

        if (!$evento) {
            return response()->json(['error' => 'Evento no encontrado'], 404);
        }

        return response()->json($evento);
    }

    public function store_api(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|in:conferencia,taller',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'ponente_id' => 'required|exists:ponentes,id',
            'capacidad_maxima' => 'required|integer|min:1',
        ]);

        $horaInicio = Carbon::parse($request->hora_inicio);
        $horaFin = Carbon::parse($request->hora_fin);

        // Validar superposición de eventos del mismo tipo
        $eventoExiste = Evento::where('tipo', $request->tipo)
            ->where('fecha', $request->fecha)
            ->where(function ($query) use ($horaInicio, $horaFin) {
                $query->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                    ->orWhereBetween('hora_fin', [$horaInicio, $horaFin]);
            })
            ->exists();

        if ($eventoExiste) {
            return response()->json(['error' => 'Ya existe un evento del mismo tipo en este horario.'], 400);
        }

        $evento = Evento::create([
            'titulo' => $request->titulo,
            'tipo' => $request->tipo,
            'fecha' => $request->fecha,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'ponente_id' => $request->ponente_id,
            'capacidad_maxima' => $request->capacidad_maxima,
        ]);

        return response()->json($evento, 201);
    }

    public function update_api(Request $request, $id)
    {
        $evento = Evento::find($id);
        if (!$evento) {
            return response()->json(['error' => 'Evento no encontrado'], 404);
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|in:conferencia,taller',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'ponente_id' => 'required|exists:ponentes,id',
            'capacidad_maxima' => 'required|integer|min:1',
        ]);

        $horaInicio = Carbon::parse($request->hora_inicio);
        $horaFin = Carbon::parse($request->hora_fin);

        // Validar superposición de eventos del mismo tipo, excluyendo el actual evento
        $eventoExiste = Evento::where('id', '!=', $evento->id)
            ->where('tipo', $request->tipo)
            ->where('fecha', $request->fecha)
            ->where(function ($query) use ($horaInicio, $horaFin) {
                $query->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                    ->orWhereBetween('hora_fin', [$horaInicio, $horaFin]);
            })
            ->exists();

        if ($eventoExiste) {
            return response()->json(['error' => 'Ya existe un evento del mismo tipo en este horario.'], 400);
        }

        $evento->update([
            'titulo' => $request->titulo,
            'tipo' => $request->tipo,
            'fecha' => $request->fecha,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'ponente_id' => $request->ponente_id,
            'capacidad_maxima' => $request->capacidad_maxima,
        ]);

        return response()->json($evento);
    }

    public function destroy_api($id)
    {
        $evento = Evento::find($id);
        if (!$evento) {
            return response()->json(['error' => 'Evento no encontrado'], 404);
        }

        $evento->delete();

        return response()->json(['message' => 'Evento eliminado con éxito']);
    }

    public function inscribirse_api($id) {

        // Intentar obtener el usuario autenticado
        $user = Auth::user();

        // Verificar si el usuario está autenticado
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }


        // Buscar el evento
        $evento = Evento::findOrFail($id);
        // Verificar si el usuario ya está inscrito
        if ($evento->asistencias()->where('usuario_id', $user->id)->exists()) {
            return response()->json(['error' => 'Ya estás inscrito en este evento'], 400);
        }




        // Verificar capacidad del evento
        if ($evento->asistencias()->count() >= $evento->capacidad_maxima) {
            return response()->json(['error' => 'El evento está completo'], 400);
        }

        // Inscribir al usuario
        $evento->asistencias()->create(['usuario_id' => $user->id]);

        return response()->json(['success' => true, 'message' => 'Inscripción exitosa']);
    }


    // Método para desapuntarse de un evento
    public function desapuntarse_api($eventoId)
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        try {
            // Verificar si el usuario está inscrito en el evento
            $asistencia = Asistencia::where('usuario_id', $usuario->id)
                ->where('evento_id', $eventoId)
                ->first();

            if (!$asistencia) {
                return response()->json(['message' => 'No estás inscrito en este evento'], 400);
            }

            // Eliminar la inscripción
            $asistencia->delete();

            return response()->json(['message' => 'Desinscripción exitosa']);
        } catch (\Exception $e) {
            \Log::error('Error al desinscribirse: ' . $e->getMessage());
            return response()->json(['message' => 'Error al procesar la solicitud'], 500);
        }
    }
}
