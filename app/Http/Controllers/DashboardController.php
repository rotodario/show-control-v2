<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\Tour;
use App\Models\TourDocument;
use App\Support\ShowAlertService;
use App\Support\ShowMessageReadService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(ShowAlertService $showAlertService, ShowMessageReadService $showMessageReadService): View
    {
        $userId = auth()->id();
        $allShows = Show::ownedBy($userId)
            ->with('sectionMessages')
            ->get();

        $upcomingShows = Show::ownedBy($userId)
            ->with(['tour', 'sectionMessages'])
            ->whereDate('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->take(6)
            ->get();

        $allShowAlerts = $showAlertService->alertsForCollection($allShows, auth()->user());
        $allUnreadMessageCounts = $showMessageReadService->unreadCountsForUser($allShows, auth()->user());

        return view('dashboard', [
            'tourCount' => Tour::ownedBy($userId)->count(),
            'showCount' => Show::ownedBy($userId)->count(),
            'tourDocumentCount' => TourDocument::query()
                ->whereHas('tour', fn ($query) => $query->ownedBy($userId))
                ->count(),
            'alertCount' => $allShowAlerts->sum(fn ($alerts) => count($alerts)),
            'unreadMessageTotal' => $allUnreadMessageCounts->sum(),
            'upcomingTours' => Tour::ownedBy($userId)
                ->withCount(['shows', 'contacts', 'documents'])
                ->latest()
                ->take(6)
                ->get(),
            'upcomingShows' => $upcomingShows,
            'showAlerts' => $showAlertService->alertsForCollection($upcomingShows, auth()->user()),
            'unreadMessageCounts' => $showMessageReadService->unreadCountsForUser($upcomingShows, auth()->user()),
        ]);
    }
}
