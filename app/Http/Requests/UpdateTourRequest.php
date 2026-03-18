<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage tours') ?? false;
    }

    public function rules(): array
    {
        $tour = $this->route('tour');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('tours', 'name')->ignore($tour?->id)],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
