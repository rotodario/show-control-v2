<?php

namespace App\Http\Middleware;

use App\Models\AppSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('app.supported_locales', ['es', 'en']);
        $defaultLocale = config('app.locale', 'es');

        if (Schema::hasTable('app_settings')) {
            $defaultLocale = AppSetting::getValue('platform_default_locale', $defaultLocale);
        }
        $locale = $defaultLocale;

        if ($request->user()?->preferences?->ui_locale) {
            $locale = $request->user()->preferences->ui_locale;
        }

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = config('app.fallback_locale', 'es');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
