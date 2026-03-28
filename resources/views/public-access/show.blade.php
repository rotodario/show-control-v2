<x-public-access-layout>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <x-validation-summary />

        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ __('ui.shared_access') }}</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-900">{{ $show->name }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $show->date->format('d/m/Y') }} &middot; {{ $show->city }} &middot; {{ $show->venue ?: __('ui.pending_venue') }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                @if ($permissions['delete_shows'])
                    <form method="POST" action="{{ route('public-access.shows.destroy', [$grant->token, $show]) }}" onsubmit="return confirm('{{ __('ui.confirm_delete_show') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center rounded-full border border-rose-200 px-4 py-2.5 text-sm font-semibold text-rose-700 transition hover:bg-rose-50">
                            {{ __('ui.delete_show') }}
                        </button>
                    </form>
                @endif
                <a href="{{ route('public-access.index', $grant->token) }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                    {{ __('ui.back') }}
                </a>
            </div>
        </div>

        <div class="mt-6 space-y-6">
            @if ($visibility['alerts'] && $alerts !== [])
                <div class="rounded-[2rem] border border-amber-200 bg-amber-50 p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-amber-950">{{ __('ui.alerts') }}</h2>
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

            @if ($permissions['update_shows'])
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">{{ __('ui.edit_show') }}</h2>
                    <form method="POST" action="{{ route('public-access.shows.update', [$grant->token, $show]) }}" class="mt-6 space-y-8">
                        @csrf
                        @method('PUT')

                        @if (in_array('date', $permissions['show_fields'], true) || in_array('city', $permissions['show_fields'], true) || in_array('venue', $permissions['show_fields'], true) || in_array('name', $permissions['show_fields'], true) || in_array('status', $permissions['show_fields'], true) || in_array('tour_id', $permissions['show_fields'], true))
                            <div class="grid gap-4 lg:grid-cols-2">
                                @if (in_array('tour_id', $permissions['show_fields'], true) && ! $grant->tour_id)
                                    <div>
                                        <x-input-label for="tour_id" :value="__('ui.tour')" />
                                        <select id="tour_id" name="tour_id" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                            <option value="">{{ __('ui.no_tour') }}</option>
                                            @foreach ($tours as $tour)
                                                <option value="{{ $tour->id }}" @selected((string) old('tour_id', $show->tour_id) === (string) $tour->id)>{{ $tour->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('tour_id')" />
                                    </div>
                                @endif
                                @if (in_array('name', $permissions['show_fields'], true))
                                    <div>
                                        <x-input-label for="name" :value="__('ui.show_name')" />
                                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $show->name)" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                    </div>
                                @endif
                                @if (in_array('date', $permissions['show_fields'], true))
                                    <div>
                                        <x-input-label for="date" :value="__('ui.date')" />
                                        <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="old('date', $show->date?->format('Y-m-d'))" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('date')" />
                                    </div>
                                @endif
                                @if (in_array('status', $permissions['show_fields'], true))
                                    <div>
                                        <x-input-label for="status" :value="__('ui.default_show_status')" />
                                        <select id="status" name="status" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                            @foreach ($statusOptions as $value => $label)
                                                <option value="{{ $value }}" @selected(old('status', $show->status) === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('status')" />
                                    </div>
                                @endif
                                @if (in_array('city', $permissions['show_fields'], true))
                                    <div>
                                        <x-input-label for="city" :value="__('ui.city')" />
                                        <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $show->city)" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('city')" />
                                    </div>
                                @endif
                                @if (in_array('venue', $permissions['show_fields'], true))
                                    <div>
                                        <x-input-label for="venue" :value="__('ui.venue')" />
                                        <x-text-input id="venue" name="venue" type="text" class="mt-1 block w-full" :value="old('venue', $show->venue)" />
                                        <x-input-error class="mt-2" :messages="$errors->get('venue')" />
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if (collect(['load_in_at', 'meal_at', 'soundcheck_at', 'doors_at', 'show_at', 'show_end_at', 'load_out_at'])->intersect($permissions['show_fields'])->isNotEmpty())
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
                                        @if (in_array($field, $permissions['show_fields'], true))
                                            <div>
                                                <x-input-label :for="$field" :value="$label" />
                                                <x-text-input :id="$field" :name="$field" type="time" class="mt-1 block w-full" :value="old($field, $show->getRawOriginal($field))" />
                                                <x-input-error class="mt-2" :messages="$errors->get($field)" />
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if (collect(['contact_name', 'contact_role', 'contact_phone', 'contact_email'])->intersect($permissions['show_fields'])->isNotEmpty())
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.contact') }}</h3>
                                <div class="mt-4 grid gap-4 lg:grid-cols-2">
                                    @if (in_array('contact_name', $permissions['show_fields'], true))
                                        <div>
                                            <x-input-label for="contact_name" :value="__('ui.name')" />
                                            <x-text-input id="contact_name" name="contact_name" type="text" class="mt-1 block w-full" :value="old('contact_name', $show->contact_name)" />
                                            <x-input-error class="mt-2" :messages="$errors->get('contact_name')" />
                                        </div>
                                    @endif
                                    @if (in_array('contact_role', $permissions['show_fields'], true))
                                        <div>
                                            <x-input-label for="contact_role" :value="__('ui.role')" />
                                            <x-text-input id="contact_role" name="contact_role" type="text" class="mt-1 block w-full" :value="old('contact_role', $show->contact_role)" />
                                            <x-input-error class="mt-2" :messages="$errors->get('contact_role')" />
                                        </div>
                                    @endif
                                    @if (in_array('contact_phone', $permissions['show_fields'], true))
                                        <div>
                                            <x-input-label for="contact_phone" :value="__('ui.phone')" />
                                            <x-text-input id="contact_phone" name="contact_phone" type="text" class="mt-1 block w-full" :value="old('contact_phone', $show->contact_phone)" />
                                            <x-input-error class="mt-2" :messages="$errors->get('contact_phone')" />
                                        </div>
                                    @endif
                                    @if (in_array('contact_email', $permissions['show_fields'], true))
                                        <div>
                                            <x-input-label for="contact_email" :value="__('ui.email')" />
                                            <x-text-input id="contact_email" name="contact_email" type="email" class="mt-1 block w-full" :value="old('contact_email', $show->contact_email)" />
                                            <x-input-error class="mt-2" :messages="$errors->get('contact_email')" />
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="grid gap-6 xl:grid-cols-2">
                            @foreach ([
                                'lighting' => __('ui.lighting'),
                                'sound' => __('ui.sound'),
                                'space' => __('ui.space_venue'),
                                'general' => __('ui.general_notes'),
                            ] as $prefix => $label)
                                @if (in_array($prefix.'_notes', $permissions['show_fields'], true) || in_array($prefix.'_validated', $permissions['show_fields'], true))
                                    <div class="rounded-3xl bg-slate-50 p-5">
                                        <div class="flex items-center justify-between gap-4">
                                            <h3 class="text-lg font-semibold text-slate-900">{{ $label }}</h3>
                                            @if (in_array($prefix.'_validated', $permissions['show_fields'], true))
                                                <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-600">
                                                    <input type="checkbox" name="{{ $prefix }}_validated" value="1" class="rounded border-slate-300 text-sky-600 shadow-sm focus:ring-sky-500" @checked(old($prefix.'_validated', $show->{$prefix.'_validated'}))>
                                                    {{ __('ui.validated') }}
                                                </label>
                                            @endif
                                        </div>
                                        @if (in_array($prefix.'_notes', $permissions['show_fields'], true))
                                            <textarea name="{{ $prefix }}_notes" rows="5" class="mt-4 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">{{ old($prefix.'_notes', $show->{$prefix.'_notes'}) }}</textarea>
                                            <x-input-error class="mt-2" :messages="$errors->get($prefix.'_notes')" />
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                {{ __('ui.save_changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
                <section class="space-y-6">
                    @if ($visibility['schedules'])
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
                    @endif

                    @if ($visibility['lighting'])
                        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-semibold text-slate-900">{{ __('ui.lighting') }}</h2>
                            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600 [&_p]:my-0 [&_strong]:font-semibold [&_strong]:text-slate-900 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5">
                                {!! $show->lighting_notes ? \Illuminate\Support\Str::markdown($show->lighting_notes, ['html_input' => 'strip', 'allow_unsafe_links' => false]) : '<p>'.e(__('ui.no_notes_yet')).'</p>' !!}
                            </div>
                            @if (in_array('lighting', $chatSections, true))
                                @include('shows.partials.section-chat', ['section' => 'lighting', 'messages' => $sectionMessages->get('lighting', collect()), 'action' => route('public-access.section-messages.store', [$grant->token, $show])])
                            @endif
                        </div>
                    @endif

                    @if ($visibility['sound'])
                        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-semibold text-slate-900">{{ __('ui.sound') }}</h2>
                            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600 [&_p]:my-0 [&_strong]:font-semibold [&_strong]:text-slate-900 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5">
                                {!! $show->sound_notes ? \Illuminate\Support\Str::markdown($show->sound_notes, ['html_input' => 'strip', 'allow_unsafe_links' => false]) : '<p>'.e(__('ui.no_notes_yet')).'</p>' !!}
                            </div>
                            @if (in_array('sound', $chatSections, true))
                                @include('shows.partials.section-chat', ['section' => 'sound', 'messages' => $sectionMessages->get('sound', collect()), 'action' => route('public-access.section-messages.store', [$grant->token, $show])])
                            @endif
                        </div>
                    @endif

                    @if ($visibility['space'])
                        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-semibold text-slate-900">{{ __('ui.space_venue') }}</h2>
                            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600 [&_p]:my-0 [&_strong]:font-semibold [&_strong]:text-slate-900 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5">
                                {!! $show->space_notes ? \Illuminate\Support\Str::markdown($show->space_notes, ['html_input' => 'strip', 'allow_unsafe_links' => false]) : '<p>'.e(__('ui.no_notes_yet')).'</p>' !!}
                            </div>
                            @if (in_array('space', $chatSections, true))
                                @include('shows.partials.section-chat', ['section' => 'space', 'messages' => $sectionMessages->get('space', collect()), 'action' => route('public-access.section-messages.store', [$grant->token, $show])])
                            @endif
                        </div>
                    @endif

                    @if ($visibility['general'])
                        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-semibold text-slate-900">{{ __('ui.general_notes') }}</h2>
                            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600 [&_p]:my-0 [&_strong]:font-semibold [&_strong]:text-slate-900 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5">
                                {!! $show->general_notes ? \Illuminate\Support\Str::markdown($show->general_notes, ['html_input' => 'strip', 'allow_unsafe_links' => false]) : '<p>'.e(__('ui.no_notes_yet')).'</p>' !!}
                            </div>
                            @if (in_array('general', $chatSections, true))
                                @include('shows.partials.section-chat', ['section' => 'general', 'messages' => $sectionMessages->get('general', collect()), 'action' => route('public-access.section-messages.store', [$grant->token, $show])])
                            @endif
                        </div>
                    @endif
                </section>

                <aside class="space-y-6">
                    @if ($visibility['contact'])
                        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-lg font-semibold text-slate-900">{{ __('ui.contact') }}</h2>
                            <div class="mt-4 space-y-2 text-sm text-slate-600">
                                <p><span class="font-semibold text-slate-900">{{ __('ui.name') }}:</span> {{ $show->contact_name ?: '-' }}</p>
                                <p><span class="font-semibold text-slate-900">{{ __('ui.role') }}:</span> {{ $show->contact_role ?: '-' }}</p>
                                <p><span class="font-semibold text-slate-900">{{ __('ui.phone') }}:</span> {{ $show->contact_phone ?: '-' }}</p>
                                <p><span class="font-semibold text-slate-900">{{ __('ui.email') }}:</span> {{ $show->contact_email ?: '-' }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($visibility['documents'])
                        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="text-lg font-semibold text-slate-900">{{ __('ui.visible_documents') }}</h2>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $documents->count() }}</span>
                            </div>

                            <div class="mt-4 space-y-3">
                                @forelse ($documents as $document)
                                    <article class="rounded-2xl border border-slate-200 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ \App\Models\ShowDocument::translatedTypeLabel($document->document_type) }}</p>
                                        <h3 class="mt-2 text-base font-semibold text-slate-900">{{ $document->title }}</h3>
                                        <p class="mt-1 break-all text-sm text-slate-500">{{ $document->original_name }}</p>
                                        <a href="{{ route('public-access.documents.show', [$grant->token, $document]) }}" class="mt-4 inline-flex items-center justify-center rounded-full bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-800">
                                            {{ __('ui.open_or_download') }}
                                        </a>
                                    </article>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500">
                                        {{ __('ui.no_visible_documents_for_access') }}
                                    </div>
                                @endforelse
                            </div>

                            @if ($permissions['upload_documents'])
                                <div class="mt-6 rounded-3xl bg-slate-50 p-5">
                                    <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">{{ __('ui.upload_document') }}</h3>
                                    <form method="POST" action="{{ route('public-access.documents.store', [$grant->token, $show]) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                                        @csrf
                                        <div>
                                            <x-input-label for="document_type" :value="__('ui.document_type')" />
                                            <select id="document_type" name="document_type" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                                @foreach ($allowedDocumentTypes as $type)
                                                    <option value="{{ $type }}" @selected(old('document_type') === $type)>{{ \App\Models\ShowDocument::translatedTypeLabel($type) }}</option>
                                                @endforeach
                                            </select>
                                            <x-input-error class="mt-2" :messages="$errors->get('document_type')" />
                                        </div>
                                        <div>
                                            <x-input-label for="title" :value="__('ui.title')" />
                                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" />
                                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                                        </div>
                                        <div>
                                            <x-input-label for="file" :value="__('ui.file')" />
                                            <input id="file" name="file" type="file" class="mt-1 block w-full rounded-2xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm">
                                            <x-input-error class="mt-2" :messages="$errors->get('file')" />
                                        </div>
                                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-500">
                                            {{ __('ui.upload_document') }}
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </div>
</x-public-access-layout>
