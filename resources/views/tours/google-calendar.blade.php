<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500 dark:text-slate-400">Giras</p>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Importar desde URL ICS</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Lee eventos desde una URL ICS y crea bolos a partir del formato <span class="font-semibold">Gira - Lugar</span>.</p>
            </div>
            <a href="{{ route('tours.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                Volver a giras
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <x-status-message />

            <section class="grid gap-6 xl:grid-cols-[0.85fr_1.15fr]">
                <aside class="space-y-6">
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/90">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Buscar eventos</h3>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Pega la URL publica o privada del calendario `.ics` y carga los eventos del rango indicado.</p>
                        <form method="GET" action="{{ route('tours.google-calendar.index') }}" class="mt-5 space-y-4">
                            <div>
                                <x-input-label for="ics_url" value="URL ICS" />
                                <x-text-input id="ics_url" name="ics_url" type="url" class="mt-1 block w-full" :value="$icsUrl" placeholder="https://..." />
                                <x-input-error class="mt-2" :messages="$errors->get('ics_url')" />
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="date_from" value="Desde" />
                                    <x-text-input id="date_from" name="date_from" type="date" class="mt-1 block w-full" :value="$dateFrom" />
                                </div>
                                <div>
                                    <x-input-label for="date_to" value="Hasta" />
                                    <x-text-input id="date_to" name="date_to" type="date" class="mt-1 block w-full" :value="$dateTo" />
                                </div>
                            </div>
                            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">
                                Cargar eventos
                            </button>
                        </form>
                    </div>
                </aside>

                <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/90">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Previsualizacion</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">La importacion crea la gira si no existe y anade el bolo solo una vez por `UID` del ICS.</p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $previewEvents->count() }} eventos</span>
                    </div>

                    @if ($previewEvents->isEmpty())
                        <div class="mt-6 rounded-2xl border border-dashed border-slate-300 p-8 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                            Pega la URL ICS y carga el rango para ver los eventos importables.
                        </div>
                    @else
                        <form method="POST" action="{{ route('tours.google-calendar.import') }}" class="mt-6 space-y-4">
                            @csrf
                            <input type="hidden" name="ics_url" value="{{ $icsUrl }}">
                            <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                            <input type="hidden" name="date_to" value="{{ $dateTo }}">

                            <div class="space-y-3">
                                @foreach ($previewEvents as $event)
                                    <label class="flex gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-sky-300 hover:bg-sky-50/40 dark:border-slate-700 dark:hover:border-sky-700 dark:hover:bg-sky-950/20">
                                        <input
                                            type="checkbox"
                                            name="selected_event_ids[]"
                                            value="{{ $event['event_id'] }}"
                                            class="mt-1 rounded border-slate-300 text-sky-600 shadow-sm focus:ring-sky-500"
                                            @checked(! $event['already_imported'])
                                            @disabled($event['already_imported'])
                                        >
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                                <div class="min-w-0">
                                                    <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ $event['summary'] }}</p>
                                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($event['date'])->format('d/m/Y') }} @if($event['show_at']) · {{ substr($event['show_at'], 0, 5) }} @endif</p>
                                                </div>
                                                @if ($event['already_imported'])
                                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">Ya importado</span>
                                                @endif
                                            </div>

                                            <div class="mt-4 grid gap-3 text-sm text-slate-600 sm:grid-cols-3 dark:text-slate-300">
                                                <div>
                                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">Gira</p>
                                                    <p class="mt-1">{{ $event['tour_name'] }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">Bolo</p>
                                                    <p class="mt-1">{{ $event['show_name'] }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">Lugar</p>
                                                    <p class="mt-1">{{ $event['venue'] ?: $event['city'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-sky-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-500">
                                Importar seleccionados
                            </button>
                        </form>
                    @endif
                </section>
            </section>
        </div>
    </div>
</x-app-layout>
