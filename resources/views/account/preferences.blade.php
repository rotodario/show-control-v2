<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ __('ui.account') }}</p>
            <h2 class="text-2xl font-semibold text-slate-900">{{ __('ui.preferences') }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('account.partials.nav')

            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.account_preferences') }}</h3>
                        <p class="mt-2 max-w-3xl text-sm text-slate-500">
                            {{ __('ui.account_preferences_description') }}
                        </p>
                    </div>

                    @if (session('status') === 'preferences-updated')
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                            {{ __('ui.saved') }}
                        </span>
                    @endif
                </div>

                <form method="POST" action="{{ route('account.preferences.update') }}" class="mt-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="default_show_status" :value="__('ui.default_show_status')" />
                            <select id="default_show_status" name="default_show_status" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('default_show_status', $settings->default_show_status ?: 'tentative') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('default_show_status')" />
                        </div>

                        <div>
                            <x-input-label for="default_travel_mode" :value="__('ui.default_travel_mode')" />
                            <select id="default_travel_mode" name="default_travel_mode" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                @foreach ($travelModeOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('default_travel_mode', $settings->default_travel_mode ?: 'van') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('default_travel_mode')" />
                        </div>

                        <div>
                            <x-input-label for="default_city" :value="__('ui.default_city')" />
                            <x-text-input id="default_city" name="default_city" type="text" class="mt-1 block w-full" :value="old('default_city', $settings->default_city)" />
                            <p class="mt-2 text-sm text-slate-500">{{ __('ui.default_city_help') }}</p>
                            <x-input-error class="mt-2" :messages="$errors->get('default_city')" />
                        </div>

                        <div>
                            <x-input-label for="default_travel_origin" :value="__('ui.default_travel_origin')" />
                            <x-text-input id="default_travel_origin" name="default_travel_origin" type="text" class="mt-1 block w-full" :value="old('default_travel_origin', $settings->default_travel_origin)" />
                            <p class="mt-2 text-sm text-slate-500">{{ __('ui.default_travel_origin_help') }}</p>
                            <x-input-error class="mt-2" :messages="$errors->get('default_travel_origin')" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="ui_locale" :value="__('ui.default_ui_language')" />
                            <select id="ui_locale" name="ui_locale" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                <option value="">{{ __('ui.platform_default_language') }}</option>
                                @foreach ($localeLabels as $value => $label)
                                    <option value="{{ $value }}" @selected(old('ui_locale', $settings->ui_locale) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-sm text-slate-500">{{ __('ui.default_ui_language_help') }}</p>
                            <x-input-error class="mt-2" :messages="$errors->get('ui_locale')" />
                        </div>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-5 text-sm text-slate-600">
                        {{ __('ui.new_show_preferences_help') }}
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            {{ __('ui.save_preferences') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
