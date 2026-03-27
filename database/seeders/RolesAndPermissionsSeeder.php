<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $accountPermissions = [
            'view dashboard',
            'manage tours',
            'manage shows',
            'manage access',
            'manage account settings',
            'view activity',
            'export show pdf',
            'view calendar',
        ];

        $platformPermissions = [
            'manage platform users',
            'manage platform settings',
            'view platform audit',
        ];

        $permissions = [
            ...$accountPermissions,
            ...$platformPermissions,
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $roles = [
            'admin' => $accountPermissions,
            'super_admin' => $permissions,
            'project_manager' => $accountPermissions,
            'lighting' => ['view dashboard', 'view calendar'],
            'sound' => ['view dashboard', 'view calendar'],
            'stage_manager' => ['view dashboard', 'view calendar'],
        ];

        foreach ($roles as $name => $rolePermissions) {
            $role = Role::findOrCreate($name, 'web');
            $role->syncPermissions($rolePermissions);
        }

        $configuredAdminEmail = env('ADMIN_EMAIL');
        $shouldCreateBootstrapAdmin = filled($configuredAdminEmail) || app()->environment(['local', 'testing']);

        if (! $shouldCreateBootstrapAdmin) {
            return;
        }

        $adminEmail = $configuredAdminEmail ?: 'admin@showcontrol.test';
        $adminPassword = env('ADMIN_PASSWORD') ?: (app()->environment('testing') ? 'password' : Str::password(32));

        $admin = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => env('ADMIN_NAME', 'Admin'),
                'password' => Hash::make($adminPassword),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $admin->syncRoles(['super_admin']);
    }
}
