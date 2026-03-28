<x-public-access-layout>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @php
            $visibleShowsCount = $shows->total();
            $visibleAlertsCount = collect($showAlerts)->sum(fn ($alerts) => count($alerts));
            $visibleUnreadCount = collect($unreadMessageCounts)->sum();
        @endphp

        @if (session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex min-w-0 items-center gap-4">
                <div class="flex h-12 min-w-[3.5rem] items-center justify-center rounded-2xl bg-slate-900 px-3 text-sm font-black tracking-[0.18em] text-white shadow-sm">
                    SC
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ __('ui.shared_access') }}</p>
                    <p class="text-lg font-semibold text-slate-900">Show Control</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 lg:flex-1 lg:justify-center">
                <span class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200">
                    {{ __('ui.shows_count', ['count' => $visibleShowsCount]) }}
                </span>
                <span class="rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 shadow-sm ring-1 ring-amber-200">
                    {{ __('ui.alerts_count', ['count' => $visibleAlertsCount]) }}
                </span>
                <span class="rounded-full bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700 shadow-sm ring-1 ring-sky-200">
                    {{ __('ui.new_messages_count', ['count' => $visibleUnreadCount]) }}
                </span>
            </div>
            <div class="max-w-full rounded-[2rem] border border-slate-200 bg-white px-6 py-6 text-center shadow-sm lg:flex-none" style="width: 100%; max-width: 17rem;">
                    <h1 class="text-2xl font-semibold text-slate-900">{{ $grant->label ?: __('ui.shared_link') }}</h1>
                    <p class="mt-1 text-sm font-medium text-slate-500">{{ \App\Models\SharedAccess::translatedRoleLabel($grant->role) }}</p>
                    <div class="mt-3 flex justify-center">
                        @if ($grant->tour)
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold text-white shadow-sm" style="background-color: {{ $grant->tour->color }}">
                                {{ $grant->tour->name }}
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                {{ __('ui.all_tours') }}
                            </span>
                        @endif
                    </div>
            </div>
        </div>

        @if ($permissions['create_shows'])
            <div class="mt-6 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">{{ __('ui.create_show') }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ __('ui.available_for_project_manager') }}</p>

                <div class="mt-6">
                    <x-validation-summary />
                </div>

                <form method="POST" action="{{ route('public-access.shows.store', $grant->token) }}" class="mt-6 space-y-8">
                    @csrf

                    <div class="grid gap-4 lg:grid-cols-2">
                        @if (! $grant->tour_id)
                            <div>
                                <x-input-label for="tour_id" :value="__('ui.tour')" />
                                <select id="tour_id" name="tour_id" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                    <option value="">{{ __('ui.no_tour') }}</option>
                                    @foreach ($tours as $tour)
                                        <option value="{{ $tour->id }}" @selected((string) old('tour_id') === (string) $tour->id)>{{ $tour->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('tour_id')" />
                            </div>
                        @endif
                        <div>
                            <x-input-label for="name" :value="__('ui.show_name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>
                        <div>
                            <x-input-label for="date" :value="__('ui.date')" />
                            <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="old('date')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('date')" />
                        </div>
                        <div>
                            <x-input-label for="status" :value="__('ui.default_show_status')" />
                            <select id="status" name="status" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', array_key_first($statusOptions)) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>
                        <div>
                            <x-input-label for="city" :value="__('ui.city')" />
                            <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('city')" />
                        </div>
                        <div>
                            <x-input-label for="venue" :value="__('ui.venue')" />
                            <x-text-input id="venue" name="venue" type="text" class="mt-1 block w-full" :value="old('venue')" />
                            <x-input-error class="mt-2" :messages="$errors->get('venue')" />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            {{ __('ui.save_show') }}
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <div class="mt-6 grid gap-4">
            @forelse ($shows as $show)
                <a href="{{ route('public-access.shows.show', [$grant->token, $show]) }}" class="block w-full overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm transition hover:border-sky-300 hover:bg-sky-50/40">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="truncate text-xl font-semibold text-slate-900">{{ $show->name }}</h2>
                                <span class="rounded-full px-3 py-1 text-xs font-medium {{ $show->currentStatusBadgeClasses() }}">{{ $show->translatedCurrentStatus() }}</span>
                                @if ($show->tour)
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold text-white shadow-sm" style="background-color: {{ $show->tour->color }}">
                                        {{ $show->tour->name }}
                                    </span>
                                @endif
                                @if (($showAlerts[$show->id] ?? []) !== [])
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">{{ __('ui.alerts_count', ['count' => count($showAlerts[$show->id])]) }}</span>
                                @endif
                                @if (($unreadMessageCounts[$show->id] ?? 0) > 0)
                                    <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">{{ __('ui.new_messages_count', ['count' => $unreadMessageCounts[$show->id]]) }}</span>
                                @endif
                            </div>
                            <p class="mt-2 text-sm text-slate-500">{{ $show->date->format('d/m/Y') }} &middot; {{ $show->city }} &middot; {{ $show->venue ?: __('ui.pending_venue') }}</p>
                            @unless ($show->tour)
                                <p class="mt-2 text-sm text-slate-600">{{ __('ui.no_tour') }}</p>
                            @endunless
                            <div class="mt-4 flex flex-wrap gap-2 text-xs font-medium text-slate-500">
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
                        <div class="text-xs text-slate-400">
                            {{ __('ui.open_record') }}
                        </div>
                    </div>
                </a>
            @empty
                <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500">
                    {{ __('ui.no_visible_shows_for_access') }}
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $shows->links() }}
        </div>
    </div>
</x-public-access-layout>
