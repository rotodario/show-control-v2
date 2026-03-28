<?php

namespace App\Support;

use App\Mail\ShowAlertSummaryMail;
use App\Mail\ShowRoadmapMail;
use App\Models\Show;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class ShowMailNotificationService
{
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
}
