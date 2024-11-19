<?php

use App\Http\Controllers\API\MobileAuthLoginController;
use App\Http\Controllers\API\MobileAuthRegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas de autenticación del registro de usuarios en la app móvil
Route::post('register', [MobileAuthRegisterController::class, 'apiStore']);

// Rutas de autenticación del login y logout de usuarios en la app móvil
Route::post('/login', [MobileAuthLoginController::class, 'apiLogin']);
Route::post('/logout', [MobileAuthLoginController::class, 'apiLogout'])->middleware('auth:sanctum');