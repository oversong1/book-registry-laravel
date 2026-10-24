<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // POST /api/v1/register - cria a conta e ja devolve um token
    public function register(Request $request): JsonResponse
    {
        // Se falhar, a API responde 422 com os erros em JSON (sem redirecionar)
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Sem Hash::make(): o cast 'hashed' do User ja faz o hash da senha
        $usuario = User::create($dados);

        return response()->json([
            // createToken('api'): cria o token; plainTextToken e o texto que o cliente guarda
            'token' => $usuario->createToken('api')->plainTextToken,
            'user' => new UserResource($usuario),
        ], 201); // 201 = criado
    }

    // POST /api/v1/login - confere e-mail/senha e devolve um token novo
    public function login(Request $request): JsonResponse
    {
        $credenciais = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $usuario = User::where('email', $credenciais['email'])->first();

        // Hash::check(): compara a senha digitada com o hash guardado no banco.
        // Mesma mensagem para "e-mail nao existe" e "senha errada", pra nao
        // revelar quais e-mails estao cadastrados.
        if (! $usuario || ! Hash::check($credenciais['password'], $usuario->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'token' => $usuario->createToken('api')->plainTextToken,
            'user' => new UserResource($usuario),
        ]);
    }

    // POST /api/v1/logout - apaga SO o token usado nesta requisicao
    // (o usuario continua logado nos outros aparelhos)
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    // GET /api/v1/me - devolve quem e o dono do token
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}