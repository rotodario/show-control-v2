<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\Tour;
use App\Support\ActivityLogger;
use App\Support\IcsCalendarService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GoogleCalendarImportController extends Controller
{
    public function index(Request $request, IcsCalendarService $icsCalendarService): View|RedirectResponse
    {
        $this->ensureImportAccess($request);

        $previewEvents = collect();
        $icsUrl = $request->string('ics_url')->toString();
        $dateFrom = $request->date('date_from')?->format('Y-m-d') ?? now()->toDateString();
        $dateTo = $request->date('date_to')?->format('Y-m-d') ?? now()->addMonths(6)->toDateString();

        if ($icsUrl !== '') {
            try {
                $previewEvents = $this->buildPreviewEvents(
                    $request,
                    $icsCalendarService,
                    $icsUrl,
                    $dateFrom,
                    $dateTo
                );
            } catch (\Throwable) {
                return back()->withErrors([
                    'ics_url' => __('ui.ics_url_read_error'),
                ])->withInput();
            }
        }

        return view('tours.google-calendar', [
            'previewEvents' => $previewEvents,
            'icsUrl' => $icsUrl,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function import(Request $request, IcsCalendarService $icsCalendarService): RedirectResponse
    {
        $this->ensureImportAccess($request);

        $validated = $request->validate([
            'ics_url' => ['required', 'url', 'max:2000'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'selected_event_ids' => ['required', 'array', 'min:1'],
            'selected_event_ids.*' => ['string'],
        ]);

        try {
            $events = collect($icsCalendarService->fetchAndParse(
                $validated['ics_url'],
                Carbon::parse($validated['date_from']),
                Carbon::parse($validated['date_to']),
            ));
        } catch (\Throwable) {
            return back()->withErrors([
                'ics_url' => __('ui.ics_url_read_error'),
            ])->withInput();
        }

        $calendarFingerprint = sha1($validated['ics_url']);
        $selectedIds = collect($validated['selected_event_ids']);
        $toImport = $events->filter(fn (array $event) => $selectedIds->contains($event['event_id']))->values();

        $imported = 0;
        $skipped = 0;

        foreach ($toImport as $event) {
            $parsed = $icsCalendarService->parseEvent($event);

            $existingShow = Show::ownedBy($request->user()->id)
                ->where('external_source', 'ics')
                ->where('external_calendar_id', $calendarFingerprint)
                ->where('external_event_id', $parsed['event_id'])
                ->first();

            if ($existingShow) {
                $skipped++;
                continue;
            }

            $tour = $this->resolveTour($request, $parsed['tour_name']);

            $show = Show::create([
                'owner_id' => $request->user()->id,
                'external_source' => 'ics',
                'external_calendar_id' => $calendarFingerprint,
                'external_event_id' => $parsed['event_id'],
                'tour_id' => $tour->id,
                'date' => $parsed['date'],
                'city' => $parsed['city'],
                'venue' => $parsed['venue'],
                'name' => $parsed['show_name'],
                'status' => 'tentative',
                'show_at' => $parsed['show_at'],
                'general_notes' => $parsed['general_notes'],
            ]);

            ActivityLogger::log(
                action: 'show.created',
                detail: 'Bolo importado desde ICS: '.$show->name,
                actor: $request->user(),
                subject: $show,
                tourId: $tour->id,
                showId: $show->id,
                properties: [
                    'source' => 'ics',
                    'ics_url' => $validated['ics_url'],
                    'event_id' => $parsed['event_id'],
                ],
            );

            $imported++;
        }

        return redirect()
            ->route('tours.google-calendar.index', [
                'ics_url' => $validated['ics_url'],
                'date_from' => $validated['date_from'],
                'date_to' => $validated['date_to'],
            ])
            ->with('status', __('ui.ics_import_completed', ['imported' => $imported, 'skipped' => $skipped]));
    }

    private function buildPreviewEvents(
        Request $request,
        IcsCalendarService $icsCalendarService,
        string $icsUrl,
        string $dateFrom,
        string $dateTo
    ): Collection {
        $events = collect($icsCalendarService->fetchAndParse(
            $icsUrl,
            Carbon::parse($dateFrom),
            Carbon::parse($dateTo),
        ));

        $calendarFingerprint = sha1($icsUrl);
        $eventIds = $events->pluck('event_id');
        $existingIds = Show::ownedBy($request->user()->id)
            ->where('external_source', 'ics')
            ->where('external_calendar_id', $calendarFingerprint)
            ->whereIn('external_event_id', $eventIds)
            ->pluck('external_event_id')
            ->all();

        return $events->map(function (array $event) use ($icsCalendarService, $existingIds) {
            $parsed = $icsCalendarService->parseEvent($event);
            $parsed['already_imported'] = in_array($parsed['event_id'], $existingIds, true);

            return $parsed;
        });
    }

    private function resolveTour(Request $request, string $tourName): Tour
    {
        $tour = Tour::ownedBy($request->user()->id)
            ->whereRaw('LOWER(name) = ?', [Str::lower($tourName)])
            ->first();

        if ($tour) {
            return $tour;
        }

        $tour = Tour::create([
            'owner_id' => $request->user()->id,
            'name' => $tourName,
            'color' => $this->defaultColorForTour($tourName),
            'notes' => __('ui.tour_created_from_ics_notes'),
        ]);

        ActivityLogger::log(
            action: 'tour.created',
            detail: 'Gira creada desde ICS: '.$tour->name,
            actor: $request->user(),
            subject: $tour,
            tourId: $tour->id,
            properties: [
                'source' => 'ics',
            ],
        );

        return $tour;
    }

    private function defaultColorForTour(string $tourName): string
    {
        $palette = ['#2563EB', '#0EA5E9', '#14B8A6', '#F97316', '#E11D48', '#8B5CF6'];

        return $palette[abs(crc32(Str::lower($tourName))) % count($palette)];
    }

    private function ensureImportAccess(Request $request): void
    {
        abort_unless(
            $request->user()?->can('manage tours') && $request->user()?->can('manage shows'),
            403
        );
    }
}
