<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ __('ui.production') }}</p>
                <h2 class="text-2xl font-semibold text-slate-900">{{ __('ui.shows_map') }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ __('ui.shows_map_page_help') }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('shows.index', request()->only('tour_id')) }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100">
                    {{ __('ui.back_to_shows') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('shows.map') }}" class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                    <div class="min-w-0 flex-1">
                        <x-input-label for="tour_filter" :value="__('ui.filter_by_tour')" />
                        <select id="tour_filter" name="tour_id" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                            <option value="">{{ __('ui.all_feminine') }}</option>
                            @foreach ($tours as $tour)
                                <option value="{{ $tour->id }}" @selected((string) $selectedTourId === (string) $tour->id)>{{ $tour->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-500">
                        {{ __('ui.filter') }}
                    </button>
                </div>
            </form>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.shows_map') }}</h3>
                        <p class="text-sm text-slate-500">{{ __('ui.shows_map_sequence_help') }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ __('ui.shows_count', ['count' => $mapShows->count()]) }}</span>
                        @if ($missingMapPointsCount > 0)
                            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 ring-1 ring-amber-200">{{ __('ui.pending_points_count', ['count' => $missingMapPointsCount]) }}</span>
                        @endif
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-slate-50 p-4">
                    <p class="text-sm text-slate-600">{{ __('ui.shows_map_city_based_notice') }}</p>
                    <form method="POST" action="{{ route('shows.map.sync') }}" x-data="{ syncing: false }" @submit="syncing = true">
                        @csrf
                        @if ($selectedTourId)
                            <input type="hidden" name="tour_id" value="{{ $selectedTourId }}">
                        @endif
                        <div class="flex flex-col items-end gap-2">
                            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-wait disabled:opacity-70" :disabled="syncing">
                                <span x-show="!syncing">{{ __('ui.sync_map_points') }}</span>
                                <span x-show="syncing" x-cloak>{{ __('ui.sync_map_points_running') }}</span>
                            </button>
                            <p x-show="syncing" x-cloak class="text-xs text-slate-500">{{ __('ui.sync_map_points_running_help') }}</p>
                        </div>
                    </form>
                </div>

                <link
                    rel="stylesheet"
                    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
                    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
                    crossorigin=""
                >

                <div class="mt-6 overflow-hidden rounded-[2rem] border border-slate-200">
                    <div class="relative">
                        <div id="shows-map-frame" class="w-full" style="height: 34rem;" data-shows='@json($mapShows)'></div>
                        @if ($mapShows->isEmpty())
                            <div class="pointer-events-none absolute inset-0 flex items-center justify-center bg-white/70 backdrop-blur-[2px]">
                                <div class="rounded-2xl border border-dashed border-slate-300 bg-white/90 px-6 py-4 text-center text-sm text-slate-500 shadow-sm">
                                    {{ __('ui.no_show_map_points') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <script
                    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
                    crossorigin=""
                ></script>
                <script>
                    (() => {
                        const initShowsMap = () => {
                            const mapElement = document.getElementById('shows-map-frame');

                            if (!mapElement || typeof L === 'undefined' || mapElement.dataset.mapReady === '1') {
                                return;
                            }

                            const shows = JSON.parse(mapElement.dataset.shows || '[]');
                            mapElement.dataset.mapReady = '1';

                            const map = L.map(mapElement, { scrollWheelZoom: false, worldCopyJump: true });

                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 18,
                                attribution: '&copy; OpenStreetMap',
                            }).addTo(map);

                            if (!Array.isArray(shows) || shows.length === 0) {
                                map.setView([20, 0], 1);
                                return;
                            }

                            const bounds = L.latLngBounds([]);

                            shows.forEach((show) => {
                                const color = show.tour_color || '#0f172a';
                                const markerIcon = L.divIcon({
                                    className: '',
                                    html: `<a href="${show.url}" aria-label="${show.name}" style="display:flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:9999px;background:${color};border:2px solid #fff;box-shadow:0 6px 16px rgba(15,23,42,.28);color:#fff;font-size:12px;font-weight:700;text-decoration:none;">${show.number}</a>`,
                                    iconSize: [28, 28],
                                    iconAnchor: [14, 14],
                                });

                                const marker = L.marker([show.lat, show.lon], { icon: markerIcon }).addTo(map);

                                marker.bindTooltip(`
                                    <div style="min-width:15rem;">
                                        <div style="display:flex;align-items:center;gap:.5rem;">
                                            <span style="display:inline-flex;align-items:center;justify-content:center;width:1.5rem;height:1.5rem;border-radius:9999px;background:${color};color:#fff;font-size:.75rem;font-weight:700;">${show.number}</span>
                                            <div style="font-weight:600;color:#0f172a;">${show.name}</div>
                                        </div>
                                        <div style="margin-top:.5rem;font-size:.875rem;color:#475569;">${show.date} · ${show.city || ''}</div>
                                        <div style="margin-top:.25rem;font-size:.875rem;color:#475569;">${show.venue || ''}</div>
                                        <div style="margin-top:.5rem;display:flex;flex-wrap:wrap;gap:.35rem;">
                                            ${show.tour_name ? `<span style="display:inline-flex;align-items:center;border-radius:9999px;background:${color};color:#fff;padding:.2rem .55rem;font-size:.72rem;font-weight:600;">${show.tour_name}</span>` : ''}
                                            <span style="display:inline-flex;align-items:center;border-radius:9999px;background:#e2e8f0;color:#334155;padding:.2rem .55rem;font-size:.72rem;font-weight:600;">${show.status}</span>
                                        </div>
                                        <div style="margin-top:.55rem;font-size:.72rem;color:#64748b;">Abrir ficha</div>
                                    </div>
                                `, {
                                    direction: 'top',
                                    offset: [0, -16],
                                    opacity: 0.96,
                                });

                                marker.on('mouseover', () => marker.openTooltip());
                                marker.on('mouseout', () => marker.closeTooltip());

                                bounds.extend([show.lat, show.lon]);
                            });

                            if (bounds.isValid()) {
                                map.fitBounds(bounds, { padding: [40, 40], maxZoom: 5 });
                            } else {
                                map.setView([20, 0], 1);
                            }
                        };

                        if (document.readyState === 'loading') {
                            document.addEventListener('DOMContentLoaded', initShowsMap, { once: true });
                        } else {
                            initShowsMap();
                        }
                    })();
                </script>

                @if ($mapShows->isNotEmpty())
                    <div class="mt-6 grid gap-3 lg:grid-cols-2">
                        @foreach ($mapShows as $show)
                            <a href="{{ $show['url'] }}" class="rounded-2xl border border-slate-200 p-5 transition hover:border-sky-300 hover:bg-sky-50/40">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-center sm:justify-start gap-2">
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold text-white shadow-sm ring-2 ring-white" style="background-color: {{ $show['tour_color'] ?: '#0f172a' }}">
                                            {{ $show['number'] }}
                                        </span>
                                        @if ($show['tour_name'])
                                            <span class="inline-flex shrink-0 whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold text-white shadow-sm" style="background-color: {{ $show['tour_color'] ?: '#0f172a' }}">
                                                {{ $show['tour_name'] }}
                                            </span>
                                        @else
                                            <span class="inline-flex shrink-0 whitespace-nowrap rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                                {{ __('ui.no_tour') }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="truncate text-lg font-semibold text-slate-900">{{ $show['name'] }}</h4>
                                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                                {{ $show['status'] }}
                                            </span>
                                        </div>
                                        <p class="mt-1 text-sm text-slate-500">{{ $show['date'] }} · {{ $show['city'] }}</p>
                                        <p class="mt-2 text-sm text-slate-600">{{ $show['venue'] ?: __('ui.pending_venue') }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
