<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // GET /register - mostra o formulario de cadastro
    public function showRegister()
    {
        return view('auth.register');
    }

    // POST /register - cria o usuario e ja loga ele (sessao)
    public function register(Request $request)
    {
        // Mesmas regras da API: se falhar, volta pro formulario com os erros
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $usuario = User::create($dados);

        Auth::login($usuario);

        // regenerate(): troca o ID da sessao depois do login (protege contra "session fixation")
        $request->session()->regenerate();

        return redirect()->route('books.index')->with('success', 'Conta criada!');
    }

    // GET /login - mostra o formulario de login
    public function showLogin()
    {
        return view('auth.login');
    }

    // POST /login - confere e-mail/senha e cria a sessao
    public function login(Request $request)
    {
        $credenciais = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Auth::attempt(): confere a senha e, se bater, ja cria a sessao de login
        if (Auth::attempt($credenciais)) {
            $request->session()->regenerate();

            // intended(): volta pra pagina que o usuario tentou abrir antes do login
            return redirect()->intended(route('books.index'));
        }

        // onlyInput('email'): devolve o e-mail preenchido, mas nunca a senha
        return back()
            ->withErrors(['email' => 'E-mail ou senha inválidos.'])
            ->onlyInput('email');
    }

    // POST /logout - encerra a sessao
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();      // apaga a sessao atual
        $request->session()->regenerateToken(); // gera um novo token CSRF

        return redirect()->route('login');
    }
}