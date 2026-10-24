<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\BookController;
use Illuminate\Support\Facades\Route;

// A pagina inicial manda direto pra lista de livros
// (se nao estiver logado, o middleware "auth" manda pro login)
Route::redirect('/', '/books');

// guest: so pra quem NAO esta logado (logado que abrir /login vai pra pagina inicial)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});

// auth: so pra quem esta logado
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // resource: as 7 rotas (index, create, store, show, edit, update, destroy)
    Route::resource('books', BookController::class);
});