<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BootstrapAdminSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        putenv('ADMIN_NAME');
        putenv('ADMIN_EMAIL');
        putenv('ADMIN_PASSWORD');
        unset($_ENV['ADMIN_NAME'], $_ENV['ADMIN_EMAIL'], $_ENV['ADMIN_PASSWORD']);
        unset($_SERVER['ADMIN_NAME'], $_SERVER['ADMIN_EMAIL'], $_SERVER['ADMIN_PASSWORD']);

        parent::tearDown();
    }

    public function test_seeder_uses_configured_bootstrap_super_admin(): void
    {
        putenv('ADMIN_NAME=Platform Owner');
        putenv('ADMIN_EMAIL=owner@example.com');
        putenv('ADMIN_PASSWORD=secure-password-2026');
        $_ENV['ADMIN_NAME'] = 'Platform Owner';
        $_ENV['ADMIN_EMAIL'] = 'owner@example.com';
        $_ENV['ADMIN_PASSWORD'] = 'secure-password-2026';
        $_SERVER['ADMIN_NAME'] = 'Platform Owner';
        $_SERVER['ADMIN_EMAIL'] = 'owner@example.com';
        $_SERVER['ADMIN_PASSWORD'] = 'secure-password-2026';

        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::query()->where('email', 'owner@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('Platform Owner', $user->name);
        $this->assertTrue($user->hasRole('super_admin'));
        $this->assertDatabaseMissing('users', [
            'email' => 'admin@showcontrol.test',
        ]);
    }
}
