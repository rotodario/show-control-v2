<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\CarbonInterface;
use Illuminate\Support\Str;

class Show extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $show): void {
            if (blank($show->public_summary_token)) {
                $show->public_summary_token = self::generatePublicSummaryToken();
            }
        });
    }

    public const STATUS_OPTIONS = [
        'tentative' => 'Tentativo',
        'confirmed' => 'Confirmado',
        'closed' => 'Cerrado',
        'cancelled' => 'Cancelado',
    ];

    public const TRAVEL_MODE_OPTIONS = [
        'car' => 'Coche',
        'van' => 'Furgo',
        'sleeper' => 'Sleeper',
        'plane' => 'Avion',
    ];

    public static function translatedStatusOptions(): array
    {
        return [
            'tentative' => __('ui.show_status_tentative'),
            'confirmed' => __('ui.show_status_confirmed'),
            'closed' => __('ui.show_status_closed'),
            'cancelled' => __('ui.show_status_cancelled'),
        ];
    }

    public static function translatedTravelModeOptions(): array
    {
        return [
            'car' => __('ui.travel_mode_car'),
            'van' => __('ui.travel_mode_van'),
            'sleeper' => __('ui.travel_mode_sleeper'),
            'plane' => __('ui.travel_mode_plane'),
        ];
    }

    public static function statusBadgeClasses(?string $status): string
    {
        return match ($status) {
            'confirmed' => 'bg-emerald-100 text-emerald-700',
            'closed' => 'bg-slate-900 text-white',
            'cancelled' => 'bg-rose-100 text-rose-700',
            default => 'bg-amber-100 text-amber-700',
        };
    }

    public static function statusDotClasses(?string $status): string
    {
        return match ($status) {
            'confirmed' => 'bg-emerald-500',
            'closed' => 'bg-slate-900',
            'cancelled' => 'bg-rose-500',
            default => 'bg-amber-400',
        };
    }

    public function currentStatus(?CarbonInterface $today = null): string
    {
        if ($this->status === 'cancelled') {
            return 'cancelled';
        }

        $today ??= now()->startOfDay();
        $showDate = $this->date?->copy()->startOfDay();

        if ($showDate && $showDate->lt($today)) {
            return 'closed';
        }

        return $this->status ?: 'tentative';
    }

    public function translatedCurrentStatus(?CarbonInterface $today = null): string
    {
        $status = $this->currentStatus($today);

        return self::translatedStatusOptions()[$status] ?? $status;
    }

    public function currentStatusBadgeClasses(?CarbonInterface $today = null): string
    {
        return self::statusBadgeClasses($this->currentStatus($today));
    }

    public function currentStatusDotClasses(?CarbonInterface $today = null): string
    {
        return self::statusDotClasses($this->currentStatus($today));
    }

    public function publicSummaryUrl(): string
    {
        return route('public-shows.show', $this->public_summary_token);
    }

    protected $fillable = [
        'owner_id',
        'external_source',
        'external_calendar_id',
        'external_event_id',
        'tour_id',
        'date',
        'city',
        'city_latitude',
        'city_longitude',
        'public_summary_token',
        'venue',
        'travel_origin',
        'travel_mode',
        'flight_origin',
        'flight_destination',
        'flight_duration_estimate',
        'flight_notes',
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
        'city_latitude' => 'float',
        'city_longitude' => 'float',
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

    private static function generatePublicSummaryToken(): string
    {
        do {
            $token = Str::lower(Str::random(32));
        } while (self::query()->where('public_summary_token', $token)->exists());

        return $token;
    }
}
