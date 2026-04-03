<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\HabilidadController;

Route::prefix('usuario')->group(function () {

    Route::post('/register', [UsuarioController::class, 'register']);
    Route::post('/login', [UsuarioController::class, 'login']);

});

// Rutas de habilidades (temporalmente sin token)
Route::get('/habilidades/{id_portafolio}',  [HabilidadController::class, 'index']);
Route::post('/habilidades',                 [HabilidadController::class, 'store']);
Route::get('/habilidades/{id}',             [HabilidadController::class, 'show']);
Route::put('/habilidades/{id}',             [HabilidadController::class, 'update']);
Route::delete('/habilidades/{id}',          [HabilidadController::class, 'destroy']);