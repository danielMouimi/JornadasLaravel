<?php

namespace App\Http\Controllers;

use App\Models\Ponente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\PonenteRequest;

class PonenteController extends Controller
{
    /**
     * Muestra la lista de ponentes.
     */
    public function index()
    {
        $ponentes = Ponente::paginate(10);
        return view('ponentes.index', compact('ponentes'));
    }

    // Vista de administración de ponentes
    public function adminIndex()
    {
        $ponentes = Ponente::paginate(10); // Paginar la lista de ponentes
        return view('ponentes.gestion', compact('ponentes'));
    }

    // Formulario para crear un ponente
    public function create()
    {

        return view('admin.ponentes.create');
    }

    // Guardar un nuevo ponente
    public function store(PonenteRequest $request){
        $fotografia = $request->file('fotografia');

        if($fotografia->isValid()){
            $fotografiaExtension = $fotografia->getClientOriginalExtension();
            $fotografiaNombre = uniqid('', true) . '_' . time() . '.' . $fotografiaExtension;
            $fotografiaPath = $request->file('fotografia')->storeAs('ponentes', $fotografiaNombre, 'public');
        }

        $ponente = Ponente::create([
            'nombre' => $request->nombre,
            'fotografia' => $fotografiaPath,
            'areas_experiencia' => $request->areas_experiencia,
            'redes_sociales' => $request->redes_sociales,
        ]);

        $data = [
            'mensaje' => 'Se ha registrado un nuevo ponente',
            'ponente' => $ponente,
            'status' => 200
        ];
        return response()->json($data, 200);
    }

    // Formulario de edición
    public function edit(Ponente $ponente)
    {
        return view('admin.ponentes.edit', compact('ponente'));
    }

    // Actualizar ponente
    public function update(Request $request, Ponente $ponente)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'experiencia' => 'nullable|string',
            'redes_sociales' => 'nullable|string',
            'foto' => 'nullable|image|max:2048'
        ]);

        $ponente->nombre = $request->nombre;
        $ponente->experiencia = $request->experiencia;
        $ponente->redes_sociales = $request->redes_sociales;

        // Guardar imagen si se sube una
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('ponentes', 'public');
            $ponente->foto = $path;
        }

        $ponente->save();

        return redirect()->route('admin.ponentes.index')->with('success', 'Ponente actualizado correctamente.');
    }


    /**
     * Muestra un ponente.
     */
    public function show(Ponente $ponente)
    {
        return view('ponentes.show', compact('ponente'));
    }

    /**
     * Elimina un ponente.
     */
    public function destroy(Ponente $ponente)
    {
        $ponente->delete();
        return redirect()->route('admin.ponentes.index')->with('success', 'Ponente eliminado.');
    }


    public function index_api()
    {
        $ponentes = Ponente::all();
        return response()->json($ponentes, 200);
    }

    public function show_api($id)
    {
        $ponente = Ponente::find($id);

        if (!$ponente) {
            return response()->json(['message' => 'Ponente no encontrado'], 404);
        }

        return response()->json($ponente, 200);
    }

    public function create_api(PonenteRequest $request)
    {




        // Guardar imagen si se sube una
        if ($request->hasFile('foto')) {


            $fotografiaExtension = $request->foto->getClientOriginalExtension();
            $fotografiaNombre = uniqid('', true) . '_' . time() . '.' . $fotografiaExtension;
            $fotografiaPath = $request->file('foto')->storeAs('ponentes', $fotografiaNombre, 'public');


            $request->foto = Storage::url($fotografiaPath);


            $ponente = Ponente::create([
                'nombre' => $request->nombre,
                'foto' => $fotografiaPath,
                'experiencia' => $request->experiencia,
                'redes_sociales' => $request->redes_sociales
            ]);
        }

//        $ponente->save();

        return response()->json(['message' => 'Ponente creado correctamente', 'ponente' => $ponente], 201);
    }

    public function update_api(PonenteRequest $request, $id)
    {
        $ponente = Ponente::find($id);

        if (!$ponente) {
            return response()->json(['message' => 'Ponente no encontrado'], 404);
        }



        $ponente->update($request->all());

        // Si se sube una nueva imagen, se reemplaza la anterior
        if ($request->hasFile('foto')) {
            // Eliminar la imagen anterior si existe
            if ($ponente->foto) {
                Storage::disk('public')->delete($ponente->foto);
            }

            $path = $request->file('foto')->store('ponentes', 'public');
            $ponente->foto = $path;
        }

        $ponente->save();

        return response()->json(['message' => 'Ponente actualizado correctamente', 'ponente' => $ponente], 200);
    }

    public function destroy_api($id)
    {
        $ponente = Ponente::find($id);

        if (!$ponente) {
            return response()->json(['message' => 'Ponente no encontrado'], 404);
        }

        // Eliminar imagen si existe
        if ($ponente->foto) {
            Storage::disk('public')->delete($ponente->foto);
        }

        $ponente->delete();

        return response()->json(['message' => 'Ponente eliminado correctamente'], 200);
    }
}
