<?php

namespace Tests\Feature;

use App\Mail\ShowAlertSummaryMail;
use App\Mail\ShowRoadmapMail;
use App\Models\Show;
use App\Models\User;
use App\Models\UserAlertSetting;
use App\Models\UserMailSetting;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ShowMailNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_send_show_roadmap_mail_to_configured_recipients(): void
    {
        Mail::fake();
        Http::fake([
            'https://nominatim.openstreetmap.org/*' => Http::response([
                ['lat' => '40.4168', 'lon' => '-3.7038'],
            ], 200),
            'https://router.project-osrm.org/*' => Http::response([
                'routes' => [[
                    'duration' => 7200,
                    'distance' => 620000,
                    'geometry' => [
                        'type' => 'LineString',
                        'coordinates' => [[-3.7038, 40.4168], [2.1734, 41.3851]],
                    ],
                ]],
            ], 200),
        ]);

        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        UserMailSetting::create([
            'user_id' => $user->id,
            'notifications_enabled' => true,
            'from_name' => 'Produccion Roto',
            'reply_to_email' => 'reply@example.com',
            'recipients' => 'road@example.com, crew@example.com',
            'cc_recipients' => 'pm@example.com',
            'subject_template' => 'Hoja {{show_name}}',
            'body_template' => 'Hola {{show_name}} {{travel_duration}}',
            'signature' => 'Produccion',
        ]);

        $show = Show::create([
            'owner_id' => $user->id,
            'date' => '2026-06-22',
            'city' => 'Barcelona',
            'venue' => 'Palau Sant Jordi',
            'travel_origin' => 'Madrid',
            'travel_mode' => 'van',
            'name' => 'Bolo Barcelona',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->post(route('shows.send-roadmap-mail', $show));

        $response->assertRedirect(route('shows.show', $show));

        Mail::assertSent(ShowRoadmapMail::class, function (ShowRoadmapMail $mail): bool {
            return $mail->hasTo('road@example.com')
                && $mail->hasTo('crew@example.com')
                && $mail->hasCc('pm@example.com');
        });
    }

    public function test_admin_can_send_show_alert_mail_to_configured_recipients(): void
    {
        Mail::fake();
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        UserAlertSetting::create([
            'user_id' => $user->id,
            'core_info_enabled' => true,
            'core_info_days' => 30,
            'status_enabled' => true,
            'status_days' => 30,
            'validations_enabled' => true,
            'validations_days' => 30,
        ]);

        UserMailSetting::create([
            'user_id' => $user->id,
            'alert_notifications_enabled' => true,
            'alert_recipients' => 'alerts@example.com',
            'alert_cc_recipients' => 'ops@example.com',
            'alert_subject_template' => 'Alerta {{show_name}}',
            'alert_body_template' => 'Hay {{alert_count}} alertas',
            'signature' => 'Produccion',
        ]);

        $show = Show::create([
            'owner_id' => $user->id,
            'date' => now()->addDays(5)->toDateString(),
            'city' => 'Barcelona',
            'name' => 'Bolo Barcelona',
            'status' => 'tentative',
        ]);

        $response = $this->actingAs($user)->post(route('shows.send-alert-mail', $show));

        $response->assertRedirect(route('shows.show', $show));

        Mail::assertSent(ShowAlertSummaryMail::class, function (ShowAlertSummaryMail $mail): bool {
            return $mail->hasTo('alerts@example.com')
                && $mail->hasCc('ops@example.com');
        });
    }

    public function test_show_alert_mail_always_includes_alert_details(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        UserAlertSetting::create([
            'user_id' => $user->id,
            'core_info_enabled' => true,
            'core_info_days' => 30,
            'status_enabled' => true,
            'status_days' => 30,
            'validations_enabled' => true,
            'validations_days' => 30,
        ]);

        $settings = UserMailSetting::create([
            'user_id' => $user->id,
            'alert_notifications_enabled' => true,
            'alert_recipients' => 'alerts@example.com',
            'alert_subject_template' => 'Alerta {{show_name}}',
            'alert_body_template' => 'Resumen corto',
            'signature' => 'Produccion',
        ]);

        $show = Show::create([
            'owner_id' => $user->id,
            'date' => now()->addDays(5)->toDateString(),
            'city' => 'Barcelona',
            'name' => 'Bolo Barcelona',
            'status' => 'tentative',
        ]);

        $mail = new ShowAlertSummaryMail($show, $user, $settings, app(\App\Support\ShowAlertService::class)->alertsForShow($show, user: $user));
        $html = $mail->render();

        $this->assertStringContainsString('Alertas activas del bolo', $html);
        $this->assertStringContainsString('Falta', $html);
    }

    public function test_show_roadmap_mail_is_skipped_without_configured_recipients(): void
    {
        Mail::fake();
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        UserMailSetting::create([
            'user_id' => $user->id,
            'notifications_enabled' => false,
        ]);

        $show = Show::create([
            'owner_id' => $user->id,
            'date' => '2026-06-22',
            'city' => 'Barcelona',
            'name' => 'Bolo Barcelona',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->post(route('shows.send-roadmap-mail', $show));

        $response->assertRedirect(route('shows.show', $show));
        Mail::assertNothingSent();
    }
}
