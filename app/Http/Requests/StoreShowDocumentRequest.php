<?php

namespace App\Http\Requests;

use App\Models\ShowDocument;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShowDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage shows') ?? false;
    }

    public function rules(): array
    {
        return [
            'document_type' => ['required', 'string', Rule::in(ShowDocument::TYPES)],
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:20480'],
        ];
    }
}
