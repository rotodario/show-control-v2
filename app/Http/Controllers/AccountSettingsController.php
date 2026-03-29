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
use App\Models\Show;
use App\Support\ShowMailNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccountSettingsController extends Controller
{
    private const MAIL_RESET_TYPES = ['roadmap', 'alert'];

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

    public function mail(Request $request, ShowMailNotificationService $showMailNotificationService): View
    {
        $user = $request->user();
        $settings = $user?->mailSettings()->first();
        $defaults = $this->defaultMailTemplates($user);

        if (! $settings) {
            $settings = new UserMailSetting([
                'notifications_enabled' => false,
                'alert_notifications_enabled' => false,
                'from_name' => $user?->name,
                'subject_template' => $defaults['roadmap']['subject'],
                'body_template' => $defaults['roadmap']['body'],
                'signature' => $user?->name,
                'alert_subject_template' => $defaults['alert']['subject'],
                'alert_body_template' => $defaults['alert']['body'],
            ]);
        }

        $previewShow = $user?->shows()->latest('date')->first() ?? new Show([
            'owner_id' => $user?->id,
            'public_summary_token' => 'preview-show',
            'date' => now()->addDays(14),
            'city' => __('ui.mail_preview_example_city'),
            'venue' => __('ui.mail_preview_example_venue'),
            'travel_origin' => __('ui.mail_preview_example_origin'),
            'travel_mode' => 'van',
            'name' => __('ui.mail_preview_example_show'),
            'status' => 'confirmed',
            'contact_name' => $user?->name ?: 'Show Control',
            'contact_phone' => '+34 600 000 000',
            'contact_email' => $user?->email ?: 'noreply@example.com',
        ]);

        $previewTravelRoute = [
            'duration_text' => '2 h 15 min',
            'distance_text' => '185 km',
        ];

        $previewAlerts = [
            [
                'title' => __('ui.mail_preview_example_alert_title'),
                'message' => __('ui.mail_preview_example_alert_message'),
                'severity' => 'warning',
            ],
        ];

        return view('account.mail', [
            'user' => $user,
            'settings' => $settings,
            'accountSection' => 'mail',
            'previewShow' => $previewShow,
            'previewAlerts' => $previewAlerts,
            'roadmapPreview' => $showMailNotificationService->roadmapPreview($previewShow, $user, $previewTravelRoute, $previewAlerts, $settings),
            'alertPreview' => $showMailNotificationService->alertPreview($previewShow, $user, $previewAlerts, $settings),
            'mailTemplateDefaults' => $defaults,
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
        $payload = $request->validated();
        $resetType = $request->string('reset_template')->toString();

        if (in_array($resetType, self::MAIL_RESET_TYPES, true)) {
            $defaults = $this->defaultMailTemplates($request->user());

            if ($resetType === 'roadmap') {
                $payload['subject_template'] = $defaults['roadmap']['subject'];
                $payload['body_template'] = $defaults['roadmap']['body'];
            }

            if ($resetType === 'alert') {
                $payload['alert_subject_template'] = $defaults['alert']['subject'];
                $payload['alert_body_template'] = $defaults['alert']['body'];
            }
        }

        UserMailSetting::query()->updateOrCreate(['user_id' => $request->user()->id], $payload);

        return redirect()
            ->route('account.mail')
            ->with('status', in_array($resetType, self::MAIL_RESET_TYPES, true)
                ? __('ui.account_mail_template_restored')
                : __('ui.account_mail_saved'));
    }

    private function defaultMailTemplates(?\App\Models\User $user): array
    {
        $signature = $user?->name ?: 'Show Control';

        return [
            'roadmap' => [
                'subject' => __('ui.mail_default_roadmap_subject'),
                'body' => str_replace('{{signature}}', $signature, __('ui.mail_default_roadmap_body')),
            ],
            'alert' => [
                'subject' => __('ui.mail_default_alert_subject'),
                'body' => str_replace('{{signature}}', $signature, __('ui.mail_default_alert_body')),
            ],
        ];
    }
}
