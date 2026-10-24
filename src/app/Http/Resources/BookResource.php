<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    // O Resource e a "View" da API: decide o formato do JSON de um livro.
    // O banco pode mudar sem quebrar quem usa a API, desde que este formato fique igual.
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'isbn' => $this->isbn,
            'value' => $this->value, // "49.90" (texto), por causa do cast decimal:2
            // ?-> : se a data for null, devolve null em vez de dar erro
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}