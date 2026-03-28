<?php

namespace App\Mail;

use App\Models\Show;
use App\Models\User;
use App\Models\UserMailSetting;
use App\Support\MailTemplateRenderer;
use App\Support\ShowRoadmapPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShowRoadmapMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Show $show,
        public User $user,
        public UserMailSetting $settings,
        public array $travelRoute = [],
        public array $alerts = [],
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
            'contact_name' => $this->show->contact_name ?: '-',
            'contact_phone' => $this->show->contact_phone ?: '-',
            'contact_email' => $this->show->contact_email ?: '-',
            'signature' => $this->settings->signature ?: $this->user->name,
        ];

        $subject = MailTemplateRenderer::render(
            $this->settings->subject_template,
            $context,
            'Hoja de ruta: {{show_name}} - {{show_date}}'
        );

        $body = MailTemplateRenderer::render(
            $this->settings->body_template,
            $context,
            "Hola,\n\nAdjuntamos la hoja de ruta del bolo {{show_name}} para el {{show_date}} en {{show_city}}.\nVenue: {{show_venue}}\nEstado: {{show_status}}\nModo de viaje: {{travel_mode}}\nTiempo estimado: {{travel_duration}}\nDistancia: {{travel_distance}}\nContacto: {{contact_name}} / {{contact_phone}} / {{contact_email}}\n\n{{signature}}"
        );

        $pdfService = app(ShowRoadmapPdfService::class);

        $mail = $this->subject($subject)
            ->view('emails.show-roadmap', [
                'subjectLine' => $subject,
                'body' => $body,
                'show' => $this->show,
            ])
            ->attachData(
                $pdfService->output($this->show, $this->user, $this->alerts, $this->travelRoute),
                $pdfService->filename($this->show),
                ['mime' => 'application/pdf']
            );

        if (filled($this->settings->from_name) && filled(config('mail.from.address'))) {
            $mail->from(config('mail.from.address'), $this->settings->from_name);
        }

        if (filled($this->settings->reply_to_email)) {
            $mail->replyTo($this->settings->reply_to_email, $this->settings->from_name ?: null);
        }

        return $mail;
    }
}
