@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-sm rounded bg-white p-6 shadow">
        <h1 class="mb-6 text-2xl font-bold">Entrar</h1>

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            {{-- @csrf: token que prova que o formulario veio do nosso site --}}
            @csrf

            <div>
                <label for="email" class="mb-1 block text-sm font-medium">E-mail</label>
                {{-- old('email'): mantem o que foi digitado se a validacao falhar --}}
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                       class="w-full rounded border border-gray-300 px-3 py-2">
                {{-- @error: mostra o erro deste campo, se tiver --}}
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium">Senha</label>
                <input type="password" name="password" id="password" required
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full rounded bg-indigo-600 py-2 font-medium text-white hover:bg-indigo-700">
                Entrar
            </button>
        </form>

        <p class="mt-4 text-center text-sm">
            Não tem conta? <a href="{{ route('register') }}" class="text-indigo-600 hover:underline">Criar conta</a>
        </p>
    </div>
@endsection