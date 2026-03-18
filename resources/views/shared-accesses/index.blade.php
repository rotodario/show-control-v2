<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500 dark:text-slate-400">Compartir</p>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Accesos por token</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Enlaces publicos sin login, limitados por rol y opcionalmente por gira.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <x-status-message />
            <x-validation-summary />

            <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/90">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Crear acceso</h3>
                    <form method="POST" action="{{ route('shared-accesses.store') }}" class="mt-6 space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="label" value="Etiqueta" />
                            <x-text-input id="label" name="label" type="text" class="mt-1 block w-full" :value="old('label')" />
                        </div>
                        <div>
                            <x-input-label for="role" value="Rol" />
                            <select id="role" name="role" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                @foreach ($roles as $value => $label)
                                    <option value="{{ $value }}" @selected(old('role') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="tour_id" value="Limitar a gira" />
                            <select id="tour_id" name="tour_id" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                <option value="">Todas las giras</option>
                                @foreach ($tours as $tour)
                                    <option value="{{ $tour->id }}" @selected((string) old('tour_id') === (string) $tour->id)>{{ $tour->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Crear acceso
                        </button>
                    </form>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/90">
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Accesos creados</h3>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $sharedAccesses->count() }}</span>
                    </div>

                    <div class="mt-6 space-y-4">
                        @forelse ($sharedAccesses as $sharedAccess)
                            <article class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ $sharedAccess->label ?: 'Sin etiqueta' }}</p>
                                            <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">{{ $roles[$sharedAccess->role] ?? $sharedAccess->role }}</span>
                                            <span class="rounded-full {{ $sharedAccess->isRevoked() ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }} px-3 py-1 text-xs font-semibold">
                                                {{ $sharedAccess->isRevoked() ? 'Revocado' : 'Activo' }}
                                            </span>
                                        </div>
                                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                                            {{ $sharedAccess->tour?->name ?: 'Todas las giras' }} · Creado por {{ $sharedAccess->creator?->name ?: 'sistema' }}
                                        </p>
                                        <p class="mt-2 break-all text-xs text-slate-400 dark:text-slate-500">{{ route('public-access.index', $sharedAccess->token) }}</p>
                                    </div>
                                    <div class="flex flex-col gap-2 sm:flex-row">
                                        @if (! $sharedAccess->isRevoked())
                                            <a href="{{ route('public-access.index', $sharedAccess->token) }}" target="_blank" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-800">
                                                Abrir
                                            </a>
                                        @endif
                                        <button type="button" onclick="navigator.clipboard.writeText('{{ route('public-access.index', $sharedAccess->token) }}')" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                            Copiar
                                        </button>
                                        @if (! $sharedAccess->isRevoked())
                                            <form method="POST" action="{{ route('shared-accesses.destroy', $sharedAccess) }}" onsubmit="return confirm('¿Revocar este acceso compartido?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center rounded-full border border-rose-200 px-4 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-50 dark:border-rose-900 dark:text-rose-300 dark:hover:bg-rose-950/50">
                                                    Revocar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                                Aun no hay accesos compartidos creados.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
