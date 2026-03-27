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

        try {
            DB::connection()->getPdo();
        } catch (\Throwable) {
            $databaseOk = false;
        }

        return [
            [
                'label' => 'Aplicacion instalada',
                'ok' => $this->installationService->isInstalled(),
                'detail' => $this->installationService->isInstalled() ? 'Marcador de instalacion presente.' : 'No se detecta instalacion completa.',
            ],
            [
                'label' => 'Conexion a base de datos',
                'ok' => $databaseOk,
                'detail' => $databaseOk ? 'Conexion operativa.' : 'No se ha podido abrir la conexion.',
            ],
            [
                'label' => 'Storage escribible',
                'ok' => is_writable(storage_path()),
                'detail' => storage_path(),
            ],
            [
                'label' => 'Bootstrap cache escribible',
                'ok' => is_writable(base_path('bootstrap/cache')),
                'detail' => base_path('bootstrap/cache'),
            ],
            [
                'label' => 'Carpeta de backups disponible',
                'ok' => $this->ensureBackupDirectory(),
                'detail' => storage_path('app/backups'),
            ],
            [
                'label' => 'Super admin activo',
                'ok' => User::role('super_admin')->where('is_active', true)->exists(),
                'detail' => User::role('super_admin')->where('is_active', true)->count().' cuentas activas.',
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
