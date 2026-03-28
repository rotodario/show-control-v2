<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ __('ui.production') }}</p>
                <h2 class="text-2xl font-semibold text-slate-900">{{ __('ui.shows_calendar_title') }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ __('ui.shows_calendar_description') }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('shows.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    {{ __('ui.view_list') }}
                </a>
                <a href="{{ route('shows.create') }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                    {{ __('ui.new_show') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <x-status-message />

            <form method="GET" action="{{ route('shows.calendar') }}" class="rounded-[2rem] border border-slate-200 bg-white p-4 shadow-sm">
                <div class="grid gap-4 lg:grid-cols-[1fr_220px_220px_auto] lg:items-end">
                    <div>
                        <x-input-label for="calendar_tour_filter" :value="__('ui.filter_by_tour')" />
                        <select id="calendar_tour_filter" name="tour_id" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                            <option value="">{{ __('ui.all_feminine') }}</option>
                            @foreach ($tours as $tour)
                                <option value="{{ $tour->id }}" @selected((string) $selectedTourId === (string) $tour->id)>{{ $tour->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="calendar_month" :value="__('ui.month')" />
                        <x-text-input id="calendar_month" name="month" type="month" class="mt-1 block w-full" :value="$currentMonth->format('Y-m')" />
                    </div>
                    <div>
                        <x-input-label for="calendar_date" :value="__('ui.day_in_agenda')" />
                        <x-text-input id="calendar_date" name="date" type="date" class="mt-1 block w-full" :value="$selectedDate->format('Y-m-d')" />
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-500">
                        {{ __('ui.apply') }}
                    </button>
                </div>
            </form>

            <div class="space-y-6">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ __('ui.active_month') }}</p>
                            <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ $currentMonth->translatedFormat('F Y') }}</h3>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('shows.calendar', ['month' => $previousMonth, 'date' => $selectedDate->copy()->subMonthNoOverflow()->format('Y-m-d'), 'tour_id' => $selectedTourId]) }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                {{ __('ui.previous_month') }}
                            </a>
                            <a href="{{ route('shows.calendar', ['month' => now()->format('Y-m'), 'date' => now()->format('Y-m-d'), 'tour_id' => $selectedTourId]) }}" class="inline-flex items-center justify-center rounded-full border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 transition hover:bg-sky-100">
                                {{ __('ui.today') }}
                            </a>
                            <a href="{{ route('shows.calendar', ['month' => $nextMonth, 'date' => $selectedDate->copy()->addMonthNoOverflow()->format('Y-m-d'), 'tour_id' => $selectedTourId]) }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                {{ __('ui.next_month') }}
                            </a>
                        </div>
                    </div>

                    <div class="mt-6">
                        <div class="w-full overflow-hidden rounded-[1.25rem] border border-slate-200">
                            <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50">
                                @foreach ($weekdays as $weekday)
                                    <div class="px-1 py-2 text-center text-[9px] font-semibold uppercase tracking-[0.08em] text-slate-500 sm:px-2 sm:text-[10px] lg:px-4 lg:py-3 lg:text-xs lg:tracking-[0.22em]">{{ $weekday }}</div>
                                @endforeach
                            </div>

                            <div class="grid grid-cols-7">
                                @foreach ($calendarDays as $day)
                                    <a href="{{ route('shows.calendar', ['month' => $currentMonth->format('Y-m'), 'date' => $day['date']->format('Y-m-d'), 'tour_id' => $selectedTourId]) }}"
                                       class="min-h-[78px] border-b border-r border-slate-200 p-1 align-top transition sm:min-h-[96px] sm:p-1.5 lg:min-h-[165px] lg:p-3 {{ $day['isSelected'] ? 'bg-sky-50' : 'bg-white hover:bg-slate-50' }} {{ $day['isCurrentMonth'] ? '' : 'bg-slate-200/70 text-slate-500' }}">
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-semibold sm:h-6 sm:w-6 sm:text-[11px] lg:h-9 lg:w-9 lg:text-sm {{ $day['isToday'] ? 'bg-slate-900 text-white' : ($day['isSelected'] ? 'bg-sky-600 text-white' : 'text-slate-900') }}">
                                                {{ $day['date']->day }}
                                            </span>
                                            @if ($day['shows']->isNotEmpty())
                                                <span class="text-[8px] font-semibold uppercase tracking-[0.04em] text-slate-400 sm:text-[9px] lg:text-[11px] lg:tracking-[0.16em]">{{ $day['shows']->count() }}</span>
                                            @endif
                                        </div>

                                        <div class="mt-1.5 space-y-1 sm:mt-2 sm:space-y-1 lg:mt-3 lg:space-y-1.5">
                                            @foreach ($day['shows']->take(1) as $show)
                                                <div class="rounded-md px-1 py-1 text-[9px] sm:px-1.5 sm:text-[10px] lg:px-2 lg:py-1.5 lg:text-xs {{ count($showAlerts[$show->id] ?? []) > 0 ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-800' }}">
                                                    <div class="flex items-center">
                                                        @if ($show->tour)
                                                            <span class="inline-flex max-w-full shrink-0 items-center rounded-full px-1.5 py-0.5 font-semibold text-white lg:px-2" style="background-color: {{ $show->tour->color }}">
                                                                <span class="truncate">{{ $show->tour->name }}</span>
                                                            </span>
                                                        @else
                                                            <span class="truncate font-semibold text-slate-500">{{ __('ui.no_tour') }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="mt-0.5 hidden items-center gap-1 sm:flex lg:gap-1.5">
                                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full lg:h-2 lg:w-2 {{ $show->currentStatusDotClasses() }}"></span>
                                                        <p class="truncate">{{ $show->city }}</p>
                                                    </div>
                                                </div>
                                            @endforeach

                                            @if ($day['shows']->count() > 1)
                                                <p class="px-0.5 text-[9px] font-semibold text-slate-500 sm:px-1 sm:text-[10px] lg:text-xs">+{{ $day['shows']->count() - 1 }}</p>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between xl:flex-col xl:items-start">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ __('ui.agenda') }}</p>
                            <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ $selectedDate->translatedFormat('l d F Y') }}</h3>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-600">{{ __('ui.shows_count', ['count' => $agendaShows->count()]) }}</span>
                    </div>

                    <div class="mt-6 grid gap-4">
                        @forelse ($agendaShows as $show)
                            <article class="rounded-[1.75rem] border border-slate-200 bg-slate-50/70 p-5">
                                <div class="flex flex-col gap-4">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="truncate text-lg font-semibold text-slate-900">{{ $show->name }}</h4>
                                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $show->currentStatusBadgeClasses() }}">
                                                {{ $show->translatedCurrentStatus() }}
                                            </span>
                                        </div>
                                        <p class="mt-2 text-sm text-slate-500">{{ $show->city }} · {{ $show->venue ?: __('ui.pending_venue') }}</p>
                                        @if ($show->tour)
                                            <p class="mt-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $show->tour->name }}</p>
                                        @endif
                                        <div class="mt-4 grid gap-2 sm:grid-cols-2">
                                            @foreach ([
                                                'load_in_at' => __('ui.load_in'),
                                                'show_at' => 'Show',
                                            ] as $field => $label)
                                                <div class="rounded-2xl bg-white px-3 py-3">
                                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $label }}</p>
                                                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $show->getRawOriginal($field) ?: '-' }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('shows.show', $show) }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                                            {{ __('ui.open_record') }}
                                        </a>
                                        <a href="{{ route('shows.edit', $show) }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                            {{ __('ui.edit') }}
                                        </a>
                                    </div>
                                </div>

                                <div class="mt-4 flex flex-wrap items-center gap-2">
                                    @foreach ([
                                        'lighting_validated' => __('ui.lighting'),
                                        'sound_validated' => __('ui.sound'),
                                        'space_validated' => __('ui.space'),
                                        'general_validated' => __('ui.general'),
                                    ] as $field => $label)
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $show->{$field} ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ $label }} {{ $show->{$field} ? __('ui.ok') : __('ui.pending') }}
                                        </span>
                                    @endforeach

                                    @if (count($showAlerts[$show->id] ?? []) > 0)
                                        <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">
                                            {{ __('ui.alerts_count', ['count' => count($showAlerts[$show->id])]) }}
                                        </span>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="rounded-[1.75rem] border border-dashed border-slate-300 p-10 text-center text-sm text-slate-500">
                                {{ __('ui.no_agenda_shows_for_day') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
