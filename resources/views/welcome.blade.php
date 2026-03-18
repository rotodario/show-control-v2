<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Show Control') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @unless (app()->environment('testing'))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endunless
    </head>
    <body class="min-h-screen bg-slate-950 text-white">
        <main class="mx-auto flex min-h-screen max-w-7xl flex-col justify-center px-6 py-16 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.4em] text-sky-300">Show Control v2</p>
                    <h1 class="mt-6 max-w-4xl text-4xl font-black tracking-tight text-white sm:text-6xl">
                        Gestion web de giras, bolos y documentacion tecnica.
                    </h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">
                        Base Laravel preparada para produccion, persistencia real, accesos por rol y arquitectura mantenible para seguir hasta calendario, alertas, actividad y PDF.
                    </p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-full bg-sky-500 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-sky-400">
                            Entrar
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full border border-slate-700 px-6 py-3 text-sm font-semibold text-white transition hover:border-slate-500 hover:bg-slate-900">
                                Crear usuario
                            </a>
                        @endif
                    </div>
                </div>

                <div class="rounded-[2rem] border border-white/10 bg-white/5 p-6 shadow-2xl shadow-sky-950/40 backdrop-blur">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-3xl bg-white/10 p-5">
                            <p class="text-sm text-slate-300">Bloque actual</p>
                            <p class="mt-2 text-2xl font-bold">Giras</p>
                            <p class="mt-3 text-sm text-slate-400">CRUD completo con contactos y documentos propios.</p>
                        </div>
                        <div class="rounded-3xl bg-white/10 p-5">
                            <p class="text-sm text-slate-300">Siguiente base</p>
                            <p class="mt-2 text-2xl font-bold">Bolos</p>
                            <p class="mt-3 text-sm text-slate-400">Horarios, validaciones tecnicas, alertas y PDF.</p>
                        </div>
                        <div class="rounded-3xl bg-white/10 p-5 sm:col-span-2">
                            <p class="text-sm text-slate-300">Stack elegido</p>
                            <p class="mt-2 text-lg font-semibold">Laravel 10 + Blade + Alpine + Tailwind + Breeze + Spatie Permission + Dompdf</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
