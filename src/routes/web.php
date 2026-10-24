<?php

use App\Http\Controllers\Web\AuthController;
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

    // PROVISORIO: so pra existir a rota "books.index" (usada no layout e depois do login).
    // Mostra so o layout (cabecalho com "Ola" e o botao Sair). Na Fase 9 vira a lista de livros.
    Route::get('/books', function () {
        return view('layouts.app');
    })->name('books.index');
});