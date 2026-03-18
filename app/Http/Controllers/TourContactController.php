<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTourContactRequest;
use App\Http\Requests\UpdateTourContactRequest;
use App\Models\Tour;
use App\Models\TourContact;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TourContactController extends Controller
{
    public function edit(Tour $tour, TourContact $contact): View
    {
        $this->ensureOwnedTour($tour);
        abort_unless($contact->tour_id === $tour->id, 404);

        return view('tours.contacts.edit', [
            'tour' => $tour,
            'contact' => $contact,
        ]);
    }

    public function store(StoreTourContactRequest $request, Tour $tour): RedirectResponse
    {
        $this->ensureOwnedTour($tour);
        $contact = $tour->contacts()->create($request->validated());

        ActivityLogger::log(
            action: 'tour_contact.created',
            detail: "Contacto de gira creado: {$contact->name}",
            actor: $request->user(),
            subject: $contact,
            tourId: $tour->id,
        );

        return redirect()
            ->route('tours.show', $tour)
            ->with('status', 'Contacto añadido a la gira.');
    }

    public function update(UpdateTourContactRequest $request, Tour $tour, TourContact $contact): RedirectResponse
    {
        $this->ensureOwnedTour($tour);
        abort_unless($contact->tour_id === $tour->id, 404);

        $contact->update($request->validated());

        ActivityLogger::log(
            action: 'tour_contact.updated',
            detail: "Contacto de gira actualizado: {$contact->name}",
            actor: $request->user(),
            subject: $contact,
            tourId: $tour->id,
        );

        return redirect()
            ->route('tours.show', $tour)
            ->with('status', 'Contacto actualizado.');
    }

    public function destroy(Tour $tour, TourContact $contact): RedirectResponse
    {
        $this->ensureOwnedTour($tour);
        abort_unless($contact->tour_id === $tour->id, 404);

        $name = $contact->name;
        $contact->delete();

        ActivityLogger::log(
            action: 'tour_contact.deleted',
            detail: "Contacto de gira eliminado: {$name}",
            actor: request()->user(),
            tourId: $tour->id,
        );

        return redirect()
            ->route('tours.show', $tour)
            ->with('status', 'Contacto eliminado.');
    }

    private function ensureOwnedTour(Tour $tour): void
    {
        abort_unless($tour->owner_id === auth()->id(), 404);
    }
}
