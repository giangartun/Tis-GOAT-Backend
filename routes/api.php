<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;

Route::prefix('usuario')->group(function () {
    Route::post('/pre-registro', [UsuarioController::class, 'preRegistro']);
    Route::get('/verificar-email/{token}', [UsuarioController::class, 'verificarEmail']);
    Route::post('/login', [UsuarioController::class, 'login']);
});