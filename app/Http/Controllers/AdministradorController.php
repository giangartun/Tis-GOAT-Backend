<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use App\Models\Portafolio;
use App\Models\Habilidad;
use App\Models\Proyecto;
use App\Models\ExperienciaLaboral;
use App\Models\ExperienciaAcademica;
use App\Models\RedesProfesionales;
use App\Models\RegistroActividad;
use App\Helpers\RegistroActividadHelper;
use App\Models\Evidencia;
use App\Models\Tecnologia;
use App\Models\ProyectoTecnologia;
use App\Models\Plantilla;

class AdministradorController extends Controller
{

    public function index(Request $request)
    {
        if ($request->user()->tipo_usuario !== 'admin') {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $query = Usuario::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'ilike', '%' . $request->search . '%')
                  ->orWhere('apellido_paterno', 'ilike', '%' . $request->search . '%')
                  ->orWhere('email', 'ilike', '%' . $request->search . '%');
            });
        }

        if ($request->estado && $request->estado !== 'todos') {
            $query->where('estado_cuenta', $request->estado);
        }

        $perPage = $request->per_page ?? 10;
        $usuarios = $query->orderBy('fecha', 'desc')->paginate($perPage);

        return response()->json([
            'total_usuarios'    => Usuario::count(),
            'total_activos'     => Usuario::where('estado_cuenta', 'activo')->count(),
            'total_suspendidos' => Usuario::where('estado_cuenta', 'suspendido')->count(),
            'current_page'      => $usuarios->currentPage(),
            'last_page'         => $usuarios->lastPage(),
            'total'             => $usuarios->total(),
            'data_usuarios'     => $usuarios->map(fn($u) => [
                'id_usuario'       => $u->id_usuario,
                'nombre'           => $u->nombre . ' ' . $u->apellido_paterno,
                'email'            => $u->email,
                'foto'             => $u->foto ?? null,
                'rol'              => $u->tipo_usuario,
                'estado'           => $u->estado_cuenta,
                'fecha_registro'   => \Carbon\Carbon::parse($u->fecha)->format('d M Y, h:i a'),
                'fecha_ult_acceso' => $u->estado_cuenta === 'suspendido'
                    ? 'Nunca'
                    : ($u->ultimo_acceso
                        ? \Carbon\Carbon::parse($u->ultimo_acceso)->format('d M Y, h:i a')
                        : 'Nunca'),
            ]),
        ]);
    }

    public function suspender(Request $request, string $id)
    {
        if ($request->user()->tipo_usuario !== 'admin') {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado.'], 404);
        }

        if ($usuario->estado_cuenta === 'suspendido') {
            return response()->json(['message' => 'La cuenta ya está suspendida.'], 400);
        }

        $usuario->estado_cuenta = 'suspendido';
        $usuario->save();

        RegistroActividadHelper::registrar($usuario->id_usuario, 'cuenta_suspendida');

        return response()->json([
            'message' => 'Cuenta suspendida correctamente.',
            'motivo'  => $request->motivo ?? null,
        ], 200);
    }

    public function reactivar(Request $request, string $id)
    {
        if ($request->user()->tipo_usuario !== 'admin') {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado.'], 404);
        }

        if ($usuario->estado_cuenta === 'activo') {
            return response()->json(['message' => 'La cuenta ya está activa.'], 400);
        }

        $usuario->estado_cuenta = 'activo';
        $usuario->save();

        RegistroActividadHelper::registrar($usuario->id_usuario, 'cuenta_reactivada');

        return response()->json([
            'message' => 'Cuenta reactivada correctamente.',
        ], 200);
    }

    public function bitacora(Request $request)
    {
        if ($request->user()->tipo_usuario !== 'admin') {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        // Sin whereIn inicial — trae todo por defecto
        $query = RegistroActividad::with('usuario');

        // Filtro por tipo de acción
        if ($request->tipo && $request->tipo !== 'todos') {
            $mapeo = [
                'suspender'      => ['cuenta_suspendida'],
                'reactivar'      => ['cuenta_reactivada'],
                'modificaciones' => [
                    'modificacion_perfil',
                    'modificacion_foto',
                    'modificacion_tecnologias',
                    'modificacion_habilidades',
                    'modificacion_experiencia_laboral',
                    'modificacion_experiencia_academica',
                    'modificacion_redes_sociales',
                    'modificacion_proyectos',
                    'modificacion_evidencias',
                    'modificacion_privacidad',
                    'modificacion_plantilla',
                ],
                'creacion'       => ['cuenta_creada'],
                'login'          => ['inicio_sesion'],
                'logout'         => ['cierre_sesion'],
            ];

            if (isset($mapeo[$request->tipo])) {
                $query->whereIn('evento', $mapeo[$request->tipo]);
            }
        }

        // Filtro por fecha desde
        if ($request->fecha_desde) {
            $query->whereDate('fecha_hr', '>=', $request->fecha_desde);
        }

        // Filtro por fecha hasta
        if ($request->fecha_hasta) {
            $query->whereDate('fecha_hr', '<=', $request->fecha_hasta);
        }

        // Filtro por usuario específico
        if ($request->id_usuario) {
            $query->where('id_usuario', $request->id_usuario);
        }

        $registros = $query->orderBy('fecha_hr', 'desc')->paginate(10);

        return response()->json([
            'total'        => $registros->total(),
            'current_page' => $registros->currentPage(),
            'last_page'    => $registros->lastPage(),
            'data'         => $registros->map(fn($r) => [
                'id_registro'   => $r->id_registro,
                'id_usuario'    => $r->usuario->id_usuario,
                'nombre'        => $r->usuario->nombre . ' ' . $r->usuario->apellido_paterno,
                'email'         => $r->usuario->email,
                'foto'          => $r->usuario->foto ?? null,
                'rol'           => $r->usuario->tipo_usuario,
                'estado_actual' => $r->usuario->estado_cuenta,
                'tipo_accion'   => $r->evento,
                'fecha_accion'  => \Carbon\Carbon::parse($r->fecha_hr)->format('d M Y, h:i a'),
            ]),
        ]);
    }

    
    public function backup(Request $request)
    {
        if ($request->user()->tipo_usuario !== 'admin') {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $data = [
            'generado_en' => now()->format('d M Y, h:i a'),
            'tablas'      => [
                'plantillas'              => Plantilla::all(),
                'tecnologias'             => Tecnologia::all(),
                'usuarios'                => DB::table('usuario')->get(),
                'portafolios'             => Portafolio::all(),
                'habilidades'             => Habilidad::all(),
                'proyectos'               => Proyecto::all(),
                'proyecto_tecnologia'     => DB::table('proyecto_tecnologia')->get(),
                'evidencias'              => Evidencia::all(),
                'experiencias_laborales'  => ExperienciaLaboral::all(),
                'experiencias_academicas' => ExperienciaAcademica::all(),
                'redes_profesionales'     => RedesProfesionales::all(),
                'registro_actividad'      => RegistroActividad::all(),
            ],
        ];

        $json     = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $filename = 'backup_' . now()->format('Y_m_d_His') . '.json';

        return response($json, 200)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    public function importar(Request $request)
    {
        if ($request->user()->tipo_usuario !== 'admin') {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $request->validate([
            'archivo' => 'required|file|mimes:json,zip|max:10240',
        ]);

        try {
            $archivo   = $request->file('archivo');
            $extension = $archivo->getClientOriginalExtension();

            if ($extension === 'zip') {
                $zip  = new \ZipArchive();
                $path = $archivo->getRealPath();

                if ($zip->open($path) !== true) {
                    return response()->json(['message' => 'No se pudo abrir el ZIP.'], 400);
                }

                $contenido = null;
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $nombre = $zip->getNameIndex($i);
                    if (str_ends_with($nombre, '.json')) {
                        $contenido = $zip->getFromIndex($i);
                        break;
                    }
                }
                $zip->close();

                if (!$contenido) {
                    return response()->json(['message' => 'No se encontró un JSON dentro del ZIP.'], 400);
                }

            } else {
                $contenido = file_get_contents($archivo->getRealPath());
            }

            $data = json_decode($contenido, true);

            if (!$data || !isset($data['tablas'])) {
                return response()->json(['message' => 'Formato de backup inválido.'], 400);
            }

            $tablas = $data['tablas'];

            DB::transaction(function () use ($tablas) {

                // Truncar en orden inverso (hijos primero)
                DB::table('registro_actividad')->truncate();
                DB::table('redes_profesionales')->truncate();
                DB::table('proyecto_tecnologia')->truncate();
                DB::table('evidencia')->truncate();
                DB::table('experiencia_academica')->truncate();
                DB::table('experiencia_laboral')->truncate();
                DB::table('habilidad')->truncate();
                DB::table('proyecto')->truncate();
                DB::table('portafolio')->truncate();
                DB::table('usuario')->truncate();
                DB::table('tecnologia')->truncate();
                DB::table('plantilla')->truncate();

                // Insertar en orden correcto (padres primero)
                if (!empty($tablas['plantillas'])) {
                    DB::table('plantilla')->insert($tablas['plantillas']);
                }
                if (!empty($tablas['tecnologias'])) {
                    DB::table('tecnologia')->insert($tablas['tecnologias']);
                }
                if (!empty($tablas['usuarios'])) {
                    DB::table('usuario')->insert($tablas['usuarios']);
                }
                if (!empty($tablas['portafolios'])) {
                    DB::table('portafolio')->insert($tablas['portafolios']);
                }
                if (!empty($tablas['experiencias_academicas'])) {
                    DB::table('experiencia_academica')->insert($tablas['experiencias_academicas']);
                }
                if (!empty($tablas['experiencias_laborales'])) {
                    DB::table('experiencia_laboral')->insert($tablas['experiencias_laborales']);
                }
                if (!empty($tablas['habilidades'])) {
                    DB::table('habilidad')->insert($tablas['habilidades']);
                }
                if (!empty($tablas['proyectos'])) {
                    DB::table('proyecto')->insert($tablas['proyectos']);
                }
                if (!empty($tablas['proyecto_tecnologia'])) {
                    DB::table('proyecto_tecnologia')->insert($tablas['proyecto_tecnologia']);
                }
                if (!empty($tablas['evidencias'])) {
                    DB::table('evidencia')->insert($tablas['evidencias']);
                }
                if (!empty($tablas['redes_profesionales'])) {
                    DB::table('redes_profesionales')->insert($tablas['redes_profesionales']);
                }
                if (!empty($tablas['registro_actividad'])) {
                    DB::table('registro_actividad')->insert($tablas['registro_actividad']);
                }
            });

            return response()->json([
                'message'      => 'Backup importado correctamente.',
                'importado_en' => now()->format('d M Y, h:i a'),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al importar el backup.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

}