<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ShowDocument extends Model
{
    use HasFactory;

    public const TYPES = [
        'Rider',
        'Hospitality',
        'Patch',
        'Plano',
        'Timing',
        'Produccion',
        'Input List',
        'Otro',
    ];

    public static function translatedTypes(): array
    {
        return [
            'Rider' => __('ui.document_type_rider'),
            'Hospitality' => __('ui.document_type_hospitality'),
            'Patch' => __('ui.document_type_patch'),
            'Plano' => __('ui.document_type_plot'),
            'Timing' => __('ui.document_type_timing'),
            'Produccion' => __('ui.document_type_production'),
            'Input List' => __('ui.document_type_input_list'),
            'Otro' => __('ui.document_type_other'),
        ];
    }

    public static function translatedTypeLabel(?string $type): string
    {
        return static::translatedTypes()[$type] ?? (string) $type;
    }

    protected $fillable = [
        'show_id',
        'document_type',
        'title',
        'original_name',
        'storage_path',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function publicUrl(): string
    {
        return Storage::disk('public')->url($this->storage_path);
    }
}
