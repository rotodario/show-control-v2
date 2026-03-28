<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ __('ui.platform') }}</p>
                <h2 class="text-2xl font-semibold text-slate-900">{{ __('ui.platform_settings_title') }}</h2>
            </div>
            <p class="max-w-2xl text-sm text-slate-500">
                {{ __('ui.platform_settings_description') }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('platform.partials.nav')
            <x-status-message />

            <form method="POST" action="{{ route('platform.settings.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.platform_default_language') }}</h3>
                    <p class="mt-2 text-sm text-slate-500">{{ __('ui.platform_default_language_help') }}</p>

                    <div class="mt-6 max-w-md">
                        <label for="platform_default_locale" class="text-sm font-semibold text-slate-900">{{ __('ui.platform_default_language') }}</label>
                        <select id="platform_default_locale" name="platform_default_locale" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400">
                            @foreach ($localeLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('platform_default_locale', $settings['platform_default_locale']) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('platform_default_locale')" />
                    </div>
                </section>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        {{ __('ui.save_platform_settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
