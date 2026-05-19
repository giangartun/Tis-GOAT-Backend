<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\RegistroActividad;
use App\Helpers\RegistroActividadHelper;

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
}