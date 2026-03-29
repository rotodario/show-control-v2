<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ __('ui.account') }}</p>
                <h2 class="text-2xl font-semibold text-slate-900">{{ __('ui.account_mail_title') }}</h2>
            </div>
            <p class="max-w-2xl text-sm text-slate-500">
                {{ __('ui.account_mail_description') }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('account.partials.nav')
            <x-status-message />

            <form method="POST" action="{{ route('account.mail.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.external_roadmap_title') }}</h3>
                            <p class="mt-2 text-sm text-slate-500">{{ __('ui.external_roadmap_help') }}</p>
                        </div>
                        <label class="inline-flex items-center gap-3 rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                            <input type="checkbox" name="notifications_enabled" value="1" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500" @checked(old('notifications_enabled', $settings->notifications_enabled))>
                            {{ __('ui.enable_roadmap') }}
                        </label>
                    </div>

                    <div class="mt-6 grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="from_name" class="text-sm font-semibold text-slate-900">{{ __('ui.visible_sender_name') }}</label>
                            <input id="from_name" name="from_name" type="text" value="{{ old('from_name', $settings->from_name) }}" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400">
                            <x-input-error class="mt-2" :messages="$errors->get('from_name')" />
                        </div>

                        <div>
                            <label for="reply_to_email" class="text-sm font-semibold text-slate-900">{{ __('ui.reply_to_email') }}</label>
                            <input id="reply_to_email" name="reply_to_email" type="email" value="{{ old('reply_to_email', $settings->reply_to_email) }}" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400">
                            <x-input-error class="mt-2" :messages="$errors->get('reply_to_email')" />
                        </div>

                        <div class="md:col-span-2">
                            <label for="recipients" class="text-sm font-semibold text-slate-900">{{ __('ui.roadmap_recipients') }}</label>
                            <textarea id="recipients" name="recipients" rows="3" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400" placeholder="tecnico@ejemplo.com, produccion@ejemplo.com">{{ old('recipients', $settings->recipients) }}</textarea>
                            <p class="mt-2 text-xs text-slate-500">{{ __('ui.recipient_split_help') }}</p>
                            <x-input-error class="mt-2" :messages="$errors->get('recipients')" />
                        </div>

                        <div class="md:col-span-2">
                            <label for="cc_recipients" class="text-sm font-semibold text-slate-900">{{ __('ui.roadmap_cc') }}</label>
                            <textarea id="cc_recipients" name="cc_recipients" rows="2" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400">{{ old('cc_recipients', $settings->cc_recipients) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('cc_recipients')" />
                        </div>
                    </div>
                </section>

                <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.roadmap_template_title') }}</h3>
                            <p class="mt-2 text-sm text-slate-500">
                                {{ __('ui.available_variables') }}:
                                @verbatim
                                    <code>{{show_name}}</code>, <code>{{show_date}}</code>, <code>{{show_city}}</code>, <code>{{show_venue}}</code>, <code>{{show_status}}</code>, <code>{{show_url}}</code>, <code>{{travel_mode}}</code>, <code>{{travel_duration}}</code>, <code>{{travel_distance}}</code>, <code>{{contact_name}}</code>, <code>{{contact_phone}}</code>, <code>{{contact_email}}</code>, <code>{{signature}}</code>.
                                @endverbatim
                            </p>
                        </div>
                        <button type="submit" name="reset_template" value="roadmap" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                            {{ __('ui.restore_default_template') }}
                        </button>
                    </div>

                    <div class="mt-6 grid gap-5">
                        <div>
                            <label for="subject_template" class="text-sm font-semibold text-slate-900">{{ __('ui.subject') }}</label>
                            <input id="subject_template" name="subject_template" type="text" value="{{ old('subject_template', $settings->subject_template) }}" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400">
                            <x-input-error class="mt-2" :messages="$errors->get('subject_template')" />
                        </div>

                        <div>
                            <label for="body_template" class="text-sm font-semibold text-slate-900">{{ __('ui.message_body') }}</label>
                            <textarea id="body_template" name="body_template" rows="12" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400">{{ old('body_template', $settings->body_template) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('body_template')" />
                        </div>

                        <div>
                            <label for="signature" class="text-sm font-semibold text-slate-900">{{ __('ui.signature') }}</label>
                            <textarea id="signature" name="signature" rows="3" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400">{{ old('signature', $settings->signature) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('signature')" />
                        </div>
                    </div>
                </section>

                <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.operational_alert_title') }}</h3>
                            <p class="mt-2 text-sm text-slate-500">{{ __('ui.operational_alert_help') }}</p>
                        </div>
                        <label class="inline-flex items-center gap-3 rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                            <input type="checkbox" name="alert_notifications_enabled" value="1" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500" @checked(old('alert_notifications_enabled', $settings->alert_notifications_enabled))>
                            {{ __('ui.enable_alerts') }}
                        </label>
                    </div>

                    <div class="mt-6 grid gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label for="alert_recipients" class="text-sm font-semibold text-slate-900">{{ __('ui.alert_recipients') }}</label>
                            <textarea id="alert_recipients" name="alert_recipients" rows="3" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400">{{ old('alert_recipients', $settings->alert_recipients) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('alert_recipients')" />
                        </div>

                        <div class="md:col-span-2">
                            <label for="alert_cc_recipients" class="text-sm font-semibold text-slate-900">{{ __('ui.alert_cc') }}</label>
                            <textarea id="alert_cc_recipients" name="alert_cc_recipients" rows="2" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400">{{ old('alert_cc_recipients', $settings->alert_cc_recipients) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('alert_cc_recipients')" />
                        </div>

                        <div>
                            <label for="alert_subject_template" class="text-sm font-semibold text-slate-900">{{ __('ui.alert_subject') }}</label>
                            <input id="alert_subject_template" name="alert_subject_template" type="text" value="{{ old('alert_subject_template', $settings->alert_subject_template) }}" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400">
                            <x-input-error class="mt-2" :messages="$errors->get('alert_subject_template')" />
                        </div>

                        <div class="md:col-span-2">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex-1">
                                    <label for="alert_body_template" class="text-sm font-semibold text-slate-900">{{ __('ui.alert_body') }}</label>
                                    <textarea id="alert_body_template" name="alert_body_template" rows="8" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400">{{ old('alert_body_template', $settings->alert_body_template) }}</textarea>
                                    <p class="mt-2 text-xs text-slate-500">
                                        {{ __('ui.available_variables') }}:
                                        @verbatim
                                            <code>{{show_name}}</code>, <code>{{show_date}}</code>, <code>{{show_city}}</code>, <code>{{show_venue}}</code>, <code>{{show_status}}</code>, <code>{{show_url}}</code>, <code>{{travel_mode}}</code>, <code>{{alert_count}}</code>, <code>{{alert_lines}}</code>, <code>{{contact_name}}</code>, <code>{{contact_phone}}</code>, <code>{{contact_email}}</code>, <code>{{signature}}</code>.
                                        @endverbatim
                                    </p>
                                    <x-input-error class="mt-2" :messages="$errors->get('alert_body_template')" />
                                </div>
                                <button type="submit" name="reset_template" value="alert" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                    {{ __('ui.restore_default_template') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.mail_preview_title') }}</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ __('ui.mail_preview_reference', [
                                'show' => $previewShow->name,
                                'date' => $previewShow->date?->format('d/m/Y') ?: '-',
                                'city' => $previewShow->city ?: '-',
                            ]) }}
                        </p>
                    </div>

                    <div class="grid gap-6 xl:grid-cols-2">
                        <section class="rounded-[2rem] border border-emerald-200 bg-emerald-50/40 p-6 shadow-sm">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <h4 class="text-lg font-semibold text-slate-900">{{ __('ui.send_roadmap') }}</h4>
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
                                    <h4 class="text-lg font-semibold text-slate-900">{{ __('ui.send_alert') }}</h4>
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
                                        @foreach ($previewAlerts as $alert)
                                            <div class="rounded-xl border border-amber-200 bg-white px-3 py-3 text-sm text-slate-700">
                                                <p class="font-semibold text-slate-900">{{ $alert['title'] }}</p>
                                                <p class="mt-1">{{ $alert['message'] }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('ui.body') }}</p>
                                    <div class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $alertPreview['body'] }}</div>
                                </div>
                            </div>
                        </section>
                    </div>
                </section>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        {{ __('ui.save_mail') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
