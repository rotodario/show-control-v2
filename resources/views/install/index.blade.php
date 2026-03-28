<x-guest-layout>
    <div class="mx-auto w-full max-w-5xl space-y-8 px-4 py-10 sm:px-6 lg:px-8">
        <div class="space-y-3">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Instalacion</p>
            <h1 class="text-3xl font-semibold text-slate-900">Configurar Show Control v2</h1>
            <p class="max-w-3xl text-sm text-slate-600">
                Crea la conexion con la base de datos, instala la version actual completa del proyecto y genera el primer super admin de la plataforma.
            </p>
        </div>

        <div class="grid gap-6 xl:grid-cols-[0.75fr_1.25fr]">
            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Comprobaciones</h2>
                <div class="mt-5 space-y-3">
                    @foreach ($requirements as $label => $passed)
                        <div class="flex items-center justify-between rounded-2xl border px-4 py-3 {{ $passed ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-rose-200 bg-rose-50 text-rose-800' }}">
                            <span class="text-sm font-medium">{{ str($label)->replace('_', ' ')->title() }}</span>
                            <span class="text-xs font-semibold uppercase tracking-[0.2em]">{{ $passed ? 'OK' : 'Fallo' }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Datos de instalacion</h2>

                @if ($errors->has('install'))
                    <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                        {{ $errors->first('install') }}
                    </div>
                @endif

                @if (! $canInstall)
                    <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                        Corrige primero los requisitos marcados como fallo. El instalador necesita poder escribir `.env`, `storage` y `bootstrap/cache`.
                    </div>
                @endif

                <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                    El instalador aplicara todas las migraciones actuales, configurara el bootstrap seguro de super admin y dejara listas las funciones de bolos, accesos compartidos, alertas, chat interno, PDF, logistica de viaje, preferencias de cuenta, usuarios de plataforma y herramientas de backup basadas en web.
                </div>

                <form method="POST" action="{{ route('install.store') }}" class="mt-6 space-y-6">
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <x-input-label for="app_name" value="Nombre de la aplicacion" />
                            <x-text-input id="app_name" name="app_name" type="text" class="mt-1 block w-full" :value="old('app_name', 'Show Control v2')" />
                            <x-input-error class="mt-2" :messages="$errors->get('app_name')" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="app_url" value="URL publica" />
                            <x-text-input id="app_url" name="app_url" type="url" class="mt-1 block w-full" :value="old('app_url')" />
                            <x-input-error class="mt-2" :messages="$errors->get('app_url')" />
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="db_host" value="Host MySQL" />
                            <x-text-input id="db_host" name="db_host" type="text" class="mt-1 block w-full" :value="old('db_host', '127.0.0.1')" />
                        </div>
                        <div>
                            <x-input-label for="db_port" value="Puerto MySQL" />
                            <x-text-input id="db_port" name="db_port" type="number" class="mt-1 block w-full" :value="old('db_port', 3306)" />
                        </div>
                        <div>
                            <x-input-label for="db_database" value="Base de datos" />
                            <x-text-input id="db_database" name="db_database" type="text" class="mt-1 block w-full" :value="old('db_database')" />
                        </div>
                        <div>
                            <x-input-label for="db_username" value="Usuario BD" />
                            <x-text-input id="db_username" name="db_username" type="text" class="mt-1 block w-full" :value="old('db_username')" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="db_password" value="Password BD" />
                            <x-text-input id="db_password" name="db_password" type="password" class="mt-1 block w-full" />
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="admin_name" value="Nombre super admin" />
                            <x-text-input id="admin_name" name="admin_name" type="text" class="mt-1 block w-full" :value="old('admin_name', 'Admin')" />
                        </div>
                        <div>
                            <x-input-label for="admin_email" value="Email super admin" />
                            <x-text-input id="admin_email" name="admin_email" type="email" class="mt-1 block w-full" :value="old('admin_email')" />
                        </div>
                        <div>
                            <x-input-label for="admin_password" value="Password super admin" />
                            <x-text-input id="admin_password" name="admin_password" type="password" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label for="admin_password_confirmation" value="Confirmar password" />
                            <x-text-input id="admin_password_confirmation" name="admin_password_confirmation" type="password" class="mt-1 block w-full" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50" @disabled(! $canInstall)>
                            Instalar aplicacion
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-guest-layout>
