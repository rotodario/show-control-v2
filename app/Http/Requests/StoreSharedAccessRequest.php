<?php

namespace App\Http\Requests;

use App\Models\SharedAccess;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSharedAccessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage access') ?? false;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'label' => ['nullable', 'string', 'max:255'],
            'role' => ['required', Rule::in(SharedAccess::ROLES)],
            'tour_id' => ['nullable', Rule::exists('tours', 'id')->where('owner_id', $userId)],
        ];
    }
}
