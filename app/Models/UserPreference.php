<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'default_show_status',
        'default_travel_mode',
        'default_city',
        'default_travel_origin',
        'ui_locale',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
