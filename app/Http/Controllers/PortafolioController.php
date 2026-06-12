<?php

namespace App\Http\Controllers;

use App\Helpers\RegistroActividadHelper;
use App\Models\Portafolio;
use Illuminate\Http\Request;

class PortafolioController extends Controller
{
    // Lista pública de portafolios
    public function index()
    {
        $portafolios = Portafolio::with([
            'usuario.redesProfesionales',
            'plantilla',
            'habilidades' => function ($query) {
                $query->where('visible', true)
                    ->where('tipo', 'tecnica')
                    ->limit(3);
            }
        ])->get()->map(function ($portafolio) {
            return [
                'id_portafolio'   => $portafolio->id_portafolio,
                'id_plantilla'    => $portafolio->id_plantilla,
                'enlace_pagi_web' => $portafolio->enlace_pagi_web,
                'enlace_publico'  => $portafolio->enlace_publico,
                'url_publica'     => $portafolio->url_publica,
                'visible'         => $portafolio->visible,
                'plantilla'       => $portafolio->plantilla ? [
                    'id_plantilla' => $portafolio->plantilla->id_plantilla,
                    'nombre'       => $portafolio->plantilla->nombre ?? null,
                    'descripcion'  => $portafolio->plantilla->descripcion ?? null,
                    'url_vista'    => $portafolio->plantilla->url_vista ?? null,
                ] : null,
                'usuario' => [
                    'id_usuario'       => $portafolio->usuario->id_usuario ?? null,
                    'nombre'           => trim(($portafolio->usuario->nombre ?? '') . ' ' . ($portafolio->usuario->apellido_paterno ?? '')),
                    'apellido_paterno' => $portafolio->usuario->apellido_paterno ?? null,
                    'apellido_materno' => $portafolio->usuario->apellido_materno ?? null,
                    'profesion'        => $portafolio->usuario->profesion ?? null,
                    'email'            => $portafolio->usuario->email ?? null,
                    'ciudad'           => $portafolio->usuario->ciudad ?? null,
                    'pais'             => $portafolio->usuario->pais ?? null,
                    'institucion'      => $portafolio->usuario->institucion ?? null,
                    'biografia'        => $portafolio->usuario->biografia ?? null,
                    'foto'             => $portafolio->usuario->foto ?? null,
                    'ubicacion'        => trim(implode(', ', array_filter([
                        $portafolio->usuario->ciudad ?? null,
                        $portafolio->usuario->pais ?? null,
                    ]))) ?: null,
                ],
                'habilidades' => $portafolio->habilidades->map(fn ($h) => [
                    'nombre' => $h->nombre,
                ])->values(),
                'redes_profesionales' => ($portafolio->usuario->redesProfesionales ?? collect())->map(function ($red) {
                    return [
                        'id_redes_prof' => $red->id_redes_prof,
                        'nombre_red'    => $red->nombre_red,
                        'url_red'       => $red->url_red,
                        'visible'       => $red->visible,
                    ];
                })->values(),
            ];
        });

        return response()->json($portafolios);
    }

    public function show($id_portafolio)
    {
        $portafolio = Portafolio::with([
            'usuario.redesProfesionales',
            'plantilla',
            'habilidades'          => fn ($q) => $q->where('visible', true),
            'experienciasLaborales',
            'experienciasAcademicas',
            'proyectos'            => fn ($q) => $q->where('visible', true),
            'proyectos.tecnologias',
            'proyectos.evidencias',
        ])->where('id_portafolio', $id_portafolio)->first();

        if (!$portafolio) {
            return response()->json(['message' => 'Portafolio no encontrado'], 404);
        }

        return response()->json($portafolio);
    }

