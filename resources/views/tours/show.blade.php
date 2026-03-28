<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex items-start gap-4">
                <span class="mt-2 h-14 w-3 rounded-full" style="background-color: {{ $tour->color }}"></span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ __('ui.tour_record') }}</p>
                    <h2 class="text-3xl font-semibold text-slate-900">{{ $tour->name }}</h2>
                    <p class="mt-1 max-w-2xl text-sm text-slate-500">{{ $tour->localizedNotes() ?: __('ui.no_tour_notes') }}</p>
                </div>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('tours.google-calendar.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    {{ __('ui.import_from_google') }}
                </a>
                <a href="{{ route('tours.edit', $tour) }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    {{ __('ui.edit_tour') }}
                </a>
                <a href="{{ route('tours.index') }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                    {{ __('ui.back_to_tours') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <x-status-message />

            <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
                <section class="space-y-6">
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.technical_contacts') }}</h3>
                                <p class="text-sm text-slate-500">{{ __('ui.technical_contacts_help') }}</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ __('ui.contacts_count', ['count' => $tour->contacts->count()]) }}</span>
                        </div>

                        <div class="mt-6 space-y-4">
                            @forelse ($tour->contacts as $contact)
                                <article class="rounded-2xl border border-slate-200 p-4">
                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                        <div class="min-w-0">
                                            <h4 class="text-base font-semibold text-slate-900">{{ $contact->name }}</h4>
                                            <p class="text-sm text-slate-500">{{ $contact->role ?: __('ui.no_role_indicated') }}</p>
                                            <div class="mt-3 grid gap-2 text-sm text-slate-600 sm:grid-cols-2">
                                                <p>{{ $contact->phone ?: __('ui.no_phone') }}</p>
                                                <p class="break-all">{{ $contact->email ?: __('ui.no_email') }}</p>
                                            </div>
                                            @if ($contact->notes)
                                                <p class="mt-3 text-sm leading-6 text-slate-600">{{ $contact->notes }}</p>
                                            @endif
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ route('tours.contacts.edit', [$tour, $contact]) }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                                {{ __('ui.edit') }}
                                            </a>
                                            <form method="POST" action="{{ route('tours.contacts.destroy', [$tour, $contact]) }}" onsubmit="return confirm('{{ __('ui.confirm_delete_tour_contact') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center rounded-full border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-50">
                                                    {{ __('ui.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500">
                                    {{ __('ui.no_tour_contacts_yet') }}
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-8 rounded-3xl bg-slate-50 p-5">
                            <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">{{ __('ui.new_contact') }}</h4>
                            <form method="POST" action="{{ route('tours.contacts.store', $tour) }}" class="mt-4 space-y-4">
                                @csrf
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <x-input-label for="contact_name" :value="__('ui.name')" />
                                        <x-text-input id="contact_name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" />
                                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                    </div>
                                    <div>
                                        <x-input-label for="contact_role" :value="__('ui.role')" />
                                        <x-text-input id="contact_role" name="role" type="text" class="mt-1 block w-full" :value="old('role')" />
                                    </div>
                                    <div>
                                        <x-input-label for="contact_phone" :value="__('ui.phone')" />
                                        <x-text-input id="contact_phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone')" />
                                    </div>
                                    <div>
                                        <x-input-label for="contact_email" :value="__('ui.email')" />
                                        <x-text-input id="contact_email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" />
                                    </div>
                                </div>
                                <div>
                                    <x-input-label for="contact_notes" :value="__('ui.notes')" />
                                    <textarea id="contact_notes" name="notes" rows="4" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">{{ old('notes') }}</textarea>
                                </div>
                                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-500">
                                    {{ __('ui.add_contact') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.recent_activity') }}</h3>
                                <p class="text-sm text-slate-500">{{ __('ui.tour_recent_activity_help') }}</p>
                            </div>
                        </div>

                        <div class="mt-6 space-y-4">
                            @forelse ($tour->activityLogs->take(12) as $log)
                                <div class="rounded-2xl border border-slate-200 p-4">
                                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                        <p class="text-sm font-semibold text-slate-900">{{ $log->translatedDetail() }}</p>
                                        <p class="text-xs text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <p class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-400">{{ $log->actor_name ?: __('ui.system') }} · {{ $log->action }}</p>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500">
                                    {{ __('ui.no_tour_activity_yet') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>

                <aside class="space-y-6">
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.tour_documents') }}</h3>
                                <p class="text-sm text-slate-500">{{ __('ui.tour_documents_help') }}</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ __('ui.docs_count', ['count' => $tour->documents->count()]) }}</span>
                        </div>

                        <div class="mt-6 space-y-4">
                            @forelse ($tour->documents as $document)
                                <article class="rounded-2xl border border-slate-200 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ \App\Models\TourDocument::translatedTypeLabel($document->document_type) }}</p>
                                    <h4 class="mt-2 text-base font-semibold text-slate-900">{{ $document->title }}</h4>
                                    <p class="mt-1 break-all text-sm text-slate-500">{{ $document->original_name }}</p>
                                    <p class="mt-2 text-xs text-slate-400">
                                        {{ $document->uploader?->name ?: __('ui.no_user') }} · {{ $document->created_at->format('d/m/Y H:i') }}
                                    </p>
                                    <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                                        <a href="{{ route('tours.documents.show', [$tour, $document]) }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-800">
                                            {{ __('ui.open_or_download') }}
                                        </a>
                                        <a href="{{ route('tours.documents.edit', [$tour, $document]) }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                            {{ __('ui.edit') }}
                                        </a>
                                        <form method="POST" action="{{ route('tours.documents.destroy', [$tour, $document]) }}" onsubmit="return confirm('{{ __('ui.confirm_delete_tour_document') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-full border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-50 sm:w-auto">
                                                {{ __('ui.delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </article>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500">
                                    {{ __('ui.no_tour_documents_yet') }}
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-8 rounded-3xl bg-slate-50 p-5">
                            <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">{{ __('ui.upload_document') }}</h4>
                            <form method="POST" action="{{ route('tours.documents.store', $tour) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                                @csrf
                                <div>
                                    <x-input-label for="document_type" :value="__('ui.document_type')" />
                                    <select id="document_type" name="document_type" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                        @foreach (\App\Models\TourDocument::TYPES as $type)
                                            <option value="{{ $type }}" @selected(old('document_type') === $type)>{{ \App\Models\TourDocument::translatedTypeLabel($type) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="document_title" :value="__('ui.title')" />
                                    <x-text-input id="document_title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" />
                                </div>
                                <div>
                                    <x-input-label for="document_file" :value="__('ui.file')" />
                                    <input id="document_file" name="file" type="file" class="mt-1 block w-full rounded-2xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm">
                                    <x-input-error class="mt-2" :messages="$errors->get('file')" />
                                </div>
                                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-500">
                                    {{ __('ui.upload_document') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.upcoming_linked_shows') }}</h3>
                        <div class="mt-4 space-y-3">
                            @forelse ($tour->shows as $show)
                                <div class="rounded-2xl border border-slate-200 p-4">
                                    <p class="text-sm font-semibold text-slate-900">{{ $show->name }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $show->date->format('d/m/Y') }} · {{ $show->city }} · {{ $show->venue ?: __('ui.pending_venue') }}</p>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500">
                                    {{ __('ui.no_linked_shows_yet') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
