<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTourRequest;
use App\Http\Requests\UpdateTourRequest;
use App\Models\Tour;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TourController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        return view('tours.index', [
            'tours' => Tour::ownedBy($userId)
                ->withCount(['contacts', 'documents', 'shows'])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('tours.create', [
            'tour' => new Tour([
                'color' => '#2563EB',
            ]),
        ]);
    }

    public function store(StoreTourRequest $request): RedirectResponse
    {
        $tour = Tour::create([
            ...$request->validated(),
            'owner_id' => $request->user()->id,
        ]);

        ActivityLogger::log(
            action: 'tour.created',
            detail: "Gira creada: {$tour->name}",
            actor: $request->user(),
            subject: $tour,
            tourId: $tour->id,
        );

        return redirect()
            ->route('tours.show', $tour)
            ->with('status', 'Gira creada correctamente.');
    }

    public function show(Tour $tour): View
    {
        $this->ensureOwnedTour($tour);

        $tour->load([
            'contacts',
            'documents.uploader',
            'shows' => fn ($query) => $query->orderBy('date')->limit(8),
            'activityLogs.actor',
        ]);

        return view('tours.show', [
            'tour' => $tour,
        ]);
    }

    public function edit(Tour $tour): View
    {
        $this->ensureOwnedTour($tour);

        return view('tours.edit', [
            'tour' => $tour,
        ]);
    }

    public function update(UpdateTourRequest $request, Tour $tour): RedirectResponse
    {
        $this->ensureOwnedTour($tour);

        $tour->update($request->validated());

        ActivityLogger::log(
            action: 'tour.updated',
            detail: "Gira actualizada: {$tour->name}",
            actor: $request->user(),
            subject: $tour,
            tourId: $tour->id,
        );

        return redirect()
            ->route('tours.show', $tour)
            ->with('status', 'Gira actualizada.');
    }

    public function destroy(Tour $tour): RedirectResponse
    {
        $this->ensureOwnedTour($tour);

        $name = $tour->name;
        $tourId = $tour->id;
        $tour->delete();

        ActivityLogger::log(
            action: 'tour.deleted',
            detail: "Gira eliminada: {$name}",
            actor: request()->user(),
            properties: [
                'deleted_tour_id' => $tourId,
            ],
        );

        return redirect()
            ->route('tours.index')
            ->with('status', 'Gira eliminada.');
    }

    private function ensureOwnedTour(Tour $tour): void
    {
        abort_unless($tour->owner_id === auth()->id(), 404);
    }
}