    // Vista pública completa
    public function showPublico($id_portafolio)
    {
        $portafolio = Portafolio::with([
            'usuario.redesProfesionales',
            'plantilla',
            'habilidades' => fn ($q) => $q->where('visible', true),
            'experienciasLaborales',
            'experienciasAcademicas',
            'proyectos' => fn ($q) => $q->where('visible', true),
            'proyectos.tecnologias',
            'proyectos.evidencias',
        ])
        ->where('id_portafolio', $id_portafolio)
        ->first();

        if (!$portafolio) {
            return response()->json(['message' => 'Portafolio no encontrado'], 404);
        }

        return response()->json([
            'data' => [
                'usuario' => $portafolio->usuario ? [
                    'id_usuario'         => $portafolio->usuario->id_usuario ?? null,
                    'nombre'             => $portafolio->usuario->nombre ?? null,
                    'apellido_paterno'   => $portafolio->usuario->apellido_paterno ?? null,
                    'apellido_materno'   => $portafolio->usuario->apellido_materno ?? null,
                    'profesion'          => $portafolio->usuario->profesion ?? null,
                    'email'              => $portafolio->usuario->email ?? null,
                    'ciudad'             => $portafolio->usuario->ciudad ?? null,
                    'pais'               => $portafolio->usuario->pais ?? null,
                    'institucion'        => $portafolio->usuario->institucion ?? null,
                    'biografia'          => $portafolio->usuario->biografia ?? null,
                    'foto'               => $portafolio->usuario->foto ?? null,
                ] : null,

                'portafolio' => [
                    'id_portafolio'   => $portafolio->id_portafolio,
                    'id_plantilla'    => $portafolio->id_plantilla,
                    'enlace_pagi_web' => $portafolio->enlace_pagi_web,
                    'enlace_publico'  => $portafolio->enlace_publico,
                    'url_publica'     => $portafolio->url_publica,
                    'visible'         => $portafolio->visible,
                    'plantilla'       => $portafolio->plantilla ? [
                        'id_plantilla' => $portafolio->plantilla->id_plantilla,
                        'nombre'       => $portafolio->plantilla->nombre ?? null,
                        'descripcion'  => $portafolio->plantilla->descripcion ?? null,
                        'url_vista'    => $portafolio->plantilla->url_vista ?? null,
                    ] : null,
                ],

                'redes_profesionales' => ($portafolio->usuario->redesProfesionales ?? collect())->map(function ($red) {
                    return [
                        'id_redes_prof' => $red->id_redes_prof,
                        'nombre_red'    => $red->nombre_red,
                        'url_red'       => $red->url_red,
                        'visible'       => $red->visible,
                    ];
                })->values(),

                'habilidades' => $portafolio->habilidades->map(function ($habilidad) {
                    return [
                        'id_habilidad' => $habilidad->id_habilidad,
                        'nombre'       => $habilidad->nombre,
                        'tipo'         => $habilidad->tipo,
                        'categoria'    => $habilidad->categoria,
                        'nivel'        => $habilidad->nivel,
                        'visible'      => $habilidad->visible,
                    ];
                })->values(),

                'experiencias_laborales' => $portafolio->experienciasLaborales->map(function ($exp) {
                    return [
                        'id_experiencia' => $exp->id_experiencia,
                        'empresa'        => $exp->empresa,
                        'cargo'          => $exp->cargo,
                        'descripcion'    => $exp->descripcion,
                        'fecha_ini'      => $exp->fecha_ini,
                        'fecha_fin'      => $exp->fecha_fin,
                        'visible'        => $exp->visible,
                    ];
                })->values(),

                'experiencias_academicas' => $portafolio->experienciasAcademicas->map(function ($exp) {
                    return [
                        'id_experiencia_academica' => $exp->id_experiencia_academica,
                        'institucion'              => $exp->institucion,
                        'titulo'                   => $exp->titulo,
                        'descripcion'              => $exp->descripcion,
                        'fecha_ini'                => $exp->fecha_ini,
                        'fecha_fin'                => $exp->fecha_fin,
                        'visible'                  => $exp->visible,
                    ];
                })->values(),

                'proyectos' => $portafolio->proyectos->map(function ($proyecto) {
                    return [
                        'id_proyecto'  => $proyecto->id_proyecto,
                        'nombre'       => $proyecto->nombre,
                        'descripcion'  => $proyecto->descripcion,
                        'url_proyecto' => $proyecto->url_proyecto,
                        'imagen_url'   => $proyecto->imagen_url,
                        'visible'      => $proyecto->visible,
                        'tecnologias'  => $proyecto->tecnologias->map(function ($tec) {
                            return [
                                'id_tecnologia' => $tec->id_tecnologia,
                                'nombre'        => $tec->nombre,
                                'tipo'          => $tec->tipo ?? null,
                            ];
                        })->values(),
                        'evidencias' => $proyecto->evidencias->map(function ($evi) {
                            return [
                                'id_evidencia'   => $evi->id_evidencia,
                                'tipo'           => $evi->tipo ?? null,
                                'url_evidencia'  => $evi->url_evidencia ?? null,
                                'nombre_archivo' => $evi->nombre_archivo ?? null,
                                'foto_url'       => $evi->foto_url ?? null,
                                'tamano_bytes'   => $evi->tamano_bytes ?? null,
                                'fecha_subida'   => $evi->fecha_subida ?? null,
                            ];
                        })->values(),
                    ];
                })->values(),
            ],
        ], 200);
    }

