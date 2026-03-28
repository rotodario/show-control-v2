<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlatformSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage platform settings') ?? false;
    }

    public function rules(): array
    {
        return [
            'platform_default_locale' => ['required', Rule::in(array_keys(config('app.locale_labels', ['es' => 'Español', 'en' => 'English'])))],
        ];
    }
}
