<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ __('ui.show_record') }}</p>
                <h2 class="text-3xl font-semibold text-slate-900">{{ __('ui.send_mail') }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $show->name }} · {{ $show->date->format('d/m/Y') }} · {{ $show->city }}</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <form method="POST" action="{{ route('shows.mail.send', $show) }}">
                    @csrf
                    <input type="hidden" name="mail_type" value="roadmap">
                    <button type="submit" class="inline-flex items-center justify-center rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-800 transition hover:bg-emerald-100 disabled:cursor-not-allowed disabled:opacity-60" @disabled(! $roadmapPreview['enabled'] || $roadmapPreview['to'] === [])>
                        {{ __('ui.send_roadmap') }}
                    </button>
                </form>
                <form method="POST" action="{{ route('shows.mail.send', $show) }}">
                    @csrf
                    <input type="hidden" name="mail_type" value="alert">
                    <button type="submit" class="inline-flex items-center justify-center rounded-full border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-semibold text-amber-900 transition hover:bg-amber-100 disabled:cursor-not-allowed disabled:opacity-60" @disabled(! $alertPreview['enabled'] || $alertPreview['to'] === [] || $alerts === [])>
                        {{ __('ui.send_alert') }}
                    </button>
                </form>
                <a href="{{ route('shows.show', $show) }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                    {{ __('ui.back_to_show') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 xl:grid-cols-2">
                <section class="rounded-[2rem] border border-emerald-200 bg-emerald-50/40 p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.send_roadmap') }}</h3>
                            <p class="text-sm text-slate-500">{{ __('ui.mail_preview_help') }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-medium {{ $roadmapPreview['enabled'] ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $roadmapPreview['enabled'] ? __('ui.enabled') : __('ui.disabled') }}
                        </span>
                    </div>

                    <div class="mt-6 space-y-4">

                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.to') }}</p>
                            <p class="mt-2 text-sm text-slate-700">{{ $roadmapPreview['to'] ? implode(', ', $roadmapPreview['to']) : __('ui.no_recipients_configured') }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.cc') }}</p>
                            <p class="mt-2 text-sm text-slate-700">{{ $roadmapPreview['cc'] ? implode(', ', $roadmapPreview['cc']) : '-' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.subject') }}</p>
                            <p class="mt-2 text-sm font-medium text-slate-900">{{ $roadmapPreview['subject'] }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.attachment') }}</p>
                            <p class="mt-2 text-sm text-slate-700">{{ $roadmapPreview['attachment_name'] }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.body') }}</p>
                            <div class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $roadmapPreview['body'] }}</div>
                        </div>
                    </div>

                </section>

                <section class="rounded-[2rem] border border-amber-200 bg-amber-50/40 p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.send_alert') }}</h3>
                            <p class="text-sm text-slate-500">{{ __('ui.mail_preview_help') }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-medium {{ $alertPreview['enabled'] ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $alertPreview['enabled'] ? __('ui.enabled') : __('ui.disabled') }}
                        </span>
                    </div>

                    <div class="mt-6 space-y-4">

                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.to') }}</p>
                            <p class="mt-2 text-sm text-slate-700">{{ $alertPreview['to'] ? implode(', ', $alertPreview['to']) : __('ui.no_recipients_configured') }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.cc') }}</p>
                            <p class="mt-2 text-sm text-slate-700">{{ $alertPreview['cc'] ? implode(', ', $alertPreview['cc']) : '-' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.subject') }}</p>
                            <p class="mt-2 text-sm font-medium text-slate-900">{{ $alertPreview['subject'] }}</p>
                        </div>
                        <div class="rounded-2xl bg-amber-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">{{ __('ui.alerts') }}</p>
                            <div class="mt-2 space-y-2">
                                @forelse ($alerts as $alert)
                                    <div class="rounded-xl border border-amber-200 bg-white px-3 py-3 text-sm text-slate-700">
                                        <p class="font-semibold text-slate-900">{{ $alert['title'] }}</p>
                                        <p class="mt-1">{{ $alert['message'] }}</p>
                                    </div>
                                @empty
                                    <p class="text-sm text-amber-800">{{ __('ui.no_active_alerts_for_mail') }}</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.body') }}</p>
                            <div class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $alertPreview['body'] }}</div>
                        </div>
                    </div>

                </section>
            </div>
        </div>
    </div>
</x-app-layout>
