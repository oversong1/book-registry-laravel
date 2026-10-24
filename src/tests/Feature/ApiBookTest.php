<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiBookTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    // setUp(): roda ANTES de cada teste desta classe
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // actingAs(): "as proximas requisicoes sao deste usuario" (sem precisar de login de verdade)
        Sanctum::actingAs($this->user);
    }

    public function test_list_shows_only_my_books(): void
    {
        // for(): cria o livro com ESTE dono
        Book::factory()->count(2)->for($this->user)->create();
        // sem for(): a Factory cria outro usuario pra ser o dono
        Book::factory()->count(3)->create();

        $this->getJson('/api/v1/books')
            ->assertOk()
            ->assertJsonCount(2, 'data'); // so os 2 meus
    }

    public function test_create_book_ignores_user_id_from_request(): void
    {
        $other = User::factory()->create();

        $this->postJson('/api/v1/books', [
            'name' => 'Clean Code',
            'isbn' => '9780132350884',
            'value' => 49.9,
            'user_id' => $other->id, // tentativa de criar no nome de outra pessoa
        ])
            ->assertCreated() // 201
            ->assertJsonPath('data.value', '49.90');

        // O livro ficou com o dono do token, nao com o user_id que veio no JSON
        $this->assertDatabaseHas('books', ['name' => 'Clean Code', 'user_id' => $this->user->id]);
    }

    public function test_validation_rules(): void
    {
        $this->postJson('/api/v1/books', [
            'isbn' => '978-01323A', // tem hifen e letra
            'value' => '12.345',    // 3 casas decimais
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'isbn', 'value']); // os 3 campos com erro
    }

    public function test_cannot_view_update_or_delete_another_users_book(): void
    {
        $book = Book::factory()->create(['name' => 'Original']); // livro de outra pessoa

        $this->getJson("/api/v1/books/{$book->id}")->assertForbidden(); // 403
        $this->patchJson("/api/v1/books/{$book->id}", ['name' => 'Hacked'])->assertForbidden();
        $this->deleteJson("/api/v1/books/{$book->id}")->assertForbidden();

        // Alem do 403, nada mudou no banco
        $this->assertDatabaseHas('books', ['id' => $book->id, 'name' => 'Original']);
    }
}