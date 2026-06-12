<?php

namespace App\Http\Controllers;

use App\Helpers\RegistroActividadHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Habilidad;
use App\Models\Portafolio;
use Illuminate\Support\Str;

class HabilidadController extends Controller
{
    public function index(Request $request)
    {
        $usuario    = $request->user();
        $portafolio = Portafolio::where('id_usuario', $usuario->id_usuario)->first();

        if (!$portafolio) {
            return response()->json(['message' => 'No tienes un portafolio asociado'], 404);
        }

        $habilidades = Habilidad::where('id_portafolio', $portafolio->id_portafolio)
            ->orderBy('tipo')
            ->orderBy('categoria')
            ->get()
            ->groupBy(['tipo', 'categoria']);

        return response()->json($habilidades);
    }

    public function show(Request $request, $id)
    {
        $usuario    = $request->user();
        $portafolio = Portafolio::where('id_usuario', $usuario->id_usuario)->first();

        if (!$portafolio) {
            return response()->json(['message' => 'No tienes un portafolio asociado'], 404);
        }

        $habilidad = Habilidad::where('id_habilidad', $id)
            ->where('id_portafolio', $portafolio->id_portafolio)
            ->first();

        if (!$habilidad) {
            return response()->json(['message' => 'Habilidad no encontrada'], 404);
        }

        return response()->json($habilidad);
    }

public function store(Request $request)
{
    $usuario    = $request->user();
    $portafolio = Portafolio::where('id_usuario', $usuario->id_usuario)->first();

    if (!$portafolio) {
        return response()->json(['message' => 'No tienes un portafolio asociado'], 404);
    }

    $request->validate([
        'nombre'    => 'required|string|max:100',
        'tipo'      => 'required|string|in:tecnica,blanda',
        'categoria' => 'required|string|max:100',
        'nivel'     => 'required|integer|min:0|max:100',
        'visible'   => 'boolean'
    ]);

    // Verificar duplicado — mismo nombre en el mismo portafolio
    $existe = Habilidad::where('id_portafolio', $portafolio->id_portafolio)
        ->whereRaw('LOWER(nombre) = ?', [strtolower($request->nombre)])
        ->exists();

    if ($existe) {
        return response()->json([
            'message' => 'Ya tienes registrada esta habilidad en tu portafolio'
        ], 409); // 409 Conflict
    }

    $habilidad = Habilidad::create([
        'id_habilidad'  => (string) Str::ulid(),
        'nombre'        => $request->nombre,
        'tipo'          => $request->tipo,
        'categoria'     => $request->categoria,
        'nivel'         => $request->nivel,
        'visible'       => $request->visible ?? true,
        'id_portafolio' => $portafolio->id_portafolio,
    ]);

    RegistroActividadHelper::registrar($usuario->id_usuario, 'modificacion_habilidades', [
        'tabla'          => 'habilidades',
        'accion'         => 'creacion',
        'id_afectado'    => $habilidad->id_habilidad,
        'registro_nuevo' => [
            'nombre'    => $habilidad->nombre,
            'tipo'      => $habilidad->tipo,
            'categoria' => $habilidad->categoria,
            'nivel'     => $habilidad->nivel,
            'visible'   => $habilidad->visible,
        ],
    ]);

    return response()->json([
        'message'   => 'Habilidad creada',
        'habilidad' => $habilidad
    ], 201);
}

    public function update(Request $request, $id)
    {
        $usuario    = $request->user();
        $portafolio = Portafolio::where('id_usuario', $usuario->id_usuario)->first();

        if (!$portafolio) {
            return response()->json(['message' => 'No tienes un portafolio asociado'], 404);
        }

        $habilidad = Habilidad::where('id_habilidad', $id)
            ->where('id_portafolio', $portafolio->id_portafolio)
            ->first();

        if (!$habilidad) {
            return response()->json(['message' => 'Habilidad no encontrada o no te pertenece'], 404);
        }

        $request->validate([
            'nombre'    => 'sometimes|string|max:100',
            'tipo'      => 'sometimes|string|in:tecnica,blanda',
            'categoria' => 'sometimes|string|max:100',
            'nivel'     => 'sometimes|integer|min:0|max:100',
            'visible'   => 'sometimes|boolean'
        ]);

        // capturar ANTES de modificar
        $anterior = $habilidad->only(['nombre', 'tipo', 'categoria', 'nivel', 'visible']);

        $habilidad->update($request->only(['nombre', 'tipo', 'categoria', 'nivel', 'visible']));

        RegistroActividadHelper::registrar($usuario->id_usuario, 'modificacion_habilidades', [
            'tabla'             => 'habilidades',
            'accion'            => 'actualizacion',
            'id_afectado'       => $habilidad->id_habilidad,
            'registro_anterior' => $anterior,
            'registro_nuevo'    => $habilidad->only(['nombre', 'tipo', 'categoria', 'nivel', 'visible']),
        ]);

        return response()->json([
            'message'   => 'Habilidad actualizada',
            'habilidad' => $habilidad
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $usuario    = $request->user();
        $portafolio = Portafolio::where('id_usuario', $usuario->id_usuario)->first();

        if (!$portafolio) {
            return response()->json(['message' => 'No tienes un portafolio asociado'], 404);
        }

        $habilidad = Habilidad::where('id_habilidad', $id)
            ->where('id_portafolio', $portafolio->id_portafolio)
            ->first();

        if (!$habilidad) {
            return response()->json(['message' => 'Habilidad no encontrada o no te pertenece'], 404);
        }

        RegistroActividadHelper::registrar($usuario->id_usuario, 'modificacion_habilidades', [
            'tabla'             => 'habilidades',
            'accion'            => 'eliminacion',
            'id_afectado'       => $habilidad->id_habilidad,
            'registro_anterior' => [
                'nombre'    => $habilidad->nombre,
                'tipo'      => $habilidad->tipo,
                'categoria' => $habilidad->categoria,
                'nivel'     => $habilidad->nivel,
                'visible'   => $habilidad->visible,
            ],
        ]);

        $habilidad->delete();

        return response()->json(['message' => 'Habilidad eliminada']);
    }
}