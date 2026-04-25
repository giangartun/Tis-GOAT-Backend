<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Portafolio;
use App\Mail\VerificacionEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class UsuarioController extends Controller
{
    // Valida los datos, guarda en caché y envía token al correo
    public function preRegistro(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'            => 'required|email|unique:usuario,email',
            'contrasena'       => 'required|min:6|confirmed',
            'nombre'           => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'required|string|max:100',
            'biografia'        => 'nullable|string',
            'foto'             => 'nullable|string',
        ], [
            'email.unique'        => 'Este correo ya está registrado.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $token = Str::random(64);

        Cache::put('registro_' . $request->email, [
            'token'            => $token,
            'email'            => $request->email,
            'contrasena'       => Hash::make($request->contrasena),
            'nombre'           => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'biografia'        => $request->biografia,
            'foto'             => $request->foto,
        ], now()->addMinutes(5));

        Cache::put('token_' . $token, $request->email, now()->addMinutes(5));

        // En produccion esto suele, fallar, se espera e para servidores de la UNI ya fucnionen sin problemas
        /*
        try {
            Mail::to($request->email)->send(
                new VerificacionEmail($token, $request->nombre)
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al enviar el correo de verificación.',
                'error'   => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Revisa tu correo para completar el registro. El enlace expira en 15 minutos.'
        ], 200);
        */
        
        // Implementacion temporal usando N8N para deployado en RENDER (Validar si quitar luego de q deployemos en el servidor real)
        Http::timeout(5)
            ->when(app()->environment('local'), fn($http) => $http->withoutVerifying())
            ->post("https://training.intersim.cloud/webhook/bdc3192b-756f-492c-b677-9cf13897e1b9", [
                'email'  => $request->email,
                'nombre' => $request->nombre,
                'token'  => $token,
            ]);

        return response()->json([
            'message' => 'Revisa tu correo para completar el registro. El enlace expira en 5 minutos.'
        ], 200);
            
    }

    // Valida el token del correo y recién crea el usuario en BD
    public function verificarEmail(string $token)
    {
        $email = Cache::get('token_' . $token);

        if (!$email) {
            return response()->json(['message' => 'Token inválido o expirado.'], 400);
        }

        $datos = Cache::get('registro_' . $email);

        if (!$datos) {
            return response()->json(['message' => 'Token inválido o expirado.'], 400);
        }

        if ($datos['token'] !== $token) {
            return response()->json(['message' => 'Token inválido.'], 400);
        }

        DB::beginTransaction();

        try {
            unset($datos['token']);

            //Crear usuario
            $datos['fecha'] = now();
            $usuario = Usuario::create($datos);

            //Generar slug limpio
            $base = Str::slug($usuario->nombre . '-' . $usuario->apellido_paterno);

            //Asegurar unicidad (usando parte del ULID)
            $slug = $base . '-' . substr($usuario->id_usuario, 0, 6);

            //Construir URL completa desde .env
            $frontend = rtrim(env('FRONTEND_URL'), '/');
            $urlCompleta = $frontend . '/' . $slug;

            //Crear portafolio
            $portafolio = Portafolio::create([
                'id_usuario' => $usuario->id_usuario,
                'id_plantilla' => null,
                'enlace_pagi_web' => $urlCompleta,
                'creado_en' => now(),
                'fecha_act' => now(),
            ]);

            DB::commit();

            Cache::forget('registro_' . $email);
            Cache::forget('token_' . $token);

            return response()->json(['message' => 'Email verificado. Ya puedes iniciar sesión.'], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al crear usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Valida credenciales y devuelve token de sesión (solo el token debe validarse en cada peticion que le llege)
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'      => 'required|email',
            'contrasena' => 'required'
        ], [
            'email.required'      => 'El correo es obligatorio.',
            'email.email'         => 'El formato del correo no es válido.',
            'contrasena.required' => 'La contraseña es obligatoria.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->contrasena, $usuario->contrasena)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $portafolio = Portafolio::where('id_usuario', $usuario->id_usuario)->first();

        $usuario->tokens()->delete();

        $token = $usuario->createToken('sesion', ['*'], now()->addDays(7))->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'token'   => $token,
            'usuario' => [
                'id_usuario' => $usuario->id_usuario,
                'nombre'     => $usuario->nombre,
                'apellido_paterno'  => $usuario->apellido_paterno,
                'apellido_materno'  => $usuario->apellido_materno,
                'email'      => $usuario->email,
            ],
            'id_portafolio' => $portafolio ? $portafolio->id_portafolio : null
        ], 200);
    }

    // Cierra la sesión eliminando el token actual
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.'
        ], 200);
    }
}