<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Tour extends Model
{
    use HasFactory;

    private const LEGACY_ICS_NOTES = [
        'Creada automaticamente desde importacion por ICS.',
        'Created automatically from ICS import.',
    ];

    protected $fillable = [
        'owner_id',
        'name',
        'color',
        'notes',
    ];

    public function scopeOwnedBy(Builder $query, ?int $userId): Builder
    {
        return $query->where('owner_id', $userId);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(TourContact::class)->orderBy('role')->orderBy('name');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(TourDocument::class)->latest();
    }

    public function shows(): HasMany
    {
        return $this->hasMany(Show::class)->orderBy('date');
    }

    public function sharedAccesses(): HasMany
    {
        return $this->hasMany(SharedAccess::class)->latest();
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class)->latest();
    }

    public function localizedNotes(): ?string
    {
        if (blank($this->notes)) {
            return $this->notes;
        }

        if (in_array($this->notes, self::LEGACY_ICS_NOTES, true)) {
            return __('ui.tour_created_from_ics_notes');
        }

        return $this->notes;
    }
}
