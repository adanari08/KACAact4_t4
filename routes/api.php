<?php

// Rutas de la API (JSON), separadas de routes/web.php (HTML).
// Este archivo lo crea automáticamente el comando
// "php artisan install:api" — aquí ya viene con nuestras rutas.

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CancionController;
use Illuminate\Support\Facades\Route;

// Rutas PÚBLICAS: cualquiera puede llamarlas sin token,
// porque son las que sirven para conseguir un token.
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas PROTEGIDAS: todo lo de adentro de este grupo exige
// un token válido (middleware auth:sanctum). Si no se manda
// token, o el token es inválido, Laravel responde 401
// automáticamente antes de siquiera llegar al controlador.
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);

    // apiResource genera automáticamente las 5 rutas de un CRUD
    // en formato API (index, store, show, update, destroy) —
    // a diferencia de Route::resource(), NO incluye las rutas
    // create/edit, porque esas son para mostrar formularios HTML,
    // algo que una API no necesita.
    Route::apiResource('canciones', CancionController::class)
    ->parameters(['canciones' => 'cancion']);
});
