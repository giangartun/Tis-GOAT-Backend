<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Mail\VerificacionEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

        Mail::to($request->email)->send(
            new VerificacionEmail($token, $request->nombre)
        );

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

        unset($datos['token']);
        Usuario::create($datos);

        Cache::forget('registro_' . $email);
        Cache::forget('token_' . $token);

        return response()->json(['message' => 'Email verificado. Ya puedes iniciar sesión.'], 201);
    }

    // Valida credenciales del usuario registrado
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'contrasena' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->contrasena, $usuario->contrasena)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        return response()->json([
            'message' => 'Login exitoso',
            'usuario' => $usuario
        ], 200);
    }
}