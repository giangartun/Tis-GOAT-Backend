<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\HabilidadController;

Route::prefix('usuario')->group(function () {
    // Endpoints de registro y autentificaciion de usuarios (no requiere token de acceso)
    Route::post('/pre-registro', [UsuarioController::class, 'preRegistro']);
    Route::get('/verificar-email/{token}', [UsuarioController::class, 'verificarEmail']);
    Route::post('/login', [UsuarioController::class, 'login']);

    // Endpoints para datos de usuario registrado (requiere token de acceso)
    Route::middleware('auth:sanctum')->group(function () {
        /*
        AQUI TENDRIA QUE PONER LOS ENDPOINTS PARA QUE VALIDE 
        SIEMPRE EL TOKEN DE REGISTRO, SI EN ALGUN CASO TALVES COMO PETICION 
        DE DATOS DEL SISTEMA U OTROS DE ENTORNO , 
        SI NO SE  NECESITA DATOS DE SESION NO TIENE SENTIDO PONERLO ACA
        */
        Route::get('/habilidades/{id_portafolio}',  [HabilidadController::class, 'index']);
        Route::post('/habilidades',                 [HabilidadController::class, 'store']);
        Route::get('/habilidades/{id}',             [HabilidadController::class, 'show']);
        Route::put('/habilidades/{id}',             [HabilidadController::class, 'update']);
        Route::delete('/habilidades/{id}',          [HabilidadController::class, 'destroy']);

        Route::post('/logout', [UsuarioController::class, 'logout']);
    });
});