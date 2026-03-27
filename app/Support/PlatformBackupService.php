<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PlatformBackupService
{
    public const TABLES = [
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
    ];

    public function createBackup(): array
    {
        $this->ensureBackupDirectory();

        $payload = [
            'format' => 'show-control-backup-v1',
            'created_at' => now()->toIso8601String(),
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'tables' => [],
        ];

        foreach (self::TABLES as $table) {
            if (! $this->tableExists($table)) {
                continue;
            }

            $payload['tables'][$table] = DB::table($table)->get()->map(fn ($row) => (array) $row)->all();
        }

        $filename = 'show-control-backup-'.now()->format('Ymd-His').'.json';
        $relativePath = 'app/backups/'.$filename;
        $fullPath = storage_path($relativePath);

        File::put($fullPath, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return [
            'filename' => $filename,
            'path' => $fullPath,
            'relative_path' => $relativePath,
        ];
    }

    public function listBackups(): array
    {
        $this->ensureBackupDirectory();

        return collect(File::files(storage_path('app/backups')))
            ->filter(fn (\SplFileInfo $file) => $file->getExtension() === 'json')
            ->sortByDesc(fn (\SplFileInfo $file) => $file->getMTime())
            ->map(function (\SplFileInfo $file): array {
                return [
                    'filename' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'modified_at' => Carbon::createFromTimestamp($file->getMTime()),
                ];
            })
            ->values()
            ->all();
    }

    public function backupPath(string $filename): ?string
    {
        $safeFilename = basename($filename);
        $path = storage_path('app/backups/'.$safeFilename);

        return is_file($path) ? $path : null;
    }

    public function restoreBackup(UploadedFile $file): void
    {
        $payload = json_decode($file->get(), true);

        if (! is_array($payload) || ($payload['format'] ?? null) !== 'show-control-backup-v1') {
            throw new \RuntimeException('El archivo de backup no tiene un formato valido.');
        }

        $tables = $payload['tables'] ?? null;

        if (! is_array($tables)) {
            throw new \RuntimeException('El backup no incluye tablas validas.');
        }

        DB::transaction(function () use ($tables): void {
            DB::getSchemaBuilder()->disableForeignKeyConstraints();

            try {
                foreach (array_reverse(self::TABLES) as $table) {
                    if ($this->tableExists($table)) {
                        DB::table($table)->truncate();
                    }
                }

                foreach (self::TABLES as $table) {
                    if (! $this->tableExists($table)) {
                        continue;
                    }

                    $rows = $tables[$table] ?? [];

                    if (! is_array($rows) || $rows === []) {
                        continue;
                    }

                    foreach (array_chunk($rows, 100) as $chunk) {
                        DB::table($table)->insert($chunk);
                    }
                }
            } finally {
                DB::getSchemaBuilder()->enableForeignKeyConstraints();
            }
        });
    }

    private function ensureBackupDirectory(): void
    {
        $path = storage_path('app/backups');

        if (! is_dir($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    private function tableExists(string $table): bool
    {
        return DB::getSchemaBuilder()->hasTable($table);
    }
}
