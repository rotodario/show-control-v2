<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ __('ui.account') }}</p>
            <h2 class="text-2xl font-semibold text-slate-900">{{ __('ui.alerts') }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('account.partials.nav')

            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.account_alerts_title') }}</h3>
                        <p class="mt-2 max-w-3xl text-sm text-slate-500">{{ __('ui.account_alerts_description') }}</p>
                    </div>

                    @if (session('status') === 'alert-settings-updated')
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                            {{ __('ui.saved') }}
                        </span>
                    @endif
                </div>

                <form method="POST" action="{{ route('account.alerts.update') }}" class="mt-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-4 lg:grid-cols-3">
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 lg:col-span-2">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h4 class="text-sm font-semibold text-slate-900">{{ __('ui.alert_core_info_title') }}</h4>
                                    <p class="mt-2 text-sm text-slate-500">{{ __('ui.alert_core_info_help') }}</p>
                                </div>
                                <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                                    <input type="hidden" name="core_info_enabled" value="0">
                                    <input
                                        type="checkbox"
                                        name="core_info_enabled"
                                        value="1"
                                        class="rounded border-slate-300 text-slate-900 focus:ring-slate-400"
                                        @checked(old('core_info_enabled', $settings->core_info_enabled))
                                    >
                                    {{ __('ui.enable') }}
                                </label>
                            </div>

                            <div class="mt-5 max-w-xs">
                                <label for="core_info_days" class="text-sm font-medium text-slate-700">{{ __('ui.days_in_advance') }}</label>
                                <input
                                    id="core_info_days"
                                    name="core_info_days"
                                    type="number"
                                    min="1"
                                    max="365"
                                    value="{{ old('core_info_days', $settings->core_info_days) }}"
                                    class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400"
                                >
                                <x-input-error class="mt-2" :messages="$errors->get('core_info_days')" />
                            </div>
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 lg:col-span-2">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h4 class="text-sm font-semibold text-slate-900">{{ __('ui.alert_status_title') }}</h4>
                                    <p class="mt-2 text-sm text-slate-500">{{ __('ui.alert_status_help') }}</p>
                                </div>
                                <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                                    <input type="hidden" name="status_enabled" value="0">
                                    <input
                                        type="checkbox"
                                        name="status_enabled"
                                        value="1"
                                        class="rounded border-slate-300 text-slate-900 focus:ring-slate-400"
                                        @checked(old('status_enabled', $settings->status_enabled))
                                    >
                                    {{ __('ui.enable') }}
                                </label>
                            </div>

                            <div class="mt-5 max-w-xs">
                                <label for="status_days" class="text-sm font-medium text-slate-700">{{ __('ui.days_in_advance') }}</label>
                                <input
                                    id="status_days"
                                    name="status_days"
                                    type="number"
                                    min="1"
                                    max="365"
                                    value="{{ old('status_days', $settings->status_days) }}"
                                    class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400"
                                >
                                <x-input-error class="mt-2" :messages="$errors->get('status_days')" />
                            </div>
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 lg:col-span-2">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h4 class="text-sm font-semibold text-slate-900">{{ __('ui.alert_validations_title') }}</h4>
                                    <p class="mt-2 text-sm text-slate-500">{{ __('ui.alert_validations_help') }}</p>
                                </div>
                                <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                                    <input type="hidden" name="validations_enabled" value="0">
                                    <input
                                        type="checkbox"
                                        name="validations_enabled"
                                        value="1"
                                        class="rounded border-slate-300 text-slate-900 focus:ring-slate-400"
                                        @checked(old('validations_enabled', $settings->validations_enabled))
                                    >
                                    {{ __('ui.enable') }}
                                </label>
                            </div>

                            <div class="mt-5 max-w-xs">
                                <label for="validations_days" class="text-sm font-medium text-slate-700">{{ __('ui.days_in_advance') }}</label>
                                <input
                                    id="validations_days"
                                    name="validations_days"
                                    type="number"
                                    min="1"
                                    max="365"
                                    value="{{ old('validations_days', $settings->validations_days) }}"
                                    class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400"
                                >
                                <x-input-error class="mt-2" :messages="$errors->get('validations_days')" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('ui.save_alerts') }}</x-primary-button>
                        <p class="text-sm text-slate-500">{{ __('ui.user_scoped_configuration') }}</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
