<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlatformMailSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage platform settings') ?? false;
    }

    public function rules(): array
    {
        return [
            'registration_notifications_enabled' => ['nullable', 'boolean'],
            'platform_mail_from_name' => ['nullable', 'string', 'max:255'],
            'platform_mail_from_address' => ['nullable', 'email', 'max:255'],
            'platform_mail_reply_to_email' => ['nullable', 'email', 'max:255'],
            'platform_registration_recipients' => ['nullable', 'string', 'max:5000'],
            'platform_registration_subject' => ['nullable', 'string', 'max:255'],
            'platform_registration_body' => ['nullable', 'string', 'max:10000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'registration_notifications_enabled' => $this->boolean('registration_notifications_enabled'),
        ]);
    }
}
