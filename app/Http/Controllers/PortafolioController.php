<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Portafolio;

class PortafolioController extends Controller
{
    // Devuelve el enlace web del portafolio del usuario autenticado
    public function obtenerUrl(Request $request)
    {
        $portafolio = Portafolio::where('id_usuario', $request->user()->id_usuario)->firstOrFail();

        return response()->json([
            'enlace_pagi_web' => $portafolio->enlace_pagi_web
        ], 200);
    }
}