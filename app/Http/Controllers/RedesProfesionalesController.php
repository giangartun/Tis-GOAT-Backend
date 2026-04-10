<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RedesProfesionales;
use Illuminate\Support\Str;

class RedesProfesionalesController extends Controller
{
    // Obtener redes de un usuario
    public function index($id_usuario)
    {
        $redes = RedesProfesionales::where('id_usuario', $id_usuario)->get();

        return response()->json($redes);
    }

    // Crear red profesional
    public function store(Request $request)
    {
        $request->validate([
            'id_usuario'  => 'required|string|exists:usuario,id_usuario',
            'nombre_red'  => 'required|string|max:100|in:linkedin,github,twitter,behance,otro',
            'url_red'     => 'required|url|max:500',
        ]);

        // Verificar que no tenga ya esa red registrada
        $existe = RedesProfesionales::where('id_usuario', $request->id_usuario)
            ->where('nombre_red', $request->nombre_red)
            ->exists();

        if ($existe) {
            return response()->json([
                'message' => 'Ya tienes registrada esta red profesional'
            ], 409);
        }

        $red = RedesProfesionales::create([
            'id_redes_prof' => (string) Str::ulid(),
            'id_usuario'    => $request->id_usuario,
            'nombre_red'    => $request->nombre_red,
            'url_red'       => $request->url_red,
        ]);

        return response()->json([
            'message' => 'Red profesional agregada',
            'red'     => $red
        ], 201);
    }

    // Actualizar red profesional
    public function update(Request $request, $id)
    {
        $red = RedesProfesionales::findOrFail($id);

        $request->validate([
            'nombre_red' => 'sometimes|string|in:linkedin,github,twitter,behance,otro',
            'url_red'    => 'sometimes|url|max:500',
        ]);

        $red->update($request->only(['nombre_red', 'url_red']));

        return response()->json([
            'message' => 'Red profesional actualizada',
            'red'     => $red
        ]);
    }

    // Eliminar red profesional
    public function destroy($id)
    {
        $red = RedesProfesionales::where('id_redes_prof', $id)->firstOrFail();
        $red->delete();

        return response()->json([
            'message' => 'Red profesional eliminada'
        ]);
    }
}