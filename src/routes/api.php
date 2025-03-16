<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\InventarioController;

Route::post('/login', [LoginController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);

    Route::post('/entradas', [EntradaController::class, 'procesarEntrada']);

    Route::get('/inventario', [InventarioController::class, 'obtenerInventario']);
    Route::patch('/inventario/{id}', [InventarioController::class, 'actualizarProducto']);
    Route::delete('/inventario/{id}', [InventarioController::class, 'eliminarProducto']);
});

