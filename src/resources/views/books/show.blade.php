@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-lg rounded bg-white p-6 shadow">
        <h1 class="mb-6 text-2xl font-bold">{{ $book->name }}</h1>

        {{-- dl/dt/dd: lista de "rotulo: valor" do HTML --}}
        <dl class="space-y-3">
            <div>
                <dt class="text-sm text-gray-500">ISBN</dt>
                <dd>{{ $book->isbn ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Valor</dt>
                <dd>{{ $book->value ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Cadastrado em</dt>
                {{-- format(): data no formato brasileiro --}}
                <dd>{{ $book->created_at->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>

        <div class="mt-6 flex items-center justify-between">
            <a href="{{ route('books.index') }}" class="text-sm text-gray-600 hover:underline">Voltar</a>

            <div class="flex gap-3">
                <a href="{{ route('books.edit', $book) }}" class="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Editar</a>
                <form action="{{ route('books.destroy', $book) }}" method="POST"
                      onsubmit="return confirm('Apagar este livro?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700">Apagar</button>
                </form>
            </div>
        </div>
    </div>
@endsection