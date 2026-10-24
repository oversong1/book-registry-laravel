<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookController extends Controller
{
    // Mesmas regras da API (Form Requests e Policy); so a resposta muda:
    // aqui devolve uma View (tela), la devolve JSON.

    // GET /books - lista os livros do usuario logado, 10 por pagina
    public function index(Request $request)
    {
        $livros = $request->user()->books()->latest('id')->paginate(10);

        return view('books.index', ['books' => $livros]);
    }

    // GET /books/create - formulario vazio
    public function create()
    {
        // new Book: um livro "em branco", pro mesmo formulario servir pra criar e editar
        return view('books.form', ['book' => new Book]);
    }

    // POST /books
    public function store(StoreBookRequest $request)
    {
        $request->user()->books()->create($request->validated());

        return redirect()->route('books.index')->with('success', 'Livro cadastrado!');
    }

    // GET /books/{book} - detalhe
    public function show(Book $book)
    {
        Gate::authorize('view', $book);

        return view('books.show', ['book' => $book]);
    }

    // GET /books/{book}/edit - o mesmo formulario, ja preenchido
    public function edit(Book $book)
    {
        Gate::authorize('update', $book);

        return view('books.form', ['book' => $book]);
    }

    // PUT /books/{book}
    public function update(UpdateBookRequest $request, Book $book)
    {
        $book->update($request->validated());

        return redirect()->route('books.show', $book)->with('success', 'Livro atualizado!');
    }

    // DELETE /books/{book}
    public function destroy(Book $book)
    {
        Gate::authorize('delete', $book);

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Livro apagado!');
    }
}