<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Plataforma</p>
                <h2 class="text-2xl font-semibold text-slate-900">Correo global</h2>
            </div>
            <p class="max-w-2xl text-sm text-slate-500">
                Ajustes globales para correos de registro y otros avisos de sistema.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('platform.partials.nav')
            <x-status-message />

            <form method="POST" action="{{ route('platform.mail.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Avisos de registro</h3>
                            <p class="mt-2 text-sm text-slate-500">
                                Cuando se registre una cuenta nueva, el sistema puede enviar un aviso a los destinatarios globales definidos aqui.
                            </p>
                        </div>
                        <label class="inline-flex items-center gap-3 rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                            <input type="checkbox" name="registration_notifications_enabled" value="1" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500" @checked(old('registration_notifications_enabled', $settings['registration_notifications_enabled']))>
                            Activar
                        </label>
                    </div>

                    <div class="mt-6 grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="platform_mail_from_name" class="text-sm font-semibold text-slate-900">Nombre remitente</label>
                            <input id="platform_mail_from_name" name="platform_mail_from_name" type="text" value="{{ old('platform_mail_from_name', $settings['platform_mail_from_name']) }}" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400">
                            <x-input-error class="mt-2" :messages="$errors->get('platform_mail_from_name')" />
                        </div>

                        <div>
                            <label for="platform_mail_from_address" class="text-sm font-semibold text-slate-900">Email remitente</label>
                            <input id="platform_mail_from_address" name="platform_mail_from_address" type="email" value="{{ old('platform_mail_from_address', $settings['platform_mail_from_address']) }}" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400">
                            <x-input-error class="mt-2" :messages="$errors->get('platform_mail_from_address')" />
                        </div>

                        <div>
                            <label for="platform_mail_reply_to_email" class="text-sm font-semibold text-slate-900">Reply-to</label>
                            <input id="platform_mail_reply_to_email" name="platform_mail_reply_to_email" type="email" value="{{ old('platform_mail_reply_to_email', $settings['platform_mail_reply_to_email']) }}" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400">
                            <x-input-error class="mt-2" :messages="$errors->get('platform_mail_reply_to_email')" />
                        </div>

                        <div class="md:col-span-2">
                            <label for="platform_registration_recipients" class="text-sm font-semibold text-slate-900">Destinatarios de registro</label>
                            <textarea id="platform_registration_recipients" name="platform_registration_recipients" rows="3" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400">{{ old('platform_registration_recipients', $settings['platform_registration_recipients']) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('platform_registration_recipients')" />
                        </div>
                    </div>
                </section>

                <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Plantilla del aviso global</h3>
                    <p class="mt-2 text-sm text-slate-500">
                        Variables disponibles:
                        @verbatim
                            <code>{{user_name}}</code>, <code>{{user_email}}</code>, <code>{{registered_at}}</code>.
                        @endverbatim
                    </p>

                    <div class="mt-6 grid gap-5">
                        <div>
                            <label for="platform_registration_subject" class="text-sm font-semibold text-slate-900">Asunto</label>
                            <input id="platform_registration_subject" name="platform_registration_subject" type="text" value="{{ old('platform_registration_subject', $settings['platform_registration_subject']) }}" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400">
                            <x-input-error class="mt-2" :messages="$errors->get('platform_registration_subject')" />
                        </div>

                        <div>
                            <label for="platform_registration_body" class="text-sm font-semibold text-slate-900">Cuerpo del mensaje</label>
                            <textarea id="platform_registration_body" name="platform_registration_body" rows="10" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-slate-400 focus:ring-slate-400">{{ old('platform_registration_body', $settings['platform_registration_body']) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('platform_registration_body')" />
                        </div>
                    </div>
                </section>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Guardar correo global
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
