<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function uploadedTourDocuments(): HasMany
    {
        return $this->hasMany(TourDocument::class, 'uploaded_by');
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class, 'owner_id');
    }

    public function shows(): HasMany
    {
        return $this->hasMany(Show::class, 'owner_id');
    }

    public function googleCalendarConnection(): HasOne
    {
        return $this->hasOne(GoogleCalendarConnection::class);
    }

    public function alertSettings(): HasOne
    {
        return $this->hasOne(UserAlertSetting::class);
    }

    public function pdfSettings(): HasOne
    {
        return $this->hasOne(UserPdfSetting::class);
    }

    public function preferences(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    public function uploadedShowDocuments(): HasMany
    {
        return $this->hasMany(ShowDocument::class, 'uploaded_by');
    }

    public function createdSharedAccesses(): HasMany
    {
        return $this->hasMany(SharedAccess::class, 'created_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'actor_id');
    }

    public function showSectionMessages(): HasMany
    {
        return $this->hasMany(ShowSectionMessage::class);
    }

    public function showMessageReads(): HasMany
    {
        return $this->hasMany(ShowMessageRead::class);
    }
}
