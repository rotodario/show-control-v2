<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\ShowSectionMessage;
use App\Support\ActivityLogger;
use App\Support\SharedAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShowSectionMessageController extends Controller
{
    public function store(Request $request, Show $show): RedirectResponse
    {
        abort_unless($show->owner_id === $request->user()?->id, 404);

        $validated = $this->validateMessage($request);

        $message = $show->sectionMessages()->create([
            'section' => $validated['section'],
            'message' => $validated['message'],
            'user_id' => $request->user()?->id,
            'author_name' => $request->user()?->name ?? 'Usuario',
        ]);

        ActivityLogger::log(
            action: 'show_section_message.created',
            detail: 'Mensaje interno en '.mb_strtolower(ShowSectionMessage::SECTIONS[$message->section] ?? $message->section),
            actor: $request->user(),
            subject: $message,
            tourId: $show->tour_id,
            showId: $show->id,
            properties: [
                'section' => $message->section,
            ],
        );

        return redirect()
            ->to(route('shows.show', $show).'#section-chat-'.$message->section)
            ->with('status', 'Mensaje interno enviado.');
    }

    public function storePublic(
        string $token,
        Show $show,
        Request $request,
        SharedAccessService $sharedAccessService
    ): RedirectResponse {
        $grant = $sharedAccessService->resolveActiveGrant($token);
        abort_if(! $grant, 404);
        abort_unless($sharedAccessService->canViewShow($grant, $show), 404);

        $validated = $this->validateMessage($request);
        abort_unless($sharedAccessService->canAccessSectionMessages($grant, $validated['section']), 403);

        $message = $show->sectionMessages()->create([
            'section' => $validated['section'],
            'message' => $validated['message'],
            'shared_access_id' => $grant->id,
            'author_name' => $grant->authorLabel(),
        ]);

        ActivityLogger::log(
            action: 'public_shared_access.show_section_message.created',
            detail: 'Mensaje interno desde acceso compartido en '.mb_strtolower(ShowSectionMessage::SECTIONS[$message->section] ?? $message->section),
            subject: $message,
            tourId: $show->tour_id,
            showId: $show->id,
            properties: [
                'section' => $message->section,
                'shared_access_id' => $grant->id,
                'shared_access_role' => $grant->role,
                'shared_access_label' => $grant->label,
            ],
        );

        return redirect()
            ->to(route('public-access.shows.show', [$grant->token, $show]).'#section-chat-'.$message->section)
            ->with('status', 'Mensaje interno enviado.');
    }

    private function validateMessage(Request $request): array
    {
        return $request->validate([
            'section' => ['required', Rule::in(array_keys(ShowSectionMessage::SECTIONS))],
            'message' => ['required', 'string', 'max:5000'],
        ]);
    }
}
