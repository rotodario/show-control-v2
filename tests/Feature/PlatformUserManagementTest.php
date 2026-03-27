<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_platform_users_index(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
        ]);
        $superAdmin->assignRole('super_admin');

        $regularAdmin = User::factory()->create([
            'name' => 'Admin Cuenta',
            'email' => 'admin@example.com',
        ]);
        $regularAdmin->assignRole('admin');

        $response = $this->actingAs($superAdmin)->get(route('platform.users.index'));

        $response->assertOk();
        $response->assertSee('Super Admin');
        $response->assertSee('Admin Cuenta');
        $response->assertSee('super_admin');
        $response->assertSee('admin');
    }

    public function test_admin_cannot_view_platform_users_index(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('platform.users.index'));

        $response->assertForbidden();
    }

    public function test_super_admin_can_update_user_role_and_status(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $admin = User::factory()->create([
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        $response = $this->actingAs($superAdmin)->put(route('platform.users.update', $admin), [
            'role' => 'super_admin',
            'is_active' => '0',
        ]);

        $response->assertRedirect(route('platform.users.index'));
        $this->assertTrue($admin->fresh()->hasRole('super_admin'));
        $this->assertFalse($admin->fresh()->is_active);
    }

    public function test_super_admin_cannot_deactivate_own_account(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = User::factory()->create([
            'is_active' => true,
        ]);
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->put(route('platform.users.update', $superAdmin), [
            'role' => 'super_admin',
            'is_active' => '0',
        ]);

        $response->assertRedirect(route('platform.users.index'));
        $this->assertTrue($superAdmin->fresh()->is_active);
    }

    public function test_last_super_admin_cannot_be_downgraded(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        User::role('super_admin')->get()->each(function (User $seededSuperAdmin): void {
            $seededSuperAdmin->syncRoles(['admin']);
        });

        $superAdmin = User::factory()->create([
            'is_active' => true,
        ]);
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->put(route('platform.users.update', $superAdmin), [
            'role' => 'admin',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('platform.users.index'));
        $this->assertTrue($superAdmin->fresh()->hasRole('super_admin'));
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => bcrypt('password'),
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
