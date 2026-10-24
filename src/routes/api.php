<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use Illuminate\Support\Facades\Route;

// Todas as rotas deste arquivo ja comecam com /api/v1 (bootstrap/app.php).
// name('api.'): todos os nomes ganham o prefixo "api." (api.login, api.books.index...)
// pra nao colidir com os nomes das rotas das telas (login, books.index...).
Route::name('api.')->group(function () {
    // Publicas. throttle:5,1 = no maximo 5 tentativas de login por minuto (forca bruta)
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login');

    // Protegidas: precisa do header "Authorization: Bearer <token>"
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // apiResource: as 5 rotas de API (index, store, show, update, destroy)
        Route::apiResource('books', BookController::class);
    });
});