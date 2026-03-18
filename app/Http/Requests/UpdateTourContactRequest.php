<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTourContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage tours') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'notes' => ['nullable', 'string', 'max:3000'],
        ];
    }
}
