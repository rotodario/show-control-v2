<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\ShowDocument;
use App\Models\Tour;
use App\Support\ActivityLogger;
use App\Support\SharedAccessService;
use App\Support\ShowAlertService;
use App\Support\ShowMessageReadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicSharedAccessController extends Controller
{
    private const TIME_FIELDS = [
        'load_in_at',
        'meal_at',
        'soundcheck_at',
        'doors_at',
        'show_at',
        'show_end_at',
        'load_out_at',
    ];

    public function index(
        string $token,
        Request $request,
        SharedAccessService $sharedAccessService,
        ShowAlertService $showAlertService,
        ShowMessageReadService $showMessageReadService
    ): View
    {
        $grant = $sharedAccessService->resolveActiveGrant($token);
        abort_if(! $grant, 404);

        $month = $request->string('month')->toString();
        $selectedDateInput = $request->string('date')->toString();
        $currentMonth = $this->resolveMonth($month);
        $selectedDate = $this->resolveSelectedDate($selectedDateInput, $currentMonth);
        $calendarStart = $currentMonth->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $currentMonth->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $calendarShows = $sharedAccessService->visibleShowsQuery($grant)
            ->whereBetween('date', [$calendarStart->toDateString(), $calendarEnd->toDateString()])
            ->get();

        $showsByDate = $calendarShows->groupBy(fn (Show $show) => $show->date->toDateString());
        $calendarDays = collect();
        $cursor = $calendarStart->copy();

        while ($cursor->lte($calendarEnd)) {
            $dateKey = $cursor->toDateString();
            $calendarDays->push([
                'date' => $cursor->copy(),
                'shows' => $showsByDate->get($dateKey, collect()),
                'isCurrentMonth' => $cursor->month === $currentMonth->month,
                'isToday' => $cursor->isToday(),
                'isSelected' => $cursor->isSameDay($selectedDate),
            ]);

            $cursor->addDay();
        }

        $shows = $sharedAccessService->visibleShowsQuery($grant)
            ->with('sectionMessages')
            ->when($selectedDateInput !== '', fn ($query) => $query->whereDate('date', $selectedDate->toDateString()))
            ->paginate(20)
            ->withQueryString();

        $permissions = $sharedAccessService->permissions($grant);
        $visibility = $sharedAccessService->sectionVisibility($grant);
        $visibleChatSections = collect(array_keys(\App\Models\ShowSectionMessage::SECTIONS))
            ->filter(fn (string $section) => $visibility[$section] ?? false)
            ->values()
            ->all();

        return view('public-access.index', [
            'grant' => $grant,
            'shows' => $shows,
            'showAlerts' => $showAlertService->alertsForCollection($shows->getCollection()),
            'unreadMessageCounts' => $showMessageReadService->unreadCountsForSharedAccess($shows->getCollection(), $grant, $visibleChatSections),
            'visibility' => $visibility,
            'permissions' => $permissions,
            'statusOptions' => Show::translatedStatusOptions(),
            'calendarDays' => $calendarDays,
            'currentMonth' => $currentMonth,
            'selectedDate' => $selectedDate,
            'selectedDateInput' => $selectedDateInput,
            'previousMonth' => $currentMonth->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $currentMonth->copy()->addMonth()->format('Y-m'),
            'weekdays' => [
                __('ui.weekday_mon'),
                __('ui.weekday_tue'),
                __('ui.weekday_wed'),
                __('ui.weekday_thu'),
                __('ui.weekday_fri'),
                __('ui.weekday_sat'),
                __('ui.weekday_sun'),
            ],
            'tours' => $grant->tour_id
                ? collect()
                : Tour::query()->ownedBy($grant->ownerId())->orderBy('name')->get(),
        ]);
    }

    public function show(
        string $token,
        Show $show,
        SharedAccessService $sharedAccessService,
        ShowAlertService $showAlertService,
        ShowMessageReadService $showMessageReadService
    ): View
    {
        $grant = $sharedAccessService->resolveActiveGrant($token);
        abort_if(! $grant, 404);
        abort_unless($sharedAccessService->canViewShow($grant, $show), 404);

        $show->load(['tour', 'documents.uploader', 'sectionMessages.user', 'sectionMessages.sharedAccess']);
        $chatSections = collect(array_keys(\App\Models\ShowSectionMessage::SECTIONS))
            ->filter(fn (string $section) => $sharedAccessService->canAccessSectionMessages($grant, $section))
            ->values()
            ->all();
        $unreadMessageIds = $showMessageReadService->unreadMessageIdsForSharedAccess($show, $grant, $chatSections);
        $showMessageReadService->markReadForSharedAccess($show, $grant);
        $permissions = $sharedAccessService->permissions($grant);

        return view('public-access.show', [
            'grant' => $grant,
            'show' => $show,
            'alerts' => $showAlertService->alertsForShow($show),
            'statusOptions' => Show::translatedStatusOptions(),
            'visibility' => $sharedAccessService->sectionVisibility($grant),
            'documents' => $sharedAccessService->visibleDocuments($grant, $show->documents),
            'permissions' => $permissions,
            'allowedDocumentTypes' => $sharedAccessService->allowedDocumentTypes($grant) ?? ShowDocument::TYPES,
            'sectionMessages' => $show->sectionMessages->groupBy('section'),
            'chatSections' => $chatSections,
            'unreadMessageIds' => $unreadMessageIds,
            'tours' => $grant->tour_id
                ? collect()
                : Tour::query()->ownedBy($grant->ownerId())->orderBy('name')->get(),
        ]);
    }

    public function storeShow(
        string $token,
        Request $request,
        SharedAccessService $sharedAccessService
    ) {
        $grant = $sharedAccessService->resolveActiveGrant($token);
        abort_if(! $grant, 404);
        abort_unless($sharedAccessService->canCreateShows($grant), 403);

        $validated = $this->validateShowPayload($request, $grant, $sharedAccessService, true);

        $show = Show::create([
            ...$validated,
            'owner_id' => $grant->ownerId(),
        ]);

        ActivityLogger::log(
            action: 'public_shared_access.show.created',
            detail: 'Bolo creado desde acceso compartido: '.$show->name,
            subject: $show,
            tourId: $show->tour_id,
            showId: $show->id,
            properties: [
                'shared_access_id' => $grant->id,
                'shared_access_role' => $grant->role,
                'shared_access_label' => $grant->label,
            ],
        );

        return redirect()
            ->route('public-access.shows.show', [$grant->token, $show])
            ->with('status', 'Bolo creado correctamente.');
    }

    public function updateShow(
        string $token,
        Show $show,
        Request $request,
        SharedAccessService $sharedAccessService
    ) {
        $grant = $sharedAccessService->resolveActiveGrant($token);
        abort_if(! $grant, 404);
        abort_unless($sharedAccessService->canViewShow($grant, $show), 404);
        abort_unless($sharedAccessService->canUpdateShow($grant), 403);

        $validated = $this->validateShowPayload($request, $grant, $sharedAccessService, false);

        $show->update($validated);

        ActivityLogger::log(
            action: 'public_shared_access.show.updated',
            detail: 'Bolo actualizado desde acceso compartido: '.$show->name,
            subject: $show,
            tourId: $show->tour_id,
            showId: $show->id,
            properties: [
                'shared_access_id' => $grant->id,
                'shared_access_role' => $grant->role,
                'shared_access_label' => $grant->label,
            ],
        );

        return redirect()
            ->route('public-access.shows.show', [$grant->token, $show])
            ->with('status', 'Bolo actualizado.');
    }

    public function destroyShow(string $token, Show $show, SharedAccessService $sharedAccessService)
    {
        $grant = $sharedAccessService->resolveActiveGrant($token);
        abort_if(! $grant, 404);
        abort_unless($sharedAccessService->canViewShow($grant, $show), 404);
        abort_unless($sharedAccessService->canDeleteShow($grant), 403);

        $name = $show->name;
        $tourId = $show->tour_id;
        $showId = $show->id;

        ActivityLogger::log(
            action: 'public_shared_access.show.deleted',
            detail: 'Bolo eliminado desde acceso compartido: '.$name,
            tourId: $tourId,
            showId: $showId,
            properties: [
                'shared_access_id' => $grant->id,
                'shared_access_role' => $grant->role,
                'shared_access_label' => $grant->label,
            ],
        );

        $show->delete();

        return redirect()
            ->route('public-access.index', $grant->token)
            ->with('status', 'Bolo eliminado.');
    }

    public function storeDocument(
        string $token,
        Show $show,
        Request $request,
        SharedAccessService $sharedAccessService
    ) {
        $grant = $sharedAccessService->resolveActiveGrant($token);
        abort_if(! $grant, 404);
        abort_unless($sharedAccessService->canViewShow($grant, $show), 404);
        abort_unless($sharedAccessService->canUploadDocuments($grant), 403);

        $allowedTypes = $sharedAccessService->allowedDocumentTypes($grant) ?? ShowDocument::TYPES;

        $validated = $request->validate([
            'document_type' => ['required', 'string', Rule::in($allowedTypes)],
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:20480'],
        ]);

        $file = $request->file('file');
        $path = $file->store("show-documents/{$show->id}", 'public');

        $document = $show->documents()->create([
            'document_type' => $validated['document_type'],
            'title' => $validated['title'],
            'original_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => null,
        ]);

        ActivityLogger::log(
            action: 'public_shared_access.show_document.created',
            detail: 'Documento subido desde acceso compartido: '.$document->title,
            subject: $document,
            tourId: $show->tour_id,
            showId: $show->id,
            properties: [
                'shared_access_id' => $grant->id,
                'shared_access_role' => $grant->role,
                'shared_access_label' => $grant->label,
            ],
        );

        return redirect()
            ->route('public-access.shows.show', [$grant->token, $show])
            ->with('status', 'Documento subido correctamente.');
    }

    public function document(string $token, ShowDocument $document, SharedAccessService $sharedAccessService): StreamedResponse
    {
        $grant = $sharedAccessService->resolveActiveGrant($token);
        abort_if(! $grant, 404);

        $document->load('show');
        abort_unless($sharedAccessService->canViewShow($grant, $document->show), 404);
        abort_unless($sharedAccessService->canDownloadDocument($grant, $document), 403);

        return Storage::disk('public')->download($document->storage_path, $document->original_name);
    }

    private function validateShowPayload(
        Request $request,
        \App\Models\SharedAccess $grant,
        SharedAccessService $sharedAccessService,
        bool $creating
    ): array {
        $request->merge($this->normalizedTimeInputs($request));

        $allRules = [
            'tour_id' => ['nullable', Rule::exists('tours', 'id')->where('owner_id', $grant->ownerId())],
            'date' => ['required', 'date'],
            'city' => ['required', 'string', 'max:255'],
            'venue' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(array_keys(Show::STATUS_OPTIONS))],
            'load_in_at' => ['nullable', 'date_format:H:i:s'],
            'meal_at' => ['nullable', 'date_format:H:i:s'],
            'soundcheck_at' => ['nullable', 'date_format:H:i:s'],
            'doors_at' => ['nullable', 'date_format:H:i:s'],
            'show_at' => ['nullable', 'date_format:H:i:s'],
            'show_end_at' => ['nullable', 'date_format:H:i:s'],
            'load_out_at' => ['nullable', 'date_format:H:i:s'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_role' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email:rfc', 'max:255'],
            'lighting_notes' => ['nullable', 'string', 'max:5000'],
            'lighting_validated' => ['nullable', 'boolean'],
            'sound_notes' => ['nullable', 'string', 'max:5000'],
            'sound_validated' => ['nullable', 'boolean'],
            'space_notes' => ['nullable', 'string', 'max:5000'],
            'space_validated' => ['nullable', 'boolean'],
            'general_notes' => ['nullable', 'string', 'max:5000'],
            'general_validated' => ['nullable', 'boolean'],
        ];

        $editableFields = $sharedAccessService->editableShowFields($grant);
        $rules = [];

        foreach ($editableFields as $field) {
            $rules[$field] = $allRules[$field];
        }

        if ($creating) {
            foreach (['date', 'city', 'name', 'status'] as $requiredField) {
                if (array_key_exists($requiredField, $allRules) && ! array_key_exists($requiredField, $rules)) {
                    $rules[$requiredField] = $allRules[$requiredField];
                }
            }
        }

        $validated = $request->validate($rules);

        foreach (['lighting_validated', 'sound_validated', 'space_validated', 'general_validated'] as $checkboxField) {
            if (in_array($checkboxField, $editableFields, true)) {
                $validated[$checkboxField] = $request->boolean($checkboxField);
            }
        }

        if ($grant->tour_id) {
            $validated['tour_id'] = $grant->tour_id;
        } elseif (! in_array('tour_id', $editableFields, true)) {
            unset($validated['tour_id']);
        }

        return $validated;
    }

    private function normalizedTimeInputs(Request $request): array
    {
        $normalized = [];

        foreach (self::TIME_FIELDS as $field) {
            if (! $request->exists($field)) {
                continue;
            }

            $value = $request->input($field);

            if (blank($value)) {
                $normalized[$field] = null;
                continue;
            }

            $normalized[$field] = $this->normalizeTimeValue($value);
        }

        return $normalized;
    }

    private function normalizeTimeValue(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $value = trim($value);

        foreach (['H:i:s', 'H:i'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('H:i:s');
            } catch (\Throwable) {
            }
        }

        return $value;
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
}
