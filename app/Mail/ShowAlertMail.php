<?php

namespace App\Mail;

use App\Models\Show;
use App\Models\User;
use App\Models\UserMailSetting;
use App\Support\MailTemplateRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShowAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Show $show,
        public User $user,
        public UserMailSetting $settings,
        public array $travelRoute = [],
    ) {
    }

    public function build(): static
    {
        $context = [
            'account_name' => $this->user->name,
            'show_name' => $this->show->name,
            'show_date' => $this->show->date?->format('d/m/Y') ?: '-',
            'show_city' => $this->show->city ?: '-',
            'show_venue' => $this->show->venue ?: '-',
            'show_status' => $this->show->translatedCurrentStatus(),
            'travel_mode' => \App\Models\Show::translatedTravelModeOptions()[$this->show->travel_mode ?: 'van'] ?? ($this->show->travel_mode ?: '-'),
            'travel_duration' => $this->travelRoute['duration_text'] ?? '-',
            'travel_distance' => $this->travelRoute['distance_text'] ?? '-',
            'show_url' => route('shows.show', $this->show),
            'contact_name' => $this->show->contact_name ?: '-',
            'contact_phone' => $this->show->contact_phone ?: '-',
            'contact_email' => $this->show->contact_email ?: '-',
            'signature' => $this->settings->signature ?: $this->user->name,
        ];

        $subject = MailTemplateRenderer::render(
            $this->settings->subject_template,
            $context,
            'Aviso de bolo: {{show_name}} - {{show_date}}'
        );

        $body = MailTemplateRenderer::render(
            $this->settings->body_template,
            $context,
            "Hola,\n\nTe enviamos el resumen del bolo {{show_name}} para el {{show_date}} en {{show_city}}.\nVenue: {{show_venue}}\nEstado: {{show_status}}\nModo de viaje: {{travel_mode}}\nTiempo estimado: {{travel_duration}}\nDistancia: {{travel_distance}}\n\nFicha del bolo: {{show_url}}\n\n{{signature}}"
        );

        $mail = $this->subject($subject)
            ->view('emails.show-alert', [
                'subjectLine' => $subject,
                'body' => $body,
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
