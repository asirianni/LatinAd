<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DisplayController;
use App\Http\Controllers\Api\AuthController;

// Rutas de autenticación (públicas)
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas por JWT
Route::middleware('auth:api')->group(function () {
    // Información del usuario autenticado
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    
    // Rutas para displays (protegidas)
    Route::apiResource('displays', DisplayController::class);
});
