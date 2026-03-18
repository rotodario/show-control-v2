<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ShowSectionMessage extends Model
{
    use HasFactory;

    private const ACCENT_COLORS = [
        '#0F766E',
        '#1D4ED8',
        '#7C3AED',
        '#BE123C',
        '#B45309',
        '#047857',
        '#4338CA',
        '#C2410C',
        '#0E7490',
        '#9333EA',
    ];

    public const SECTIONS = [
        'lighting' => 'Iluminacion',
        'sound' => 'Sonido',
        'space' => 'Espacio / venue',
        'general' => 'Notas generales',
    ];

    protected $fillable = [
        'show_id',
        'section',
        'message',
        'user_id',
        'shared_access_id',
        'author_name',
    ];

    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sharedAccess(): BelongsTo
    {
        return $this->belongsTo(SharedAccess::class);
    }

    public function authorDisplayName(): string
    {
        if ($this->sharedAccess) {
            return $this->sharedAccess->label ?: 'Acceso compartido';
        }

        return $this->user?->name ?: $this->author_name;
    }

    public function authorRoleLabel(): ?string
    {
        if ($this->sharedAccess) {
            return SharedAccess::ROLE_LABELS[$this->sharedAccess->role] ?? Str::headline($this->sharedAccess->role);
        }

        $role = $this->user?->getRoleNames()->first();

        return $role ? Str::headline($role) : null;
    }

    public function accentColor(): string
    {
        $key = $this->shared_access_id
            ? 'shared-'.$this->shared_access_id
            : ($this->user_id ? 'user-'.$this->user_id : 'author-'.$this->author_name);

        return self::ACCENT_COLORS[abs(crc32($key)) % count(self::ACCENT_COLORS)];
    }
}
