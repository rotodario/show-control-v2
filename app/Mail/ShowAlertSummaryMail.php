<?php

namespace App\Mail;

use App\Models\Show;
use App\Models\User;
use App\Models\UserMailSetting;
use App\Support\MailTemplateRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShowAlertSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Show $show,
        public User $user,
        public UserMailSetting $settings,
        public array $alerts,
    ) {
    }

    public function build(): static
    {
        $alertLines = collect($this->alerts)
            ->map(fn (array $alert): string => '- '.$alert['title'].': '.$alert['message'])
            ->implode("\n");

        $context = [
            'account_name' => $this->user->name,
            'show_name' => $this->show->name,
            'show_date' => $this->show->date?->format('d/m/Y') ?: '-',
            'show_city' => $this->show->city ?: '-',
            'show_venue' => $this->show->venue ?: '-',
            'show_status' => $this->show->translatedCurrentStatus(),
            'show_url' => $this->show->publicSummaryUrl(),
            'travel_mode' => \App\Models\Show::translatedTravelModeOptions()[$this->show->travel_mode ?: 'van'] ?? ($this->show->travel_mode ?: '-'),
            'alert_count' => count($this->alerts),
            'alert_lines' => $alertLines ?: '- Sin detalle',
            'contact_name' => $this->show->contact_name ?: '-',
            'contact_phone' => $this->show->contact_phone ?: '-',
            'contact_email' => $this->show->contact_email ?: '-',
            'signature' => $this->settings->signature ?: $this->user->name,
        ];

        $subject = MailTemplateRenderer::render(
            $this->settings->alert_subject_template,
            $context,
            __('ui.mail_default_alert_subject')
        );

        $body = MailTemplateRenderer::render(
            $this->settings->alert_body_template,
            $context,
            __('ui.mail_default_alert_body')
        );

        $mail = $this->subject($subject)
            ->view('emails.show-alert-summary', [
                'subjectLine' => $subject,
                'body' => $body,
                'alertLines' => $alertLines,
                'show' => $this->show,
            ]);

        if (filled($this->settings->from_name) && filled(config('mail.from.address'))) {
            $mail->from(config('mail.from.address'), $this->settings->from_name);
        }

        if (filled($this->settings->reply_to_email)) {
            $mail->replyTo($this->settings->reply_to_email, $this->settings->from_name ?: null);
        }

        return $mail;
    }
}
