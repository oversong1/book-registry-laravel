<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            // Dono do livro. constrained() aponta pra tabela "users";
            // cascadeOnDelete(): se o usuario for apagado, os livros dele vao junto
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            // Texto e nao numero: um ISBN pode comecar com zero, e como numero o zero sumiria
            $table->string('isbn', 13)->nullable();
            // DECIMAL e nao FLOAT: valor e dinheiro, nao pode ter erro de arredondamento
            $table->decimal('value', 10, 2)->nullable();
            $table->timestamps();

            // O mesmo usuario nao pode repetir o ISBN; usuarios diferentes podem.
            // Esse indice tambem deixa rapido o "WHERE user_id = ?"
            $table->unique(['user_id', 'isbn']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
