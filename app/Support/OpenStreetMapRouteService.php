<?php

namespace App\Support;

use App\Models\Show;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class OpenStreetMapRouteService
{
    public function routeForShow(Show $show): array
    {
        $origin = trim((string) $show->travel_origin);
        $destination = trim(collect([$show->venue, $show->city])->filter()->implode(', '));
        $travelMode = $show->travel_mode ?: 'van';

        if ($travelMode === 'plane') {
            return [
                'available' => false,
                'reason' => 'plane_mode',
                'origin' => $origin,
                'destination' => $destination,
                'travel_mode' => $travelMode,
            ];
        }

        if ($origin === '' || $destination === '') {
            return [
                'available' => false,
                'reason' => 'missing_addresses',
                'origin' => $origin,
                'destination' => $destination,
                'travel_mode' => $travelMode,
            ];
        }

        $cacheKey = 'osm-route:'.sha1($travelMode.'|'.$origin.'|'.$destination);

        return Cache::remember($cacheKey, now()->addHours(12), function () use ($origin, $destination, $travelMode): array {
            try {
                $originPoint = $this->geocode($origin);
                $destinationPoint = $this->geocode($destination);

                if (! $originPoint || ! $destinationPoint) {
                    return [
                        'available' => false,
                        'reason' => 'geocoding_failed',
                        'origin' => $origin,
                        'destination' => $destination,
                        'travel_mode' => $travelMode,
                    ];
                }

                $routeResponse = Http::timeout(15)
                    ->acceptJson()
                    ->get(sprintf(
                        'https://router.project-osrm.org/route/v1/driving/%s,%s;%s,%s',
                        $originPoint['lon'],
                        $originPoint['lat'],
                        $destinationPoint['lon'],
                        $destinationPoint['lat']
                    ), [
                        'overview' => 'full',
                        'geometries' => 'geojson',
                        'steps' => 'false',
                    ])
                    ->throw();

                $route = data_get($routeResponse->json(), 'routes.0');
                $duration = (float) data_get($route, 'duration', 0);
                $distance = (float) data_get($route, 'distance', 0);
                $geometry = data_get($route, 'geometry');

                if ($duration <= 0 || $distance <= 0 || ! is_array($geometry)) {
                    return [
                        'available' => false,
                        'reason' => 'empty_route',
                        'origin' => $origin,
                        'destination' => $destination,
                        'travel_mode' => $travelMode,
                    ];
                }

                return [
                    'available' => true,
                    'reason' => null,
                    'origin' => $origin,
                    'destination' => $destination,
                    'travel_mode' => $travelMode,
                    'duration_text' => $this->formatDuration($duration),
                    'distance_text' => $this->formatDistance($distance),
                    'directions_url' => $this->directionsUrl($originPoint, $destinationPoint),
                    'geometry' => $geometry,
                    'origin_point' => $originPoint,
                    'destination_point' => $destinationPoint,
                ];
            } catch (\Throwable) {
                return [
                    'available' => false,
                    'reason' => 'request_failed',
                    'origin' => $origin,
                    'destination' => $destination,
                    'travel_mode' => $travelMode,
                ];
            }
        });
    }

    private function geocode(string $query): ?array
    {
        $response = Http::timeout(15)
            ->acceptJson()
            ->withHeaders([
                'User-Agent' => 'Show Control/'.app()->version().' ('.config('app.url').')',
                'Referer' => config('app.url'),
            ])
            ->get('https://nominatim.openstreetmap.org/search', [
                'q' => $query,
                'format' => 'jsonv2',
                'limit' => 1,
            ])
            ->throw();

        $place = data_get($response->json(), '0');

        if (! $place) {
            return null;
        }

        return [
            'lat' => (float) data_get($place, 'lat'),
            'lon' => (float) data_get($place, 'lon'),
        ];
    }

    private function formatDuration(float $seconds): string
    {
        $minutes = (int) round($seconds / 60);
        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($hours === 0) {
            return $minutes.' min';
        }

        if ($remainingMinutes === 0) {
            return $hours.' h';
        }

        return $hours.' h '.$remainingMinutes.' min';
    }

    private function formatDistance(float $meters): string
    {
        if ($meters < 1000) {
            return (int) round($meters).' m';
        }

        return number_format($meters / 1000, 1, ',', '').' km';
    }

    private function directionsUrl(array $originPoint, array $destinationPoint): string
    {
        return sprintf(
            'https://www.openstreetmap.org/directions?engine=fossgis_osrm_car&route=%s,%s;%s,%s',
            $originPoint['lat'],
            $originPoint['lon'],
            $destinationPoint['lat'],
            $destinationPoint['lon']
        );
    }
}
