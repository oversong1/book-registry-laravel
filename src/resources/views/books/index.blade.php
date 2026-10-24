@extends('layouts.app')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Meus livros</h1>
        <a href="{{ route('books.create') }}" class="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
            Novo livro
        </a>
    </div>

    {{-- @forelse: igual ao @foreach, mas com um bloco @empty pra lista vazia --}}
    <div class="overflow-hidden rounded bg-white shadow">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-sm text-gray-600">
                <tr>
                    <th class="px-4 py-3">Nome</th>
                    <th class="px-4 py-3">ISBN</th>
                    <th class="px-4 py-3">Valor</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($books as $book)
                    <tr class="border-t border-gray-200">
                        <td class="px-4 py-3">{{ $book->name }}</td>
                        {{-- ?? : se for null, mostra um traco --}}
                        <td class="px-4 py-3">{{ $book->isbn ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $book->value ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-3 text-sm">
                                <a href="{{ route('books.show', $book) }}" class="text-indigo-600 hover:underline">Ver</a>
                                <a href="{{ route('books.edit', $book) }}" class="text-indigo-600 hover:underline">Editar</a>
                                {{-- confirm(): janela "OK/Cancelar" do navegador; Cancelar nao envia o form --}}
                                <form action="{{ route('books.destroy', $book) }}" method="POST"
                                      onsubmit="return confirm('Apagar este livro?')">
                                    @csrf
                                    {{-- formulario HTML so faz GET/POST; @method('DELETE') "finge" o DELETE --}}
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Apagar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">Nenhum livro cadastrado ainda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- links(): os botoes de paginacao (o Laravel ja gera com classes do Tailwind) --}}
    <div class="mt-6">
        {{ $books->links() }}
    </div>
@endsection