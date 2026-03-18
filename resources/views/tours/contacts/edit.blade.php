<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Contactos de gira</p>
            <h2 class="text-2xl font-semibold text-slate-900">Editar contacto</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <form method="POST" action="{{ route('tours.contacts.update', [$tour, $contact]) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="name" value="Nombre" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $contact->name)" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>
                        <div>
                            <x-input-label for="role" value="Rol" />
                            <x-text-input id="role" name="role" type="text" class="mt-1 block w-full" :value="old('role', $contact->role)" />
                        </div>
                        <div>
                            <x-input-label for="phone" value="Telefono" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $contact->phone)" />
                        </div>
                        <div>
                            <x-input-label for="email" value="Email" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $contact->email)" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="notes" value="Notas" />
                        <textarea id="notes" name="notes" rows="5" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">{{ old('notes', $contact->notes) }}</textarea>
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