    public function obtenerCompleto(Request $request)
    {
        $usuario = auth()->user();

        if (!$usuario) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $usuario->load([
            'portafolio.plantilla',
            'portafolio.proyectos.tecnologias',
            'portafolio.proyectos.evidencias',
            'portafolio.habilidades',
            'portafolio.experienciasLaborales',
            'portafolio.experienciasAcademicas',
            'redesProfesionales',
        ]);

        $portafolio = $usuario->portafolio;

        return response()->json([
            'usuario' => [
                'id_usuario'       => $usuario->id_usuario,
                'nombre'           => $usuario->nombre,
                'apellido_paterno' => $usuario->apellido_paterno,
                'apellido_materno' => $usuario->apellido_materno,
                'profesion'        => $usuario->profesion ?? null,
                'email'            => $usuario->email,
                'ciudad'           => $usuario->ciudad ?? null,
                'pais'             => $usuario->pais ?? null,
                'institucion'      => $usuario->institucion ?? null,
                'biografia'        => $usuario->biografia,
                'foto'             => $usuario->foto,
                'fecha'            => $usuario->fecha,
            ],
            'portafolio' => $portafolio ? [
                'id_portafolio'   => $portafolio->id_portafolio,
                'id_plantilla'    => $portafolio->id_plantilla,
                'enlace_pagi_web' => $portafolio->enlace_pagi_web,
                'visible'         => $portafolio->visible,
                'creado_en'       => $portafolio->creado_en,
                'fecha_act'       => $portafolio->fecha_act,
                'plantilla'       => $portafolio->plantilla ? [
                    'id_plantilla' => $portafolio->plantilla->id_plantilla,
                    'nombre'       => $portafolio->plantilla->nombre ?? null,
                    'descripcion'  => $portafolio->plantilla->descripcion ?? null,
                    'url_vista'    => $portafolio->plantilla->url_vista ?? null,
                ] : null,
            ] : null,

            'redes_profesionales' => ($usuario->redesProfesionales ?? collect())->map(function ($red) {
                return [
                    'id_redes_prof' => $red->id_redes_prof,
                    'nombre_red'    => $red->nombre_red,
                    'url_red'       => $red->url_red,
                    'visible'       => $red->visible,
                ];
            })->values(),

            'habilidades' => $portafolio
                ? $portafolio->habilidades->map(function ($habilidad) {
                    return [
                        'id_habilidad' => $habilidad->id_habilidad,
                        'nombre'       => $habilidad->nombre,
                        'tipo'         => $habilidad->tipo,
                        'categoria'    => $habilidad->categoria,
                        'nivel'        => $habilidad->nivel,
                        'visible'      => $habilidad->visible,
                    ];
                })->values()
                : [],

            'experiencias_laborales' => $portafolio
                ? $portafolio->experienciasLaborales->map(function ($exp) {
                    return [
                        'id_experiencia' => $exp->id_experiencia,
                        'empresa'        => $exp->empresa,
                        'cargo'          => $exp->cargo,
                        'descripcion'    => $exp->descripcion,
                        'fecha_ini'      => $exp->fecha_ini,
                        'fecha_fin'      => $exp->fecha_fin,
                        'visible'        => $exp->visible,
                    ];
                })->values()
                : [],

            'experiencias_academicas' => $portafolio
                ? $portafolio->experienciasAcademicas->map(function ($exp) {
                    return [
                        'id_experiencia_academica' => $exp->id_experiencia_academica,
                        'institucion'              => $exp->institucion,
                        'titulo'                   => $exp->titulo,
                        'descripcion'              => $exp->descripcion,
                        'fecha_ini'                => $exp->fecha_ini,
                        'fecha_fin'                => $exp->fecha_fin,
                        'visible'                  => $exp->visible,
                    ];
                })->values()
                : [],

            'proyectos' => $portafolio
                ? $portafolio->proyectos->map(function ($proyecto) {
                    return [
                        'id_proyecto'  => $proyecto->id_proyecto,
                        'nombre'       => $proyecto->nombre,
                        'descripcion'  => $proyecto->descripcion,
                        'url_proyecto' => $proyecto->url_proyecto,
                        'imagen_url'   => $proyecto->imagen_url,
                        'fecha_ini'    => $proyecto->fecha_ini,
                        'fecha_fin'    => $proyecto->fecha_fin,
                        'visible'      => $proyecto->visible,
                        'tecnologias'  => $proyecto->tecnologias->map(function ($tec) {
                            return [
                                'id_tecnologia' => $tec->id_tecnologia,
                                'nombre'        => $tec->nombre,
                                'tipo'          => $tec->tipo ?? null,
                            ];
                        })->values(),
                        'evidencias' => $proyecto->evidencias->map(function ($evi) {
                            return [
                                'id_evidencia'   => $evi->id_evidencia,
                                'tipo'           => $evi->tipo ?? null,
                                'url_evidencia'  => $evi->url_evidencia ?? null,
                                'nombre_archivo' => $evi->nombre_archivo ?? null,
                                'foto_url'       => $evi->foto_url ?? null,
                                'tamano_bytes'   => $evi->tamano_bytes ?? null,
                                'fecha_subida'   => $evi->fecha_subida ?? null,
                            ];
                        })->values(),
                    ];
                })->values()
                : [],
        ], 200);
    }

