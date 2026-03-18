<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'encrypted',
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        if (! $setting->encrypted || blank($setting->value)) {
            return $setting->value ?? $default;
        }

        try {
            return Crypt::decryptString($setting->value);
        } catch (\Throwable) {
            return $default;
        }
    }

    public static function putValue(string $key, mixed $value, bool $encrypted = false): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => blank($value) ? null : ($encrypted ? Crypt::encryptString((string) $value) : $value),
                'encrypted' => $encrypted,
            ]
        );
    }
}
