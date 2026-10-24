<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    // O Laravel acha esta classe sozinho pelo nome (Book -> BookPolicy).
    // Cada metodo responde true/false: "este usuario pode fazer isso com este livro?"

    // So o dono ve o livro
    public function view(User $user, Book $book): bool
    {
        return $user->id === $book->user_id;
    }

    // So o dono edita o livro
    public function update(User $user, Book $book): bool
    {
        return $user->id === $book->user_id;
    }

    // So o dono apaga o livro
    public function delete(User $user, Book $book): bool
    {
        return $user->id === $book->user_id;
    }
}