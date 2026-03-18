<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Bolos</p>
            <h2 class="text-2xl font-semibold text-slate-900">Editar {{ $show->name }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <x-validation-summary />
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <form method="POST" action="{{ route('shows.update', $show) }}">
                    @method('PUT')
                    @include('shows.partials.form')
                </form>
            </div>

            <div id="show-documents" class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Documentos del bolo</h3>
                        <p class="text-sm text-slate-500">La subida y gestion de adjuntos se hace desde editar.</p>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $show->documents()->count() }} docs</span>
                </div>

                <div class="mt-6 grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
                    <div class="space-y-4">
                        @forelse ($show->documents()->with('uploader')->latest()->get() as $document)
                            <article class="rounded-2xl border border-slate-200 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $document->document_type }}</p>
                                <h4 class="mt-2 text-base font-semibold text-slate-900">{{ $document->title }}</h4>
                                <p class="mt-1 break-all text-sm text-slate-500">{{ $document->original_name }}</p>
                                <p class="mt-2 text-xs text-slate-400">{{ $document->uploader?->name ?: 'Sin usuario' }} · {{ $document->created_at->format('d/m/Y H:i') }}</p>
                                <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                                    <a href="{{ route('shows.documents.show', [$show, $document]) }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-800">
                                        Abrir o descargar
                                    </a>
                                    <a href="{{ route('shows.documents.edit', [$show, $document]) }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                        Editar
                                    </a>
                                    <form method="POST" action="{{ route('shows.documents.destroy', [$show, $document]) }}" onsubmit="return confirm('¿Eliminar este documento del bolo?');">
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
                                Todavia no hay documentos en este bolo.
                            </div>
                        @endforelse
                    </div>

                    <div class="rounded-3xl bg-slate-50 p-5">
                        <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Subir documento</h4>
                        <form method="POST" action="{{ route('shows.documents.store', $show) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                            @csrf
                            <div>
                                <x-input-label for="show_document_type" value="Tipo de documento" />
                                <select id="show_document_type" name="document_type" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                    @foreach (\App\Models\ShowDocument::TYPES as $type)
                                        <option value="{{ $type }}" @selected(old('document_type') === $type)>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="show_document_title" value="Titulo" />
                                <x-text-input id="show_document_title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" />
                            </div>
                            <div>
                                <x-input-label for="show_document_file" value="Archivo" />
                                <input id="show_document_file" name="file" type="file" class="mt-1 block w-full rounded-2xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm">
                                <x-input-error class="mt-2" :messages="$errors->get('file')" />
                            </div>
                            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-500">
                                Subir documento
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
