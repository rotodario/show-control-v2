<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMailSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notifications_enabled',
        'alert_notifications_enabled',
        'from_name',
        'reply_to_email',
        'recipients',
        'cc_recipients',
        'subject_template',
        'body_template',
        'signature',
        'alert_recipients',
        'alert_cc_recipients',
        'alert_subject_template',
        'alert_body_template',
    ];

    protected $casts = [
        'notifications_enabled' => 'boolean',
        'alert_notifications_enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
