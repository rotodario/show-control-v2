<x-public-access-layout>
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ __('ui.public_show_summary') }}</p>
                    <h1 class="mt-3 text-3xl font-semibold text-slate-900">{{ $show->name }}</h1>
                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $show->currentStatusBadgeClasses() }}">
                            {{ $show->translatedCurrentStatus() }}
                        </span>
                        @if ($show->tour)
                            <span class="rounded-full px-3 py-1 text-xs font-semibold text-white" style="background-color: {{ $show->tour->color ?: '#0f172a' }}">
                                {{ $show->tour->name }}
                            </span>
                        @endif
                    </div>
                    <p class="mt-4 text-sm text-slate-600">
                        {{ $show->date?->format('d/m/Y') ?: '-' }} · {{ $show->city ?: '-' }} · {{ $show->venue ?: __('ui.pending_venue') }}
                    </p>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-600">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.contact') }}</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $show->contact_name ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.phone') }}</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $show->contact_phone ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.email') }}</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $show->contact_email ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.travel_mode') }}</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $travelModeOptions[$show->travel_mode ?: 'van'] ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <section class="space-y-6">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">{{ __('ui.schedules') }}</h2>
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
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $label }}</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900">{{ $show->getRawOriginal($field) ?: '-' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                @foreach ([
                    'lighting_notes' => __('ui.lighting'),
                    'sound_notes' => __('ui.sound'),
                    'space_notes' => __('ui.space_venue'),
                    'general_notes' => __('ui.general_notes'),
                ] as $field => $label)
                    @if (filled($show->{$field}))
                        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-semibold text-slate-900">{{ $label }}</h2>
                            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600 [&_p]:my-0 [&_strong]:font-semibold [&_strong]:text-slate-900 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5">
                                {!! \Illuminate\Support\Str::markdown($show->{$field}, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
                            </div>
                        </div>
                    @endif
                @endforeach
            </section>

            <aside class="space-y-6">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">{{ __('ui.route_summary') }}</h2>

                    @if (($travelRoute['mode'] ?? null) === 'plane')
                        <div class="mt-4 space-y-3 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-900">{{ __('ui.flight_origin') }}:</span> {{ $show->flight_origin ?: '-' }}</p>
                            <p><span class="font-semibold text-slate-900">{{ __('ui.flight_destination') }}:</span> {{ $show->flight_destination ?: '-' }}</p>
                            <p><span class="font-semibold text-slate-900">{{ __('ui.estimated_duration') }}:</span> {{ $show->flight_duration_estimate ?: '-' }}</p>
                            <p><span class="font-semibold text-slate-900">{{ __('ui.notes') }}:</span> {{ $show->flight_notes ?: '-' }}</p>
                        </div>
                    @else
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.travel_origin') }}</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900">{{ $show->travel_origin ?: '-' }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.destination') }}</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900">{{ $show->venue ?: ($show->city ?: '-') }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.estimated_duration') }}</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900">{{ $travelRoute['duration_text'] ?? '-' }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.distance') }}</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900">{{ $travelRoute['distance_text'] ?? '-' }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</x-public-access-layout>
