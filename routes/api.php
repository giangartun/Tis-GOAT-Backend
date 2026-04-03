<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\HabilidadController;

// --- Rutas de Usuario ---
Route::prefix('usuario')->group(function () {
    // Endpoints de registro y autentificaciion de usuarios (no requiere token de acceso)
    Route::post('/pre-registro', [UsuarioController::class, 'preRegistro']);
    Route::get('/verificar-email/{token}', [UsuarioController::class, 'verificarEmail']);
    Route::post('/login', [UsuarioController::class, 'login']);

    // Endpoints para datos de usuario registrado (requiere token de acceso)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [UsuarioController::class, 'logout']);

        // Habilidades
        Route::get('/habilidades/{id_portafolio}', [HabilidadController::class, 'index']);
        Route::post('/habilidades', [HabilidadController::class, 'store']);
        Route::get('/habilidades/{id}', [HabilidadController::class, 'show']);
        Route::put('/habilidades/{id}', [HabilidadController::class, 'update']);
        Route::delete('/habilidades/{id}', [HabilidadController::class, 'destroy']);
    });
});

// --- Rutas de Gestión de Proyectos (SEPARADAS) ---
Route::prefix('gestion-proyectos')->group(function () {
    Route::get('/{id_portafolio}', [ProyectoController::class, 'index']);    // Listar
    Route::post('/', [ProyectoController::class, 'store']);                 // Crear
    Route::put('/{id}', [ProyectoController::class, 'update']);             // Editar
    Route::delete('/{id}', [ProyectoController::class, 'destroy']);          // Eliminar
});