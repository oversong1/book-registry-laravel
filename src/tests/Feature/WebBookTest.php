<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebBookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // withoutVite(): nos testes nao precisa do CSS/JS compilado
        $this->withoutVite();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/books')->assertRedirect('/login');
    }

    public function test_user_can_login_through_the_form(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect('/books');

        // assertAuthenticatedAs(): confere que a sessao ficou com este usuario
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_create_a_book_through_the_form(): void
    {
        $user = User::factory()->create();

        // actingAs(): faz o "login" por sessao, como as telas usam
        $this->actingAs($user)
            ->post('/books', ['name' => 'Dune', 'isbn' => '9780441172719', 'value' => '39.90'])
            ->assertRedirect('/books');

        $this->assertDatabaseHas('books', ['name' => 'Dune', 'user_id' => $user->id]);
    }

    public function test_user_cannot_open_another_users_book_pages(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(); // de outra pessoa

        $this->actingAs($user)->get("/books/{$book->id}")->assertForbidden();
        $this->actingAs($user)->get("/books/{$book->id}/edit")->assertForbidden();
    }
}