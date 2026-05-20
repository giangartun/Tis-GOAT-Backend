<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Helpers\RegistroActividadHelper;
use App\Models\Portafolio;
use App\Mail\VerificacionEmail;
use App\Mail\RecuperarPasswordMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
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
            'tipo_usuario'     => 'usuario',  
            'estado_cuenta'    => 'activo',  
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
            
            RegistroActividadHelper::registrar($usuario->id_usuario, 'cuenta_creada');

            $codigo = substr($usuario->id_usuario, 0, 6);
            $nombre = Str::slug($usuario->nombre . '-' . $usuario->apellido_paterno);
            $frontend = rtrim(env('FRONTEND_URL'), '/'); 
            $urlCompleta = $frontend . '/' . $codigo . '/' . $nombre;

            //Crear portafolio
            $portafolio = Portafolio::create([
                'id_usuario' => $usuario->id_usuario,
                'id_plantilla' => null,
                'enlace_pagi_web' => $urlCompleta,
                'visible' => true,
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

        if ($usuario->estado_cuenta === 'suspendido') {
            return response()->json(['message' => 'Esta cuenta ha sido suspendida'], 403);
        }

        $portafolio = Portafolio::where('id_usuario', $usuario->id_usuario)->first();

        $usuario->tokens()->delete();

        $usuario->fecha_ult_acceso = now(); 
        $usuario->save();         
        
        RegistroActividadHelper::registrar($usuario->id_usuario, 'inicio_sesion');

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
                'estado_cuenta' => $usuario->estado_cuenta,
                'tipo_usuario' => $usuario->tipo_usuario,
            ],
            'id_portafolio' => $portafolio ? $portafolio->id_portafolio : null
        ], 200);
    }

    // Cierra la sesión eliminando el token actual
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        RegistroActividadHelper::registrar($request->user()->id_usuario, 'cierre_sesion');

        return response()->json([
            'message' => 'Sesión cerrada correctamente.'
        ], 200);
    }

    // HU12: Generar token de recuperación y enviar email
    public function enviarEnlaceReset(Request $request)
    {
        // 1. Valida que el email exista en la tabla 'usuario'
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:usuario,email'
        ], [
            'email.exists' => 'No encontramos ningún usuario con ese correo electrónico.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $email = $request->email;
        $token = Str::random(64);

        try {
            // 2. Guardamos en la tabla migrada (password_reset_tokens)
            // Si ya pidió uno antes, se actualiza el token y la fecha
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]
            );

            // 3. Enviamos el correo (usando el SMTP)
            Mail::to($email)->send(new RecuperarPasswordMail($token));

            return response()->json([
                'message' => 'Se ha enviado un enlace de recuperación a tu correo.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // HU12 - Parte 2: Validar token y actualizar la contraseña
    public function resetearContrasena(Request $request)
    {
        // 1. Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'token'      => 'required',
            'email'      => 'required|email|exists:usuario,email',
            'contrasena' => 'required|min:6|confirmed', // 'confirmed' busca 'contrasena_confirmation'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // 2. Verificar si el token existe y es válido para ese email
        $registro = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$registro) {
            return response()->json(['message' => 'El token es inválido o el correo no coincide.'], 400);
        }

        // 3. (Opcional) Verificar si el token expiró (ejemplo: 60 minutos)
        $expiracion = 60;
        if (Carbon::parse($registro->created_at)->addMinutes($expiracion)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json(['message' => 'El enlace ha expirado.'], 400);
        }

        // 4. Actualiza la contraseña en la tabla 'usuario'
        $usuario = Usuario::where('email', $request->email)->first();
        $usuario->contrasena = Hash::make($request->contrasena);
        $usuario->save();

        // 5. Borra el token para que no se pueda usar de nuevo
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Tu contraseña ha sido actualizada con éxito.'
        ], 200);
    }
}