<?php

namespace Tests\Feature;

use App\Models\UserAlertSetting;
use App\Models\UserMailSetting;
use App\Models\UserPreference;
use App\Models\UserPdfSetting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_account_sections(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)->get(route('account.profile'))
            ->assertOk()
            ->assertSee('Perfil')
            ->assertSee('Alertas');

        $this->actingAs($user)->get(route('account.alerts'))
            ->assertOk()
            ->assertSee('Configuracion de alertas');

        $this->actingAs($user)->get(route('account.pdf'))
            ->assertOk()
            ->assertSee('Personalizacion de documentos');

        $this->actingAs($user)->get(route('account.preferences'))
            ->assertOk()
            ->assertSee('Preferencias de cuenta');

        $this->actingAs($user)->get(route('account.mail'))
            ->assertOk()
            ->assertSee('Correo operativo');
    }

    public function test_admin_can_update_preferences(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)->put(route('account.preferences.update'), [
            'default_show_status' => 'confirmed',
            'default_travel_mode' => 'sleeper',
            'default_city' => 'Madrid',
            'default_travel_origin' => 'Calle Alcala 45, Madrid',
            'ui_locale' => 'en',
        ]);

        $response->assertRedirect(route('account.preferences'));

        $this->assertDatabaseHas('user_preferences', [
            'user_id' => $user->id,
            'default_show_status' => 'confirmed',
            'default_travel_mode' => 'sleeper',
            'default_city' => 'Madrid',
            'default_travel_origin' => 'Calle Alcala 45, Madrid',
            'ui_locale' => 'en',
        ]);
    }

    public function test_preferences_page_uses_saved_values(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        UserPreference::create([
            'user_id' => $user->id,
            'default_show_status' => 'confirmed',
            'default_travel_mode' => 'car',
            'default_city' => 'Barcelona',
            'default_travel_origin' => 'Sants, Barcelona',
            'ui_locale' => 'en',
        ]);

        $this->actingAs($user)->get(route('account.preferences'))
            ->assertOk()
            ->assertSee('Barcelona')
            ->assertSee('Sants, Barcelona')
            ->assertSee('English');
    }

    public function test_admin_can_update_alert_settings(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)->put(route('account.alerts.update'), [
            'core_info_enabled' => '1',
            'core_info_days' => 45,
            'status_enabled' => '0',
            'status_days' => 20,
            'validations_enabled' => '1',
            'validations_days' => 5,
        ]);

        $response->assertRedirect(route('account.alerts'));

        $this->assertDatabaseHas('user_alert_settings', [
            'user_id' => $user->id,
            'core_info_enabled' => true,
            'core_info_days' => 45,
            'status_enabled' => false,
            'status_days' => 20,
            'validations_enabled' => true,
            'validations_days' => 5,
        ]);
    }

    public function test_alert_settings_page_uses_saved_values(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        UserAlertSetting::create([
            'user_id' => $user->id,
            'core_info_enabled' => false,
            'core_info_days' => 60,
            'status_enabled' => true,
            'status_days' => 15,
            'validations_enabled' => false,
            'validations_days' => 3,
        ]);

        $this->actingAs($user)->get(route('account.alerts'))
            ->assertOk()
            ->assertSee('value="60"', false)
            ->assertSee('value="15"', false)
            ->assertSee('value="3"', false);
    }

    public function test_admin_can_update_pdf_settings(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)->put(route('account.pdf.update'), [
            'brand_name' => 'Roto Touring',
            'primary_color' => '#1d4ed8',
            'header_text' => 'Produccion y coordinacion tecnica',
            'footer_text' => 'Documento interno de trabajo',
            'show_generated_at' => '1',
        ]);

        $response->assertRedirect(route('account.pdf'));

        $this->assertDatabaseHas('user_pdf_settings', [
            'user_id' => $user->id,
            'brand_name' => 'Roto Touring',
            'primary_color' => '#1d4ed8',
            'header_text' => 'Produccion y coordinacion tecnica',
            'footer_text' => 'Documento interno de trabajo',
            'show_generated_at' => true,
        ]);
    }

    public function test_pdf_settings_page_uses_saved_values(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        UserPdfSetting::create([
            'user_id' => $user->id,
            'brand_name' => 'Roto Touring',
            'primary_color' => '#1d4ed8',
            'header_text' => 'Cabecera PDF',
            'footer_text' => 'Pie PDF',
            'show_generated_at' => false,
        ]);

        $this->actingAs($user)->get(route('account.pdf'))
            ->assertOk()
            ->assertSee('Roto Touring')
            ->assertSee('value="#1d4ed8"', false)
            ->assertSee('Cabecera PDF')
            ->assertSee('Pie PDF');
    }

    public function test_admin_can_update_mail_settings(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)->put(route('account.mail.update'), [
            'notifications_enabled' => '1',
            'from_name' => 'Produccion Roto',
            'reply_to_email' => 'reply@example.com',
            'recipients' => "one@example.com\ntwo@example.com",
            'cc_recipients' => 'cc@example.com',
            'subject_template' => 'Aviso {{show_name}}',
            'body_template' => 'Hola {{show_name}}',
            'signature' => 'Equipo de produccion',
            'alert_notifications_enabled' => '1',
            'alert_recipients' => 'alerts@example.com',
            'alert_cc_recipients' => 'alert-cc@example.com',
            'alert_subject_template' => 'Alerta {{show_name}}',
            'alert_body_template' => 'Alertas {{alert_count}}',
        ]);

        $response->assertRedirect(route('account.mail'));

        $this->assertDatabaseHas('user_mail_settings', [
            'user_id' => $user->id,
            'notifications_enabled' => true,
            'from_name' => 'Produccion Roto',
            'reply_to_email' => 'reply@example.com',
            'subject_template' => 'Aviso {{show_name}}',
            'body_template' => 'Hola {{show_name}}',
            'signature' => 'Equipo de produccion',
            'alert_notifications_enabled' => true,
            'alert_recipients' => 'alerts@example.com',
            'alert_cc_recipients' => 'alert-cc@example.com',
            'alert_subject_template' => 'Alerta {{show_name}}',
            'alert_body_template' => 'Alertas {{alert_count}}',
        ]);
    }

    public function test_mail_settings_page_uses_saved_values(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        UserMailSetting::create([
            'user_id' => $user->id,
            'notifications_enabled' => true,
            'from_name' => 'Produccion Roto',
            'reply_to_email' => 'reply@example.com',
            'recipients' => 'one@example.com, two@example.com',
            'cc_recipients' => 'cc@example.com',
            'subject_template' => 'Aviso {{show_name}}',
            'body_template' => 'Hola {{show_name}}',
            'signature' => 'Equipo de produccion',
        ]);

        $this->actingAs($user)->get(route('account.mail'))
            ->assertOk()
            ->assertSee('Produccion Roto')
            ->assertSee('reply@example.com')
            ->assertSee('one@example.com')
            ->assertSee('Aviso {{show_name}}', false);
    }

    public function test_account_index_redirects_to_profile(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)->get(route('account.index'))
            ->assertRedirect(route('account.profile'));
    }

    public function test_new_show_uses_saved_preferences_as_defaults(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        UserPreference::create([
            'user_id' => $user->id,
            'default_show_status' => 'confirmed',
            'default_travel_mode' => 'sleeper',
            'default_city' => 'Sevilla',
            'default_travel_origin' => 'Base tecnica Sevilla',
            'ui_locale' => 'en',
        ]);

        $this->actingAs($user)->get(route('shows.create'))
            ->assertOk()
            ->assertSee('value="Sevilla"', false)
            ->assertSee('Base tecnica Sevilla')
            ->assertSee('value="confirmed" selected', false)
            ->assertSee('value="sleeper" selected', false);
    }

    public function test_user_locale_preference_changes_shared_ui_language(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        UserPreference::create([
            'user_id' => $user->id,
            'default_show_status' => 'confirmed',
            'default_travel_mode' => 'van',
            'ui_locale' => 'en',
        ]);

        $this->actingAs($user)->get(route('account.preferences'))
            ->assertOk()
            ->assertSee('Account')
            ->assertSee('Profile')
            ->assertSee('Preferences');
    }
}
