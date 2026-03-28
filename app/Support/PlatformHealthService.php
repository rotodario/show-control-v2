<?php

namespace App\Support;

use App\Models\Show;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PlatformHealthService
{
    public function __construct(private readonly InstallationService $installationService)
    {
    }

    public function checks(): array
    {
        $databaseOk = true;
        $installed = $this->installationService->isInstalled();
        $activeSuperAdmins = User::role('super_admin')->where('is_active', true)->count();

        try {
            DB::connection()->getPdo();
        } catch (\Throwable) {
            $databaseOk = false;
        }

        return [
            [
                'label' => __('ui.health_app_installed'),
                'ok' => $installed,
                'detail' => $installed ? __('ui.health_app_installed_ok') : __('ui.health_app_installed_fail'),
            ],
            [
                'label' => __('ui.health_database'),
                'ok' => $databaseOk,
                'detail' => $databaseOk ? __('ui.health_database_ok') : __('ui.health_database_fail'),
            ],
            [
                'label' => __('ui.health_storage_writable'),
                'ok' => is_writable(storage_path()),
                'detail' => storage_path(),
            ],
            [
                'label' => __('ui.health_bootstrap_cache_writable'),
                'ok' => is_writable(base_path('bootstrap/cache')),
                'detail' => base_path('bootstrap/cache'),
            ],
            [
                'label' => __('ui.health_backup_directory'),
                'ok' => $this->ensureBackupDirectory(),
                'detail' => storage_path('app/backups'),
            ],
            [
                'label' => __('ui.health_active_super_admin'),
                'ok' => $activeSuperAdmins > 0,
                'detail' => trans_choice('ui.active_accounts_count', $activeSuperAdmins, ['count' => $activeSuperAdmins]),
            ],
        ];
    }

    public function metrics(): array
    {
        return [
            'users_total' => User::count(),
            'users_active' => User::where('is_active', true)->count(),
            'super_admins_active' => User::role('super_admin')->where('is_active', true)->count(),
            'tours_total' => Tour::count(),
            'shows_total' => Show::count(),
        ];
    }

    private function ensureBackupDirectory(): bool
    {
        $path = storage_path('app/backups');

        if (! is_dir($path)) {
            @mkdir($path, 0755, true);
        }

        return is_dir($path) && is_writable($path);
    }
}
