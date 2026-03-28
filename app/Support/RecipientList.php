<?php

namespace App\Support;

class RecipientList
{
    public static function parse(?string $raw): array
    {
        if (blank($raw)) {
            return [];
        }

        return collect(preg_split('/[\s,;]+/', (string) $raw) ?: [])
            ->map(fn (?string $email): string => trim((string) $email))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
