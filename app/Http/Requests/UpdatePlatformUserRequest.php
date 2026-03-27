<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlatformUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage platform users') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'role' => ['required', Rule::in(['admin', 'super_admin'])],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
