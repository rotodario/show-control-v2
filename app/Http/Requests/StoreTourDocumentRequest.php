<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTourDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage tours') ?? false;
    }

    public function rules(): array
    {
        return [
            'document_type' => ['required', 'string', Rule::in(\App\Models\TourDocument::TYPES)],
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:20480'],
        ];
    }
}
