<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Documentos de gira</p>
            <h2 class="text-2xl font-semibold text-slate-900">Editar documento</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <form method="POST" action="{{ route('tours.documents.update', [$tour, $document]) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="document_type" value="Tipo de documento" />
                        <select id="document_type" name="document_type" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                            @foreach ($documentTypes as $type)
                                <option value="{{ $type }}" @selected(old('document_type', $document->document_type) === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="title" value="Titulo" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $document->title)" />
                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-600">
                        Archivo actual: <span class="font-semibold">{{ $document->original_name }}</span>
                    </div>
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <a href="{{ route('tours.show', $tour) }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                            Volver
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
