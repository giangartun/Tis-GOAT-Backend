<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\HabilidadController;
use App\Http\Controllers\EvidenciaController;
use App\Http\Controllers\PrivacidadPortafolioController;
use App\Http\Controllers\PortafolioController;
use App\Http\Controllers\RedesProfesionalesController;
use App\Http\Controllers\FotoPerfilController;
use App\Http\Controllers\ExperienciaLaboralController;
use App\Http\Controllers\ExperienciaAcademicaController;

// --- Rutas de Usuario ---
Route::prefix('usuario')->group(function () {
    // Endpoints de registro y autenticación de usuarios (no requiere token)
    Route::post('/pre-registro', [UsuarioController::class, 'preRegistro']);
    Route::get('/verificar-email/{token}', [UsuarioController::class, 'verificarEmail']);
    Route::post('/login', [UsuarioController::class, 'login']);
    Route::post('/contrasena/olvido', [UsuarioController::class, 'enviarEnlaceReset']);
    Route::post('/contrasena/resetear', [UsuarioController::class, 'resetearContrasena']);


    // Endpoints para datos de usuario registrado (requiere token)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [UsuarioController::class, 'logout']);

        // TODO: Organizar con prefijos por sección si agrega más endpoints protegidos
    });
});

/**
 * PUEDE QUE ALGUNAS RUTAS O ENDPOINTS NO QUIERAN SI O SI DE UN TOKEN DE USUARIO (CAPAS PARA RELLENAR UN SELECTOR DE OPCIONES), POR ESO
 * SE DEBE ANALIZAR CUAL REQUIERE SI UN TOKEN Y CUALES NO, ES MAS CONTROL INTERNO Y DE QUE ES LO QUE REALMENTE QUIERE SU FRONTEND
 */

// --- Rutas de Plantillas ---
// Catálogo público para que el usuario elija
Route::get('plantillas/catalogo', [App\Http\Controllers\PlantillaController::class, 'index']);

// --- Rutas de Experiencia Laboral ---
// El método index (listar) es público para que cualquiera vea el portafolio
Route::get('experiencia-laboral/{id_portafolio}', [ExperienciaLaboralController::class, 'index']);

// --- Rutas de Experiencia Académica ---
// Endpoint público para renderizar el portafolio de estudios
Route::get('experiencia-academica/{id_portafolio}', [ExperienciaAcademicaController::class, 'index']);

// Rutas de Experiencia laboral (CRUD ); Los métodos de alteración de datos requieren Token de sesión para experiencia laboral
Route::prefix('experiencia-laboral')->middleware('auth:sanctum')->group(function () {
    Route::post('/',       [ExperienciaLaboralController::class, 'store']);
    Route::put('/{id}',    [ExperienciaLaboralController::class, 'update']);
    Route::delete('/{id}', [ExperienciaLaboralController::class, 'destroy']);
});

// Endpoints protegidos para gestionar el CRUD para experiencia académica (crear, actualizar, eliminar) requieren token de sesión
Route::prefix('experiencia-academica')->middleware('auth:sanctum')->group(function () {
    Route::post('/',       [ExperienciaAcademicaController::class, 'store']);
    Route::put('/{id}',    [ExperienciaAcademicaController::class, 'update']);
    Route::delete('/{id}', [ExperienciaAcademicaController::class, 'destroy']);
});

// --- Rutas de Habilidad ---
Route::prefix('habilidad')->middleware('auth:sanctum')->group(function () {
    Route::get('/',        [HabilidadController::class, 'index']);
    Route::post('/',       [HabilidadController::class, 'store']);
    Route::get('/{id}',    [HabilidadController::class, 'show']);
    Route::put('/{id}',    [HabilidadController::class, 'update']);
    Route::delete('/{id}', [HabilidadController::class, 'destroy']);
});

// --- Rutas de Redes Profesionales ---
Route::prefix('redes-profesionales')->middleware('auth:sanctum')->group(function () {
    Route::get('/{id_usuario}',  [RedesProfesionalesController::class, 'index']);
    Route::post('/',             [RedesProfesionalesController::class, 'store']);
    Route::put('/{id}',         [RedesProfesionalesController::class, 'update']);
    Route::delete('/{id}',      [RedesProfesionalesController::class, 'destroy']);
});

// --- Rutas de Proyecto ---
Route::prefix('proyecto/gestion-proyectos')->middleware('auth:sanctum')->group(function () {
    Route::get('/tecnologias/lista', [ProyectoController::class, 'listarTecnologias']);
    Route::get('/{id_portafolio}', [ProyectoController::class, 'index']);
    Route::post('/', [ProyectoController::class, 'store']);
    Route::put('/{id}', [ProyectoController::class, 'update']);
    Route::delete('/{id}', [ProyectoController::class, 'destroy']);
});

// Rutas públicas de portafolios (no requieren token)
Route::prefix('portafolios')->group(function () {
    Route::get('/publicos', [PortafolioController::class, 'index']);
    Route::get('/{id}',     [PortafolioController::class, 'show']);
});

// --- Rutas de Evidencias ---
Route::prefix('proyecto/evidencias')->middleware('auth:sanctum')->group(function () {
    Route::post('/subir', [EvidenciaController::class, 'subir']);
    Route::get('/mostrar/{id}', [EvidenciaController::class, 'mostrar']);
    Route::delete('/eliminar/{id}', [EvidenciaController::class, 'eliminar']);
});

// --- Rutas de Privacidad Portafolios ---
Route::prefix('privacidad')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [PrivacidadPortafolioController::class, 'index']);
    Route::post('/actualizar', [PrivacidadPortafolioController::class, 'actualizar']);
    Route::post('/restablecer', [PrivacidadPortafolioController::class, 'restablecer']);
});

// --- Rutas de Portafolios ---
Route::prefix('portafolio')->middleware('auth:sanctum')->group(function () {
    Route::get('/obtener_url', [PortafolioController::class, 'obtenerUrl']);
        Route::get('/completo', [PortafolioController::class, 'obtenerCompleto']);
        // Nueva ruta para Seleccionar plantilla
        Route::patch('/actualizar-plantilla', [PortafolioController::class, 'actualizarPlantilla']);
});

// --- Rutas de Foto de Perfil ---
Route::prefix('usuario/foto')->middleware('auth:sanctum')->group(function () {
    Route::post('/',   [FotoPerfilController::class, 'subir']);
    Route::delete('/', [FotoPerfilController::class, 'eliminar']);
});