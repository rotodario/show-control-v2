<?php

namespace App\Listeners;

use App\Mail\PlatformRegistrationMail;
use App\Support\PlatformMailSettings;
use App\Support\RecipientList;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;

class SendPlatformRegistrationNotification
{
    public function __construct(
        protected PlatformMailSettings $platformMailSettings,
    ) {
    }

    public function handle(Registered $event): void
    {
        $settings = $this->platformMailSettings->all();
        $recipients = RecipientList::parse($settings['platform_registration_recipients'] ?? null);

        if (! ($settings['registration_notifications_enabled'] ?? false) || $recipients === []) {
            return;
        }

        Mail::to($recipients)->send(new PlatformRegistrationMail($event->user, $settings));
    }
}
