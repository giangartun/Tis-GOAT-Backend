<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Habilidad;
use App\Models\Portafolio;
use Illuminate\Support\Str;

class HabilidadController extends Controller
{
    // Obtener habilidades del portafolio asociado al token
    public function index(Request $request)
    {
        $usuario = $request->user();

        $portafolio = Portafolio::where('id_usuario', $usuario->id_usuario)->first();

        if (!$portafolio) {
            return response()->json([
                'message' => 'No tienes un portafolio asociado'
            ], 404);
        }

        $habilidades = Habilidad::where('id_portafolio', $portafolio->id_portafolio)
            ->orderBy('tipo')
            ->get()
            ->groupBy('tipo');

        return response()->json($habilidades);
    }

    // Mostrar una habilidad específica
    public function show(Request $request, $id)
    {
        $usuario = $request->user();

        $portafolio = Portafolio::where('id_usuario', $usuario->id_usuario)->first();

        if (!$portafolio) {
            return response()->json([
                'message' => 'No tienes un portafolio asociado'
            ], 404);
        }

        $habilidad = Habilidad::where('id_habilidad', $id)
            ->where('id_portafolio', $portafolio->id_portafolio)
            ->first();

        if (!$habilidad) {
            return response()->json([
                'message' => 'Habilidad no encontrada'
            ], 404);
        }

        return response()->json($habilidad);
    }

    // Crear habilidad usando el portafolio del token
    public function store(Request $request)
    {
        $usuario = $request->user();

        $portafolio = Portafolio::where('id_usuario', $usuario->id_usuario)->first();

        if (!$portafolio) {
            return response()->json([
                'message' => 'No tienes un portafolio asociado'
            ], 404);
        }

        $request->validate([
            'nombre'  => 'required|string|max:100',
            'tipo'    => 'required|string|in:tecnica,blanda',
            'nivel'   => 'required|integer|min:0|max:100',
            'visible' => 'boolean'
        ]);

        $habilidad = Habilidad::create([
            'id_habilidad'  => (string) Str::ulid(),
            'nombre'        => $request->nombre,
            'tipo'          => $request->tipo,
            'nivel'         => $request->nivel,
            'visible'       => $request->visible ?? true,
            'id_portafolio' => $portafolio->id_portafolio
        ]);

        return response()->json([
            'message'   => 'Habilidad creada',
            'habilidad' => $habilidad
        ], 201);
    }

    // Actualizar habilidad verificando que pertenece al portafolio del token
    public function update(Request $request, $id)
    {
        $usuario = $request->user();

        $portafolio = Portafolio::where('id_usuario', $usuario->id_usuario)->first();

        if (!$portafolio) {
            return response()->json([
                'message' => 'No tienes un portafolio asociado'
            ], 404);
        }

        $habilidad = Habilidad::where('id_habilidad', $id)
            ->where('id_portafolio', $portafolio->id_portafolio)
            ->first();

        if (!$habilidad) {
            return response()->json([
                'message' => 'Habilidad no encontrada o no te pertenece'
            ], 404);
        }

        $request->validate([
            'nombre'  => 'sometimes|string|max:100',
            'tipo'    => 'sometimes|string|in:tecnica,blanda',
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

    // Eliminar habilidad verificando que pertenece al portafolio del token
    public function destroy(Request $request, $id)
    {
        $usuario = $request->user();

        $portafolio = Portafolio::where('id_usuario', $usuario->id_usuario)->first();

        if (!$portafolio) {
            return response()->json([
                'message' => 'No tienes un portafolio asociado'
            ], 404);
        }

        $habilidad = Habilidad::where('id_habilidad', $id)
            ->where('id_portafolio', $portafolio->id_portafolio)
            ->first();

        if (!$habilidad) {
            return response()->json([
                'message' => 'Habilidad no encontrada o no te pertenece'
            ], 404);
        }

        $habilidad->delete();

        return response()->json([
            'message' => 'Habilidad eliminada'
        ]);
    }
}