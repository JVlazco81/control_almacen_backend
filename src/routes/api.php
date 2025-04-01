<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\HistorialEntradaController;
use App\Http\Controllers\ValeEntradaController;


Route::post('/login', [LoginController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);

    Route::post('/entradas', [EntradaController::class, 'procesarEntrada']);
    
    Route::get('/historial-entradas', [HistorialEntradaController::class, 'obtenerHistorial']);
    Route::get('/vale-entrada/{id_entrada}', [ValeEntradaController::class, 'generarVale']);

    Route::get('/inventario', [InventarioController::class, 'obtenerInventario']);
    Route::patch('/inventario/{id}', [InventarioController::class, 'actualizarProducto']);
    Route::delete('/inventario/{id}', [InventarioController::class, 'eliminarProducto']);
    Route::get('/inventario/reporte', [InventarioController::class, 'generarReporteInventario']);
});

