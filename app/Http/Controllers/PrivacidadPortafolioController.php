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
    private function buildSectionResponse($items, string $primaryKey): array
    {
        $bloque = ['TODOS' => $items->contains('visible', true)];

        foreach ($items as $item) {
            $bloque[$item->$primaryKey] = (bool) $item->visible;
        }

        return $bloque;
    }

    private function updateSection($query, string $primaryKey, array $seccion): void
    {
        if (empty($seccion)) return;

        foreach ($seccion as $id => $valor) {
            (clone $query)
                ->where($primaryKey, $id)
                ->update(['visible' => (bool) $valor]);
        }
    }

    public function index(Request $request)
    {
        $usuario    = $request->user();
        $portafolio = Portafolio::where('id_usuario', $usuario->id_usuario)->firstOrFail();

        $proyectos          = Proyecto::where('id_portafolio', $portafolio->id_portafolio)
                                ->select('id_proyecto', 'nombre', 'visible')->get();

        $habilidades        = Habilidad::where('id_portafolio', $portafolio->id_portafolio)
                                ->select('id_habilidad', 'nombre', 'visible')->get();

        $expAcademica       = ExperienciaAcademica::where('id_portafolio', $portafolio->id_portafolio)
                                ->select('id_experiencia_academica', 'titulo', 'visible')->get();

        $expLaboral         = ExperienciaLaboral::where('id_portafolio', $portafolio->id_portafolio)
                                ->select('id_experiencia', 'cargo', 'visible')->get();

        $redesProfesionales = RedesProfesionales::where('id_usuario', $usuario->id_usuario)
                                ->select('id_redes_prof', 'nombre_red', 'visible')->get();

        return response()->json([
            'portafolio'            => (bool) $portafolio->visible,
            'proyectos'             => $this->buildSectionResponse($proyectos,          'id_proyecto'),
            'habilidades'           => $this->buildSectionResponse($habilidades,        'id_habilidad'),
            'experiencia_academica' => $this->buildSectionResponse($expAcademica,       'id_experiencia_academica'),
            'experiencia_laboral'   => $this->buildSectionResponse($expLaboral,         'id_experiencia'),
            'redes_profesionales'   => $this->buildSectionResponse($redesProfesionales, 'id_redes_prof'),
        ]);
    }

    public function actualizar(Request $request)
    {
        $request->validate([
            'portafolio'            => 'required|boolean',
            'proyectos'             => 'sometimes|array',
            'habilidades'           => 'sometimes|array',
            'experiencia_academica' => 'sometimes|array',
            'experiencia_laboral'   => 'sometimes|array',
            'redes_profesionales'   => 'sometimes|array',
        ]);

        $usuario    = $request->user();
        $portafolio = Portafolio::where('id_usuario', $usuario->id_usuario)->firstOrFail();

        $portafolio->update(['visible' => $request->portafolio]);

        $this->updateSection(
            Proyecto::where('id_portafolio', $portafolio->id_portafolio),
            'id_proyecto',
            $request->proyectos
        );

        $this->updateSection(
            Habilidad::where('id_portafolio', $portafolio->id_portafolio),
            'id_habilidad',
            $request->habilidades
        );

        $this->updateSection(
            ExperienciaAcademica::where('id_portafolio', $portafolio->id_portafolio),
            'id_experiencia_academica',
            $request->experiencia_academica
        );

        $this->updateSection(
            ExperienciaLaboral::where('id_portafolio', $portafolio->id_portafolio),
            'id_experiencia',
            $request->experiencia_laboral
        );

        $this->updateSection(
            RedesProfesionales::where('id_usuario', $usuario->id_usuario),
            'id_redes_prof',
            $request->redes_profesionales
        );

        return response()->json([
            'message' => 'Configuración de privacidad actualizada correctamente.'
        ], 200);
    }

    public function restablecer(Request $request)
    {
        $usuario    = $request->user();
        $portafolio = Portafolio::where('id_usuario', $usuario->id_usuario)->firstOrFail();

        $portafolio->update(['visible' => true]);

        Proyecto::where('id_portafolio', $portafolio->id_portafolio)->update(['visible' => true]);
        Habilidad::where('id_portafolio', $portafolio->id_portafolio)->update(['visible' => true]);
        ExperienciaAcademica::where('id_portafolio', $portafolio->id_portafolio)->update(['visible' => true]);
        ExperienciaLaboral::where('id_portafolio', $portafolio->id_portafolio)->update(['visible' => true]);
        RedesProfesionales::where('id_usuario', $usuario->id_usuario)->update(['visible' => true]);

        return response()->json([
            'message' => 'Privacidad restablecida. Todo es visible nuevamente.'
        ], 200);
    }
}