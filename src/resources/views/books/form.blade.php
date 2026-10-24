@extends('layouts.app')

{{-- Um formulario so pra criar e editar.
     $book->exists: false num livro novo (new Book), true num livro que veio do banco --}}
@section('content')
    <div class="mx-auto max-w-lg rounded bg-white p-6 shadow">
        <h1 class="mb-6 text-2xl font-bold">{{ $book->exists ? 'Editar livro' : 'Novo livro' }}</h1>

        <form action="{{ $book->exists ? route('books.update', $book) : route('books.store') }}" method="POST" class="space-y-4">
            @csrf
            {{-- Editar usa PUT; criar usa o POST normal --}}
            @if ($book->exists)
                @method('PUT')
            @endif

            <div>
                <label for="name" class="mb-1 block text-sm font-medium">Nome *</label>
                {{-- old('name', $book->name): se a validacao falhou, o que foi digitado;
                     senao, o valor atual do livro (vazio quando e novo) --}}
                <input type="text" name="name" id="name" value="{{ old('name', $book->name) }}"
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="isbn" class="mb-1 block text-sm font-medium">ISBN (10 ou 13 dígitos)</label>
                {{-- inputmode="numeric": no celular abre o teclado numerico.
                     A regra de verdade e a do Form Request: o HTML pode ser burlado. --}}
                <input type="text" name="isbn" id="isbn" value="{{ old('isbn', $book->isbn) }}" inputmode="numeric"
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('isbn')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="value" class="mb-1 block text-sm font-medium">Valor (R$)</label>
                {{-- type="text" (e nao number) pra aceitar a mascara 1.234,56.
                     data-mask="money": o resources/js/money-mask.js formata enquanto digita
                     e, ao salvar, converte pra 1234.56 (o formato que a validacao espera).
                     O valor que vem do banco ("1234.56") e mostrado ja formatado ("1.234,56"). --}}
                @php($valor = old('value', $book->value))
                <input type="text" name="value" id="value" inputmode="decimal" data-mask="money" placeholder="0,00"
                       value="{{ is_numeric($valor) ? number_format((float) $valor, 2, ',', '.') : $valor }}"
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('value')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('books.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                    Salvar
                </button>
            </div>
        </form>
    </div>
@endsection