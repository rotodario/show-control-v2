<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Show extends Model
{
    use HasFactory;

    public const STATUS_OPTIONS = [
        'tentative' => 'Tentativo',
        'confirmed' => 'Confirmado',
        'closed' => 'Cerrado',
        'cancelled' => 'Cancelado',
    ];

    protected $fillable = [
        'owner_id',
        'external_source',
        'external_calendar_id',
        'external_event_id',
        'tour_id',
        'date',
        'city',
        'venue',
        'name',
        'status',
        'load_in_at',
        'meal_at',
        'soundcheck_at',
        'doors_at',
        'show_at',
        'show_end_at',
        'load_out_at',
        'contact_name',
        'contact_role',
        'contact_phone',
        'contact_email',
        'lighting_notes',
        'lighting_validated',
        'sound_notes',
        'sound_validated',
        'space_notes',
        'space_validated',
        'general_notes',
        'general_validated',
    ];

    protected $casts = [
        'date' => 'date',
        'load_in_at' => 'datetime:H:i',
        'meal_at' => 'datetime:H:i',
        'soundcheck_at' => 'datetime:H:i',
        'doors_at' => 'datetime:H:i',
        'show_at' => 'datetime:H:i',
        'show_end_at' => 'datetime:H:i',
        'load_out_at' => 'datetime:H:i',
        'lighting_validated' => 'boolean',
        'sound_validated' => 'boolean',
        'space_validated' => 'boolean',
        'general_validated' => 'boolean',
    ];

    public function scopeOwnedBy(Builder $query, ?int $userId): Builder
    {
        return $query->where('owner_id', $userId);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ShowDocument::class)->latest();
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class)->latest();
    }

    public function sectionMessages(): HasMany
    {
        return $this->hasMany(ShowSectionMessage::class)->oldest();
    }

    public function messageReads(): HasMany
    {
        return $this->hasMany(ShowMessageRead::class);
    }
}
