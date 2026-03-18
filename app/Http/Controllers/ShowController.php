<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShowRequest;
use App\Http\Requests\UpdateShowRequest;
use App\Models\Show;
use App\Models\Tour;
use App\Support\ActivityLogger;
use App\Support\ShowAlertService;
use App\Support\ShowMessageReadService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class ShowController extends Controller
{
    public function index(Request $request, ShowAlertService $showAlertService, ShowMessageReadService $showMessageReadService): View
    {
        $userId = auth()->id();
        $tourId = $request->integer('tour_id');
        $shows = Show::ownedBy($userId)
            ->with(['tour', 'sectionMessages'])
            ->when($tourId, fn ($query) => $query->where('tour_id', $tourId))
            ->orderBy('date')
            ->orderBy('city')
            ->paginate(15)
            ->withQueryString();

        return view('shows.index', [
            'shows' => $shows,
            'tours' => Tour::ownedBy($userId)->orderBy('name')->get(),
            'selectedTourId' => $tourId,
            'statusOptions' => Show::STATUS_OPTIONS,
            'showAlerts' => $showAlertService->alertsForCollection($shows->getCollection()),
            'unreadMessageCounts' => $showMessageReadService->unreadCountsForUser($shows->getCollection(), $request->user()),
        ]);
    }

    public function calendar(Request $request, ShowAlertService $showAlertService): View
    {
        $userId = auth()->id();
        $tourId = $request->integer('tour_id');
        $month = $request->string('month')->toString();
        $selectedDateInput = $request->string('date')->toString();

        $currentMonth = $this->resolveMonth($month);
        $selectedDate = $this->resolveSelectedDate($selectedDateInput, $currentMonth);
        $calendarStart = $currentMonth->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $currentMonth->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $monthShows = Show::ownedBy($userId)
            ->with('tour')
            ->when($tourId, fn ($query) => $query->where('tour_id', $tourId))
            ->whereBetween('date', [$calendarStart->toDateString(), $calendarEnd->toDateString()])
            ->orderBy('date')
            ->orderBy('show_at')
            ->orderBy('city')
            ->get();

        $showsByDate = $monthShows->groupBy(fn (Show $show) => $show->date->toDateString());
        $showAlerts = $showAlertService->alertsForCollection($monthShows);

        $calendarDays = collect();
        $cursor = $calendarStart->copy();

        while ($cursor->lte($calendarEnd)) {
            $dateKey = $cursor->toDateString();
            $dayShows = $showsByDate->get($dateKey, collect());

            $calendarDays->push([
                'date' => $cursor->copy(),
                'isCurrentMonth' => $cursor->month === $currentMonth->month,
                'isToday' => $cursor->isToday(),
                'isSelected' => $cursor->isSameDay($selectedDate),
                'shows' => $dayShows,
            ]);

            $cursor->addDay();
        }

        $agendaShows = $showsByDate->get($selectedDate->toDateString(), collect())->values();

        return view('shows.calendar', [
            'calendarDays' => $calendarDays,
            'agendaShows' => $agendaShows,
            'currentMonth' => $currentMonth,
            'previousMonth' => $currentMonth->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $currentMonth->copy()->addMonth()->format('Y-m'),
            'selectedDate' => $selectedDate,
            'selectedTourId' => $tourId,
            'showAlerts' => $showAlerts,
            'statusOptions' => Show::STATUS_OPTIONS,
            'tours' => Tour::ownedBy($userId)->orderBy('name')->get(),
            'weekdays' => ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'],
        ]);
    }

    public function create(): View
    {
        return view('shows.create', [
            'show' => new Show([
                'date' => now(),
                'status' => array_key_first(Show::STATUS_OPTIONS),
            ]),
            'tours' => Tour::ownedBy(auth()->id())->orderBy('name')->get(),
            'statusOptions' => Show::STATUS_OPTIONS,
        ]);
    }

    public function store(StoreShowRequest $request): RedirectResponse
    {
        $show = Show::create([
            ...$this->payload($request),
            'owner_id' => $request->user()->id,
        ]);

        ActivityLogger::log(
            action: 'show.created',
            detail: "Bolo creado: {$show->name}",
            actor: $request->user(),
            subject: $show,
            tourId: $show->tour_id,
            showId: $show->id,
        );

        return redirect()
            ->route('shows.show', $show)
            ->with('status', 'Bolo creado correctamente.');
    }

    public function show(Show $show, ShowAlertService $showAlertService, ShowMessageReadService $showMessageReadService): View
    {
        $this->ensureOwnedShow($show);

        $show->load(['tour', 'documents.uploader', 'activityLogs.actor', 'sectionMessages.user', 'sectionMessages.sharedAccess']);
        $showMessageReadService->markReadForUser($show, request()->user());

        return view('shows.show', [
            'show' => $show,
            'statusOptions' => Show::STATUS_OPTIONS,
            'alerts' => $showAlertService->alertsForShow($show),
            'sectionMessages' => $show->sectionMessages->groupBy('section'),
        ]);
    }

    public function pdf(Show $show, Request $request, ShowAlertService $showAlertService): Response
    {
        $this->ensureOwnedShow($show);

        $show->load('tour.contacts');
        $this->ensurePdfRuntimePaths();

        $pdf = Pdf::loadView('shows.pdf.roadmap', [
            'show' => $show,
            'statusOptions' => Show::STATUS_OPTIONS,
            'alerts' => $showAlertService->alertsForShow($show),
        ])->setPaper('a4');

        $filename = sprintf(
            'hoja-ruta-%s-%s.pdf',
            str($show->date->format('Y-m-d'))->lower(),
            str($show->name)->slug()
        );

        if ($request->string('disposition')->toString() === 'download') {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    public function edit(Show $show): View
    {
        $this->ensureOwnedShow($show);

        return view('shows.edit', [
            'show' => $show,
            'tours' => Tour::ownedBy(auth()->id())->orderBy('name')->get(),
            'statusOptions' => Show::STATUS_OPTIONS,
        ]);
    }

    public function update(UpdateShowRequest $request, Show $show): RedirectResponse
    {
        $this->ensureOwnedShow($show);

        $show->update($this->payload($request));

        ActivityLogger::log(
            action: 'show.updated',
            detail: "Bolo actualizado: {$show->name}",
            actor: $request->user(),
            subject: $show,
            tourId: $show->tour_id,
            showId: $show->id,
        );

        return redirect()
            ->route('shows.show', $show)
            ->with('status', 'Bolo actualizado.');
    }

    public function destroy(Show $show): RedirectResponse
    {
        $this->ensureOwnedShow($show);

        $name = $show->name;
        $tourId = $show->tour_id;
        $showId = $show->id;

        ActivityLogger::log(
            action: 'show.deleted',
            detail: "Bolo eliminado: {$name}",
            actor: request()->user(),
            tourId: $tourId,
            showId: $showId,
        );

        $show->delete();

        return redirect()
            ->route('shows.index')
            ->with('status', 'Bolo eliminado.');
    }

    private function payload(StoreShowRequest|UpdateShowRequest $request): array
    {
        $validated = $request->validated();

        foreach ([
            'lighting_validated',
            'sound_validated',
            'space_validated',
            'general_validated',
        ] as $checkboxField) {
            $validated[$checkboxField] = $request->boolean($checkboxField);
        }

        return $validated;
    }

    private function resolveMonth(string $month): Carbon
    {
        if ($month !== '') {
            try {
                return Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            } catch (\Throwable) {
            }
        }

        return now()->startOfMonth();
    }

    private function resolveSelectedDate(string $date, Carbon $fallbackMonth): Carbon
    {
        if ($date !== '') {
            try {
                return Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
            } catch (\Throwable) {
            }
        }

        return $fallbackMonth->copy()->isCurrentMonth()
            ? now()->startOfDay()
            : $fallbackMonth->copy()->startOfMonth();
    }

    private function ensureOwnedShow(Show $show): void
    {
        abort_unless($show->owner_id === auth()->id(), 404);
    }

    private function ensurePdfRuntimePaths(): void
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
