<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TourDocument extends Model
{
    use HasFactory;

    public const TYPES = [
        'Rider',
        'Hospitality',
        'Patch',
        'Plano',
        'Timing',
        'Produccion',
        'Otro',
    ];

    protected $fillable = [
        'tour_id',
        'document_type',
        'title',
        'original_name',
        'storage_path',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
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
