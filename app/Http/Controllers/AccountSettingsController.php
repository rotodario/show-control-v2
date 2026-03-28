<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserAlertSettingsRequest;
use App\Http\Requests\UpdateUserMailSettingsRequest;
use App\Http\Requests\UpdateUserPreferencesRequest;
use App\Http\Requests\UpdateUserPdfSettingsRequest;
use App\Models\UserAlertSetting;
use App\Models\UserMailSetting;
use App\Models\UserPreference;
use App\Models\UserPdfSetting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccountSettingsController extends Controller
{
    public function profile(Request $request): View
    {
        return view('account.profile', [
            'user' => $request->user(),
            'accountSection' => 'profile',
        ]);
    }

    public function alerts(Request $request): View
    {
        return view('account.alerts', [
            'user' => $request->user(),
            'settings' => $request->user()?->alertSettings()->firstOrNew(),
            'accountSection' => 'alerts',
        ]);
    }

    public function updateAlerts(UpdateUserAlertSettingsRequest $request): RedirectResponse
    {
        UserAlertSetting::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->validated(),
        );

        return redirect()
            ->route('account.alerts')
            ->with('status', 'alert-settings-updated');
    }

    public function pdf(Request $request): View
    {
        return view('account.pdf', [
            'user' => $request->user(),
            'settings' => $request->user()?->pdfSettings()->firstOrNew(),
            'accountSection' => 'pdf',
        ]);
    }

    public function updatePdf(UpdateUserPdfSettingsRequest $request): RedirectResponse
    {
        UserPdfSetting::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->validated(),
        );

        return redirect()
            ->route('account.pdf')
            ->with('status', 'pdf-settings-updated');
    }

    public function preferences(Request $request): View
    {
        return view('account.preferences', [
            'user' => $request->user(),
            'settings' => $request->user()?->preferences()->firstOrNew(),
            'statusOptions' => \App\Models\Show::STATUS_OPTIONS,
            'travelModeOptions' => \App\Models\Show::TRAVEL_MODE_OPTIONS,
            'localeLabels' => config('app.locale_labels', ['es' => 'Español', 'en' => 'English']),
            'accountSection' => 'preferences',
        ]);
    }

    public function mail(Request $request): View
    {
        $settings = $request->user()?->mailSettings()->first();

        if (! $settings) {
            $settings = new UserMailSetting([
                'notifications_enabled' => false,
                'alert_notifications_enabled' => false,
                'from_name' => $request->user()?->name,
                'subject_template' => 'Hoja de ruta: {{show_name}} - {{show_date}}',
                'body_template' => "Hola,\n\nAdjuntamos la hoja de ruta del bolo {{show_name}} para el {{show_date}} en {{show_city}}.\nVenue: {{show_venue}}\nEstado: {{show_status}}\nModo de viaje: {{travel_mode}}\nTiempo estimado: {{travel_duration}}\nDistancia: {{travel_distance}}\nContacto: {{contact_name}} / {{contact_phone}} / {{contact_email}}\n\n{{signature}}",
                'signature' => $request->user()?->name,
                'alert_subject_template' => 'Alerta de bolo: {{show_name}} ({{alert_count}} pendientes)',
                'alert_body_template' => "Hola,\n\nHay {{alert_count}} alertas pendientes en el bolo {{show_name}} del {{show_date}} en {{show_city}}.\nVenue: {{show_venue}}\n\n{{alert_lines}}\n\nContacto: {{contact_name}} / {{contact_phone}} / {{contact_email}}\n\n{{signature}}",
            ]);
        }

        return view('account.mail', [
            'user' => $request->user(),
            'settings' => $settings,
            'accountSection' => 'mail',
        ]);
    }

    public function updatePreferences(UpdateUserPreferencesRequest $request): RedirectResponse
    {
        UserPreference::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->validated(),
        );

        return redirect()
            ->route('account.preferences')
            ->with('status', 'preferences-updated');
    }

    public function updateMail(UpdateUserMailSettingsRequest $request): RedirectResponse
    {
        UserMailSetting::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->validated(),
        );

        return redirect()
            ->route('account.mail')
            ->with('status', 'Correo operativo actualizado.');
    }
}
