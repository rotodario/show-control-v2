<?php

namespace App\Support;

use App\Models\Show;
use App\Models\User;
use App\Models\UserPdfSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class ShowRoadmapPdfService
{
    public function filename(Show $show): string
    {
        return sprintf(
            'hoja-ruta-%s-%s.pdf',
            str($show->date->format('Y-m-d'))->lower(),
            str($show->name)->slug()
        );
    }

    public function make(Show $show, User $user, array $alerts, array $travelRoute)
    {
        $show->loadMissing('tour.contacts');
        $this->ensureRuntimePaths();

        return Pdf::loadView('shows.pdf.roadmap', [
            'show' => $show,
            'statusOptions' => Show::STATUS_OPTIONS,
            'alerts' => $alerts,
            'pdfSettings' => $user->pdfSettings ?? new UserPdfSetting(),
            'travelRoute' => $travelRoute,
            'travelModeOptions' => Show::TRAVEL_MODE_OPTIONS,
        ])->setPaper('a4');
    }

    public function streamResponse(Show $show, User $user, array $alerts, array $travelRoute, bool $download = false): Response
    {
        $pdf = $this->make($show, $user, $alerts, $travelRoute);
        $filename = $this->filename($show);

        return $download
            ? $pdf->download($filename)
            : $pdf->stream($filename);
    }

    public function output(Show $show, User $user, array $alerts, array $travelRoute): string
    {
        return $this->make($show, $user, $alerts, $travelRoute)->output();
    }

    private function ensureRuntimePaths(): void
    {
        $fontPath = storage_path('framework/dompdf/fonts');
        $tempPath = storage_path('framework/dompdf/tmp');

        foreach ([$fontPath, $tempPath] as $path) {
            if (! File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }
        }

        config([
            'dompdf.public_path' => public_path(),
            'dompdf.options.font_dir' => $fontPath,
            'dompdf.options.font_cache' => $fontPath,
            'dompdf.options.temp_dir' => $tempPath,
        ]);
    }
}
