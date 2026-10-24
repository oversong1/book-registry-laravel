<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    // Recria as tabelas antes de cada teste (cada teste comeca com o banco zerado)
    use RefreshDatabase;

    public function test_login_returns_a_token(): void
    {
        // Preparar: usuario com a senha "password" (padrao da UserFactory)
        $user = User::factory()->create();

        // Agir: postJson() faz a requisicao ja com "Accept: application/json"
        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Conferir: 200 e as chaves token/user no JSON (o token e aleatorio, so conferimos que existe)
        $response->assertOk()->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/v1/login', ['email' => $user->email, 'password' => 'wrong-password'])
            ->assertUnprocessable()                // 422
            ->assertJsonValidationErrors('email'); // erro no campo email
    }

    public function test_logout_deletes_the_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        // withToken(): manda o header "Authorization: Bearer <token>"
        $this->withToken($token)->postJson('/api/v1/logout')->assertOk();

        // Nenhum token sobrou no banco
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_api_returns_401_json_without_token(): void
    {
        // get() SEM "Json": de proposito sem o header Accept. Mesmo assim tem que vir JSON.
        $this->get('/api/v1/books')
            ->assertUnauthorized() // 401
            ->assertJson(['message' => 'Unauthenticated.']);
    }
}