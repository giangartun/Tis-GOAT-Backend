<?php

namespace App\Http\Controllers;

use App\Models\Plantilla;
use Illuminate\Http\Request;

class PlantillaController extends Controller
{
    /**
     * Devuelve el catálogo de plantillas disponibles
     */
    public function index()
    {
        // Traemos todas las plantillas de la base de datos
        $plantillas = Plantilla::all();
        
        return response()->json([
            'success' => true,
            'data' => $plantillas
        ], 200);
    }
}