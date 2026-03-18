<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class IcsCalendarService
{
    public function fetchAndParse(string $url, Carbon $from, Carbon $to): array
    {
        $content = Http::timeout(15)->get($url)->throw()->body();

        return $this->parse($content, $from, $to);
    }

    public function parse(string $content, Carbon $from, Carbon $to): array
    {
        $content = preg_replace("/\r\n[ \t]/", '', $content) ?? $content;
        $lines = preg_split("/\r\n|\n|\r/", $content) ?: [];
        $events = [];
        $currentEvent = null;

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === 'BEGIN:VEVENT') {
                $currentEvent = [];
                continue;
            }

            if ($line === 'END:VEVENT') {
                if ($currentEvent !== null) {
                    $normalized = $this->normalizeEvent($currentEvent);

                    if ($normalized && $this->isWithinRange($normalized['start'], $from, $to)) {
                        $events[] = $normalized;
                    }
                }

                $currentEvent = null;
                continue;
            }

            if ($currentEvent === null || ! str_contains($line, ':')) {
                continue;
            }

            [$key, $value] = explode(':', $line, 2);
            $field = strtoupper(Str::before($key, ';'));
            $currentEvent[$field] = $value;
        }

        usort($events, fn (array $a, array $b) => $a['start']->timestamp <=> $b['start']->timestamp);

        return $events;
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

        $city = $placeSegment !== ''
            ? Str::of($placeSegment)->before(',')->trim()->toString()
            : Str::of($location)->before(',')->trim()->toString();

        $showName = $placeSegment !== '' ? $placeSegment : $summary;

        return [
            'event_id' => $event['event_id'],
            'summary' => $summary,
            'tour_name' => $tourName,
            'show_name' => $showName,
            'city' => $city !== '' ? $city : 'Por definir',
            'venue' => $location !== '' ? $location : null,
            'date' => $event['start']->toDateString(),
            'show_at' => $event['all_day'] ? null : $event['start']->format('H:i:s'),
            'general_notes' => trim(implode("\n\n", array_filter([
                $location !== '' ? 'ICS · Ubicacion: '.$location : null,
                filled($event['description'] ?? null) ? trim((string) $event['description']) : null,
            ]))),
        ];
    }

    private function normalizeEvent(array $event): ?array
    {
        $startValue = $event['DTSTART'] ?? null;

        if (blank($startValue)) {
            return null;
        }

        $start = $this->parseDateValue($startValue);

        if (! $start) {
            return null;
        }

        return [
            'event_id' => $event['UID'] ?? sha1(($event['SUMMARY'] ?? '').'|'.$start->toIso8601String()),
            'summary' => $this->decodeText($event['SUMMARY'] ?? 'Evento sin titulo'),
            'location' => $this->decodeText($event['LOCATION'] ?? ''),
            'description' => $this->decodeText($event['DESCRIPTION'] ?? ''),
            'start' => $start,
            'all_day' => strlen((string) $startValue) === 8,
        ];
    }

    private function parseDateValue(string $value): ?Carbon
    {
        $value = trim($value);

        foreach (['Ymd\THis\Z', 'Ymd\THis', 'Ymd'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value, str_ends_with($format, '\Z') ? 'UTC' : null);
            } catch (\Throwable) {
            }
        }

        return null;
    }

    private function decodeText(string $value): string
    {
        return str_replace(
            ['\\n', '\\N', '\\,', '\\;', '\\\\'],
            ["\n", "\n", ',', ';', '\\'],
            $value
        );
    }

    private function isWithinRange(Carbon $date, Carbon $from, Carbon $to): bool
    {
        return $date->copy()->startOfDay()->betweenIncluded(
            $from->copy()->startOfDay(),
            $to->copy()->endOfDay()
        );
    }
}
