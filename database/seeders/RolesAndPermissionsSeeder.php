<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'view dashboard',
            'manage tours',
            'manage shows',
            'manage access',
            'view activity',
            'export show pdf',
            'view calendar',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $roles = [
            'admin' => $permissions,
            'project_manager' => $permissions,
            'lighting' => ['view dashboard', 'view calendar'],
            'sound' => ['view dashboard', 'view calendar'],
            'stage_manager' => ['view dashboard', 'view calendar'],
        ];

        foreach ($roles as $name => $rolePermissions) {
            $role = Role::findOrCreate($name, 'web');
            $role->syncPermissions($rolePermissions);
        }

        $admin = User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@showcontrol.test')],
            [
                'name' => env('ADMIN_NAME', 'Admin'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'email_verified_at' => now(),
            ]
        );

        $admin->syncRoles(['admin']);
    }
}
