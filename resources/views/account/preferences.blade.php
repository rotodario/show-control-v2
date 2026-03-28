<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Cuenta</p>
            <h2 class="text-2xl font-semibold text-slate-900">Preferencias</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('account.partials.nav')

            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Preferencias de cuenta</h3>
                        <p class="mt-2 max-w-3xl text-sm text-slate-500">
                            Ajusta los valores por defecto que se precargan al crear nuevos bolos para acelerar el flujo de trabajo.
                        </p>
                    </div>

                    @if (session('status') === 'preferences-updated')
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                            Guardado
                        </span>
                    @endif
                </div>

                <form method="POST" action="{{ route('account.preferences.update') }}" class="mt-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="default_show_status" value="Estado por defecto del bolo" />
                            <select id="default_show_status" name="default_show_status" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('default_show_status', $settings->default_show_status ?: 'tentative') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('default_show_status')" />
                        </div>

                        <div>
                            <x-input-label for="default_travel_mode" value="Modo de viaje por defecto" />
                            <select id="default_travel_mode" name="default_travel_mode" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                @foreach ($travelModeOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('default_travel_mode', $settings->default_travel_mode ?: 'van') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('default_travel_mode')" />
                        </div>

                        <div>
                            <x-input-label for="default_city" value="Ciudad por defecto" />
                            <x-text-input id="default_city" name="default_city" type="text" class="mt-1 block w-full" :value="old('default_city', $settings->default_city)" />
                            <p class="mt-2 text-sm text-slate-500">Se propone automaticamente al crear un nuevo bolo.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('default_city')" />
                        </div>

                        <div>
                            <x-input-label for="default_travel_origin" value="Origen de viaje por defecto" />
                            <x-text-input id="default_travel_origin" name="default_travel_origin" type="text" class="mt-1 block w-full" :value="old('default_travel_origin', $settings->default_travel_origin)" />
                            <p class="mt-2 text-sm text-slate-500">Util para bases, oficinas, hoteles o puntos de salida habituales.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('default_travel_origin')" />
                        </div>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-5 text-sm text-slate-600">
                        Estas preferencias afectan a <strong class="text-slate-900">Nuevo bolo</strong>. No modifican los bolos ya creados.
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Guardar preferencias
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
