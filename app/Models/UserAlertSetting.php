<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAlertSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'core_info_enabled',
        'core_info_days',
        'status_enabled',
        'status_days',
        'validations_enabled',
        'validations_days',
    ];

    protected $casts = [
        'core_info_enabled' => 'boolean',
        'status_enabled' => 'boolean',
        'validations_enabled' => 'boolean',
    ];

    protected $attributes = [
        'core_info_enabled' => true,
        'core_info_days' => 90,
        'status_enabled' => true,
        'status_days' => 30,
        'validations_enabled' => true,
        'validations_days' => 7,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
