<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PlatformToolsTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_platform_tools(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get(route('platform.tools.index'));

        $response->assertOk();
        $response->assertSee('Herramientas');
        $response->assertSee('Salud del sistema');
    }

    public function test_super_admin_can_create_backup(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->post(route('platform.tools.backup'));

        $response->assertOk();
        $response->assertHeader('content-disposition');

        $backups = File::files(storage_path('app/backups'));
        $this->assertNotEmpty($backups);
    }

    public function test_super_admin_can_restore_backup_from_json(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $seededSuperAdmins = User::role('super_admin')->get();
        $seededSuperAdmins->each(fn (User $user) => $user->syncRoles(['admin']));

        $superAdmin = User::factory()->create([
            'name' => 'Restore Admin',
            'email' => 'restore@example.com',
            'is_active' => true,
        ]);
        $superAdmin->assignRole('super_admin');

        AppSetting::putValue('test.key', 'before');

        $payload = [
            'format' => 'show-control-backup-v1',
            'created_at' => now()->toIso8601String(),
            'app_name' => 'Show Control',
            'app_url' => 'http://localhost',
            'tables' => [],
        ];

        foreach ([
            'users',
            'permissions',
            'roles',
            'model_has_permissions',
            'model_has_roles',
            'role_has_permissions',
            'app_settings',
            'user_alert_settings',
            'user_pdf_settings',
            'tours',
            'tour_contacts',
            'tour_documents',
            'shows',
            'show_documents',
            'shared_accesses',
            'activity_logs',
            'google_calendar_connections',
            'show_section_messages',
            'show_message_reads',
            'password_reset_tokens',
            'personal_access_tokens',
            'failed_jobs',
        ] as $table) {
            if (\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable($table)) {
                $payload['tables'][$table] = \Illuminate\Support\Facades\DB::table($table)->get()->map(fn ($row) => (array) $row)->all();
            }
        }

        AppSetting::putValue('test.key', 'after');

        $response = $this->actingAs($superAdmin)->post(route('platform.tools.restore'), [
            'backup_file' => UploadedFile::fake()->createWithContent('backup.json', json_encode($payload, JSON_PRETTY_PRINT)),
            'confirmation' => 'RESTAURAR',
        ]);

        $response->assertRedirect(route('platform.tools.index'));
        $this->assertSame('before', AppSetting::getValue('test.key'));
    }
}
