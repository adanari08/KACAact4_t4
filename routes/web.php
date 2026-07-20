<?php

use Illuminate\Support\Facades\Route;

// Ruta "login" de emergencia: este proyecto es 100% API, no tiene
// páginas de inicio de sesión con HTML. Pero Laravel, quien es un
// framework para prácticamente CUALQUIER cosa (no solo APIs), por
// defecto intenta redirigir aquí cuando alguien sin token intenta
// entrar a una ruta protegida y no manda las cabeceras típicas de
// un cliente API. Con esto, en vez de tronar, responde igual con
// un JSON de error.
Route::get('/login', function () {
    return response()->json(['message' => 'Unauthenticated.'], 401);
})->name('login');