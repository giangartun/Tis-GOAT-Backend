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

        //TODOS LOS ENDPOINTS AQUÍ DENTRO, RECOMENDABLE USAR PREFIX PARA ORGANIZAR POR SECCIÓN                                                                                 
        // Proyectos 
        // URL que será es: api/usuario/proyecto/gestion-proyectos
            Route::prefix('proyecto/gestion-proyectos')->group(function () {
            Route::get('/{id_portafolio}', [ProyectoController::class, 'index']);
            Route::post('/', [ProyectoController::class, 'store']);
            Route::put('/{id}', [ProyectoController::class, 'update']);
            Route::delete('/{id}', [ProyectoController::class, 'destroy']);
        });

        // Habilidades
        Route::get('/habilidades/{id_portafolio}', [HabilidadController::class, 'index']);
        Route::post('/habilidades', [HabilidadController::class, 'store']);
        Route::get('/habilidades/{id}', [HabilidadController::class, 'show']);
        Route::put('/habilidades/{id}', [HabilidadController::class, 'update']);
        Route::delete('/habilidades/{id}', [HabilidadController::class, 'destroy']);
    });
});


