<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RedesProfesionales;
use Illuminate\Support\Str;

class RedesProfesionalesController extends Controller
{
    // Obtener redes profesionales de un portafolio
    public function index($id_portafolio)
    {
        $redes = RedesProfesionales::where('id_portafolio', $id_portafolio)->get();

        return response()->json($redes);
    }

    // Crear red profesional
    public function store(Request $request)
    {
        $request->validate([
            'nombre_red'    => 'required|string|max:100',
            'url'           => 'required|url',
            'id_portafolio' => 'required|string'
        ]);

        $red = RedesProfesionales::create([
            'id_red'        => (string) Str::ulid(),
            'nombre_red'    => $request->nombre_red,
            'url'           => $request->url,
            'id_portafolio' => $request->id_portafolio
        ]);

        return response()->json([
            'message' => 'Red profesional creada',
            'red' => $red
        ], 201);
    }

    // Mostrar una red específica
    public function show($id)
    {
        $red = RedesProfesionales::findOrFail($id);

        return response()->json($red);
    }

    // Actualizar red profesional
    public function update(Request $request, $id)
    {
        $red = RedesProfesionales::findOrFail($id);

        $request->validate([
            'nombre_red' => 'sometimes|string|max:100',
            'url' => 'sometimes|url'
        ]);

        $red->update($request->only([
            'nombre_red',
            'url'
        ]));

        return response()->json([
            'message' => 'Red profesional actualizada',
            'red' => $red
        ]);
    }

    // Eliminar red profesional
    public function destroy($id)
    {
        $red = RedesProfesionales::findOrFail($id);
        $red->delete();

        return response()->json([
            'message' => 'Red profesional eliminada'
        ]);
    }
}