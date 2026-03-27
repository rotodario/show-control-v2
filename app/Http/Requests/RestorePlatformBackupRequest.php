<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestorePlatformBackupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage platform settings') ?? false;
    }

    public function rules(): array
    {
        return [
            'backup_file' => ['required', 'file', 'mimes:json', 'max:10240'],
            'confirmation' => ['required', 'in:RESTAURAR'],
        ];
    }
}
