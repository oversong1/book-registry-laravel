@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-sm rounded bg-white p-6 shadow">
        <h1 class="mb-6 text-2xl font-bold">Criar conta</h1>

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="mb-1 block text-sm font-medium">Nome</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="mb-1 block text-sm font-medium">E-mail</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium">Senha (mínimo 8 caracteres)</label>
                <input type="password" name="password" id="password" required
                       class="w-full rounded border border-gray-300 px-3 py-2">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="mb-1 block text-sm font-medium">Confirme a senha</label>
                {{-- "password_confirmation": o nome exato que a regra "confirmed" espera --}}
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="w-full rounded border border-gray-300 px-3 py-2">
            </div>

            <button type="submit" class="w-full rounded bg-indigo-600 py-2 font-medium text-white hover:bg-indigo-700">
                Criar conta
            </button>
        </form>

        <p class="mt-4 text-center text-sm">
            Já tem conta? <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Entrar</a>
        </p>
    </div>
@endsection