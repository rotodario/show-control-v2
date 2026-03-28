<?php

namespace App\Support;

use App\Models\AppSetting;

class PlatformMailSettings
{
    public function all(): array
    {
        return [
            'registration_notifications_enabled' => (bool) AppSetting::getValue('registration_notifications_enabled', false),
            'platform_mail_from_name' => AppSetting::getValue('platform_mail_from_name', config('mail.from.name')),
            'platform_mail_from_address' => AppSetting::getValue('platform_mail_from_address', config('mail.from.address')),
            'platform_mail_reply_to_email' => AppSetting::getValue('platform_mail_reply_to_email', ''),
            'platform_registration_recipients' => AppSetting::getValue('platform_registration_recipients', ''),
            'platform_registration_subject' => AppSetting::getValue('platform_registration_subject', 'Nuevo registro en Show Control: {{user_name}}'),
            'platform_registration_body' => AppSetting::getValue('platform_registration_body', "Se ha registrado una nueva cuenta en Show Control.\n\nNombre: {{user_name}}\nEmail: {{user_email}}\nFecha: {{registered_at}}\n"),
        ];
    }

    public function save(array $values): void
    {
        foreach ($values as $key => $value) {
            AppSetting::putValue($key, is_bool($value) ? ($value ? '1' : '0') : $value);
        }
    }
}
