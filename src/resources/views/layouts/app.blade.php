<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Registry</title>
    {{-- @vite: gera as tags do CSS/JS compilado (o Tailwind esta dentro do app.css) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 text-gray-800">
    <header class="bg-white shadow">
        <div class="mx-auto flex max-w-4xl items-center justify-between px-4 py-3">
            <a href="{{ route('books.index') }}" class="text-lg font-bold text-indigo-600">Book Registry</a>

            {{-- @auth / @guest: mostram o bloco so se estiver (ou nao) logado --}}
            @auth
                <div class="flex items-center gap-4">
                    <span class="text-sm">Olá, {{ auth()->user()->name }}</span>
                    {{-- Logout e POST (acao que muda a sessao), por isso e um form e nao um link --}}
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:underline">Sair</button>
                    </form>
                </div>
            @endauth

            @guest
                <div class="flex gap-4 text-sm">
                    <a href="{{ route('login') }}" class="hover:underline">Entrar</a>
                    <a href="{{ route('register') }}" class="hover:underline">Criar conta</a>
                </div>
            @endguest
        </div>
    </header>

    <main class="mx-auto max-w-4xl px-4 py-8">
        {{-- Mensagem de sucesso enviada pelo Controller com ->with('success', ...) --}}
        @if (session('success'))
            <div class="mb-6 rounded bg-green-100 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- @yield: aqui entra o conteudo de cada pagina --}}
        @yield('content')
    </main>
</body>
</html>