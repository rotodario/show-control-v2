<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ __('ui.platform') }}</p>
                <h2 class="text-2xl font-semibold text-slate-900">{{ __('ui.tools') }}</h2>
            </div>
            <p class="max-w-2xl text-sm text-slate-500">
                {{ __('ui.platform_tools_description') }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('platform.partials.nav')

            @if (session('platform_status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                    {{ session('platform_status') }}
                </div>
            @endif

            @if (session('platform_error'))
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-medium text-rose-700">
                    {{ session('platform_error') }}
                </div>
            @endif

            <div class="flex flex-wrap gap-2">
                <div class="flex h-[4.6rem] w-[11.5rem] flex-col justify-between rounded-2xl border border-slate-200 bg-white px-3 py-2.5 shadow-sm">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('ui.users') }}</p>
                    <div class="mt-1 flex items-end justify-between gap-2">
                        <p class="text-xl font-semibold text-slate-900">{{ $metrics['users_total'] }}</p>
                        <p class="text-[10px] text-slate-500">{{ $metrics['users_active'] }} {{ __('ui.active_plural') }}</p>
                    </div>
                </div>
                <div class="flex h-[4.6rem] w-[11.5rem] flex-col justify-between rounded-2xl border border-slate-200 bg-white px-3 py-2.5 shadow-sm">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('ui.super_admin') }}</p>
                    <div class="mt-1 flex items-end justify-between gap-2">
                        <p class="text-xl font-semibold text-slate-900">{{ $metrics['super_admins_active'] }}</p>
                        <p class="text-[10px] text-slate-500">{{ __('ui.active_plural') }}</p>
                    </div>
                </div>
                <div class="flex h-[4.6rem] w-[11.5rem] flex-col justify-between rounded-2xl border border-slate-200 bg-white px-3 py-2.5 shadow-sm">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('ui.tours') }}</p>
                    <div class="mt-1 flex items-end justify-between gap-2">
                        <p class="text-xl font-semibold text-slate-900">{{ $metrics['tours_total'] }}</p>
                    </div>
                </div>
                <div class="flex h-[4.6rem] w-[11.5rem] flex-col justify-between rounded-2xl border border-slate-200 bg-white px-3 py-2.5 shadow-sm">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('ui.shows') }}</p>
                    <div class="mt-1 flex items-end justify-between gap-2">
                        <p class="text-xl font-semibold text-slate-900">{{ $metrics['shows_total'] }}</p>
                    </div>
                </div>
                <div class="flex h-[4.6rem] w-[11.5rem] flex-col justify-between rounded-2xl border border-slate-200 bg-white px-3 py-2.5 shadow-sm">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('ui.backups') }}</p>
                    <div class="mt-1 flex items-end justify-between gap-2">
                        <p class="text-xl font-semibold text-slate-900">{{ count($backups) }}</p>
                        <p class="text-[10px] text-slate-500">{{ __('ui.storage') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.system_health') }}</h3>
                    <div class="mt-6 space-y-3">
                        @foreach ($checks as $check)
                            <div class="rounded-2xl border px-4 py-4 {{ $check['ok'] ? 'border-emerald-200 bg-emerald-50' : 'border-rose-200 bg-rose-50' }}">
                                <div class="flex items-center justify-between gap-4">
                                    <p class="text-sm font-semibold {{ $check['ok'] ? 'text-emerald-800' : 'text-rose-800' }}">{{ $check['label'] }}</p>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $check['ok'] ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $check['ok'] ? __('ui.ok') : __('ui.fail') }}
                                    </span>
                                </div>
                                <p class="mt-2 text-sm {{ $check['ok'] ? 'text-emerald-700' : 'text-rose-700' }}">{{ $check['detail'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="space-y-6">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.create_backup') }}</h3>
                                <p class="mt-2 text-sm text-slate-500">
                                    {{ __('ui.create_backup_help') }}
                                </p>
                            </div>

                            <form method="POST" action="{{ route('platform.tools.backup') }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                    {{ __('ui.create_and_download_backup') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.saved_backups') }}</h3>
                        <div class="mt-5 space-y-3">
                            @forelse ($backups as $backup)
                                <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $backup['filename'] }}</p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $backup['modified_at']->format('d/m/Y H:i') }} · {{ $backup['size_human'] }}
                                        </p>
                                    </div>
                                    <a href="{{ route('platform.tools.download', $backup['filename']) }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                        {{ __('ui.download') }}
                                    </a>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500">
                                    {{ __('ui.no_saved_backups') }}
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-3xl border border-rose-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.restore_backup') }}</h3>
                        <p class="mt-2 text-sm text-slate-500">
                            {{ __('ui.restore_backup_help') }}
                        </p>
                        <p class="mt-2 text-sm font-medium text-rose-700">
                            {{ __('ui.restore_backup_warning') }}
                        </p>

                        <form method="POST" action="{{ route('platform.tools.restore') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                            @csrf

                            <div>
                                <label for="backup_file" class="text-sm font-semibold text-slate-900">{{ __('ui.backup_json_file') }}</label>
                                <input id="backup_file" name="backup_file" type="file" accept=".json,application/json" class="mt-2 block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-700">
                                <x-input-error class="mt-2" :messages="$errors->get('backup_file')" />
                            </div>

                            <div>
                                <label for="confirmation" class="text-sm font-semibold text-slate-900">{{ __('ui.type_restore_to_confirm') }}</label>
                                <input id="confirmation" name="confirmation" type="text" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400" placeholder="RESTAURAR">
                                <x-input-error class="mt-2" :messages="$errors->get('confirmation')" />
                            </div>

                            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-rose-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-rose-500">
                                {{ __('ui.restore_backup') }}
                            </button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
