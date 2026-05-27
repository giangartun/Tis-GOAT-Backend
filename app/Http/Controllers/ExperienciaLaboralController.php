<?php

namespace App\Http\Controllers;

use App\Helpers\RegistroActividadHelper;
use Illuminate\Http\Request;
use App\Models\ExperienciaLaboral;
use App\Models\Portafolio;
use Illuminate\Support\Facades\Validator;

class ExperienciaLaboralController extends Controller
{
    public function index($id_portafolio)
    {
        $experiencias = ExperienciaLaboral::where('id_portafolio', $id_portafolio)
            ->with('evidencias')
            ->orderBy('fecha_ini', 'desc')
            ->get();

        return response()->json($experiencias, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_portafolio' => 'required|exists:portafolio,id_portafolio',
            'empresa'       => 'required|string|max:150',
            'cargo'         => 'required|string|max:150',
            'descripcion'   => 'nullable|string',
            'fecha_ini'     => 'required|date',
            'fecha_fin'     => 'nullable|date|after_or_equal:fecha_ini',
            'visible'       => 'boolean'
        ], [
            'fecha_fin.after_or_equal' => 'La fecha de finalización no puede ser anterior a la fecha de inicio.',
            'id_portafolio.exists'     => 'El portafolio especificado no existe.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $portafolio = Portafolio::where('id_portafolio', $request->id_portafolio)
            ->where('id_usuario', $request->user()->id_usuario)
            ->first();

        if (!$portafolio) {
            return response()->json(['message' => 'No tienes permisos para alterar este portafolio.'], 403);
        }

        $experiencia = ExperienciaLaboral::create($request->all());

        RegistroActividadHelper::registrar($request->user()->id_usuario, 'modificacion_experiencia_laboral', [
            'tabla'        => 'experiencia_laboral',
            'accion'       => 'creacion',
            'id_afectado'  => $experiencia->id_experiencia,
            'registro_nuevo' => [
                'empresa'     => $experiencia->empresa,
                'cargo'       => $experiencia->cargo,
                'descripcion' => $experiencia->descripcion,
                'fecha_ini'   => $experiencia->fecha_ini,
                'fecha_fin'   => $experiencia->fecha_fin,
                'visible'     => $experiencia->visible,
            ],
        ]);

        return response()->json([
            'message' => 'Experiencia laboral registrada con éxito.',
            'data'    => $experiencia
        ], 210);
    }

    public function update(Request $request, $id)
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

        $anterior = $experiencia->only([
            'empresa', 'cargo', 'descripcion', 'fecha_ini', 'fecha_fin', 'visible'
        ]);

        $experiencia->update($request->all());

        RegistroActividadHelper::registrar($request->user()->id_usuario, 'modificacion_experiencia_laboral', [
            'tabla'             => 'experiencia_laboral',
            'accion'            => 'actualizacion',
            'id_afectado'       => $experiencia->id_experiencia,
            'registro_anterior' => $anterior,
            'registro_nuevo'    => $experiencia->only([
                'empresa', 'cargo', 'descripcion', 'fecha_ini', 'fecha_fin', 'visible'
            ]),
        ]);

        return response()->json([
            'message' => 'Experiencia laboral actualizada con éxito.',
            'data'    => $experiencia
        ], 200);
    }

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

        RegistroActividadHelper::registrar($request->user()->id_usuario, 'modificacion_experiencia_laboral', [
            'tabla'             => 'experiencia_laboral',
            'accion'            => 'eliminacion',
            'id_afectado'       => $experiencia->id_experiencia,
            'registro_anterior' => [
                'empresa'     => $experiencia->empresa,
                'cargo'       => $experiencia->cargo,
                'descripcion' => $experiencia->descripcion,
                'fecha_ini'   => $experiencia->fecha_ini,
                'fecha_fin'   => $experiencia->fecha_fin,
                'visible'     => $experiencia->visible,
            ],
        ]);

        $experiencia->delete();

        return response()->json(['message' => 'Experiencia laboral eliminada correctamente.'], 200);
    }
}