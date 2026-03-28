<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_and_update_platform_settings(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin)->get(route('platform.settings.edit'))
            ->assertOk()
            ->assertSee('Ajustes');

        $response = $this->actingAs($superAdmin)->put(route('platform.settings.update'), [
            'platform_default_locale' => 'en',
        ]);

        $response->assertRedirect(route('platform.settings.edit'));
        $this->assertSame('en', AppSetting::getValue('platform_default_locale'));
    }

    public function test_platform_default_locale_applies_to_guest_pages(): void
    {
        AppSetting::putValue('platform_default_locale', 'en');

        $this->get('/login')
            ->assertOk()
            ->assertSee('Show Control by Jose Osuna. MIT License.');
    }
}
