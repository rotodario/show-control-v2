<?php

namespace App\Http\Requests;

use App\Models\Show;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserPreferencesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage account settings') ?? false;
    }

    public function rules(): array
    {
        return [
            'default_show_status' => ['required', Rule::in(array_keys(Show::STATUS_OPTIONS))],
            'default_travel_mode' => ['required', Rule::in(array_keys(Show::TRAVEL_MODE_OPTIONS))],
            'default_city' => ['nullable', 'string', 'max:255'],
            'default_travel_origin' => ['nullable', 'string', 'max:255'],
            'ui_locale' => ['nullable', Rule::in(array_keys(config('app.locale_labels', ['es' => 'Español', 'en' => 'English'])))],
        ];
    }
}
