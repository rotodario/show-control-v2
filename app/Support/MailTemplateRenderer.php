<?php

namespace App\Support;

class MailTemplateRenderer
{
    public static function render(?string $template, array $context, string $fallback = ''): string
    {
        $output = (string) ($template ?: $fallback);

        foreach ($context as $key => $value) {
            $output = str_replace('{{'.$key.'}}', (string) $value, $output);
        }

        return $output;
    }
}
