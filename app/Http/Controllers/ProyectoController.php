<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Http\Requests\ProyectoStoreRequest; 
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    // Listar proyectos de un portafolio específico (Útil para la vista "Mis Proyectos")
    public function index($id_portafolio)
    {
        $proyectos = Proyecto::with('tecnologias')
            ->where('id_portafolio', $id_portafolio)
            ->get();

        return response()->json($proyectos, 200);
    }

    // AGREGAR: Crea el proyecto y vincula tecnologías
    public function store(ProyectoStoreRequest $request)
    {
        $datos = $request->validated();
        $datos['creado_en'] = now(); // Seteamos la fecha de creación

        $proyecto = Proyecto::create($datos);

        if ($request->has('tecnologias')) {
            $proyecto->tecnologias()->attach($request->tecnologias);
        }

        return response()->json([
            'message' => 'Proyecto creado exitosamente',
            'data' => $proyecto->load('tecnologias')
        ], 201);
    }

    // EDITAR: Actualiza datos y sincroniza tecnologías
    public function update(ProyectoStoreRequest $request, $id)
    {
        $proyecto = Proyecto::findOrFail($id);
        
        $proyecto->update($request->validated());

        if ($request->has('tecnologias')) {
            // sync() es mejor que attach() en edición: quita las viejas y pone las nuevas
            $proyecto->tecnologias()->sync($request->tecnologias);
        }

        return response()->json([
            'message' => 'Proyecto actualizado correctamente',
            'data' => $proyecto->load('tecnologias')
        ], 200);
    }

    // ELIMINAR: Borra el proyecto (la tabla intermedia se limpia sola por el CASCADE)
    public function destroy($id)
    {
        $proyecto = Proyecto::findOrFail($id);
        $proyecto->delete();

        return response()->json([
            'message' => 'Proyecto eliminado correctamente'
        ], 200);
    }
}