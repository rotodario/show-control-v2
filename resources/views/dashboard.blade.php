<x-app-layout>
    <x-slot name="header">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Operacion</p>
                    <h2 class="text-2xl font-semibold text-slate-900">Panel de control</h2>
                </div>
                <div class="flex flex-wrap items-center gap-2 lg:flex-1 lg:justify-center">
                    <span class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200">{{ $tourCount }} giras</span>
                    <span class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200">{{ $showCount }} bolos</span>
                    <span class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200">{{ $tourDocumentCount }} docs gira</span>
                    <span class="rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 shadow-sm ring-1 ring-amber-200">{{ $alertCount }} alertas</span>
                    <span class="rounded-full bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700 shadow-sm ring-1 ring-sky-200">{{ $unreadMessageTotal }} mensajes nuevos</span>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row lg:ml-auto">
                    @can('manage shows')
                        <a href="{{ route('shows.create') }}" class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                            Nuevo bolo
                        </a>
                    @endcan
                    @can('manage tours')
                        <a href="{{ route('tours.create') }}" class="inline-flex items-center rounded-full bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-500">
                            Nueva gira
                        </a>
                    @endcan
                </div>
            </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Ultimas giras</h3>
                        <p class="text-sm text-slate-500">Base inicial lista para seguir con bolos, actividad, alertas y PDF.</p>
                    </div>
                    @can('manage tours')
                        <a href="{{ route('tours.index') }}" class="text-sm font-semibold text-sky-700 hover:text-sky-600">Ver todas</a>
                    @endcan
                </div>

                <div class="mt-6 grid gap-4 lg:grid-cols-2">
                    @forelse ($upcomingTours as $tour)
                        <a href="{{ route('tours.show', $tour) }}" class="rounded-2xl border border-slate-200 p-5 transition hover:border-sky-300 hover:bg-sky-50/40">
                            <div class="flex items-start gap-4">
                                <span class="mt-1 h-12 w-3 rounded-full" style="background-color: {{ $tour->color }}"></span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="truncate text-lg font-semibold text-slate-900">{{ $tour->name }}</h4>
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ $tour->shows_count }} bolos</span>
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ $tour->contacts_count }} contactos</span>
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ $tour->documents_count }} docs</span>
                                    </div>
                                    <p class="mt-3 text-sm text-slate-600">{{ $tour->notes ?: 'Sin notas de gira todavia.' }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-sm text-slate-500">
                            No hay giras creadas todavia.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Proximos bolos</h3>
                        <p class="text-sm text-slate-500">Acceso rapido a la produccion diaria.</p>
                    </div>
                    @can('manage shows')
                        <a href="{{ route('shows.index') }}" class="text-sm font-semibold text-sky-700 hover:text-sky-600">Ver bolos</a>
                    @endcan
                </div>

                <div class="mt-6 grid gap-4 lg:grid-cols-2">
                    @forelse ($upcomingShows as $show)
                        <a href="{{ route('shows.show', $show) }}" class="rounded-2xl border border-slate-200 p-5 transition hover:border-sky-300 hover:bg-sky-50/40">
                            <div class="space-y-3">
                                <div class="flex justify-center sm:justify-start">
                                    @if ($show->tour)
                                        <span class="inline-flex shrink-0 whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold text-white shadow-sm" style="background-color: {{ $show->tour->color }}">
                                            {{ $show->tour->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex shrink-0 whitespace-nowrap rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                            Sin gira
                                        </span>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <h4 class="truncate text-lg font-semibold text-slate-900">{{ $show->name }}</h4>
                                    <p class="mt-1 text-sm text-slate-500">{{ $show->date->format('d/m/Y') }} · {{ $show->city }}</p>
                                    <p class="mt-2 text-sm text-slate-600">{{ $show->venue ?: 'Venue pendiente' }}</p>

                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach ([
                                            'lighting_validated' => 'Luces',
                                            'sound_validated' => 'Sonido',
                                            'space_validated' => 'Espacio',
                                            'general_validated' => 'General',
                                        ] as $field => $label)
                                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $show->{$field} ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                                {{ $label }} {{ $show->{$field} ? 'OK' : 'pendiente' }}
                                            </span>
                                        @endforeach
                                    </div>

                                    @if (($showAlerts[$show->id] ?? []) !== [])
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                                {{ count($showAlerts[$show->id]) }} alertas
                                            </span>
                                            @if (($unreadMessageCounts[$show->id] ?? 0) > 0)
                                                <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">
                                                    {{ $unreadMessageCounts[$show->id] }} mensajes nuevos
                                                </span>
                                            @endif
                                        </div>
                                    @elseif (($unreadMessageCounts[$show->id] ?? 0) > 0)
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">
                                                {{ $unreadMessageCounts[$show->id] }} mensajes nuevos
                                            </span>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-sm text-slate-500">
                            No hay bolos creados todavia.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
