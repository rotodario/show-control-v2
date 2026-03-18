<x-public-access-layout>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Acceso compartido</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">{{ \App\Models\SharedAccess::ROLE_LABELS[$grant->role] ?? $grant->role }}</h1>
            <p class="mt-2 text-sm text-slate-500">
                {{ $grant->label ?: 'Enlace compartido' }} · {{ $grant->tour?->name ?: 'Todas las giras' }}
            </p>
        </div>

        @if ($permissions['create_shows'])
            <div class="mt-6 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Crear bolo</h2>
                <p class="mt-1 text-sm text-slate-500">Disponible para Project Manager.</p>

                <div class="mt-6">
                    <x-validation-summary />
                </div>

                <form method="POST" action="{{ route('public-access.shows.store', $grant->token) }}" class="mt-6 space-y-8">
                    @csrf

                    <div class="grid gap-4 lg:grid-cols-2">
                        @if (! $grant->tour_id)
                            <div>
                                <x-input-label for="tour_id" value="Gira" />
                                <select id="tour_id" name="tour_id" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                    <option value="">Sin gira</option>
                                    @foreach ($tours as $tour)
                                        <option value="{{ $tour->id }}" @selected((string) old('tour_id') === (string) $tour->id)>{{ $tour->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('tour_id')" />
                            </div>
                        @endif
                        <div>
                            <x-input-label for="name" value="Nombre del bolo" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>
                        <div>
                            <x-input-label for="date" value="Fecha" />
                            <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="old('date')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('date')" />
                        </div>
                        <div>
                            <x-input-label for="status" value="Estado" />
                            <select id="status" name="status" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', array_key_first($statusOptions)) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>
                        <div>
                            <x-input-label for="city" value="Ciudad" />
                            <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('city')" />
                        </div>
                        <div>
                            <x-input-label for="venue" value="Venue" />
                            <x-text-input id="venue" name="venue" type="text" class="mt-1 block w-full" :value="old('venue')" />
                            <x-input-error class="mt-2" :messages="$errors->get('venue')" />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Guardar bolo
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <div class="mt-6 grid gap-4">
            @forelse ($shows as $show)
                <a href="{{ route('public-access.shows.show', [$grant->token, $show]) }}" class="block w-full overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm transition hover:border-sky-300 hover:bg-sky-50/40">
                    @php
                        $statusClasses = match ($show->status) {
                            'confirmed' => 'bg-emerald-100 text-emerald-700',
                            'closed' => 'bg-slate-200 text-slate-700',
                            'cancelled' => 'bg-rose-100 text-rose-700',
                            default => 'bg-amber-100 text-amber-700',
                        };
                    @endphp
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="truncate text-xl font-semibold text-slate-900">{{ $show->name }}</h2>
                                <span class="rounded-full px-3 py-1 text-xs font-medium {{ $statusClasses }}">{{ $statusOptions[$show->status] ?? $show->status }}</span>
                                @if ($show->tour)
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold text-white shadow-sm" style="background-color: {{ $show->tour->color }}">
                                        {{ $show->tour->name }}
                                    </span>
                                @endif
                                @if (($showAlerts[$show->id] ?? []) !== [])
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">{{ count($showAlerts[$show->id]) }} alertas</span>
                                @endif
                                @if (($unreadMessageCounts[$show->id] ?? 0) > 0)
                                    <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">{{ $unreadMessageCounts[$show->id] }} mensajes nuevos</span>
                                @endif
                            </div>
                            <p class="mt-2 text-sm text-slate-500">{{ $show->date->format('d/m/Y') }} · {{ $show->city }} · {{ $show->venue ?: 'Venue pendiente' }}</p>
                            @unless ($show->tour)
                                <p class="mt-2 text-sm text-slate-600">Sin gira</p>
                            @endunless
                            <div class="mt-4 flex flex-wrap gap-2 text-xs font-medium text-slate-500">
                                @foreach ([
                                    'lighting_validated' => 'Luces',
                                    'sound_validated' => 'Sonido',
                                    'space_validated' => 'Espacio',
                                    'general_validated' => 'General',
                                ] as $field => $label)
                                    <span class="rounded-full px-3 py-1 {{ $show->{$field} ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $label }} {{ $show->{$field} ? 'OK' : 'pendiente' }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="text-xs text-slate-400">
                            Abrir ficha
                        </div>
                    </div>
                </a>
            @empty
                <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500">
                    No hay bolos visibles para este acceso.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $shows->links() }}
        </div>
    </div>
</x-public-access-layout>
