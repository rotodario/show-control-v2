<?php

namespace Tests\Feature;

use App\Mail\PlatformRegistrationMail;
use App\Models\AppSetting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PlatformMailSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_and_update_platform_mail_settings(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin)->get(route('platform.mail.edit'))
            ->assertOk()
            ->assertSee('Correo global');

        $response = $this->actingAs($superAdmin)->put(route('platform.mail.update'), [
            'registration_notifications_enabled' => '1',
            'platform_mail_from_name' => 'Show Control',
            'platform_mail_from_address' => 'platform@example.com',
            'platform_mail_reply_to_email' => 'reply@example.com',
            'platform_registration_recipients' => 'ops@example.com, admin@example.com',
            'platform_registration_subject' => 'Nuevo usuario {{user_name}}',
            'platform_registration_body' => 'Alta: {{user_email}}',
        ]);

        $response->assertRedirect(route('platform.mail.edit'));
        $this->assertSame('1', AppSetting::getValue('registration_notifications_enabled'));
        $this->assertSame('platform@example.com', AppSetting::getValue('platform_mail_from_address'));
        $this->assertSame('ops@example.com, admin@example.com', AppSetting::getValue('platform_registration_recipients'));
    }

    public function test_registration_can_send_platform_notice_when_enabled(): void
    {
        Mail::fake();

        AppSetting::putValue('registration_notifications_enabled', '1');
        AppSetting::putValue('platform_mail_from_name', 'Show Control');
        AppSetting::putValue('platform_mail_from_address', 'platform@example.com');
        AppSetting::putValue('platform_registration_recipients', 'ops@example.com admin@example.com');
        AppSetting::putValue('platform_registration_subject', 'Nuevo usuario {{user_name}}');
        AppSetting::putValue('platform_registration_body', 'Alta {{user_email}}');

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(\App\Providers\RouteServiceProvider::HOME);

        Mail::assertSent(PlatformRegistrationMail::class, function (PlatformRegistrationMail $mail): bool {
            return $mail->hasTo('ops@example.com')
                && $mail->hasTo('admin@example.com');
        });
    }
}
