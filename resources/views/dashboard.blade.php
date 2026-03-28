<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ __('ui.operation') }}</p>
                <h2 class="text-2xl font-semibold text-slate-900">{{ __('ui.dashboard_title') }}</h2>
            </div>
            <div class="flex flex-wrap items-center gap-2 lg:flex-1 lg:justify-center">
                <span class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200">{{ __('ui.tours_count', ['count' => $tourCount]) }}</span>
                <span class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200">{{ __('ui.shows_count', ['count' => $showCount]) }}</span>
                <span class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200">{{ __('ui.tour_docs_count', ['count' => $tourDocumentCount]) }}</span>
                <span class="rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 shadow-sm ring-1 ring-amber-200">{{ __('ui.alerts_count', ['count' => $alertCount]) }}</span>
                <span class="rounded-full bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700 shadow-sm ring-1 ring-sky-200">{{ __('ui.new_messages_count', ['count' => $unreadMessageTotal]) }}</span>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row lg:ml-auto">
                @can('manage shows')
                    <a href="{{ route('shows.create') }}" class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                        {{ __('ui.new_show') }}
                    </a>
                @endcan
                @can('manage tours')
                    <a href="{{ route('tours.create') }}" class="inline-flex items-center rounded-full bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-500">
                        {{ __('ui.new_tour') }}
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
                        <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.latest_tours') }}</h3>
                        <p class="text-sm text-slate-500">{{ __('ui.latest_tours_help') }}</p>
                    </div>
                    @can('manage tours')
                        <a href="{{ route('tours.index') }}" class="text-sm font-semibold text-sky-700 hover:text-sky-600">{{ __('ui.view_all') }}</a>
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
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ __('ui.shows_count', ['count' => $tour->shows_count]) }}</span>
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ __('ui.contacts_count', ['count' => $tour->contacts_count]) }}</span>
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ __('ui.docs_count', ['count' => $tour->documents_count]) }}</span>
                                    </div>
                                    <p class="mt-3 text-sm text-slate-600">{{ $tour->localizedNotes() ?: __('ui.no_tour_notes_yet') }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-sm text-slate-500">
                            {{ __('ui.no_tours_yet') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.upcoming_shows') }}</h3>
                        <p class="text-sm text-slate-500">{{ __('ui.upcoming_shows_help') }}</p>
                    </div>
                    @can('manage shows')
                        <a href="{{ route('shows.index') }}" class="text-sm font-semibold text-sky-700 hover:text-sky-600">{{ __('ui.view_shows') }}</a>
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
                                            {{ __('ui.no_tour') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="truncate text-lg font-semibold text-slate-900">{{ $show->name }}</h4>
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $show->currentStatusBadgeClasses() }}">
                                            {{ $show->translatedCurrentStatus() }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm text-slate-500">{{ $show->date->format('d/m/Y') }} &middot; {{ $show->city }}</p>
                                    <p class="mt-2 text-sm text-slate-600">{{ $show->venue ?: __('ui.pending_venue') }}</p>

                                    <div class="mt-3 flex flex-wrap gap-2">
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
                                    </div>

                                    @if (($showAlerts[$show->id] ?? []) !== [])
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                                {{ __('ui.alerts_count', ['count' => count($showAlerts[$show->id])]) }}
                                            </span>
                                            @if (($unreadMessageCounts[$show->id] ?? 0) > 0)
                                                <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">
                                                    {{ __('ui.new_messages_count', ['count' => $unreadMessageCounts[$show->id]]) }}
                                                </span>
                                            @endif
                                        </div>
                                    @elseif (($unreadMessageCounts[$show->id] ?? 0) > 0)
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">
                                                {{ __('ui.new_messages_count', ['count' => $unreadMessageCounts[$show->id]]) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-sm text-slate-500">
                            {{ __('ui.no_shows_yet') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
