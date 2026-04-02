<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;

Route::prefix('usuario')->group(function () {
    Route::post('/register', [UsuarioController::class, 'register']);
    Route::post('/login', [UsuarioController::class, 'login']);

    // Ruta para crear proyectos
    Route::post('/proyectos', [ProyectoController::class, 'store']);


    // Prefijo opcional para organizar mejor
    Route::prefix('gestion-proyectos')->group(function () {
    Route::get('/{id_portafolio}', [ProyectoController::class, 'index']);    // Listar
    Route::post('/', [ProyectoController::class, 'store']);                 // Crear
    Route::put('/{id}', [ProyectoController::class, 'update']);             // Editar
    Route::delete('/{id}', [ProyectoController::class, 'destroy']);          // Eliminar
});
});