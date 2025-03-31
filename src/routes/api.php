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

Route::post('login', [LoginController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {

Route::post('logout', [LoginController::class, 'logout']);

Route::post('/entradas', [EntradaController::class, 'procesarEntrada']);

Route::get('/inventario', [InventarioController::class, 'obtenerInventario']);

Route::get('/historial-entradas', [HistorialEntradaController::class, 'obtenerHistorial']);


Route::post('/salidas/generar', [SalidaController::class, 'generarVale']);



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
});
Route::get('/vale-entrada/{id_entrada}', [ValeEntradaController::class, 'generarVale']);

Route::get('/vale-salida/{id_salida}', [ValeSalidaController::class, 'generarVale']);

