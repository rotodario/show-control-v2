<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Show Control') }}</title>
        @unless (app()->environment('testing'))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endunless
    </head>
    <body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(14,165,233,0.12),_transparent_35%),linear-gradient(180deg,_#f8fafc_0%,_#eef2ff_100%)]">
            <main>
                {{ $slot }}
            </main>

            <footer class="px-4 pb-8 pt-2 text-sm text-slate-500 sm:px-6 lg:px-8">
                <div class="mx-auto flex max-w-7xl items-center justify-center gap-3">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-900 text-[10px] font-black tracking-[0.18em] text-white">
                        SC
                    </span>
                    <span>{{ __('ui.footer', ['year' => now()->year]) }}</span>
                </div>
            </footer>
        </div>
    </body>
</html>
