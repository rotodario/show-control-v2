<?php

namespace App\Support;

use App\Models\AppSetting;
use App\Models\GoogleCalendarConnection;
use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoogleCalendarService
{
    private const GOOGLE_AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const GOOGLE_TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const GOOGLE_CALENDAR_API = 'https://www.googleapis.com/calendar/v3';
    private const GOOGLE_SCOPE = 'https://www.googleapis.com/auth/calendar.readonly';

    public function isConfigured(): bool
    {
        return filled($this->clientId())
            && filled($this->clientSecret())
            && filled($this->redirectUri());
    }

    public function authorizationUrl(string $state): string
    {
        return self::GOOGLE_AUTH_URL.'?'.http_build_query([
            'client_id' => $this->clientId(),
            'redirect_uri' => $this->redirectUri(),
            'response_type' => 'code',
            'scope' => self::GOOGLE_SCOPE,
            'access_type' => 'offline',
            'prompt' => 'consent',
            'include_granted_scopes' => 'true',
            'state' => $state,
        ]);
    }

    public function exchangeCode(string $code): array
    {
        $response = Http::asForm()->acceptJson()->post(self::GOOGLE_TOKEN_URL, [
            'code' => $code,
            'client_id' => $this->clientId(),
            'client_secret' => $this->clientSecret(),
            'redirect_uri' => $this->redirectUri(),
            'grant_type' => 'authorization_code',
        ])->throw()->json();

        return $this->normalizeTokenPayload($response);
    }

    public function refreshConnection(GoogleCalendarConnection $connection): GoogleCalendarConnection
    {
        if (! $connection->isExpired()) {
            return $connection;
        }

        if (blank($connection->refresh_token)) {
            throw new \RuntimeException('La conexion de Google Calendar no dispone de refresh token.');
        }

        $response = Http::asForm()->acceptJson()->post(self::GOOGLE_TOKEN_URL, [
            'client_id' => $this->clientId(),
            'client_secret' => $this->clientSecret(),
            'refresh_token' => $connection->refresh_token,
            'grant_type' => 'refresh_token',
        ])->throw()->json();

        $normalized = $this->normalizeTokenPayload($response);

        $connection->forceFill([
            'access_token' => $normalized['access_token'],
            'refresh_token' => $normalized['refresh_token'] ?: $connection->refresh_token,
            'expires_at' => $normalized['expires_at'],
        ])->save();

        return $connection->fresh();
    }

    public function listCalendars(GoogleCalendarConnection $connection): array
    {
        $items = [];
        $pageToken = null;

        do {
            $response = $this->authorizedRequest($connection)
                ->get(self::GOOGLE_CALENDAR_API.'/users/me/calendarList', array_filter([
                    'pageToken' => $pageToken,
                    'showHidden' => false,
                ]))
                ->throw()
                ->json();

            $items = array_merge($items, $response['items'] ?? []);
            $pageToken = $response['nextPageToken'] ?? null;
        } while ($pageToken);

        return collect($items)
            ->filter(fn (array $item) => ! ($item['deleted'] ?? false))
            ->map(fn (array $item) => [
                'id' => $item['id'],
                'summary' => $item['summaryOverride'] ?? $item['summary'] ?? $item['id'],
                'primary' => (bool) ($item['primary'] ?? false),
                'time_zone' => $item['timeZone'] ?? null,
            ])
            ->sortByDesc('primary')
            ->values()
            ->all();
    }

    public function listEvents(
        GoogleCalendarConnection $connection,
        string $calendarId,
        Carbon $from,
        Carbon $to
    ): array {
        $items = [];
        $pageToken = null;

        do {
            $response = $this->authorizedRequest($connection)
                ->get(self::GOOGLE_CALENDAR_API.'/calendars/'.rawurlencode($calendarId).'/events', array_filter([
                    'singleEvents' => 'true',
                    'orderBy' => 'startTime',
                    'timeMin' => $from->copy()->startOfDay()->toRfc3339String(),
                    'timeMax' => $to->copy()->endOfDay()->toRfc3339String(),
                    'pageToken' => $pageToken,
                    'maxResults' => 250,
                ]))
                ->throw()
                ->json();

            $items = array_merge($items, $response['items'] ?? []);
            $pageToken = $response['nextPageToken'] ?? null;
        } while ($pageToken);

        return collect($items)
            ->reject(fn (array $event) => ($event['status'] ?? null) === 'cancelled')
            ->values()
            ->all();
    }

    public function parseEvent(array $event): array
    {
        $summary = trim((string) ($event['summary'] ?? 'Evento sin titulo'));
        $segments = collect(preg_split('/\s[-|–]\s/u', $summary) ?: [])
            ->map(fn (string $part) => trim($part))
            ->filter()
            ->values();

        $tourName = $segments->first() ?: $summary;
        $placeSegment = $segments->slice(1)->implode(' - ');
        $location = trim((string) ($event['location'] ?? ''));

        $startDateTime = data_get($event, 'start.dateTime');
        $startDate = data_get($event, 'start.date');

        $start = $startDateTime
            ? Carbon::parse($startDateTime)
            : Carbon::parse($startDate);

        $city = $placeSegment !== ''
            ? Str::of($placeSegment)->before(',')->trim()->toString()
            : Str::of($location)->before(',')->trim()->toString();

        $showName = $placeSegment !== '' ? $placeSegment : $summary;

        return [
            'event_id' => $event['id'],
            'summary' => $summary,
            'tour_name' => $tourName,
            'show_name' => $showName,
            'city' => $city !== '' ? $city : 'Por definir',
            'venue' => $location !== '' ? $location : null,
            'date' => $start->toDateString(),
            'show_at' => $startDateTime ? $start->format('H:i:s') : null,
            'general_notes' => trim(implode("\n\n", array_filter([
                $location !== '' ? 'Google Calendar · Ubicacion: '.$location : null,
                filled($event['description'] ?? null) ? trim((string) $event['description']) : null,
            ]))),
            'raw' => $event,
        ];
    }

    private function authorizedRequest(GoogleCalendarConnection $connection): PendingRequest
    {
        $connection = $this->refreshConnection($connection);

        return Http::acceptJson()->withToken($connection->access_token);
    }

    private function normalizeTokenPayload(array $payload): array
    {
        return [
            'access_token' => $payload['access_token'],
            'refresh_token' => $payload['refresh_token'] ?? null,
            'expires_at' => isset($payload['expires_in'])
                ? now()->addSeconds((int) $payload['expires_in'])
                : null,
        ];
    }

    public function currentSettings(): array
    {
        return [
            'client_id' => $this->clientId(),
            'client_secret' => $this->clientSecret(),
            'redirect' => $this->redirectUri(),
        ];
    }

    private function clientId(): ?string
    {
        return AppSetting::getValue('google_calendar.client_id')
            ?: config('services.google_calendar.client_id');
    }

    private function clientSecret(): ?string
    {
        return AppSetting::getValue('google_calendar.client_secret')
            ?: config('services.google_calendar.client_secret');
    }

    private function redirectUri(): ?string
    {
        return AppSetting::getValue('google_calendar.redirect')
            ?: config('services.google_calendar.redirect');
    }
}
