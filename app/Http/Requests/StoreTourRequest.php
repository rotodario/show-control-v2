<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage tours') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:tours,name'],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
