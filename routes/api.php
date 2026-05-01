<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\HabilidadController;
use App\Http\Controllers\EvidenciaController;
use App\Http\Controllers\PrivacidadPortafolioController;
use App\Http\Controllers\PortafolioController;
use App\Http\Controllers\RedesProfesionalesController;


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
        /*
            Route::prefix('proyecto/gestion-proyectos')->group(function () {
            Route::get('/{id_portafolio}', [ProyectoController::class, 'index']);
            Route::post('/', [ProyectoController::class, 'store']);
            Route::put('/{id}', [ProyectoController::class, 'update']);
            Route::delete('/{id}', [ProyectoController::class, 'destroy']);
        });

        */

        // Habilidades
        /*
        Route::get('/habilidades/{id_portafolio}', [HabilidadController::class, 'index']);
        Route::post('/habilidades', [HabilidadController::class, 'store']);
        Route::get('/habilidades/{id}', [HabilidadController::class, 'show']);
        Route::put('/habilidades/{id}', [HabilidadController::class, 'update']);
        Route::delete('/habilidades/{id}', [HabilidadController::class, 'destroy']);
        */
    });
});


/**
 * PUEDE QUE ALGUNAS RUTAS O ENDPOINTS NO QUIERAN SI O SI DE UN TOKEN DE USUARIO (CAPAS PARA RELLENAR UN SELECTOR DE OPCIONES), POR ESO
 * SE DEBE ANALIZAR CUAL REQUIERE SI UN TOKEN Y CUALES NO, ES MAS CONTROL INTERNO Y DE QUE ES LO QUE REALMENTE QUIERE SU FROTEND
 */


// --- Rutas de Habilidad ---
Route::prefix('habilidad')->middleware('auth:sanctum')->group(function () {
    Route::get('/',        [HabilidadController::class, 'index']);
    Route::post('/',       [HabilidadController::class, 'store']);
    Route::get('/{id}',    [HabilidadController::class, 'show']);
    Route::put('/{id}',    [HabilidadController::class, 'update']);
    Route::delete('/{id}', [HabilidadController::class, 'destroy']);
});

// --- Rutas de Redes Profesionales ---
Route::prefix('redes-profesionales')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/{id_usuario}',  [RedesProfesionalesController::class, 'index']);
        Route::post('/',             [RedesProfesionalesController::class, 'store']);
        Route::put('/{id}',         [RedesProfesionalesController::class, 'update']);
        Route::delete('/{id}',      [RedesProfesionalesController::class, 'destroy']);
    });
});

// --- Rutas de Proyecto ---
Route::prefix('proyecto/gestion-proyectos')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        // Nuevo endpoint para el selector del Frontend
        Route::get('/tecnologias/lista', [ProyectoController::class, 'listarTecnologias']);
        Route::get('/{id_portafolio}', [ProyectoController::class, 'index']);
        Route::post('/', [ProyectoController::class, 'store']);
        Route::put('/{id}', [ProyectoController::class, 'update']);
        Route::delete('/{id}', [ProyectoController::class, 'destroy']);
    });
});

// --- Rutas de Evidencias ---
Route::prefix('proyecto/evidencias')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/subir', [EvidenciaController::class, 'subir']);
        Route::get('/mostrar/{id}', [EvidenciaController::class, 'mostrar']);
        Route::delete('/eliminar/{id}', [EvidenciaController::class, 'eliminar']);
    });
});


// --- Rutas de Privacidad Portafolios ---
Route::prefix('privacidad')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [PrivacidadPortafolioController::class, 'index']);
        Route::post('/actualizar', [PrivacidadPortafolioController::class, 'actualizar']);
        Route::post('/restablecer', [PrivacidadPortafolioController::class, 'restablecer']);
    });
});

// --- Rutas de Portafolios ---
Route::prefix('portafolio')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/obtener_url', [PortafolioController::class, 'obtenerUrl']);
        Route::get('/completo', [PortafolioController::class, 'obtenerCompleto']);
    });
});