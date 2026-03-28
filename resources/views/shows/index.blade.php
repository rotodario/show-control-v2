<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500 dark:text-slate-400">{{ __('ui.production') }}</p>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">{{ __('ui.shows') }}</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('ui.shows_index_description') }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('shows.map', request()->only('tour_id')) }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                    {{ __('ui.view_map') }}
                </a>
                <a href="{{ route('shows.calendar') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                    {{ __('ui.view_calendar') }}
                </a>
                <a href="{{ route('shows.create') }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                    {{ __('ui.new_show') }}
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

            <div class="grid gap-4">
                @forelse ($shows as $show)
                    <article class="relative rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm transition hover:border-sky-300 hover:bg-sky-50/40 dark:border-slate-700 dark:bg-slate-900/90 dark:hover:bg-slate-900">
                        <a href="{{ route('shows.show', $show) }}" class="absolute inset-0 rounded-[2rem]" aria-label="{{ __('ui.open') }} {{ $show->name }}"></a>
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0 pointer-events-none">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="truncate text-xl font-semibold text-slate-900 dark:text-slate-100">{{ $show->name }}</h3>
                                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $show->currentStatusBadgeClasses() }}">{{ $show->translatedCurrentStatus() }}</span>
                                    @if ($show->tour)
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold text-white shadow-sm" style="background-color: {{ $show->tour->color }}">
                                            {{ $show->tour->name }}
                                        </span>
                                    @endif
                                    @if (($showAlerts[$show->id] ?? []) !== [])
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                            {{ __('ui.alerts_count', ['count' => count($showAlerts[$show->id])]) }}
                                        </span>
                                    @endif
                                    @if (($unreadMessageCounts[$show->id] ?? 0) > 0)
                                        <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">
                                            {{ __('ui.new_messages_count', ['count' => $unreadMessageCounts[$show->id]]) }}
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $show->date->format('d/m/Y') }} · {{ $show->city }} · {{ $show->venue ?: __('ui.pending_venue') }}</p>
                                <div class="mt-4 flex flex-wrap gap-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                                    @foreach ([
                                        'lighting_validated' => __('ui.lighting'),
                                        'sound_validated' => __('ui.sound'),
                                        'space_validated' => __('ui.space'),
                                        'general_validated' => __('ui.general'),
                                    ] as $field => $label)
                                        <span class="rounded-full px-3 py-1 {{ $show->{$field} ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ $label }} {{ $show->{$field} ? __('ui.ok') : __('ui.pending') }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="relative z-20 flex flex-col gap-3 sm:flex-row">
                                <a href="{{ route('shows.show', $show) }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                                    {{ __('ui.open') }}
                                </a>
                                <a href="{{ route('shows.edit', $show) }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                    {{ __('ui.edit') }}
                                </a>
                                <form method="POST" action="{{ route('shows.destroy', $show) }}" onsubmit="return confirm('{{ __('ui.confirm_delete_show') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-full border border-rose-200 px-4 py-2.5 text-sm font-semibold text-rose-700 transition hover:bg-rose-50 dark:border-rose-900 dark:text-rose-300 dark:hover:bg-rose-950/50 sm:w-auto">
                                        {{ __('ui.delete') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900/90 dark:text-slate-400">
                        {{ __('ui.no_shows_yet') }}
                    </div>
                @endforelse
            </div>

            {{ $shows->links() }}
        </div>
    </div>
</x-app-layout>
