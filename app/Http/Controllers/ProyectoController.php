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
    $datos['id_proyecto'] = (string) \Illuminate\Support\Str::ulid(); // Generamos el ID del proyecto
    $datos['creado_en'] = now();

    $proyecto = Proyecto::create($datos);

    if ($request->has('tecnologias') && !empty($request->tecnologias)) {
        // Preparamos los datos para la tabla intermedia con sus propios IDs
        $tecnologiasConId = [];
        foreach ($request->tecnologias as $tecId) {
            $tecnologiasConId[$tecId] = [
                'id_proyecto_tecnologia' => (string) \Illuminate\Support\Str::ulid()
            ];
        }
        
        // Usamos sync o attach pasando los IDs adicionales
        $proyecto->tecnologias()->attach($tecnologiasConId);
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
    $tecnologiasConId = [];
    foreach ($request->tecnologias as $tecId) {
        $tecnologiasConId[$tecId] = [
            'id_proyecto_tecnologia' => (string) \Illuminate\Support\Str::ulid()
        ];
    }
    // sync() borrará las viejas y pondrá las nuevas con sus nuevos IDs
    $proyecto->tecnologias()->sync($tecnologiasConId);
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