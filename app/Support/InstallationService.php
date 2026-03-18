<?php

namespace App\Support;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InstallationService
{
    public function isInstalled(): bool
    {
        if (app()->runningUnitTests()) {
            return true;
        }

        return file_exists($this->markerPath());
    }

    public function requirements(): array
    {
        return [
            'php_8_1' => PHP_VERSION_ID >= 80100,
            'pdo' => extension_loaded('pdo'),
            'pdo_mysql' => extension_loaded('pdo_mysql'),
            'mbstring' => extension_loaded('mbstring'),
            'openssl' => extension_loaded('openssl'),
            'json' => extension_loaded('json'),
            'storage_writable' => is_writable(storage_path()),
            'bootstrap_cache_writable' => is_writable(base_path('bootstrap/cache')),
            'env_writable' => file_exists(base_path('.env'))
                ? is_writable(base_path('.env'))
                : is_writable(base_path()),
        ];
    }

    public function canInstall(): bool
    {
        return ! in_array(false, $this->requirements(), true);
    }

    public function install(array $data): void
    {
        $appKey = config('app.key') ?: 'base64:'.base64_encode(Encrypter::generateKey(config('app.cipher')));

        $this->writeEnvironmentFile([
            'ASSET_URL' => rtrim($data['app_url'], '/'),
            'APP_NAME' => '"'.$data['app_name'].'"',
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'APP_URL' => $data['app_url'],
            'APP_PUBLIC_PATH' => $this->resolvePublicPath(),
            'APP_KEY' => $appKey,
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $data['db_host'],
            'DB_PORT' => $data['db_port'],
            'DB_DATABASE' => $data['db_database'],
            'DB_USERNAME' => $data['db_username'],
            'DB_PASSWORD' => $data['db_password'],
        ]);

        $this->reconfigureDatabase($data);

        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('storage:link');

        app(RolesAndPermissionsSeeder::class)->run();

        $user = User::query()->firstOrCreate(
            ['email' => $data['admin_email']],
            [
                'name' => $data['admin_name'],
                'password' => Hash::make($data['admin_password']),
                'email_verified_at' => now(),
            ]
        );

        $user->forceFill([
            'name' => $data['admin_name'],
            'password' => Hash::make($data['admin_password']),
            'email_verified_at' => now(),
        ])->save();

        $user->syncRoles(['admin']);

        $this->markInstalled();
    }

    private function reconfigureDatabase(array $data): void
    {
        $updates = [
            'database.default' => 'mysql',
            'database.connections.mysql.host' => $data['db_host'],
            'database.connections.mysql.port' => $data['db_port'],
            'database.connections.mysql.database' => $data['db_database'],
            'database.connections.mysql.username' => $data['db_username'],
            'database.connections.mysql.password' => $data['db_password'],
        ];

        foreach ($updates as $key => $value) {
            Config::set($key, $value);
        }

        DB::purge('mysql');
        DB::reconnect('mysql');
    }

    private function resolvePublicPath(): string
    {
        $configured = env('APP_PUBLIC_PATH');

        if (is_string($configured) && $configured !== '') {
            return $configured;
        }

        $currentPublicPath = public_path();

        if (File::isDirectory($currentPublicPath)) {
            return $currentPublicPath;
        }

        return base_path('public');
    }

    private function writeEnvironmentFile(array $values): void
    {
        $envPath = base_path('.env');
        $existing = file_exists($envPath)
            ? (file($envPath, FILE_IGNORE_NEW_LINES) ?: [])
            : (file(base_path('.env.example'), FILE_IGNORE_NEW_LINES) ?: []);

        $keys = array_keys($values);
        $remaining = $values;
        $output = [];

        foreach ($existing as $line) {
            $trimmed = ltrim($line);

            if ($trimmed === '' || str_starts_with($trimmed, '#') || ! str_contains($line, '=')) {
                $output[] = $line;
                continue;
            }

            $key = Str::before($line, '=');

            if (in_array($key, $keys, true)) {
                $output[] = $key.'='.Arr::pull($remaining, $key);
                continue;
            }

            $output[] = $line;
        }

        foreach ($remaining as $key => $value) {
            $output[] = $key.'='.$value;
        }

        file_put_contents($envPath, implode(PHP_EOL, $output).PHP_EOL);
    }

    private function markInstalled(): void
    {
        if (! is_dir(dirname($this->markerPath()))) {
            mkdir(dirname($this->markerPath()), 0755, true);
        }

        file_put_contents($this->markerPath(), now()->toIso8601String());
    }

    private function markerPath(): string
    {
        return storage_path('app/installed');
    }
}
