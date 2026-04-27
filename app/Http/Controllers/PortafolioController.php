<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Portafolio;
use App\Models\Habilidad;
use Illuminate\Http\Request;

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
}