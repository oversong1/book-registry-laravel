<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class BookController extends Controller
{
    // GET /api/v1/books - so os livros do dono do token, paginados
    public function index(Request $request): AnonymousResourceCollection
    {
        // ?per_page= da URL, preso entre 1 e 100 (padrao 15), pra ninguem pedir 1 milhao de linhas
        $porPagina = min(max((int) $request->query('per_page', 15), 1), 100);

        // books(): ja filtra pelo dono; latest('id'): mais novos primeiro
        $livros = $request->user()->books()->latest('id')->paginate($porPagina);

        // collection(): aplica o BookResource em cada livro e adiciona links/meta da paginacao
        return BookResource::collection($livros);
    }

    // POST /api/v1/books
    public function store(StoreBookRequest $request): JsonResponse
    {
        // Quando chega aqui, a validacao ja passou (senao ja teria saido 422).
        // validated(): so os campos que passaram pelas regras.
        // Criando PELA RELACAO, o Laravel preenche o user_id com o dono do token.
        $livro = $request->user()->books()->create($request->validated());

        // Resource sai com 200 por padrao; criar e 201
        return (new BookResource($livro))->response()->setStatusCode(201);
    }

    // GET /api/v1/books/{book}
    // Book $book: o Laravel ja buscou o livro pelo ID da URL (404 se nao existir)
    public function show(Book $book): BookResource
    {
        // Pergunta pra BookPolicy::view(); se for false, responde 403 sozinho
        Gate::authorize('view', $book);

        return new BookResource($book);
    }

    // PUT/PATCH /api/v1/books/{book}
    // A checagem de dono ja foi feita no authorize() do UpdateBookRequest
    public function update(UpdateBookRequest $request, Book $book): BookResource
    {
        $book->update($request->validated());

        return new BookResource($book);
    }

    // DELETE /api/v1/books/{book}
    public function destroy(Book $book): JsonResponse
    {
        Gate::authorize('delete', $book);

        $book->delete();

        // JSON com 200 (e nao 204 vazio): o enunciado pede JSON em todas as respostas
        return response()->json(['message' => 'Book deleted.']);
    }
}