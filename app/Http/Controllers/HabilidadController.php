<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Habilidad;
use Illuminate\Support\Str;

class HabilidadController extends Controller
{
    // Obtener habilidades de un portafolio
    public function index($id_portafolio)
    {
        $habilidades = Habilidad::where('id_portafolio', $id_portafolio)
            ->orderBy('tipo')
            ->get();

        return response()->json($habilidades);
    }

    // Crear habilidad
    public function store(Request $request)
    {
        $request->validate([
            'nombre'        => 'required|string|max:100',
            'tipo'          => 'required|string',
            'nivel'         => 'required|integer|min:0|max:100',
            'id_portafolio' => 'required|string',
            'visible'       => 'boolean'
        ]);

        $habilidad = Habilidad::create([
            'id_habilidad'  => (string) Str::ulid(),
            'nombre'        => $request->nombre,
            'tipo'          => $request->tipo,
            'nivel'         => $request->nivel,
            'visible'       => $request->visible ?? true,
            'id_portafolio' => $request->id_portafolio
        ]);

        return response()->json([
            'message'   => 'Habilidad creada',
            'habilidad' => $habilidad
        ], 201);
    }

    // Mostrar una habilidad específica
    public function show($id)
    {
        $habilidad = Habilidad::findOrFail($id);

        return response()->json($habilidad);
    }

    // Actualizar habilidad
    public function update(Request $request, $id)
    {
        $habilidad = Habilidad::findOrFail($id);

        $request->validate([
            'nombre'  => 'sometimes|string|max:100',
            'tipo'    => 'sometimes|string',
            'nivel'   => 'sometimes|integer|min:0|max:100',
            'visible' => 'sometimes|boolean'
        ]);

        $habilidad->update($request->only([
            'nombre', 'tipo', 'nivel', 'visible'
        ]));

        return response()->json([
            'message'   => 'Habilidad actualizada',
            'habilidad' => $habilidad
        ]);
    }

    // Eliminar habilidad
    public function destroy($id)
    {
        $habilidad = Habilidad::findOrFail($id);
        $habilidad->delete();

        return response()->json([
            'message' => 'Habilidad eliminada'
        ]);
    }
}