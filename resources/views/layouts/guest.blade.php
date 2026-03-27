<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Show Control') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        <script>
            (() => {
                const storedTheme = localStorage.getItem('theme');
                const theme = storedTheme === 'dark' || storedTheme === 'light'
                    ? storedTheme
                    : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

                document.documentElement.classList.toggle('dark', theme === 'dark');
                document.documentElement.dataset.theme = theme;
            })();
        </script>

        @unless (app()->environment('testing'))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endunless
    </head>
    <body class="font-sans text-gray-900 antialiased transition-colors">
        <div class="min-h-screen flex flex-col bg-gray-100 pt-6 transition-colors sm:pt-0">
            <div class="flex flex-1 flex-col items-center justify-center px-4">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md transition-colors sm:max-w-md sm:rounded-lg">
                {{ $slot }}
            </div>
            </div>

            <footer class="px-4 py-4 text-center text-xs text-slate-500">
                Show Control por Jose Osuna. Licencia MIT. &copy; {{ now()->year }}
            </footer>
        </div>
    </body>
</html>
