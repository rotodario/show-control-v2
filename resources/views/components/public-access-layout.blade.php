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
        <main class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(14,165,233,0.12),_transparent_35%),linear-gradient(180deg,_#f8fafc_0%,_#eef2ff_100%)]">
            {{ $slot }}
        </main>
    </body>
</html>
