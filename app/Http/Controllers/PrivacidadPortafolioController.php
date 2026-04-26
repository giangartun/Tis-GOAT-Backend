<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Portafolio;
use App\Models\Proyecto;
use App\Models\Habilidad;
use App\Models\ExperienciaAcademica;
use App\Models\ExperienciaLaboral;
use App\Models\RedesProfesionales;

class PrivacidadPortafolioController extends Controller
{

    public function index(Request $request)
    {
        $usuario   = $request->user();
        $portafolio = Portafolio::where('id_usuario', $usuario->id_usuario)->firstOrFail();

        return response()->json([
            'portafolio'            => $portafolio->visible,
            'proyectos'             => !Proyecto::where('id_portafolio', $portafolio->id_portafolio)
                                            ->where('visible', false)->exists()
                                        ? true
                                        : Proyecto::where('id_portafolio', $portafolio->id_portafolio)
                                            ->select('id_proyecto', 'nombre', 'visible')->get(),
            'habilidades'           => !Habilidad::where('id_portafolio', $portafolio->id_portafolio)
                                            ->where('visible', false)->exists()
                                        ? true
                                        : Habilidad::where('id_portafolio', $portafolio->id_portafolio)
                                            ->select('id_habilidad', 'nombre', 'visible')->get(),
            'experiencia_academica' => !ExperienciaAcademica::where('id_portafolio', $portafolio->id_portafolio)
                                            ->where('visible', false)->exists()
                                        ? true
                                        : ExperienciaAcademica::where('id_portafolio', $portafolio->id_portafolio)
                                            ->select('id_experiencia_academica', 'titulo', 'visible')->get(),
            'experiencia_laboral'   => !ExperienciaLaboral::where('id_portafolio', $portafolio->id_portafolio)
                                            ->where('visible', false)->exists()
                                        ? true
                                        : ExperienciaLaboral::where('id_portafolio', $portafolio->id_portafolio)
                                            ->select('id_experiencia', 'cargo', 'visible')->get(),
            'redes_profesionales'   => !RedesProfesionales::where('id_usuario', $usuario->id_usuario)
                                            ->where('visible', false)->exists()
                                        ? true
                                        : RedesProfesionales::where('id_usuario', $usuario->id_usuario)
                                            ->select('id_redes_prof', 'nombre_red', 'visible')->get(),
        ]);
    }

    /**
     * POST /api/privacidad
     * Actualiza la visibilidad de todas las secciones
     *
     * Body esperado:
     * {
     *   "portafolio": true,
     *   "proyectos": false,
     *   "habilidades": true,
     *   "experiencia_academica": false,
     *   "experiencia_laboral": true,
     *   "redes_profesionales": false
     * }
     */
    public function actualizar(Request $request)
    {
        $request->validate([
            'portafolio'            => 'required|boolean',
            'proyectos'             => 'required|boolean',
            'habilidades'           => 'required|boolean',
            'experiencia_academica' => 'required|boolean',
            'experiencia_laboral'   => 'required|boolean',
            'redes_profesionales'   => 'required|boolean',
        ]);

        $usuario    = $request->user();
        $portafolio = Portafolio::where('id_usuario', $usuario->id_usuario)->firstOrFail();

        // Portafolio
        $portafolio->update(['visible' => $request->portafolio]);

        // Proyectos
        Proyecto::where('id_portafolio', $portafolio->id_portafolio)
            ->update(['visible' => $request->proyectos]);

        // Habilidades
        Habilidad::where('id_portafolio', $portafolio->id_portafolio)
            ->update(['visible' => $request->habilidades]);

        // Experiencia Académica
        ExperienciaAcademica::where('id_portafolio', $portafolio->id_portafolio)
            ->update(['visible' => $request->experiencia_academica]);

        // Experiencia Laboral
        ExperienciaLaboral::where('id_portafolio', $portafolio->id_portafolio)
            ->update(['visible' => $request->experiencia_laboral]);

        // Redes Profesionales (usa id_usuario directamente)
        RedesProfesionales::where('id_usuario', $usuario->id_usuario)
            ->update(['visible' => $request->redes_profesionales]);

        return response()->json([
            'message' => 'Configuración de privacidad actualizada correctamente.'
        ], 200);
    }

    /**
     * POST /api/privacidad/restablecer
     * Restablece toda la visibilidad a true
     */
    public function restablecer(Request $request)
    {
        $usuario    = $request->user();
        $portafolio = Portafolio::where('id_usuario', $usuario->id_usuario)->firstOrFail();

        $portafolio->update(['visible' => true]);

        Proyecto::where('id_portafolio', $portafolio->id_portafolio)
            ->update(['visible' => true]);

        Habilidad::where('id_portafolio', $portafolio->id_portafolio)
            ->update(['visible' => true]);

        ExperienciaAcademica::where('id_portafolio', $portafolio->id_portafolio)
            ->update(['visible' => true]);

        ExperienciaLaboral::where('id_portafolio', $portafolio->id_portafolio)
            ->update(['visible' => true]);

        RedesProfesionales::where('id_usuario', $usuario->id_usuario)
            ->update(['visible' => true]);

        return response()->json([
            'message' => 'Privacidad restablecida. Todo es visible nuevamente.'
        ], 200);
    }
}