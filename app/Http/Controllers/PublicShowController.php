<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Support\OpenStreetMapRouteService;
use Illuminate\View\View;

class PublicShowController extends Controller
{
    public function show(string $token, OpenStreetMapRouteService $openStreetMapRouteService): View
    {
        $show = Show::query()
            ->with('tour')
            ->where('public_summary_token', $token)
            ->firstOrFail();

        return view('public-shows.show', [
            'show' => $show,
            'travelRoute' => $openStreetMapRouteService->routeForShow($show),
            'travelModeOptions' => Show::translatedTravelModeOptions(),
        ]);
    }
}