    public function actualizarPlantilla(Request $request)
    {
        $request->validate([
            'id_plantilla' => 'required|exists:plantilla,id_plantilla',
        ]);

        $usuario = auth()->user();

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado.'
            ], 401);
        }

        $portafolio = Portafolio::where('id_usuario', $usuario->id_usuario)->first();

        if (!$portafolio) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró un portafolio para este usuario.'
            ], 404);
        }

        $plantillaAnterior = $portafolio->id_plantilla;

        $portafolio->id_plantilla = $request->id_plantilla;
        $portafolio->fecha_act = now();
        $portafolio->save();

        $portafolio->load('plantilla');

        RegistroActividadHelper::registrar($usuario->id_usuario, 'modificacion_plantilla', [
            'tabla'               => 'portafolios',
            'accion'              => 'actualizacion',
            'id_afectado'         => $portafolio->id_portafolio,
            'registro_anterior'   => [
                'id_plantilla' => $plantillaAnterior,
            ],
            'registro_nuevo'      => [
                'id_plantilla' => $portafolio->id_plantilla,
                'nombre'       => $portafolio->plantilla->nombre ?? null,
                'url_vista'    => $portafolio->plantilla->url_vista ?? null,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Plantilla actualizada correctamente.',
            'data'    => [
                'id_portafolio' => $portafolio->id_portafolio,
                'id_plantilla'  => $portafolio->id_plantilla,
                'fecha_act'     => $portafolio->fecha_act,
                'plantilla'     => $portafolio->plantilla,
            ]
        ], 200);
    }

    public function obtenerUrl(Request $request)
    {
        $portafolio = Portafolio::where(
            'id_usuario',
            auth()->user()->id_usuario
        )->firstOrFail();

        return response()->json([
            'enlace_pagi_web' => $portafolio->enlace_pagi_web
        ], 200);
    }
}