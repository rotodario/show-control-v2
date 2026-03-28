<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserMailSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage account settings') ?? false;
    }

    public function rules(): array
    {
        return [
            'notifications_enabled' => ['nullable', 'boolean'],
            'alert_notifications_enabled' => ['nullable', 'boolean'],
            'from_name' => ['nullable', 'string', 'max:255'],
            'reply_to_email' => ['nullable', 'email', 'max:255'],
            'recipients' => ['nullable', 'string', 'max:5000'],
            'cc_recipients' => ['nullable', 'string', 'max:5000'],
            'subject_template' => ['nullable', 'string', 'max:255'],
            'body_template' => ['nullable', 'string', 'max:10000'],
            'signature' => ['nullable', 'string', 'max:5000'],
            'alert_recipients' => ['nullable', 'string', 'max:5000'],
            'alert_cc_recipients' => ['nullable', 'string', 'max:5000'],
            'alert_subject_template' => ['nullable', 'string', 'max:255'],
            'alert_body_template' => ['nullable', 'string', 'max:10000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'notifications_enabled' => $this->boolean('notifications_enabled'),
            'alert_notifications_enabled' => $this->boolean('alert_notifications_enabled'),
        ]);
    }
}
