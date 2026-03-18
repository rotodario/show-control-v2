<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500 dark:text-slate-400">Produccion</p>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Bolos</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Listado operativo para crear y gestionar bolos.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('shows.calendar') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                    Ver calendario
                </a>
                <a href="{{ route('shows.create') }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                    Nuevo bolo
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <x-status-message />

            <form method="GET" action="{{ route('shows.index') }}" class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900/90">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                    <div class="min-w-0 flex-1">
                        <x-input-label for="tour_filter" value="Filtrar por gira" />
                        <select id="tour_filter" name="tour_id" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                            <option value="">Todas</option>
                            @foreach ($tours as $tour)
                                <option value="{{ $tour->id }}" @selected((string) $selectedTourId === (string) $tour->id)>{{ $tour->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-500">
                        Filtrar
                    </button>
                </div>
            </form>

            <div class="grid gap-4">
                @forelse ($shows as $show)
                    <article class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/90">
                        @php
                            $statusClasses = match ($show->status) {
                                'confirmed' => 'bg-emerald-100 text-emerald-700',
                                'closed' => 'bg-slate-200 text-slate-700',
                                'cancelled' => 'bg-rose-100 text-rose-700',
                                default => 'bg-amber-100 text-amber-700',
                            };
                        @endphp
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="truncate text-xl font-semibold text-slate-900 dark:text-slate-100">{{ $show->name }}</h3>
                                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $statusClasses }}">{{ $statusOptions[$show->status] ?? $show->status }}</span>
                                    @if ($show->tour)
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold text-white shadow-sm" style="background-color: {{ $show->tour->color }}">
                                            {{ $show->tour->name }}
                                        </span>
                                    @endif
                                    @if (($showAlerts[$show->id] ?? []) !== [])
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                            {{ count($showAlerts[$show->id]) }} alertas
                                        </span>
                                    @endif
                                    @if (($unreadMessageCounts[$show->id] ?? 0) > 0)
                                        <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">
                                            {{ $unreadMessageCounts[$show->id] }} mensajes nuevos
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $show->date->format('d/m/Y') }} · {{ $show->city }} · {{ $show->venue ?: 'Venue pendiente' }}</p>
                                <div class="mt-4 flex flex-wrap gap-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                                    @foreach ([
                                        'lighting_validated' => 'Luces',
                                        'sound_validated' => 'Sonido',
                                        'space_validated' => 'Espacio',
                                        'general_validated' => 'General',
                                    ] as $field => $label)
                                        <span class="rounded-full px-3 py-1 {{ $show->{$field} ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ $label }} {{ $show->{$field} ? 'OK' : 'pendiente' }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex flex-col gap-3 sm:flex-row">
                                <a href="{{ route('shows.show', $show) }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                                    Abrir
                                </a>
                                <a href="{{ route('shows.edit', $show) }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                    Editar
                                </a>
                                <form method="POST" action="{{ route('shows.destroy', $show) }}" onsubmit="return confirm('¿Eliminar este bolo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-full border border-rose-200 px-4 py-2.5 text-sm font-semibold text-rose-700 transition hover:bg-rose-50 dark:border-rose-900 dark:text-rose-300 dark:hover:bg-rose-950/50 sm:w-auto">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900/90 dark:text-slate-400">
                        Todavia no hay bolos creados.
                    </div>
                @endforelse
            </div>

            {{ $shows->links() }}
        </div>
    </div>
</x-app-layout>
