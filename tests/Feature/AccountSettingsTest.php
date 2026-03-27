<?php

namespace Tests\Feature;

use App\Models\UserAlertSetting;
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

    public function test_account_index_redirects_to_profile(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)->get(route('account.index'))
            ->assertRedirect(route('account.profile'));
    }
}
