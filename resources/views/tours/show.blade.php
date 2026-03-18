<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex items-start gap-4">
                <span class="mt-2 h-14 w-3 rounded-full" style="background-color: {{ $tour->color }}"></span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Ficha de gira</p>
                    <h2 class="text-3xl font-semibold text-slate-900">{{ $tour->name }}</h2>
                    <p class="mt-1 max-w-2xl text-sm text-slate-500">{{ $tour->notes ?: 'Sin notas de gira.' }}</p>
                </div>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('tours.google-calendar.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    Importar desde Google
                </a>
                <a href="{{ route('tours.edit', $tour) }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    Editar gira
                </a>
                <a href="{{ route('tours.index') }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Volver a giras
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
                                <h3 class="text-lg font-semibold text-slate-900">Contactos tecnicos</h3>
                                <p class="text-sm text-slate-500">Equipo propio de la gira.</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $tour->contacts->count() }} contactos</span>
                        </div>

                        <div class="mt-6 space-y-4">
                            @forelse ($tour->contacts as $contact)
                                <article class="rounded-2xl border border-slate-200 p-4">
                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                        <div class="min-w-0">
                                            <h4 class="text-base font-semibold text-slate-900">{{ $contact->name }}</h4>
                                            <p class="text-sm text-slate-500">{{ $contact->role ?: 'Sin rol indicado' }}</p>
                                            <div class="mt-3 grid gap-2 text-sm text-slate-600 sm:grid-cols-2">
                                                <p>{{ $contact->phone ?: 'Sin telefono' }}</p>
                                                <p class="break-all">{{ $contact->email ?: 'Sin email' }}</p>
                                            </div>
                                            @if ($contact->notes)
                                                <p class="mt-3 text-sm leading-6 text-slate-600">{{ $contact->notes }}</p>
                                            @endif
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ route('tours.contacts.edit', [$tour, $contact]) }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                                Editar
                                            </a>
                                            <form method="POST" action="{{ route('tours.contacts.destroy', [$tour, $contact]) }}" onsubmit="return confirm('¿Eliminar este contacto de gira?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center rounded-full border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-50">
                                                    Borrar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500">
                                    Todavia no hay contactos en esta gira.
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-8 rounded-3xl bg-slate-50 p-5">
                            <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Nuevo contacto</h4>
                            <form method="POST" action="{{ route('tours.contacts.store', $tour) }}" class="mt-4 space-y-4">
                                @csrf
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <x-input-label for="contact_name" value="Nombre" />
                                        <x-text-input id="contact_name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" />
                                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                    </div>
                                    <div>
                                        <x-input-label for="contact_role" value="Rol" />
                                        <x-text-input id="contact_role" name="role" type="text" class="mt-1 block w-full" :value="old('role')" />
                                    </div>
                                    <div>
                                        <x-input-label for="contact_phone" value="Telefono" />
                                        <x-text-input id="contact_phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone')" />
                                    </div>
                                    <div>
                                        <x-input-label for="contact_email" value="Email" />
                                        <x-text-input id="contact_email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" />
                                    </div>
                                </div>
                                <div>
                                    <x-input-label for="contact_notes" value="Notas" />
                                    <textarea id="contact_notes" name="notes" rows="4" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">{{ old('notes') }}</textarea>
                                </div>
                                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-500">
                                    Añadir contacto
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">Actividad reciente</h3>
                                <p class="text-sm text-slate-500">Registro de cambios importantes ligados a esta gira.</p>
                            </div>
                        </div>

                        <div class="mt-6 space-y-4">
                            @forelse ($tour->activityLogs->take(12) as $log)
                                <div class="rounded-2xl border border-slate-200 p-4">
                                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                        <p class="text-sm font-semibold text-slate-900">{{ $log->detail }}</p>
                                        <p class="text-xs text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <p class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-400">{{ $log->actor_name ?: 'Sistema' }} · {{ $log->action }}</p>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500">
                                    Aun no hay actividad registrada para esta gira.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>

                <aside class="space-y-6">
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">Documentos de gira</h3>
                                <p class="text-sm text-slate-500">Riders, hospitality, patch, planos y mas.</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $tour->documents->count() }} docs</span>
                        </div>

                        <div class="mt-6 space-y-4">
                            @forelse ($tour->documents as $document)
                                <article class="rounded-2xl border border-slate-200 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $document->document_type }}</p>
                                    <h4 class="mt-2 text-base font-semibold text-slate-900">{{ $document->title }}</h4>
                                    <p class="mt-1 break-all text-sm text-slate-500">{{ $document->original_name }}</p>
                                    <p class="mt-2 text-xs text-slate-400">
                                        {{ $document->uploader?->name ?: 'Sin usuario' }} · {{ $document->created_at->format('d/m/Y H:i') }}
                                    </p>
                                    <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                                        <a href="{{ route('tours.documents.show', [$tour, $document]) }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-800">
                                            Abrir o descargar
                                        </a>
                                        <a href="{{ route('tours.documents.edit', [$tour, $document]) }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('tours.documents.destroy', [$tour, $document]) }}" onsubmit="return confirm('¿Eliminar este documento?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-full border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-50 sm:w-auto">
                                                Borrar
                                            </button>
                                        </form>
                                    </div>
                                </article>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500">
                                    No hay documentos subidos en esta gira.
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-8 rounded-3xl bg-slate-50 p-5">
                            <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Subir documento</h4>
                            <form method="POST" action="{{ route('tours.documents.store', $tour) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                                @csrf
                                <div>
                                    <x-input-label for="document_type" value="Tipo de documento" />
                                    <select id="document_type" name="document_type" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                        @foreach (\App\Models\TourDocument::TYPES as $type)
                                            <option value="{{ $type }}" @selected(old('document_type') === $type)>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="document_title" value="Titulo" />
                                    <x-text-input id="document_title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" />
                                </div>
                                <div>
                                    <x-input-label for="document_file" value="Archivo" />
                                    <input id="document_file" name="file" type="file" class="mt-1 block w-full rounded-2xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm">
                                    <x-input-error class="mt-2" :messages="$errors->get('file')" />
                                </div>
                                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-500">
                                    Subir documento
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">Proximos bolos ligados</h3>
                        <div class="mt-4 space-y-3">
                            @forelse ($tour->shows as $show)
                                <div class="rounded-2xl border border-slate-200 p-4">
                                    <p class="text-sm font-semibold text-slate-900">{{ $show->name }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $show->date->format('d/m/Y') }} · {{ $show->city }} · {{ $show->venue ?: 'Venue pendiente' }}</p>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500">
                                    Aun no hay bolos asociados. La base de datos ya esta preparada para el siguiente bloque.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
