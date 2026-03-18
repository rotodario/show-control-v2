<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShowDocumentRequest;
use App\Http\Requests\UpdateShowDocumentRequest;
use App\Models\Show;
use App\Models\ShowDocument;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ShowDocumentController extends Controller
{
    public function edit(Show $show, ShowDocument $document): View
    {
        $this->ensureOwnedShow($show);
        abort_unless($document->show_id === $show->id, 404);

        return view('shows.documents.edit', [
            'show' => $show,
            'document' => $document,
            'documentTypes' => ShowDocument::TYPES,
        ]);
    }

    public function store(StoreShowDocumentRequest $request, Show $show): RedirectResponse
    {
        $this->ensureOwnedShow($show);
        $file = $request->file('file');
        $path = $file->store("show-documents/{$show->id}", 'public');

        $document = $show->documents()->create([
            'document_type' => $request->string('document_type')->toString(),
            'title' => $request->string('title')->toString(),
            'original_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $request->user()?->id,
        ]);

        ActivityLogger::log(
            action: 'show_document.created',
            detail: "Documento de bolo añadido: {$document->title}",
            actor: $request->user(),
            subject: $document,
            tourId: $show->tour_id,
            showId: $show->id,
        );

        return redirect()
            ->route('shows.show', $show)
            ->with('status', 'Documento de bolo subido correctamente.');
    }

    public function show(Show $show, ShowDocument $document): StreamedResponse
    {
        $this->ensureOwnedShow($show);
        abort_unless($document->show_id === $show->id, 404);

        return Storage::disk('public')->download($document->storage_path, $document->original_name);
    }

    public function update(UpdateShowDocumentRequest $request, Show $show, ShowDocument $document): RedirectResponse
    {
        $this->ensureOwnedShow($show);
        abort_unless($document->show_id === $show->id, 404);

        $document->update($request->validated());

        ActivityLogger::log(
            action: 'show_document.updated',
            detail: "Documento de bolo actualizado: {$document->title}",
            actor: $request->user(),
            subject: $document,
            tourId: $show->tour_id,
            showId: $show->id,
        );

        return redirect()
            ->route('shows.show', $show)
            ->with('status', 'Documento de bolo actualizado.');
    }

    public function destroy(Show $show, ShowDocument $document): RedirectResponse
    {
        $this->ensureOwnedShow($show);
        abort_unless($document->show_id === $show->id, 404);

        Storage::disk('public')->delete($document->storage_path);
        $title = $document->title;
        $document->delete();

        ActivityLogger::log(
            action: 'show_document.deleted',
            detail: "Documento de bolo eliminado: {$title}",
            actor: request()->user(),
            tourId: $show->tour_id,
            showId: $show->id,
        );

        return redirect()
            ->route('shows.show', $show)
            ->with('status', 'Documento de bolo eliminado.');
    }

    private function ensureOwnedShow(Show $show): void
    {
        abort_unless($show->owner_id === auth()->id(), 404);
    }
}
