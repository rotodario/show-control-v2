<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserAlertSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage account settings') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'core_info_enabled' => $this->boolean('core_info_enabled'),
            'status_enabled' => $this->boolean('status_enabled'),
            'validations_enabled' => $this->boolean('validations_enabled'),
        ]);
    }

    public function rules(): array
    {
        return [
            'core_info_enabled' => ['required', 'boolean'],
            'core_info_days' => ['required', 'integer', 'min:1', 'max:365'],
            'status_enabled' => ['required', 'boolean'],
            'status_days' => ['required', 'integer', 'min:1', 'max:365'],
            'validations_enabled' => ['required', 'boolean'],
            'validations_days' => ['required', 'integer', 'min:1', 'max:365'],
        ];
    }
}
