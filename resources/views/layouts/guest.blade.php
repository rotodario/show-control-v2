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
    <body class="font-sans text-gray-900 antialiased transition-colors dark:bg-slate-950 dark:text-slate-100">
        <div class="min-h-screen flex flex-col bg-gray-100 pt-6 transition-colors dark:bg-slate-950 sm:pt-0">
            <div class="flex flex-1 flex-col items-center justify-center px-4">
            <div>
                <a href="/" class="flex flex-col items-center gap-3">
                    <span class="flex h-20 w-20 items-center justify-center rounded-[1.75rem] bg-slate-900 text-2xl font-black tracking-[0.18em] text-white shadow-lg shadow-slate-900/20 dark:bg-white dark:text-slate-950">
                        SC
                    </span>
                    <span class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400">Show Control</span>
                </a>
            </div>

            <div class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md transition-colors dark:bg-slate-900 sm:max-w-md sm:rounded-lg">
                {{ $slot }}
            </div>
            </div>

            <footer class="border-t border-slate-200/70 bg-transparent px-4 py-4 text-center text-xs text-slate-500 transition-colors dark:border-slate-800/60 dark:text-slate-400">
                <div class="flex items-center justify-center gap-3">
                    <span class="flex h-6 w-6 items-center justify-center rounded-xl bg-slate-900 text-[10px] font-black tracking-[0.16em] text-white dark:bg-white dark:text-slate-950">
                        SC
                    </span>
                    <span>{{ __('ui.footer', ['year' => now()->year]) }}</span>
                </div>
            </footer>
        </div>
    </body>
</html>
