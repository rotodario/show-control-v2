<?php

namespace App\Support;

use App\Models\Show;
use App\Models\User;
use App\Models\UserAlertSetting;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class ShowAlertService
{
    public function alertsForShow(Show $show, ?CarbonInterface $today = null, ?User $user = null): array
    {
        $today ??= now()->startOfDay();
        $showDate = $show->date?->copy()->startOfDay();

        if (! $showDate) {
            return [];
        }

        $daysUntil = $today->diffInDays($showDate, false);

        if ($daysUntil < 0) {
            return [];
        }

        $alerts = [];
        $settings = $this->resolveSettings($show, $user);

        if ($settings->core_info_enabled && $daysUntil <= $settings->core_info_days && ! $this->hasCoreInfo($show)) {
            $alerts[] = [
                'key' => 'missing_core_info',
                'severity' => 'warning',
                'title' => 'Falta info base',
                'message' => 'Quedan menos de 3 meses y el bolo sigue sin la informacion base completa.',
            ];
        }

        if ($settings->status_enabled && $daysUntil <= $settings->status_days && ! in_array($show->status, ['confirmed', 'closed'], true)) {
            $alerts[] = [
                'key' => 'status_not_ready',
                'severity' => 'danger',
                'title' => 'Estado pendiente',
                'message' => 'Queda menos de 1 mes y el bolo no esta confirmado o cerrado.',
            ];
        }

        if ($settings->validations_enabled && $daysUntil <= $settings->validations_days) {
            $missingValidations = $this->missingValidations($show);

            if ($missingValidations !== []) {
                $alerts[] = [
                    'key' => 'missing_validations',
                    'severity' => 'danger',
                    'title' => 'Validaciones pendientes',
                    'message' => 'Queda menos de 1 semana y faltan por validar: '.implode(', ', $missingValidations).'.',
                ];
            }
        }

        return $alerts;
    }

    public function alertsForCollection(iterable $shows, ?User $user = null): Collection
    {
        return collect($shows)->mapWithKeys(function (Show $show) use ($user): array {
            return [$show->id => $this->alertsForShow($show, user: $user)];
        });
    }

    public function hasCoreInfo(Show $show): bool
    {
        $required = [
            $show->venue,
            $show->contact_name,
            $show->contact_phone,
            $show->lighting_notes,
            $show->sound_notes,
            $show->space_notes,
            $show->general_notes,
            $show->getRawOriginal('load_in_at') ?: $show->load_in_at,
            $show->getRawOriginal('show_at') ?: $show->show_at,
        ];

        foreach ($required as $value) {
            if (blank($value)) {
                return false;
            }
        }

        return true;
    }

    public function missingValidations(Show $show): array
    {
        $missing = [];

        if (! $show->lighting_validated) {
            $missing[] = 'iluminacion';
        }

        if (! $show->sound_validated) {
            $missing[] = 'sonido';
        }

        if (! $show->space_validated) {
            $missing[] = 'espacio';
        }

        if (! $show->general_validated) {
            $missing[] = 'general';
        }

        return $missing;
    }

    private function resolveSettings(Show $show, ?User $user = null): UserAlertSetting
    {
        $owner = $user
            ?? $show->owner
            ?? ($show->owner_id ? User::query()->with('alertSettings')->find($show->owner_id) : null);

        return $owner?->alertSettings ?? new UserAlertSetting();
    }
}
