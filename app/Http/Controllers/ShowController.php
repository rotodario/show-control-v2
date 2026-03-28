<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShowRequest;
use App\Http\Requests\UpdateShowRequest;
use App\Models\Show;
use App\Models\Tour;
use App\Support\ActivityLogger;
use App\Support\OpenStreetMapRouteService;
use App\Support\ShowAlertService;
use App\Support\ShowMailNotificationService;
use App\Support\ShowMessageReadService;
use App\Support\ShowRoadmapPdfService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
            'statusOptions' => Show::translatedStatusOptions(),
            'showAlerts' => $showAlertService->alertsForCollection($shows->getCollection(), $request->user()),
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
        $showAlerts = $showAlertService->alertsForCollection($monthShows, $request->user());

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
            'statusOptions' => Show::translatedStatusOptions(),
            'tours' => Tour::ownedBy($userId)->orderBy('name')->get(),
            'weekdays' => [
                __('ui.weekday_mon'),
                __('ui.weekday_tue'),
                __('ui.weekday_wed'),
                __('ui.weekday_thu'),
                __('ui.weekday_fri'),
                __('ui.weekday_sat'),
                __('ui.weekday_sun'),
            ],
        ]);
    }

    public function create(): View
    {
        $preferences = auth()->user()?->preferences()->firstOrNew();

        return view('shows.create', [
            'show' => new Show([
                'date' => now(),
                'status' => $preferences->default_show_status ?: array_key_first(Show::STATUS_OPTIONS),
                'travel_mode' => $preferences->default_travel_mode ?: 'van',
                'city' => $preferences->default_city,
                'travel_origin' => $preferences->default_travel_origin,
            ]),
            'tours' => Tour::ownedBy(auth()->id())->orderBy('name')->get(),
            'statusOptions' => Show::STATUS_OPTIONS,
            'travelModeOptions' => Show::translatedTravelModeOptions(),
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
            ->with('status', __('ui.show_created'));
    }

    public function show(
        Show $show,
        ShowAlertService $showAlertService,
        ShowMessageReadService $showMessageReadService,
        OpenStreetMapRouteService $openStreetMapRouteService
    ): View
    {
        $this->ensureOwnedShow($show);

        $show->load(['tour', 'documents.uploader', 'activityLogs.actor', 'sectionMessages.user', 'sectionMessages.sharedAccess']);
        $unreadMessageIds = $showMessageReadService->unreadMessageIdsForUser($show, request()->user());
        $showMessageReadService->markReadForUser($show, request()->user());

        return view('shows.show', [
            'show' => $show,
            'statusOptions' => Show::translatedStatusOptions(),
            'alerts' => $showAlertService->alertsForShow($show, user: request()->user()),
            'sectionMessages' => $show->sectionMessages->groupBy('section'),
            'unreadMessageIds' => $unreadMessageIds,
            'travelRoute' => $openStreetMapRouteService->routeForShow($show),
            'travelModeOptions' => Show::translatedTravelModeOptions(),
        ]);
    }

    public function pdf(
        Show $show,
        Request $request,
        ShowAlertService $showAlertService,
        OpenStreetMapRouteService $openStreetMapRouteService,
        ShowRoadmapPdfService $showRoadmapPdfService
    ): Response
    {
        $this->ensureOwnedShow($show);

        $travelRoute = $openStreetMapRouteService->routeForShow($show);
        $alerts = $showAlertService->alertsForShow($show, user: $request->user());

        return $showRoadmapPdfService->streamResponse(
            $show,
            $request->user(),
            $alerts,
            $travelRoute,
            $request->string('disposition')->toString() === 'download'
        );
    }

    public function sendRoadmapMail(
        Request $request,
        Show $show,
        OpenStreetMapRouteService $openStreetMapRouteService,
        ShowMailNotificationService $showMailNotificationService,
        ShowAlertService $showAlertService
    ): RedirectResponse
    {
        $this->ensureOwnedShow($show);

        $travelRoute = $openStreetMapRouteService->routeForShow($show);
        $alerts = $showAlertService->alertsForShow($show, user: $request->user());

        $sent = $showMailNotificationService->sendRoadmapForShow(
            $show,
            $request->user(),
            $travelRoute,
            $alerts,
        );

        return redirect()
            ->route('shows.show', $show)
            ->with('status', $sent
                ? __('ui.roadmap_mail_sent')
                : __('ui.roadmap_mail_not_sent'));
    }

    public function sendAlertMail(
        Request $request,
        Show $show,
        ShowMailNotificationService $showMailNotificationService,
        ShowAlertService $showAlertService
    ): RedirectResponse
    {
        $this->ensureOwnedShow($show);

        $alerts = $showAlertService->alertsForShow($show, user: $request->user());
        $sent = $showMailNotificationService->sendAlertForShow(
            $show,
            $request->user(),
            $alerts,
        );

        return redirect()
            ->route('shows.show', $show)
            ->with('status', $sent
                ? __('ui.alert_mail_sent')
                : __('ui.alert_mail_not_sent'));
    }

    public function edit(Show $show): View
    {
        $this->ensureOwnedShow($show);

        return view('shows.edit', [
            'show' => $show,
            'tours' => Tour::ownedBy(auth()->id())->orderBy('name')->get(),
            'statusOptions' => Show::translatedStatusOptions(),
            'travelModeOptions' => Show::translatedTravelModeOptions(),
            'travelPreview' => session('travel_preview'),
        ]);
    }

    public function previewRoute(UpdateShowRequest $request, Show $show, OpenStreetMapRouteService $openStreetMapRouteService): RedirectResponse
    {
        $this->ensureOwnedShow($show);

        $previewShow = $show->replicate();
        $previewShow->forceFill([
            ...$show->getAttributes(),
            ...$this->payload($request),
        ]);

        return redirect()
            ->route('shows.edit', $show)
            ->withInput()
            ->with('travel_preview', $openStreetMapRouteService->routeForShow($previewShow))
            ->with('status', __('ui.route_preview_ready'));
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
            ->with('status', __('ui.show_updated'));
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
            ->with('status', __('ui.show_deleted'));
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
}
