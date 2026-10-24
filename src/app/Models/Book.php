<?php

namespace App\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory;

    // Campos que podem ser preenchidos em massa (create/update com array).
    // "user_id" NAO esta aqui de proposito: o dono vem sempre do usuario logado,
    // nunca do que o cliente mandou no formulario ou no JSON.
    protected $fillable = [
        'name',
        'isbn',
        'value',
    ];

    protected function casts(): array
    {
        return [
            // Garante que user_id e sempre int (a Policy compara com ===)
            'user_id' => 'integer',
            // Ao ler $book->value, vem a string "49.90" (sempre 2 casas, sem arredondar)
            'value' => 'decimal:2',
        ];
    }

    // belongsTo: um livro PERTENCE A um usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}