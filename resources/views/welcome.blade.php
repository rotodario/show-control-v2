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
            <div class="grid gap-12 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.4em] text-sky-300">Show Control v2.2</p>
                    <h1 class="mt-6 max-w-4xl text-4xl font-black tracking-tight text-white sm:text-6xl">
                        Gestion web de giras, bolos y documentacion tecnica.
                    </h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">
                        Base preparada para produccion con bolos, calendario, alertas, roadmap PDF, accesos por token, correo operativo, logistica de viaje y herramientas de plataforma.
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
                    <div class="space-y-4">
                        <div class="rounded-3xl bg-white/10 p-5">
                            <p class="text-sm text-slate-300">Hoja de ruta</p>
                            <p class="mt-2 text-xl font-bold text-white">Genera y envia roadmap PDF rapidamente.</p>
                            <p class="mt-3 text-sm text-slate-400">Con branding, contacto, estado del bolo y resumen operativo listo para compartir.</p>
                        </div>
                        <div class="rounded-3xl bg-white/10 p-5">
                            <p class="text-sm text-slate-300">Chat interno</p>
                            <p class="mt-2 text-xl font-bold text-white">Coordina por secciones sin perder contexto.</p>
                            <p class="mt-3 text-sm text-slate-400">Iluminacion, sonido, espacio y general con mensajes persistentes y no leidos.</p>
                        </div>
                        <div class="rounded-3xl bg-white/10 p-5">
                            <p class="text-sm text-slate-300">Accesos y equipo</p>
                            <p class="mt-2 text-xl font-bold text-white">Comparte solo lo que toca ver.</p>
                            <p class="mt-3 text-sm text-slate-400">Enlaces por token, permisos por rol y gestion rapida de usuarios desde plataforma.</p>
                        </div>
                        <div class="rounded-3xl bg-white/10 p-5">
                            <p class="text-sm text-slate-300">Logistica</p>
                            <p class="mt-2 text-xl font-bold text-white">Ten clara la ruta antes del dia de show.</p>
                            <p class="mt-3 text-sm text-slate-400">Tiempo, distancia, mapa embebido y datos manuales de vuelo cuando hacen falta.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
