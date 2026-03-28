<?php

namespace App\Mail;

use App\Models\User;
use App\Support\MailTemplateRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlatformRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $registeredUser,
        public array $settings,
    ) {
    }

    public function build(): static
    {
        $context = [
            'user_name' => $this->registeredUser->name,
            'user_email' => $this->registeredUser->email,
            'registered_at' => now()->format('d/m/Y H:i'),
        ];

        $subject = MailTemplateRenderer::render(
            $this->settings['platform_registration_subject'] ?? null,
            $context,
            'Nuevo registro en Show Control: {{user_name}}'
        );

        $body = MailTemplateRenderer::render(
            $this->settings['platform_registration_body'] ?? null,
            $context,
            "Se ha registrado una nueva cuenta en Show Control.\n\nNombre: {{user_name}}\nEmail: {{user_email}}\nFecha: {{registered_at}}\n"
        );

        $mail = $this->subject($subject)
            ->view('emails.platform-registration', [
                'subjectLine' => $subject,
                'body' => $body,
                'registeredUser' => $this->registeredUser,
            ]);

        if (filled($this->settings['platform_mail_from_address'] ?? null)) {
            $mail->from(
                $this->settings['platform_mail_from_address'],
                $this->settings['platform_mail_from_name'] ?: null
            );
        }

        if (filled($this->settings['platform_mail_reply_to_email'] ?? null)) {
            $mail->replyTo(
                $this->settings['platform_mail_reply_to_email'],
                $this->settings['platform_mail_from_name'] ?: null
            );
        }

        return $mail;
    }
}
