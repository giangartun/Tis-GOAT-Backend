<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Habilidad;
use Illuminate\Support\Str;

class HabilidadController extends Controller
{
    // Obtener habilidades de un portafolio
    public function index($id_portafolio)
    {
        $habilidades = Habilidad::where('id_portafolio', $id_portafolio)
            ->orderBy('tipo')
            ->get();

        return response()->json($habilidades);
    }

    // Crear habilidad
    public function store(Request $request)
    /*
    DEBE VALIDAR QUE EL ID PORTAFOLIO EXISTA, COMO TAL LOS ID FORANEAS NO DEBERIAN PASARSE COMO PARAMETRO
    YA QUE SI COMO TAL ESTAMOS MANEJANDO TOKEN DE USUARIO, CUANDO RECIBAS ESE TOKEN, OBTIENE SUS DATOS DE USUARIO, BUSCAS SU PORTAFOLIO
    Y OBTIENES AHI EL ID DE PORTAFOLIO ASOCIADO, EL FRONTEND NO PUEDE ENVIAR ID DE PORTAFOLIO PORQ NO SABE CUAL ES EL ID ASOCIADOS
    A ESE TOKEN, EL BACKEND CONTROLA CUAL IDs ESTAN ASOCIADAS Y LO VA PONIENDO, Y SI EN CASO REQUIERES UNA COMBINACION DE VARIOS IDs
    COMO TAL PUEDES TENER UN ENDPOINT QUE TE DEVULEVA DIRECTAMENTE EL ID O IDs QUE REQUIERAS Y YA AHI LO MANDAS  COMO PARAMETRO
    , PERO SERIA UNA COORDINACION CON FRONTEND PARA QUE HAGA TODO ESA MOVIDA DE DOS LLAMADAS, PERO LO IDEAL SIEMPRE SERIA QUE EL MISMO ENDPOINT
    CONTROLE Y VALIDE DE DONDE Y CUALES SON LOS IDs ASOCIADOS AL TOKEN QUE LE ESTA LLEGANDA 
    */
    {
        $request->validate([
            'nombre'        => 'required|string|max:100',
            'tipo'          => 'required|string',
            'nivel'         => 'required|integer|min:0|max:100',
            'id_portafolio' => 'required|string',
            'visible'       => 'boolean'
        ]);

        $habilidad = Habilidad::create([
            'id_habilidad'  => (string) Str::ulid(),
            'nombre'        => $request->nombre,
            'tipo'          => $request->tipo,
            'nivel'         => $request->nivel,
            'visible'       => $request->visible ?? true,
            'id_portafolio' => $request->id_portafolio
        ]);

        return response()->json([
            'message'   => 'Habilidad creada',
            'habilidad' => $habilidad
        ], 201);
    }

    // Mostrar una habilidad específica
    public function show($id)
    {
        $habilidad = Habilidad::findOrFail($id);

        return response()->json($habilidad);
    }

    // Actualizar habilidad
    public function update(Request $request, $id)
    {
        $habilidad = Habilidad::findOrFail($id);

        $request->validate([
            'nombre'  => 'sometimes|string|max:100',
            'tipo'    => 'sometimes|string',
            'nivel'   => 'sometimes|integer|min:0|max:100',
            'visible' => 'sometimes|boolean'
        ]);

        $habilidad->update($request->only([
            'nombre', 'tipo', 'nivel', 'visible'
        ]));

        return response()->json([
            'message'   => 'Habilidad actualizada',
            'habilidad' => $habilidad
        ]);
    }

    // Eliminar habilidad
    public function destroy($id)
    {
        $habilidad = Habilidad::findOrFail($id);
        $habilidad->delete();

        return response()->json([
            'message' => 'Habilidad eliminada'
        ]);
    }
}