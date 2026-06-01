<?php

namespace App\Http\Controllers;

use App\Helpers\RegistroActividadHelper;
use App\Models\Proyecto;
use App\Models\Tecnologia;
use App\Models\Portafolio;
use App\Http\Requests\ProyectoStoreRequest; 
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    // 1. LISTAR TECNOLOGIAS (Llena el selector del Front)
    public function listarTecnologias()
    {
        // Trae todas las tecnologías (id, nombre, categoria)
        $tecnologias = Tecnologia::all();
        return response()->json($tecnologias, 200);
    }

    // 2. LISTAR PROYECTOS (Con buscador integrado y carga de evidencias)
    public function index(Request $request, $id_portafolio)
    {
        // Creamos la base de la consulta cargando sus tecnologías y evidencias asociadas
        $query = Proyecto::with(['tecnologias', 'evidencias'])
            ->where('id_portafolio', $id_portafolio);

        // Si el usuario escribió algo en el buscador (?buscar=...)
        if ($request->has('buscar') && !empty($request->buscar)) {
            $termino = $request->buscar;
            
            // Usamos una función anidada para que el "OR" no rompa el filtro del id_portafolio
            $query->where(function($q) use ($termino) {
                $q->where('nombre', 'LIKE', '%' . $termino . '%')
                  ->orWhere('descripcion', 'LIKE', '%' . $termino . '%');
            });
        }

        $proyectos = $query->get();
        return response()->json($proyectos, 200);
    }

    // 3. CREAR (POST): Vincula tecnologías generando ULIDs para la tabla intermedia
    public function store(ProyectoStoreRequest $request)
    {
        // Seguridad: Verificar pertenencia del portafolio
        $portafolio = Portafolio::where('id_portafolio', $request->id_portafolio)
            ->where('id_usuario', $request->user()->id_usuario)
            ->first();

        if (!$portafolio) {
            return response()->json(['message' => 'No tienes permisos para alterar este portafolio.'], 403);
        }

        $datos = $request->validated();
        $id_proyecto = (string) \Illuminate\Support\Str::ulid();
        $datos['id_proyecto'] = $id_proyecto; 

        $proyecto = Proyecto::create($datos);

        RegistroActividadHelper::registrar($request->user()->id_usuario, 'modificacion_proyectos');

        // Lógica especial para la tabla pivote con ULIDs
        if ($request->has('tecnologias') && !empty($request->tecnologias)) {
            $tecnologiasConId = [];
            foreach ($request->tecnologias as $tecId) {
                $tecnologiasConId[$tecId] = [
                    'id_proyecto_tecnologia' => (string) \Illuminate\Support\Str::ulid()
                ];
            }
            $proyecto->tecnologias()->attach($tecnologiasConId);
        }

        return response()->json([
            'message' => 'Proyecto creado exitosamente',
            'proyecto' => $proyecto->load('tecnologias')
        ], 201);
    }

    // 4. EDITAR (PUT): Sincroniza limpiando y regenerando ULIDs
    public function update(ProyectoStoreRequest $request, $id)
    {
        $proyecto = Proyecto::findOrFail($id);
        
        // Seguridad: Verificar pertenencia
        $portafolio = Portafolio::where('id_portafolio', $proyecto->id_portafolio)
            ->where('id_usuario', $request->user()->id_usuario)
            ->first();

        if (!$portafolio) {
            return response()->json(['message' => 'Acceso denegado.'], 403);
        }

        $proyecto->update($request->validated());

        RegistroActividadHelper::registrar($request->user()->id_usuario, 'modificacion_proyectos');

        if ($request->has('tecnologias')) {
            $tecnologiasConId = [];
            foreach ($request->tecnologias as $tecId) {
                $tecnologiasConId[$tecId] = [
                    'id_proyecto_tecnologia' => (string) \Illuminate\Support\Str::ulid()
                ];
            }
            $proyecto->tecnologias()->sync($tecnologiasConId);
        }

        return response()->json([
            'message' => 'Proyecto actualizado correctamente',
            'proyecto' => $proyecto->load('tecnologias')
        ], 200);
    }

    // 5. ELIMINAR (DELETE)
    public function destroy(Request $request, $id)
    {
        $proyecto = Proyecto::findOrFail($id);

        $portafolio = Portafolio::where('id_portafolio', $proyecto->id_portafolio)
            ->where('id_usuario', $request->user()->id_usuario)
            ->first();

        if (!$portafolio) {
            return response()->json(['message' => 'Acceso denegado.'], 403);
        }

        RegistroActividadHelper::registrar($request->user()->id_usuario, 'modificacion_proyectos');

        $proyecto->delete();

        return response()->json([
            'message' => 'Proyecto eliminado correctamente'
        ], 200);
    }
}