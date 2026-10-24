<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // sometimes: so valida o campo SE ele vier (permite PATCH com 1 campo so)
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'isbn' => [
                'sometimes',
                'nullable',
                'string',
                'regex:/^(\d{10}|\d{13})$/',
                Rule::unique('books', 'isbn')
                    ->where('user_id', $this->user()->id)
                    // ignore(): o proprio livro ja tem esse ISBN, entao nao conta como repetido
                    ->ignore($this->route('book')),
            ],
            'value' => ['sometimes', 'nullable', 'numeric', 'decimal:0,2', 'min:0', 'max:99999999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'isbn.regex' => 'The isbn field must contain only digits (10 or 13).',
        ];
    }
}