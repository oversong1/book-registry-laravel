<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookRequest extends FormRequest
{
    // Qualquer usuario logado pode criar livro (a rota ja exige login)
    public function authorize(): bool
    {
        return true;
    }

    // As mesmas regras servem pra API (erro vira 422 em JSON)
    // e pras telas (erro volta pro formulario com $errors)
    public function rules(): array
    {
        return [
            // obrigatorio, texto, cabe no VARCHAR(255)
            'name' => ['required', 'string', 'max:255'],
            'isbn' => [
                'nullable', // pode nao vir
                'string',   // precisa vir como texto, pra nao perder zero a esquerda
                // ^ e $ = o texto INTEIRO; \d = digito; exatamente 10 OU 13 digitos
                'regex:/^(\d{10}|\d{13})$/',
                // nao repete ISBN, mas so entre os livros DESTE usuario
                Rule::unique('books', 'isbn')->where('user_id', $this->user()->id),
            ],
            // numero >= 0 com no maximo 2 casas; max = o maior valor que cabe no DECIMAL(10,2)
            'value' => ['nullable', 'numeric', 'decimal:0,2', 'min:0', 'max:99999999.99'],
        ];
    }

    // Mensagem mais clara que o padrao "The isbn field format is invalid."
    public function messages(): array
    {
        return [
            'isbn.regex' => 'The isbn field must contain only digits (10 or 13).',
        ];
    }
}