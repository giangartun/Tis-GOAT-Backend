<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Portafolio;
use App\Models\Habilidad;
use Illuminate\Http\Request;
use App\Models\Usuario;

class PortafolioController extends Controller
{
    // Lista pública de portafolios (sin token)
    public function index()
    {
        $portafolios = Portafolio::with(['usuario', 'habilidades' => function ($query) {
                $query->where('visible', true)
                    ->where('tipo', 'tecnica')
                      ->limit(3); // solo muestra 3 etiquetas como en el mockup
            }])
            ->get()
            ->map(function ($portafolio) {
                return [
                    'id_portafolio' => $portafolio->id_portafolio,
                    'enlace_pagi_web' => $portafolio->enlace_pagi_web,
                    'usuario' => [
                        'nombre'          => $portafolio->usuario->nombre . ' ' . 
                                            $portafolio->usuario->apellido_paterno,
                        'profesion'       => $portafolio->usuario->biografia,
                        'foto'            => $portafolio->usuario->foto,
                        'ubicacion'       => null, // si no tienes ese campo aún
                    ],
                    'habilidades' => $portafolio->habilidades->map(fn($h) => [
                        'nombre' => $h->nombre
                    ])
                ];
            });

        return response()->json($portafolios);
    }

    // Ver perfil público de un portafolio específico
    public function show($id_portafolio)
    {
        $portafolio = Portafolio::with([
            'usuario',
            'habilidades'         => fn($q) => $q->where('visible', true),
            'experienciasLaborales',
            'experienciasAcademicas',
            'proyectos'           => fn($q) => $q->where('visible', true) ?? $q,
        ])->where('id_portafolio', $id_portafolio)->first();

        if (!$portafolio) {
            return response()->json(['message' => 'Portafolio no encontrado'], 404);
        }

        return response()->json($portafolio);
    }

    /**
     * Devuelve toda la información del portafolio del usuario autenticado
     */
    public function obtenerCompleto(Request $request)
    {
        $usuario = Usuario::with([
            'portafolio.plantilla',
            'portafolio.proyectos.tecnologias',
            'portafolio.proyectos.evidencias',
            'portafolio.habilidades',
            'portafolio.experienciasLaborales',
            'portafolio.experienciasAcademicas',
            'redesProfesionales',
        ])
        ->where('id_usuario', $request->user()->id_usuario)
        ->firstOrFail();

        $portafolio = $usuario->portafolio;

        return response()->json([
            'usuario' => [
                'id_usuario' => $usuario->id_usuario,
                'nombre' => $usuario->nombre,
                'apellido_paterno' => $usuario->apellido_paterno,
                'apellido_materno' => $usuario->apellido_materno,
                'email' => $usuario->email,
                'biografia' => $usuario->biografia,
                'foto' => $usuario->foto,
                'fecha' => $usuario->fecha,
            ],
            'portafolio' => $portafolio ? [
                'id_portafolio' => $portafolio->id_portafolio,
                'id_plantilla' => $portafolio->id_plantilla,
                'enlace_pagi_web' => $portafolio->enlace_pagi_web,
                'visible' => $portafolio->visible,
                'creado_en' => $portafolio->creado_en,
                'fecha_act' => $portafolio->fecha_act,
            ] : null,
            'redes_profesionales' => $usuario->redesProfesionales->map(function ($red) {
                return [
                    'id_redes_prof' => $red->id_redes_prof,
                    'nombre_red' => $red->nombre_red,
                    'url_red' => $red->url_red,
                    'visible' => $red->visible,
                ];
            })->values(),
            'habilidades' => $portafolio
                ? $portafolio->habilidades->map(function ($habilidad) {
                    return [
                        'id_habilidad' => $habilidad->id_habilidad,
                        'nombre' => $habilidad->nombre,
                        'tipo' => $habilidad->tipo,
                        'categoria' => $habilidad->categoria,
                        'nivel' => $habilidad->nivel,
                        'visible' => $habilidad->visible,
                    ];
                })->values()
                : [],
            'experiencia_laboral' => $portafolio
                ? $portafolio->experienciasLaborales->map(function ($exp) {
                    return [
                        'id_experiencia' => $exp->id_experiencia,
                        'empresa' => $exp->empresa,
                        'cargo' => $exp->cargo,
                        'descripcion' => $exp->descripcion,
                        'fecha_ini' => $exp->fecha_ini,
                        'fecha_fin' => $exp->fecha_fin,
                        'visible' => $exp->visible,
                    ];
                })->values()
                : [],
            'experiencia_academica' => $portafolio
                ? $portafolio->experienciasAcademicas->map(function ($exp) {
                    return [
                        'id_experiencia_academica' => $exp->id_experiencia_academica,
                        'institucion' => $exp->institucion,
                        'titulo' => $exp->titulo,
                        'descripcion' => $exp->descripcion,
                        'fecha_ini' => $exp->fecha_ini,
                        'fecha_fin' => $exp->fecha_fin,
                        'visible' => $exp->visible,
                    ];
                })->values()
                : [],
            'proyectos' => $portafolio
                ? $portafolio->proyectos->map(function ($proyecto) {
                    return [
                        'id_proyecto' => $proyecto->id_proyecto,
                        'nombre' => $proyecto->nombre,
                        'descripcion' => $proyecto->descripcion,
                        'url_proyecto' => $proyecto->url_proyecto,
                        'imagen_url' => $proyecto->imagen_url,
                        'fecha_ini' => $proyecto->fecha_ini,
                        'fecha_fin' => $proyecto->fecha_fin,
                        'visible' => $proyecto->visible,
                        'tecnologias' => $proyecto->tecnologias->map(function ($tec) {
                            return [
                                'id_tecnologia' => $tec->id_tecnologia,
                                'nombre' => $tec->nombre,
                                'tipo' => $tec->tipo ?? null,
                            ];
                        })->values(),
                        'evidencias' => $proyecto->evidencias->map(function ($evi) {
                            return [
                                'id_evidencia' => $evi->id_evidencia,
                                'url' => $evi->url ?? null,
                            ];
                        })->values(),
                    ];
                })->values()
                : [],
        ], 200);
    }

    /**
     * Si todavía quieres conservar el endpoint de solo URL
     */
    public function obtenerUrl(Request $request)
    {
        $portafolio = \App\Models\Portafolio::where('id_usuario', $request->user()->id_usuario)->firstOrFail();

        return response()->json([
            'enlace_pagi_web' => $portafolio->enlace_pagi_web
        ], 200);
    }
}
