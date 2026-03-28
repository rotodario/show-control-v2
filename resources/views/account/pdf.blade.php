<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ __('ui.account') }}</p>
            <h2 class="text-2xl font-semibold text-slate-900">{{ __('ui.pdf_branding') }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('account.partials.nav')

            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.pdf_customization_title') }}</h3>
                        <p class="mt-2 max-w-3xl text-sm text-slate-500">{{ __('ui.pdf_customization_description') }}</p>
                    </div>

                    @if (session('status') === 'pdf-settings-updated')
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                            {{ __('ui.saved') }}
                        </span>
                    @endif
                </div>

                <form method="POST" action="{{ route('account.pdf.update') }}" class="mt-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                            <label for="brand_name" class="text-sm font-semibold text-slate-900">{{ __('ui.brand_name') }}</label>
                            <p class="mt-2 text-sm text-slate-500">{{ __('ui.brand_name_help') }}</p>
                            <input
                                id="brand_name"
                                name="brand_name"
                                type="text"
                                maxlength="120"
                                value="{{ old('brand_name', $settings->brand_name) }}"
                                class="mt-4 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400"
                                placeholder="{{ __('ui.brand_name_placeholder') }}"
                            >
                            <x-input-error class="mt-2" :messages="$errors->get('brand_name')" />
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                            <label for="primary_color_picker" class="text-sm font-semibold text-slate-900">{{ __('ui.primary_color') }}</label>
                            <p class="mt-2 text-sm text-slate-500">{{ __('ui.primary_color_help') }}</p>
                            <div class="mt-4 flex items-center gap-3">
                                <input
                                    id="primary_color_picker"
                                    type="color"
                                    value="{{ old('primary_color', $settings->primary_color ?: '#0f172a') }}"
                                    oninput="this.nextElementSibling.value = this.value"
                                    class="h-12 w-16 rounded-xl border border-slate-300 bg-white p-1"
                                >
                                <input
                                    id="primary_color"
                                    name="primary_color"
                                    type="text"
                                    maxlength="7"
                                    value="{{ old('primary_color', $settings->primary_color ?: '#0f172a') }}"
                                    class="block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400"
                                    placeholder="#0f172a"
                                >
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('primary_color')" />
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                            <label for="header_text" class="text-sm font-semibold text-slate-900">{{ __('ui.header_text') }}</label>
                            <p class="mt-2 text-sm text-slate-500">{{ __('ui.header_text_help') }}</p>
                            <input
                                id="header_text"
                                name="header_text"
                                type="text"
                                maxlength="120"
                                value="{{ old('header_text', $settings->header_text) }}"
                                class="mt-4 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400"
                                placeholder="{{ __('ui.header_text_placeholder') }}"
                            >
                            <x-input-error class="mt-2" :messages="$errors->get('header_text')" />
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                            <label for="footer_text" class="text-sm font-semibold text-slate-900">{{ __('ui.footer_text') }}</label>
                            <p class="mt-2 text-sm text-slate-500">{{ __('ui.footer_text_help') }}</p>
                            <input
                                id="footer_text"
                                name="footer_text"
                                type="text"
                                maxlength="160"
                                value="{{ old('footer_text', $settings->footer_text) }}"
                                class="mt-4 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400"
                                placeholder="{{ __('ui.footer_text_placeholder') }}"
                            >
                            <x-input-error class="mt-2" :messages="$errors->get('footer_text')" />
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-white p-6">
                        <label class="inline-flex items-center gap-3 text-sm font-medium text-slate-700">
                            <input type="hidden" name="show_generated_at" value="0">
                            <input
                                type="checkbox"
                                name="show_generated_at"
                                value="1"
                                class="rounded border-slate-300 text-slate-900 focus:ring-slate-400"
                                @checked(old('show_generated_at', $settings->show_generated_at))
                            >
                            {{ __('ui.show_generated_at') }}
                        </label>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                        <p class="text-sm font-semibold text-slate-900">{{ __('ui.preview') }}</p>
                        <div class="mt-4 rounded-3xl border border-slate-200 bg-white p-6">
                            <div class="text-xs font-semibold uppercase tracking-[0.3em]" style="color: {{ old('primary_color', $settings->primary_color ?: '#0f172a') }}">
                                {{ old('brand_name', $settings->brand_name ?: __('ui.your_brand')) }}
                            </div>
                            <div class="mt-3 text-2xl font-semibold text-slate-900">{{ __('ui.roadmap') }}</div>
                            <div class="mt-2 text-sm text-slate-500">{{ old('header_text', $settings->header_text ?: __('ui.optional_header_text')) }}</div>
                            <div class="mt-5 inline-flex rounded-full px-3 py-1 text-xs font-semibold text-white" style="background: {{ old('primary_color', $settings->primary_color ?: '#0f172a') }}">
                                {{ __('ui.pdf_accent') }}
                            </div>
                            <div class="mt-6 text-sm text-slate-500">{{ old('footer_text', $settings->footer_text ?: __('ui.optional_footer_text')) }}</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('ui.save_pdf') }}</x-primary-button>
                        <p class="text-sm text-slate-500">{{ __('ui.user_scoped_configuration') }}</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
