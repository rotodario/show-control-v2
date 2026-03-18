<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTourDocumentRequest;
use App\Http\Requests\UpdateTourDocumentRequest;
use App\Models\Tour;
use App\Models\TourDocument;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TourDocumentController extends Controller
{
    public function edit(Tour $tour, TourDocument $document): View
    {
        $this->ensureOwnedTour($tour);
        abort_unless($document->tour_id === $tour->id, 404);

        return view('tours.documents.edit', [
            'tour' => $tour,
            'document' => $document,
            'documentTypes' => TourDocument::TYPES,
        ]);
    }

    public function store(StoreTourDocumentRequest $request, Tour $tour): RedirectResponse
    {
        $this->ensureOwnedTour($tour);
        $file = $request->file('file');
        $path = $file->store("tour-documents/{$tour->id}", 'public');

        $document = $tour->documents()->create([
            'document_type' => $request->string('document_type')->toString(),
            'title' => $request->string('title')->toString(),
            'original_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $request->user()?->id,
        ]);

        ActivityLogger::log(
            action: 'tour_document.created',
            detail: "Documento de gira añadido: {$document->title}",
            actor: $request->user(),
            subject: $document,
            tourId: $tour->id,
        );

        return redirect()
            ->route('tours.show', $tour)
            ->with('status', 'Documento subido correctamente.');
    }

    public function update(UpdateTourDocumentRequest $request, Tour $tour, TourDocument $document): RedirectResponse
    {
        $this->ensureOwnedTour($tour);
        abort_unless($document->tour_id === $tour->id, 404);

        $document->update($request->validated());

        ActivityLogger::log(
            action: 'tour_document.updated',
            detail: "Documento de gira actualizado: {$document->title}",
            actor: $request->user(),
            subject: $document,
            tourId: $tour->id,
        );

        return redirect()
            ->route('tours.show', $tour)
            ->with('status', 'Documento actualizado.');
    }

    public function show(Tour $tour, TourDocument $document): StreamedResponse
    {
        $this->ensureOwnedTour($tour);
        abort_unless($document->tour_id === $tour->id, 404);

        return Storage::disk('public')->download($document->storage_path, $document->original_name);
    }

    public function destroy(Tour $tour, TourDocument $document): RedirectResponse
    {
        $this->ensureOwnedTour($tour);
        abort_unless($document->tour_id === $tour->id, 404);

        Storage::disk('public')->delete($document->storage_path);
        $title = $document->title;
        $document->delete();

        ActivityLogger::log(
            action: 'tour_document.deleted',
            detail: "Documento de gira eliminado: {$title}",
            actor: request()->user(),
            tourId: $tour->id,
        );

        return redirect()
            ->route('tours.show', $tour)
            ->with('status', 'Documento eliminado.');
    }

    private function ensureOwnedTour(Tour $tour): void
    {
        abort_unless($tour->owner_id === auth()->id(), 404);
    }
}
