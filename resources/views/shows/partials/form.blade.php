@csrf

<div x-data="{ travelMode: '{{ old('travel_mode', $show->travel_mode ?: 'van') }}', calculatingRoute: false }" class="space-y-8">
    <div class="grid gap-4 lg:grid-cols-2">
        <div>
            <x-input-label for="name" :value="__('ui.show_name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $show->name)" required />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>
        <div>
            <x-input-label for="tour_id" :value="__('ui.tours')" />
            <select id="tour_id" name="tour_id" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                <option value="">{{ __('ui.no_tour') }}</option>
                @foreach ($tours as $tour)
                    <option value="{{ $tour->id }}" @selected((string) old('tour_id', $show->tour_id) === (string) $tour->id)>{{ $tour->name }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('tour_id')" />
        </div>
        <div>
            <x-input-label for="date" :value="__('ui.date')" />
            <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="old('date', $show->date?->format('Y-m-d') ?? $show->date)" required />
            <x-input-error class="mt-2" :messages="$errors->get('date')" />
        </div>
        <div>
            <x-input-label for="status" :value="__('ui.status')" />
            <select id="status" name="status" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                @foreach ($statusOptions as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $show->status) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('status')" />
        </div>
        <div>
            <x-input-label for="city" :value="__('ui.city')" />
            <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $show->city)" required />
            <x-input-error class="mt-2" :messages="$errors->get('city')" />
        </div>
        <div>
            <x-input-label for="venue" :value="__('ui.venue')" />
            <x-text-input id="venue" name="venue" type="text" class="mt-1 block w-full" :value="old('venue', $show->venue)" />
            <x-input-error class="mt-2" :messages="$errors->get('venue')" />
        </div>
        <div class="lg:col-span-2">
            <x-input-label for="travel_origin" :value="__('ui.travel_origin')" />
            <x-text-input id="travel_origin" name="travel_origin" type="text" class="mt-1 block w-full" :value="old('travel_origin', $show->travel_origin)" />
            <p class="mt-2 text-sm text-slate-500">{{ __('ui.travel_origin_help') }}</p>
            <x-input-error class="mt-2" :messages="$errors->get('travel_origin')" />
        </div>
        <div class="lg:col-span-2">
            <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
                <div>
                    <x-input-label for="travel_mode" :value="__('ui.travel_mode')" />
                    <select id="travel_mode" name="travel_mode" x-model="travelMode" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                        @foreach ($travelModeOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('travel_mode', $show->travel_mode ?: 'van') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('travel_mode')" />
                </div>

                @if ($show->exists)
                    <button
                        type="submit"
                        formaction="{{ route('shows.preview-route', $show) }}"
                        formmethod="POST"
                        x-on:click="calculatingRoute = true"
                        class="inline-flex items-center justify-center rounded-full border border-sky-300 px-5 py-3 text-sm font-semibold text-sky-700 transition hover:bg-sky-50"
                    >
                        <span x-text="calculatingRoute ? '{{ __('ui.calculating') }}' : '{{ __('ui.calculate_route') }}'"></span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div x-show="travelMode === 'plane'" x-cloak class="rounded-[2rem] border border-sky-200 bg-sky-50 p-6">
        <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.flight_data') }}</h3>
        <p class="mt-2 text-sm text-slate-600">{{ __('ui.flight_data_help') }}</p>

        <div class="mt-5 grid gap-4 lg:grid-cols-2">
            <div>
                <x-input-label for="flight_origin" :value="__('ui.flight_origin')" />
                <x-text-input id="flight_origin" name="flight_origin" type="text" class="mt-1 block w-full" :value="old('flight_origin', $show->flight_origin)" />
                <x-input-error class="mt-2" :messages="$errors->get('flight_origin')" />
            </div>
            <div>
                <x-input-label for="flight_destination" :value="__('ui.flight_destination')" />
                <x-text-input id="flight_destination" name="flight_destination" type="text" class="mt-1 block w-full" :value="old('flight_destination', $show->flight_destination)" />
                <x-input-error class="mt-2" :messages="$errors->get('flight_destination')" />
            </div>
            <div>
                <x-input-label for="flight_duration_estimate" :value="__('ui.estimated_duration')" />
                <x-text-input id="flight_duration_estimate" name="flight_duration_estimate" type="text" class="mt-1 block w-full" :value="old('flight_duration_estimate', $show->flight_duration_estimate)" />
                <x-input-error class="mt-2" :messages="$errors->get('flight_duration_estimate')" />
            </div>
            <div class="lg:col-span-2">
                <x-input-label for="flight_notes" :value="__('ui.flight_notes_and_transfers')" />
                <textarea id="flight_notes" name="flight_notes" rows="4" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">{{ old('flight_notes', $show->flight_notes) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('flight_notes')" />
            </div>
        </div>
    </div>

    @if (! empty($travelPreview ?? null))
        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ __('ui.preview') }}</p>
                    <h3 class="mt-1 text-lg font-semibold text-slate-900">{{ __('ui.route_calculation_result') }}</h3>
                </div>
                @if (! empty($travelPreview['available']))
                    <a href="{{ $travelPreview['directions_url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-full border border-sky-200 px-4 py-2 text-sm font-semibold text-sky-700 transition hover:bg-sky-50">
                        {{ __('ui.open_route') }}
                    </a>
                @endif
            </div>

            <div class="mt-5 grid gap-4 lg:grid-cols-2">
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.origin') }}</p>
                    <p class="mt-2 text-sm font-medium text-slate-900">{{ $travelPreview['origin'] ?: __('ui.pending') }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.destination') }}</p>
                    <p class="mt-2 text-sm font-medium text-slate-900">{{ $travelPreview['destination'] ?: __('ui.pending') }}</p>
                </div>
            </div>

            @if (! empty($travelPreview['available']))
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-sky-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-600">{{ __('ui.estimated_time') }}</p>
                        <p class="mt-2 text-xl font-semibold text-slate-900">{{ $travelPreview['duration_text'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.distance') }}</p>
                        <p class="mt-2 text-xl font-semibold text-slate-900">{{ $travelPreview['distance_text'] }}</p>
                    </div>
                </div>
            @elseif (($travelPreview['reason'] ?? null) === 'plane_mode')
                <div class="mt-4 rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-800">
                    {{ __('ui.plane_mode_route_notice') }}
                </div>
            @elseif (($travelPreview['reason'] ?? null) === 'missing_addresses')
                <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    {{ __('ui.missing_addresses_notice') }}
                </div>
            @elseif (($travelPreview['reason'] ?? null) === 'geocoding_failed')
                <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    {{ __('ui.geocoding_failed_notice') }}
                </div>
            @else
                <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                    {{ __('ui.route_unavailable_notice') }}
                </div>
            @endif
        </div>
    @endif

    <div>
        <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.schedules') }}</h3>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                'load_in_at' => __('ui.load_in'),
                'meal_at' => __('ui.meal'),
                'soundcheck_at' => __('ui.soundcheck'),
                'doors_at' => __('ui.doors'),
                'show_at' => 'Show',
                'show_end_at' => __('ui.show_end'),
                'load_out_at' => __('ui.load_out'),
            ] as $field => $label)
                <div>
                    <x-input-label :for="$field" :value="$label" />
                    <x-text-input :id="$field" :name="$field" type="time" class="mt-1 block w-full" :value="old($field, $show->getRawOriginal($field))" />
                    <x-input-error class="mt-2" :messages="$errors->get($field)" />
                </div>
            @endforeach
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.contact') }}</h3>
        <div class="mt-4 grid gap-4 lg:grid-cols-2">
            <div>
                <x-input-label for="contact_name" :value="__('ui.name')" />
                <x-text-input id="contact_name" name="contact_name" type="text" class="mt-1 block w-full" :value="old('contact_name', $show->contact_name)" />
                <x-input-error class="mt-2" :messages="$errors->get('contact_name')" />
            </div>
            <div>
                <x-input-label for="contact_role" :value="__('ui.role')" />
                <x-text-input id="contact_role" name="contact_role" type="text" class="mt-1 block w-full" :value="old('contact_role', $show->contact_role)" />
                <x-input-error class="mt-2" :messages="$errors->get('contact_role')" />
            </div>
            <div>
                <x-input-label for="contact_phone" :value="__('ui.phone')" />
                <x-text-input id="contact_phone" name="contact_phone" type="text" class="mt-1 block w-full" :value="old('contact_phone', $show->contact_phone)" />
                <x-input-error class="mt-2" :messages="$errors->get('contact_phone')" />
            </div>
            <div>
                <x-input-label for="contact_email" :value="__('ui.email')" />
                <x-text-input id="contact_email" name="contact_email" type="email" class="mt-1 block w-full" :value="old('contact_email', $show->contact_email)" />
                <x-input-error class="mt-2" :messages="$errors->get('contact_email')" />
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        @foreach ([
            'lighting' => __('ui.lighting'),
            'sound' => __('ui.sound'),
            'space' => __('ui.space_venue'),
            'general' => __('ui.general_notes'),
        ] as $prefix => $label)
            <div class="rounded-3xl bg-slate-50 p-5">
                <div class="flex items-center justify-between gap-4">
                    <h3 class="text-lg font-semibold text-slate-900">{{ $label }}</h3>
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-600">
                        <input type="checkbox" name="{{ $prefix }}_validated" value="1" class="rounded border-slate-300 text-sky-600 shadow-sm focus:ring-sky-500" @checked(old($prefix.'_validated', $show->{$prefix.'_validated'}))>
                        {{ __('ui.validated') }}
                    </label>
                </div>
                <textarea name="{{ $prefix }}_notes" rows="5" class="mt-4 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">{{ old($prefix.'_notes', $show->{$prefix.'_notes'}) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get($prefix.'_notes')" />
            </div>
        @endforeach
    </div>
</div>

<div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ $show->exists ? route('shows.show', $show) : route('shows.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
        {{ __('ui.cancel') }}
    </a>
    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
        {{ __('ui.save_show') }}
    </button>
</div>
