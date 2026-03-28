<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Ficha de bolo</p>
                <h2 class="text-3xl font-semibold text-slate-900">{{ $show->name }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $show->date->format('d/m/Y') }} · {{ $show->city }} · {{ $show->venue ?: 'Venue pendiente' }}</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <form method="POST" action="{{ route('shows.send-roadmap-mail', $show) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center rounded-full border border-emerald-200 px-4 py-2.5 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50">
                        Enviar hoja de ruta
                    </button>
                </form>
                <form method="POST" action="{{ route('shows.send-alert-mail', $show) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center rounded-full border border-amber-200 px-4 py-2.5 text-sm font-semibold text-amber-700 transition hover:bg-amber-50">
                        Enviar alerta
                    </button>
                </form>
                <a href="{{ route('shows.pdf', $show) }}" target="_blank" class="inline-flex items-center justify-center rounded-full bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-500">
                    Abrir PDF
                </a>
                <a href="{{ route('shows.pdf', [$show, 'disposition' => 'download']) }}" class="inline-flex items-center justify-center rounded-full border border-rose-200 px-4 py-2.5 text-sm font-semibold text-rose-700 transition hover:bg-rose-50">
                    Descargar PDF
                </a>
                <a href="{{ route('shows.edit', $show) }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    Editar bolo
                </a>
                <a href="{{ route('shows.index') }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Volver a bolos
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <x-status-message />

            <div class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
                <section class="space-y-6">
                    @if ($alerts !== [])
                        <div class="rounded-[2rem] border border-amber-200 bg-amber-50 p-6 shadow-sm">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-700">Alertas</p>
                                    <h3 class="mt-1 text-lg font-semibold text-amber-950">Revisiones pendientes para este bolo</h3>
                                </div>
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">{{ count($alerts) }}</span>
                            </div>
                            <div class="mt-4 space-y-3">
                                @foreach ($alerts as $alert)
                                    <div class="rounded-2xl border px-4 py-4 {{ $alert['severity'] === 'danger' ? 'border-rose-200 bg-rose-50' : 'border-amber-200 bg-white/70' }}">
                                        <p class="text-sm font-semibold {{ $alert['severity'] === 'danger' ? 'text-rose-900' : 'text-amber-950' }}">{{ $alert['title'] }}</p>
                                        <p class="mt-1 text-sm {{ $alert['severity'] === 'danger' ? 'text-rose-700' : 'text-amber-800' }}">{{ $alert['message'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $statusOptions[$show->status] ?? $show->status }}</span>
                            <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-medium text-sky-700">{{ $show->tour?->name ?: 'Sin gira' }}</span>
                        </div>

                        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            @foreach ([
                                'load_in_at' => 'Montaje',
                                'meal_at' => 'Comida',
                                'soundcheck_at' => 'Pruebas',
                                'doors_at' => 'Puertas',
                                'show_at' => 'Show',
                                'show_end_at' => 'Fin show',
                                'load_out_at' => 'Desmontaje',
                            ] as $field => $label)
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $label }}</p>
                                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $show->getRawOriginal($field) ?: '-' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-6">
                        @foreach ([
                            'lighting' => 'Iluminacion',
                            'sound' => 'Sonido',
                            'space' => 'Espacio / venue',
                            'general' => 'Notas generales',
                        ] as $prefix => $label)
                            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                                <div class="flex items-center justify-between gap-4">
                                    <h3 class="text-lg font-semibold text-slate-900">{{ $label }}</h3>
                                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $show->{$prefix.'_validated'} ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $show->{$prefix.'_validated'} ? 'Validado' : 'Pendiente' }}
                                    </span>
                                </div>
                                <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600 [&_p]:my-0 [&_strong]:font-semibold [&_strong]:text-slate-900 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5">
                                    {!! $show->{$prefix.'_notes'}
                                        ? \Illuminate\Support\Str::markdown($show->{$prefix.'_notes'}, [
                                            'html_input' => 'strip',
                                            'allow_unsafe_links' => false,
                                        ])
                                        : '<p>Sin notas todavia.</p>' !!}
                                </div>
                                @include('shows.partials.section-chat', [
                                    'section' => $prefix,
                                    'messages' => $sectionMessages->get($prefix, collect()),
                                    'action' => route('shows.section-messages.store', $show),
                                ])
                            </div>
                        @endforeach
                    </div>
                </section>

                <aside class="space-y-6">
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">Contacto</h3>
                        <div class="mt-4 space-y-2 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-900">Nombre:</span> {{ $show->contact_name ?: '-' }}</p>
                            <p><span class="font-semibold text-slate-900">Rol:</span> {{ $show->contact_role ?: '-' }}</p>
                            <p><span class="font-semibold text-slate-900">Telefono:</span> {{ $show->contact_phone ?: '-' }}</p>
                            <p><span class="font-semibold text-slate-900">Email:</span> {{ $show->contact_email ?: '-' }}</p>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">Ruta al venue</h3>
                                <p class="mt-1 text-sm text-slate-500">Trayecto estimado desde el origen configurado para este bolo.</p>
                            </div>
                            @if (! empty($travelRoute['available']))
                                <a href="{{ $travelRoute['directions_url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-full border border-sky-200 px-3 py-2 text-xs font-semibold text-sky-700 transition hover:bg-sky-50">
                                    Abrir ruta
                                </a>
                            @endif
                        </div>

                        <div class="mt-4 space-y-4">
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Modo de viaje</p>
                                    <p class="mt-2 text-sm font-medium text-slate-900">{{ $travelModeOptions[$show->travel_mode ?: 'van'] ?? ($show->travel_mode ?: 'van') }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Origen</p>
                                    <p class="mt-2 text-sm font-medium text-slate-900">{{ $travelRoute['origin'] ?: 'Pendiente' }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Destino</p>
                                    <p class="mt-2 text-sm font-medium text-slate-900">{{ $travelRoute['destination'] ?: 'Pendiente' }}</p>
                                </div>
                            </div>

                            @if (! empty($travelRoute['available']))
                                <link
                                    rel="stylesheet"
                                    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
                                    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
                                    crossorigin=""
                                >

                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div class="rounded-2xl bg-sky-50 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-600">Tiempo estimado</p>
                                        <p class="mt-2 text-xl font-semibold text-slate-900">{{ $travelRoute['duration_text'] }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Distancia</p>
                                        <p class="mt-2 text-xl font-semibold text-slate-900">{{ $travelRoute['distance_text'] }}</p>
                                    </div>
                                </div>

                                <div class="overflow-hidden rounded-[1.5rem] border border-slate-200">
                                    <div
                                        id="show-route-map"
                                        class="w-full"
                                        style="height: 22rem;"
                                        data-route-geometry='@json($travelRoute["geometry"])'
                                        data-origin-point='@json($travelRoute["origin_point"])'
                                        data-destination-point='@json($travelRoute["destination_point"])'
                                    ></div>
                                </div>

                                <script
                                    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                                    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
                                    crossorigin=""
                                ></script>
                                <script>
                                    (() => {
                                        const initRouteMap = () => {
                                            const mapElement = document.getElementById('show-route-map');

                                            if (!mapElement || typeof L === 'undefined' || mapElement.dataset.mapReady === '1') {
                                                return;
                                            }

                                            const geometry = JSON.parse(mapElement.dataset.routeGeometry || '{}');
                                            const originPoint = JSON.parse(mapElement.dataset.originPoint || '{}');
                                            const destinationPoint = JSON.parse(mapElement.dataset.destinationPoint || '{}');

                                            if (!Array.isArray(geometry.coordinates) || geometry.coordinates.length === 0) {
                                                return;
                                            }

                                            mapElement.dataset.mapReady = '1';

                                            const map = L.map(mapElement, { scrollWheelZoom: false });

                                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                maxZoom: 18,
                                                attribution: '&copy; OpenStreetMap',
                                            }).addTo(map);

                                            const routeCoordinates = geometry.coordinates.map(([lng, lat]) => [lat, lng]);
                                            const routeLine = L.polyline(routeCoordinates, {
                                                color: '#2563eb',
                                                weight: 5,
                                                opacity: 0.85,
                                            }).addTo(map);

                                            if (originPoint.lat && originPoint.lon) {
                                                L.marker([originPoint.lat, originPoint.lon]).addTo(map).bindPopup('Origen');
                                            }

                                            if (destinationPoint.lat && destinationPoint.lon) {
                                                L.marker([destinationPoint.lat, destinationPoint.lon]).addTo(map).bindPopup('Destino');
                                            }

                                            map.fitBounds(routeLine.getBounds(), { padding: [24, 24] });
                                        };

                                        if (document.readyState === 'loading') {
                                            document.addEventListener('DOMContentLoaded', initRouteMap, { once: true });
                                        } else {
                                            initRouteMap();
                                        }
                                    })();
                                </script>

                            @elseif (($travelRoute['reason'] ?? null) === 'plane_mode')
                                <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-800">
                                    El modo de viaje es avion. No se calcula ruta por carretera, pero puedes seguir usando origen y destino como referencia logistica.
                                </div>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div class="rounded-2xl bg-white p-4 ring-1 ring-sky-100">
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Origen vuelo</p>
                                        <p class="mt-2 text-sm font-medium text-slate-900">{{ $show->flight_origin ?: 'Pendiente' }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-white p-4 ring-1 ring-sky-100">
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Destino vuelo</p>
                                        <p class="mt-2 text-sm font-medium text-slate-900">{{ $show->flight_destination ?: 'Pendiente' }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-white p-4 ring-1 ring-sky-100">
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Duracion estimada</p>
                                        <p class="mt-2 text-sm font-medium text-slate-900">{{ $show->flight_duration_estimate ?: 'Pendiente' }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-white p-4 ring-1 ring-sky-100 sm:col-span-2">
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Notas de vuelo y traslados</p>
                                        <div class="mt-2 text-sm leading-6 text-slate-700 whitespace-pre-line">{{ $show->flight_notes ?: 'Sin notas.' }}</div>
                                    </div>
                                </div>
                            @elseif (($travelRoute['reason'] ?? null) === 'missing_addresses')
                                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                                    Completa el origen de viaje y asegúrate de que el venue tenga texto suficiente para calcular la ruta.
                                </div>
                            @elseif (($travelRoute['reason'] ?? null) === 'geocoding_failed')
                                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                                    No se han encontrado bien las direcciones. Revisa el origen o detalla mejor el venue.
                                </div>
                            @else
                                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                                    No se ha podido calcular la ruta ahora mismo. Inténtalo más tarde.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">Documentos del bolo</h3>
                                <p class="text-sm text-slate-500">En la ficha solo se muestran los documentos ya subidos.</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $show->documents->count() }} docs</span>
                        </div>

                        <div class="mt-6 space-y-4">
                            @forelse ($show->documents as $document)
                                <article class="rounded-2xl border border-slate-200 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $document->document_type }}</p>
                                    <h4 class="mt-2 text-base font-semibold text-slate-900">{{ $document->title }}</h4>
                                    <p class="mt-1 break-all text-sm text-slate-500">{{ $document->original_name }}</p>
                                    <p class="mt-2 text-xs text-slate-400">
                                        {{ $document->uploader?->name ?: 'Sin usuario' }} · {{ $document->created_at->format('d/m/Y H:i') }}
                                    </p>
                                    <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                                        <a href="{{ route('shows.documents.show', [$show, $document]) }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-800">
                                            Abrir o descargar
                                        </a>
                                        <a href="{{ route('shows.edit', $show) }}#show-documents" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                            Gestionar en editar
                                        </a>
                                    </div>
                                </article>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500">
                                    Todavia no hay documentos en este bolo.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">Actividad reciente</h3>
                        <div class="mt-4 space-y-3">
                            @forelse ($show->activityLogs->take(12) as $log)
                                <div class="rounded-2xl border border-slate-200 p-4">
                                    <p class="text-sm font-semibold text-slate-900">{{ $log->detail }}</p>
                                    <p class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-400">{{ $log->actor_name ?: 'Sistema' }} · {{ $log->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500">
                                    Aun no hay actividad para este bolo.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
