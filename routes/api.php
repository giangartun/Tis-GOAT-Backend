<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ProyectoController; // ¡Importante importar este también!

// --- Rutas de Usuario ---
Route::prefix('usuario')->group(function () {
    Route::post('/register', [UsuarioController::class, 'register']);
    Route::post('/login', [UsuarioController::class, 'login']);
});

// --- Rutas de Gestión de Proyectos (SEPARADAS) ---
Route::prefix('gestion-proyectos')->group(function () {
    Route::get('/{id_portafolio}', [ProyectoController::class, 'index']);    // Listar
    Route::post('/', [ProyectoController::class, 'store']);                 // Crear
    Route::put('/{id}', [ProyectoController::class, 'update']);             // Editar
    Route::delete('/{id}', [ProyectoController::class, 'destroy']);          // Eliminar
});