<?php

namespace App\Support;

use App\Mail\ShowAlertSummaryMail;
use App\Mail\ShowRoadmapMail;
use App\Models\Show;
use App\Models\User;
use App\Models\UserMailSetting;
use Illuminate\Support\Facades\Mail;

class ShowMailNotificationService
{
    public function roadmapPreview(Show $show, User $user, array $travelRoute = [], array $alerts = [], ?UserMailSetting $settings = null): array
    {
        $settings ??= $user->mailSettings()->firstOrNew();
        $context = $this->roadmapContext($show, $user, $settings, $travelRoute);

        return [
            'enabled' => (bool) $settings->notifications_enabled,
            'to' => RecipientList::parse($settings->recipients),
            'cc' => RecipientList::parse($settings->cc_recipients),
            'subject' => MailTemplateRenderer::render(
                $settings->subject_template,
                $context,
                __('ui.mail_default_roadmap_subject')
            ),
            'body' => MailTemplateRenderer::render(
                $settings->body_template,
                $context,
                __('ui.mail_default_roadmap_body')
            ),
            'reply_to' => $settings->reply_to_email,
            'from_name' => $settings->from_name,
            'attachment_name' => app(ShowRoadmapPdfService::class)->filename($show),
            'alerts' => $alerts,
        ];
    }

    public function alertPreview(Show $show, User $user, array $alerts, ?UserMailSetting $settings = null): array
    {
        $settings ??= $user->mailSettings()->firstOrNew();
        $context = $this->alertContext($show, $user, $settings, $alerts);
        $alertLines = collect($alerts)
            ->map(fn (array $alert): string => '- '.$alert['title'].': '.$alert['message'])
            ->implode("\n");

        return [
            'enabled' => (bool) $settings->alert_notifications_enabled,
            'to' => RecipientList::parse($settings->alert_recipients),
            'cc' => RecipientList::parse($settings->alert_cc_recipients),
            'subject' => MailTemplateRenderer::render(
                $settings->alert_subject_template,
                $context,
                __('ui.mail_default_alert_subject')
            ),
            'body' => MailTemplateRenderer::render(
                $settings->alert_body_template,
                $context,
                __('ui.mail_default_alert_body')
            ),
            'reply_to' => $settings->reply_to_email,
            'from_name' => $settings->from_name,
            'alerts' => $alerts,
            'alert_lines' => $alertLines,
        ];
    }

    public function sendRoadmapForShow(Show $show, User $user, array $travelRoute = [], array $alerts = []): bool
    {
        $settings = $user->mailSettings()->firstOrNew();
        $to = RecipientList::parse($settings->recipients);
        $cc = RecipientList::parse($settings->cc_recipients);

        if (! $settings->notifications_enabled || $to === []) {
            return false;
        }

        Mail::to($to)
            ->cc($cc)
            ->send(new ShowRoadmapMail($show, $user, $settings, $travelRoute, $alerts));

        return true;
    }

    public function sendAlertForShow(Show $show, User $user, array $alerts): bool
    {
        $settings = $user->mailSettings()->firstOrNew();
        $to = RecipientList::parse($settings->alert_recipients);
        $cc = RecipientList::parse($settings->alert_cc_recipients);

        if (! $settings->alert_notifications_enabled || $to === [] || $alerts === []) {
            return false;
        }

        Mail::to($to)
            ->cc($cc)
            ->send(new ShowAlertSummaryMail($show, $user, $settings, $alerts));

        return true;
    }

    private function roadmapContext(Show $show, User $user, UserMailSetting $settings, array $travelRoute): array
    {
        return array_merge($this->baseShowContext($show, $user, $settings), [
            'account_name' => $user->name,
            'travel_mode' => \App\Models\Show::translatedTravelModeOptions()[$show->travel_mode ?: 'van'] ?? ($show->travel_mode ?: '-'),
            'travel_duration' => $travelRoute['duration_text'] ?? '-',
            'travel_distance' => $travelRoute['distance_text'] ?? '-',
        ]);
    }

    private function alertContext(Show $show, User $user, UserMailSetting $settings, array $alerts): array
    {
        $alertLines = collect($alerts)
            ->map(fn (array $alert): string => '- '.$alert['title'].': '.$alert['message'])
            ->implode("\n");

        return array_merge($this->baseShowContext($show, $user, $settings), [
            'account_name' => $user->name,
            'alert_count' => count($alerts),
            'alert_lines' => $alertLines ?: '- Sin detalle',
        ]);
    }

    private function baseShowContext(Show $show, User $user, UserMailSetting $settings): array
    {
        return [
            'account_name' => $user->name,
            'show_name' => $show->name,
            'show_date' => $show->date?->format('d/m/Y') ?: '-',
            'show_city' => $show->city ?: '-',
            'show_venue' => $show->venue ?: '-',
            'show_status' => $show->translatedCurrentStatus(),
            'show_url' => $show->publicSummaryUrl(),
            'travel_mode' => \App\Models\Show::translatedTravelModeOptions()[$show->travel_mode ?: 'van'] ?? ($show->travel_mode ?: '-'),
            'travel_duration' => '-',
            'travel_distance' => '-',
            'contact_name' => $show->contact_name ?: '-',
            'contact_phone' => $show->contact_phone ?: '-',
            'contact_email' => $show->contact_email ?: '-',
            'signature' => $settings->signature ?: $user->name,
        ];
    }
}
