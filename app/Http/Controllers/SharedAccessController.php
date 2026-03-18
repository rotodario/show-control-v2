<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSharedAccessRequest;
use App\Models\SharedAccess;
use App\Models\Tour;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SharedAccessController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        return view('shared-accesses.index', [
            'sharedAccesses' => SharedAccess::with(['tour', 'creator'])
                ->where('created_by', $userId)
                ->latest()
                ->get(),
            'tours' => Tour::ownedBy($userId)->orderBy('name')->get(),
            'roles' => SharedAccess::ROLE_LABELS,
        ]);
    }

    public function store(StoreSharedAccessRequest $request): RedirectResponse
    {
        $sharedAccess = SharedAccess::create([
            ...$request->validated(),
            'created_by' => $request->user()?->id,
        ]);

        ActivityLogger::log(
            action: 'shared_access.created',
            detail: sprintf(
                'Acceso compartido creado: %s · %s',
                SharedAccess::ROLE_LABELS[$sharedAccess->role] ?? $sharedAccess->role,
                $sharedAccess->label ?: 'Sin etiqueta'
            ),
            actor: $request->user(),
            subject: $sharedAccess,
            tourId: $sharedAccess->tour_id,
        );

        return redirect()
            ->route('shared-accesses.index')
            ->with('status', 'Acceso compartido creado.');
    }

    public function destroy(SharedAccess $sharedAccess): RedirectResponse
    {
        abort_unless($sharedAccess->created_by === auth()->id(), 404);

        $sharedAccess->update([
            'revoked_at' => now(),
        ]);

        ActivityLogger::log(
            action: 'shared_access.revoked',
            detail: sprintf(
                'Acceso compartido revocado: %s',
                $sharedAccess->label ?: '#'.$sharedAccess->id
            ),
            actor: request()->user(),
            subject: $sharedAccess,
            tourId: $sharedAccess->tour_id,
        );

        return redirect()
            ->route('shared-accesses.index')
            ->with('status', 'Acceso compartido revocado.');
    }
}
