<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExperienciaLaboral;
use App\Models\Portafolio;
use Illuminate\Support\Facades\Validator;

class ExperienciaLaboralController extends Controller
{
    // 1. LISTAR (GET): Ordenado de más reciente a más antiguo
    public function index($id_portafolio)
    {
        // Buscamos las experiencias de ese portafolio ordenadas por fecha de inicio descendente
        $experiencias = ExperienciaLaboral::where('id_portafolio', $id_portafolio)
            ->orderBy('fecha_ini', 'desc')
            ->get();

        return response()->json($experiencias, 200);
    }

    // 2. CREAR (POST): Con validaciones lógicas
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_portafolio' => 'required|exists:portafolio,id_portafolio',
            'empresa'       => 'required|string|max:150',
            'cargo'         => 'required|string|max:150',
            'descripcion'   => 'nullable|string',
            'fecha_ini'     => 'required|date',
            // 'after_or_equal' asegura que la fecha fin no sea menor que la de inicio
            'fecha_fin'     => 'nullable|date|after_or_equal:fecha_ini',
            'visible'       => 'boolean'
        ], [
            'fecha_fin.after_or_equal' => 'La fecha de finalización no puede ser anterior a la fecha de inicio.',
            'id_portafolio.exists'     => 'El portafolio especificado no existe.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Validación de Seguridad: Verificar que el portafolio pertenezca al usuario autenticado
        $portafolio = Portafolio::where('id_portafolio', $request->id_portafolio)
            ->where('id_usuario', $request->user()->id_usuario)
            ->first();

        if (!$portafolio) {
            return response()->json(['message' => 'No tienes permisos para alterar este portafolio.'], 403);
        }

        $experiencia = ExperienciaLaboral::create($request->all());

        return response()->json([
            'message' => 'Experiencia laboral registrada con éxito.',
            'data'    => $experiencia
        ], 210);
    }

    // 3. ACTUALIZAR (PUT)
    public function update(Request $request, $id)
    {
        $experiencia = ExperienciaLaboral::find($id);

        if (!$experiencia) {
            return response()->json(['message' => 'Registro no encontrado.'], 404);
        }

        // Validación de Seguridad a través del portafolio del usuario logueado
        $portafolio = Portafolio::where('id_portafolio', $experiencia->id_portafolio)
            ->where('id_usuario', $request->user()->id_usuario)
            ->first();

        if (!$portafolio) {
            return response()->json(['message' => 'Acceso denegado.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'empresa'     => 'sometimes|required|string|max:150',
            'cargo'       => 'sometimes|required|string|max:150',
            'descripcion' => 'nullable|string',
            'fecha_ini'   => 'sometimes|required|date',
            'fecha_fin'   => 'nullable|date|after_or_equal:fecha_ini',
            'visible'     => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $experiencia->update($request->all());

        return response()->json([
            'message' => 'Experiencia laboral actualizada con éxito.',
            'data'    => $experiencia
        ], 200);
    }

    // 4. ELIMINAR (DELETE)
    public function destroy(Request $request, $id)
    {
        $experiencia = ExperienciaLaboral::find($id);

        if (!$experiencia) {
            return response()->json(['message' => 'Registro no encontrado.'], 404);
        }

        $portafolio = Portafolio::where('id_portafolio', $experiencia->id_portafolio)
            ->where('id_usuario', $request->user()->id_usuario)
            ->first();

        if (!$portafolio) {
            return response()->json(['message' => 'Acceso denegado.'], 403);
        }

        $experiencia->delete();

        return response()->json(['message' => 'Experiencia laboral eliminada correctamente.'], 200);
    }
}