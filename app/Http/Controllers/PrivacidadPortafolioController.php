<?php

namespace App\Http\Controllers;

use App\Helpers\RegistroActividadHelper;
use Illuminate\Http\Request;
use App\Models\Portafolio;
use App\Models\Proyecto;
use App\Models\Habilidad;
use App\Models\ExperienciaAcademica;
use App\Models\ExperienciaLaboral;
use App\Models\RedesProfesionales;

class PrivacidadPortafolioController extends Controller
{
    private function buildSectionResponse($items, string $primaryKey, string $labelKey): array
    {
        $bloque = [
            'TODOS' => $items->contains('visible', true)
        ];

        foreach ($items as $item) {
            $bloque[$item->$primaryKey] = [
                'nombre'  => $item->$labelKey,
                'visible' => (bool) $item->visible
            ];
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

    // captura el estado visible/oculto de todas las secciones antes de modificar
    private function capturarEstadoPrivacidad(Portafolio $portafolio, string $id_usuario): array
    {
        return [
            'portafolio' => (bool) $portafolio->visible,
            'proyectos' => Proyecto::where('id_portafolio', $portafolio->id_portafolio)
                ->select('id_proyecto', 'nombre', 'visible')->get()
                ->map(fn($p) => ['id' => $p->id_proyecto, 'nombre' => $p->nombre, 'visible' => (bool) $p->visible])
                ->toArray(),
            'habilidades' => Habilidad::where('id_portafolio', $portafolio->id_portafolio)
                ->select('id_habilidad', 'nombre', 'visible')->get()
                ->map(fn($h) => ['id' => $h->id_habilidad, 'nombre' => $h->nombre, 'visible' => (bool) $h->visible])
                ->toArray(),
            'experiencia_academica' => ExperienciaAcademica::where('id_portafolio', $portafolio->id_portafolio)
                ->select('id_experiencia_academica', 'titulo', 'visible')->get()
                ->map(fn($e) => ['id' => $e->id_experiencia_academica, 'titulo' => $e->titulo, 'visible' => (bool) $e->visible])
                ->toArray(),
            'experiencia_laboral' => ExperienciaLaboral::where('id_portafolio', $portafolio->id_portafolio)
                ->select('id_experiencia', 'cargo', 'visible')->get()
                ->map(fn($e) => ['id' => $e->id_experiencia, 'cargo' => $e->cargo, 'visible' => (bool) $e->visible])
                ->toArray(),
            'redes_profesionales' => RedesProfesionales::where('id_usuario', $id_usuario)
                ->select('id_redes_prof', 'nombre_red', 'visible')->get()
                ->map(fn($r) => ['id' => $r->id_redes_prof, 'nombre_red' => $r->nombre_red, 'visible' => (bool) $r->visible])
                ->toArray(),
        ];
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
            'proyectos'             => $this->buildSectionResponse($proyectos, 'id_proyecto', 'nombre'),
            'habilidades'           => $this->buildSectionResponse($habilidades, 'id_habilidad', 'nombre'),
            'experiencia_academica' => $this->buildSectionResponse($expAcademica, 'id_experiencia_academica', 'titulo'),
            'experiencia_laboral'   => $this->buildSectionResponse($expLaboral, 'id_experiencia', 'cargo'),
            'redes_profesionales'   => $this->buildSectionResponse($redesProfesionales, 'id_redes_prof', 'nombre_red'),
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

        // capturar estado ANTES de cualquier cambio
        $estadoAnterior = $this->capturarEstadoPrivacidad($portafolio, $usuario->id_usuario);

        $portafolio->update(['visible' => $request->portafolio]);

        $this->updateSection(
            Proyecto::where('id_portafolio', $portafolio->id_portafolio),
            'id_proyecto',
            $request->proyectos ?? []
        );
        $this->updateSection(
            Habilidad::where('id_portafolio', $portafolio->id_portafolio),
            'id_habilidad',
            $request->habilidades ?? []
        );
        $this->updateSection(
            ExperienciaAcademica::where('id_portafolio', $portafolio->id_portafolio),
            'id_experiencia_academica',
            $request->experiencia_academica ?? []
        );
        $this->updateSection(
            ExperienciaLaboral::where('id_portafolio', $portafolio->id_portafolio),
            'id_experiencia',
            $request->experiencia_laboral ?? []
        );
        $this->updateSection(
            RedesProfesionales::where('id_usuario', $usuario->id_usuario),
            'id_redes_prof',
            $request->redes_profesionales ?? []
        );
        
        $portafolio->refresh();
        $estadoNuevo = $this->capturarEstadoPrivacidad($portafolio, $usuario->id_usuario);

        RegistroActividadHelper::registrar($usuario->id_usuario, 'modificacion_privacidad', [
            'tablas_afectadas' => [
                'portafolios',
                'proyectos',
                'habilidades',
                'experiencia_academica',
                'experiencia_laboral',
                'redes_profesionales',
            ],
            'accion'            => 'actualizacion_selectiva',
            'registro_anterior' => $estadoAnterior,
            'registro_nuevo'    => $estadoNuevo,
        ]);

        return response()->json([
            'message' => 'Configuración de privacidad actualizada correctamente.'
        ], 200);
    }

    public function restablecer(Request $request)
    {
        $usuario    = $request->user();
        $portafolio = Portafolio::where('id_usuario', $usuario->id_usuario)->firstOrFail();
        $estadoAnterior = $this->capturarEstadoPrivacidad($portafolio, $usuario->id_usuario);

        $portafolio->update(['visible' => true]);
        Proyecto::where('id_portafolio', $portafolio->id_portafolio)->update(['visible' => true]);
        Habilidad::where('id_portafolio', $portafolio->id_portafolio)->update(['visible' => true]);
        ExperienciaAcademica::where('id_portafolio', $portafolio->id_portafolio)->update(['visible' => true]);
        ExperienciaLaboral::where('id_portafolio', $portafolio->id_portafolio)->update(['visible' => true]);
        RedesProfesionales::where('id_usuario', $usuario->id_usuario)->update(['visible' => true]);

        RegistroActividadHelper::registrar($usuario->id_usuario, 'modificacion_privacidad', [
            'tablas_afectadas' => [
                'portafolios',
                'proyectos',
                'habilidades',
                'experiencia_academica',
                'experiencia_laboral',
                'redes_profesionales',
            ],
            'accion'            => 'restablecimiento_total',  
            'registro_anterior' => $estadoAnterior,
            'registro_nuevo'    => 'todo_visible',            
        ]);

        return response()->json([
            'message' => 'Privacidad restablecida. Todo es visible nuevamente.'
        ], 200);
    }
}