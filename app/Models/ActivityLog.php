<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'actor_id',
        'actor_name',
        'action',
        'detail',
        'tour_id',
        'show_id',
        'subject_type',
        'subject_id',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function translatedDetail(): string
    {
        $key = 'ui.activity_'.str_replace('.', '_', $this->action);

        $translated = __($key, [
            'subject' => $this->subjectLabel(),
        ]);

        if ($translated !== $key) {
            return $translated;
        }

        return $this->detail;
    }

    private function subjectLabel(): string
    {
        if ($this->subject && isset($this->subject->title) && filled($this->subject->title)) {
            return $this->subject->title;
        }

        if ($this->subject && isset($this->subject->name) && filled($this->subject->name)) {
            return $this->subject->name;
        }

        if (filled(data_get($this->properties, 'shared_access_label'))) {
            return (string) data_get($this->properties, 'shared_access_label');
        }

        if (filled(data_get($this->properties, 'shared_access_role'))) {
            return (string) data_get($this->properties, 'shared_access_role');
        }

        if ($this->show && filled($this->show->name)) {
            return $this->show->name;
        }

        if ($this->tour && filled($this->tour->name)) {
            return $this->tour->name;
        }

        return Str::headline(class_basename((string) $this->subject_type));
    }
}
