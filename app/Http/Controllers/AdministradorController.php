<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;

class AdministradorController extends Controller
{

    public function index(Request $request)
    {

        // Verificar que sea admin
        if ($request->user()->tipo_usuario !== 'admin') {
             return response()->json(['message' => 'No autorizado.'], 403);
        }

        $query = Usuario::query();

        // Filtro por nombre, apellido o correo
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'ilike', '%' . $request->search . '%')
                  ->orWhere('apellido_paterno', 'ilike', '%' . $request->search . '%')
                  ->orWhere('email', 'ilike', '%' . $request->search . '%');
            });
        }

        // Filtro por estado
        if ($request->estado && $request->estado !== 'todos') {
            $query->where('estado_cuenta', $request->estado);
        }

        $perPage = $request->per_page ?? 10;
        $usuarios = $query->orderBy('fecha', 'desc')->paginate($perPage);

        return response()->json([
            // Cabecera — totales globales (no afectados por filtros)
            'total_usuarios'    => Usuario::count(),
            'total_activos'     => Usuario::where('estado_cuenta', 'activo')->count(),
            'total_suspendidos' => Usuario::where('estado_cuenta', 'suspendido')->count(),

            // Paginación
            'current_page' => $usuarios->currentPage(),
            'last_page'    => $usuarios->lastPage(),
            'total'        => $usuarios->total(),

            // Lista de usuarios
            'data_usuarios' => $usuarios->map(fn($u) => [
                'id_usuario'     => $u->id_usuario,
                'nombre'         => $u->nombre . ' ' . $u->apellido_paterno,
                'email'          => $u->email,
                'foto'           => $u->foto ?? null,
                'rol'            => $u->tipo_usuario,
                'estado'         => $u->estado_cuenta,
                'fecha_registro'   => \Carbon\Carbon::parse($u->fecha)->format('d M Y, h:i a'),
                'fecha_ult_acceso' => $u->estado_cuenta === 'suspendido'
                    ? 'Nunca'
                    : ($u->fecha_ult_acceso
                        ? \Carbon\Carbon::parse($u->fecha_ult_acceso)->format('d M Y, h:i a')
                        : 'Nunca'),
            ]),
        ]);
    }
}