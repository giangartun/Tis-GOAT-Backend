<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExperienciaAcademica;
use App\Models\Portafolio;
use Illuminate\Support\Facades\Validator;

class ExperienciaAcademicaController extends Controller
{
    // 1. LISTAR (GET): Público y ordenado desde el más reciente
    public function index($id_portafolio)
    {
        $academicas = ExperienciaAcademica::where('id_portafolio', $id_portafolio)
            ->orderBy('fecha_ini', 'desc')
            ->get();

        return response()->json($academicas, 200);
    }

    // 2. CREAR (POST): Protegido por Token y validado
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_portafolio' => 'required|exists:portafolio,id_portafolio',
            'institucion'   => 'required|string|max:150',
            'titulo'        => 'required|string|max:150',
            'descripcion'   => 'nullable|string',
            'fecha_ini'     => 'required|date',
            'fecha_fin'     => 'nullable|date|after_or_equal:fecha_ini',
            'visible'       => 'boolean'
        ], [
            'fecha_fin.after_or_equal' => 'La fecha de finalización no puede ser anterior a la de inicio.',
            'id_portafolio.exists'     => 'El portafolio especificado no existe.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Seguridad: El portafolio debe pertenecer al usuario autenticado
        $portafolio = Portafolio::where('id_portafolio', $request->id_portafolio)
            ->where('id_usuario', $request->user()->id_usuario)
            ->first();

        if (!$portafolio) {
            return response()->json(['message' => 'No tienes permisos para alterar este portafolio.'], 403);
        }

        $academica = ExperienciaAcademica::create($request->all());

        return response()->json([
            'message' => 'Formación académica registrada con éxito.',
            'data'    => $academica
        ], 201);
    }

    // 3. ACTUALIZAR (PUT)
    public function update(Request $request, $id)
    {
        $academica = ExperienciaAcademica::find($id);

        if (!$academica) {
            return response()->json(['message' => 'Registro no encontrado.'], 404);
        }

        // Seguridad: Validar dueño mediante el portafolio
        $portafolio = Portafolio::where('id_portafolio', $academica->id_portafolio)
            ->where('id_usuario', $request->user()->id_usuario)
            ->first();

        if (!$portafolio) {
            return response()->json(['message' => 'Acceso denegado.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'institucion' => 'sometimes|required|string|max:150',
            'titulo'      => 'sometimes|required|string|max:150',
            'descripcion' => 'nullable|string',
            'fecha_ini'   => 'sometimes|required|date',
            'fecha_fin'   => 'nullable|date|after_or_equal:fecha_ini',
            'visible'     => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $academica->update($request->all());

        return response()->json([
            'message' => 'Formación académica actualizada con éxito.',
            'data'    => $academica
        ], 200);
    }

    // 4. ELIMINAR (DELETE)
    public function destroy(Request $request, $id)
    {
        $academica = ExperienciaAcademica::find($id);

        if (!$academica) {
            return response()->json(['message' => 'Registro no encontrado.'], 404);
        }

        $portafolio = Portafolio::where('id_portafolio', $academica->id_portafolio)
            ->where('id_usuario', $request->user()->id_usuario)
            ->first();

        if (!$portafolio) {
            return response()->json(['message' => 'Acceso denegado.'], 403);
        }

        $academica->delete();

        return response()->json(['message' => 'Formación académica eliminada correctamente.'], 200);
    }
}