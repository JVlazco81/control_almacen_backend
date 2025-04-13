<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\HistorialEntradaController;
use App\Http\Controllers\ValeEntradaController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\ValeSalidaController;
use App\Http\Controllers\Auth\UsuarioController;

Route::post('/login', [LoginController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [LoginController::class, 'logout']);

    Route::post('/entradas', [EntradaController::class, 'procesarEntrada']);

    Route::get('/historial-entradas', [HistorialEntradaController::class, 'obtenerHistorial']);
    Route::get('/vale-entrada/{id_entrada}', [ValeEntradaController::class, 'generarVale']);
    Route::delete('/entradas/{id}', [EntradaController::class, 'destroy']);

    Route::post('/salidas/generar', [SalidaController::class, 'generarVale']);
    Route::get('/vale-salida/{id_salida}', [ValeSalidaController::class, 'generarVale']);
    Route::delete('salidas/{id}', [SalidaController::class, 'destroy']);

    Route::get('/inventario', [InventarioController::class, 'obtenerInventario']);
    Route::patch('/inventario/{id}', [InventarioController::class, 'actualizarProducto']);
    Route::delete('/inventario/{id}', [InventarioController::class, 'eliminarProducto']);
    Route::get('/inventario/reporte', [InventarioController::class, 'generarReporteInventario']);
});

Route::middleware(['auth:sanctum', 'role:director'])->group(function () {
    // Listar todos los usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index']);

    // Mostrar un usuario individual
    Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);

    // Crear un nuevo usuario
    Route::post('/usuarios', [UsuarioController::class, 'store']);

    // Actualizar un usuario existente
    Route::patch('/usuarios/{id}', [UsuarioController::class, 'update']);

    // Borrado lógico de un usuario
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);
});
