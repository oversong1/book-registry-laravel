<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Usuario de teste, com 5 livros (senha "password", vem da UserFactory)
        // has(): cria o usuario E os livros dele de uma vez
        User::factory()
            ->has(Book::factory()->count(5))
            ->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        // Segundo usuario, com 3 livros: serve pra conferir que um nao ve os livros do outro
        User::factory()
            ->has(Book::factory()->count(3))
            ->create([
                'name' => 'Other User',
                'email' => 'other@example.com',
            ]);
    }
}