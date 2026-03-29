<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SharedAccess extends Model
{
    use HasFactory;

    public const ROLES = [
        'admin',
        'project_manager',
        'lighting',
        'sound',
        'stage_manager',
    ];

    public const ROLE_LABELS = [
        'admin' => 'Admin',
        'project_manager' => 'Project Manager',
        'lighting' => 'Lighting',
        'sound' => 'Sound',
        'stage_manager' => 'Stage Manager',
    ];

    public static function translatedRoleLabels(): array
    {
        return [
            'admin' => __('ui.shared_role_admin'),
            'project_manager' => __('ui.shared_role_project_manager'),
            'lighting' => __('ui.shared_role_lighting'),
            'sound' => __('ui.shared_role_sound'),
            'stage_manager' => __('ui.shared_role_stage_manager'),
        ];
    }

    public static function translatedRoleLabel(?string $role): string
    {
        return self::translatedRoleLabels()[$role] ?? ($role ?: __('ui.shared_access'));
    }

    protected $fillable = [
        'label',
        'role',
        'token',
        'tour_id',
        'created_by',
        'revoked_at',
        'last_used_at',
    ];

    protected $casts = [
        'revoked_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (SharedAccess $sharedAccess): void {
            if (blank($sharedAccess->token)) {
                $sharedAccess->token = Str::random(48);
            }
        });
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function showSectionMessages(): HasMany
    {
        return $this->hasMany(ShowSectionMessage::class);
    }

    public function showMessageReads(): HasMany
    {
        return $this->hasMany(ShowMessageRead::class);
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function ownerId(): ?int
    {
        return $this->tour?->owner_id ?? $this->created_by;
    }

    public function authorLabel(): string
    {
        $role = self::translatedRoleLabel($this->role);

        return $this->label
            ? "{$this->label} ({$role})"
            : __('ui.shared_access')." ({$role})";
    }

    public function avatarInitials(): string
    {
        $source = $this->label ?: self::translatedRoleLabel($this->role);
        $source = preg_replace('/\s+/', '', trim((string) $source)) ?: '';
        $initials = Str::upper(Str::substr($source, 0, 2));

        return $initials !== '' ? $initials : 'SC';
    }
}
