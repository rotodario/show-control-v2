<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPdfSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'brand_name',
        'primary_color',
        'header_text',
        'footer_text',
        'show_generated_at',
    ];

    protected $casts = [
        'show_generated_at' => 'boolean',
    ];

    protected $attributes = [
        'primary_color' => '#0f172a',
        'show_generated_at' => true,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
