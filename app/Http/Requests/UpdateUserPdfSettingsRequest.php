<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserPdfSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage account settings') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'show_generated_at' => $this->boolean('show_generated_at'),
            'brand_name' => $this->string('brand_name')->trim()->toString(),
            'primary_color' => $this->string('primary_color')->trim()->toString(),
            'header_text' => $this->string('header_text')->trim()->toString(),
            'footer_text' => $this->string('footer_text')->trim()->toString(),
        ]);
    }

    public function rules(): array
    {
        return [
            'brand_name' => ['nullable', 'string', 'max:120'],
            'primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'header_text' => ['nullable', 'string', 'max:120'],
            'footer_text' => ['nullable', 'string', 'max:160'],
            'show_generated_at' => ['required', 'boolean'],
        ];
    }
}
