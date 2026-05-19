<?php

namespace App\Http\Controllers;

use App\Helpers\RegistroActividadHelper;
use App\Models\Proyecto;
use App\Models\Tecnologia;
use App\Http\Requests\ProyectoStoreRequest; 
use Illuminate\Http\Request;

class ProyectoController extends Controller
{

    public function listarTecnologias()
    {
        // Trae todas las tecnologías (id, nombre, categoria)
        $tecnologias = Tecnologia::all();
        return response()->json($tecnologias, 200);
    }
    // Listar proyectos con opción de búsqueda integrada
    public function index(Request $request, $id_portafolio)
    {
        // 1. Creamos la base de la consulta filtrando por el portafolio
        $query = Proyecto::with(['tecnologias', 'evidencias'])
            ->where('id_portafolio', $id_portafolio);

        // 2. Si el usuario escribió algo en el buscador (?buscar=...)
        if ($request->has('buscar') && !empty($request->buscar)) {
            $termino = $request->buscar;
            
            // Usamos una función anidada para que el "OR" no rompa el filtro del id_portafolio
            $query->where(function($q) use ($termino) {
                $q->where('nombre', 'LIKE', '%' . $termino . '%')
                ->orWhere('descripcion', 'LIKE', '%' . $termino . '%');
            });
        }

        // 3. Obtenemos los resultados finalizados
        $proyectos = $query->get();

        return response()->json($proyectos, 200);
    }

    // AGREGAR: Crea el proyecto y vincula tecnologías
    public function store(ProyectoStoreRequest $request)
    {
    $datos = $request->validated();
    $datos['id_proyecto'] = (string) \Illuminate\Support\Str::ulid(); // Generamos el ID del proyecto
    $datos['creado_en'] = now();

    $proyecto = Proyecto::create($datos);

    RegistroActividadHelper::registrar($request->user()->id_usuario, 'modificacion_proyectos');

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

        RegistroActividadHelper::registrar($request->user()->id_usuario, 'modificacion_proyectos');

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

        RegistroActividadHelper::registrar($proyecto->portafolio->id_usuario, 'modificacion_proyectos');

        $proyecto->delete();

        return response()->json([
            'message' => 'Proyecto eliminado correctamente'
        ], 200);
    }
}