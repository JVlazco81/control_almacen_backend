<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::post('login', [LoginController::class, 'login']);

//Route::post('auth/logout', [LoginController::class, 'logout']);



/*Route::post('/logout', [LoginController::class, 'logout']
)->middleware('auth:sanctum');*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [LoginController::class, 'logout']);
});
