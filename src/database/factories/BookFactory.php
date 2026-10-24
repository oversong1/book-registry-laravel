<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Se ninguem disser o dono, a Factory cria um usuario novo pra ele
            'user_id' => User::factory(),
            'name' => fake()->sentence(3),
            // isbn13(): 13 digitos, so numeros
            'isbn' => fake()->isbn13(),
            // numero com 2 casas, entre 5 e 200
            'value' => fake()->randomFloat(2, 5, 200),
        ];
    }
}