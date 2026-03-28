<?php

namespace App\Http\Controllers;

use App\Http\Requests\RestorePlatformBackupRequest;
use App\Support\PlatformBackupService;
use App\Support\PlatformHealthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PlatformToolController extends Controller
{
    public function index(PlatformHealthService $healthService, PlatformBackupService $backupService): View
    {
        return view('platform.tools.index', [
            'checks' => $healthService->checks(),
            'metrics' => $healthService->metrics(),
            'backups' => collect($backupService->listBackups())->map(function (array $backup): array {
                $backup['size_human'] = $this->formatFileSize($backup['size']);

                return $backup;
            })->all(),
        ]);
    }

    public function backup(PlatformBackupService $backupService): StreamedResponse
    {
        $backup = $backupService->createBackup();

        return response()->streamDownload(
            fn () => print(File::get($backup['path'])),
            $backup['filename'],
            ['Content-Type' => 'application/json']
        );
    }

    public function download(string $filename, PlatformBackupService $backupService): StreamedResponse
    {
        $path = $backupService->backupPath($filename);
        abort_unless($path, 404);

        return response()->streamDownload(
            fn () => print(File::get($path)),
            basename($path),
            ['Content-Type' => 'application/json']
        );
    }

    public function restore(RestorePlatformBackupRequest $request, PlatformBackupService $backupService): RedirectResponse
    {
        try {
            $backupService->restoreBackup($request->file('backup_file'));
        } catch (\Throwable) {
            return redirect()
                ->route('platform.tools.index')
                ->with('platform_error', __('ui.platform_restore_error'));
        }

        return redirect()
            ->route('platform.tools.index')
            ->with('platform_status', __('ui.platform_restore_success'));
    }

    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = (float) $bytes;
        $index = 0;

        while ($size >= 1024 && $index < count($units) - 1) {
            $size /= 1024;
            $index++;
        }

        return number_format($size, $index === 0 ? 0 : 1, '.', '').' '.$units[$index];
    }
}
