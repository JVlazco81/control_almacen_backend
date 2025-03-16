<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\HistorialEntradaController;
use App\Http\Controllers\ValeEntradaController;


Route::post('auth/login', [LoginController::class, 'login']);

Route::post('auth/logout', [LoginController::class, 'logout']);

Route::post('/entradas', [EntradaController::class, 'procesarEntrada']);

Route::get('/inventario', [InventarioController::class, 'obtenerInventario']);

Route::get('/historial-entradas', [HistorialEntradaController::class, 'obtenerHistorial']);

Route::get('/vale-entrada/{id_entrada}', [ValeEntradaController::class, 'generarVale']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

