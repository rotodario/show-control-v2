<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePlatformSettingsRequest;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlatformSettingsController extends Controller
{
    public function edit(): View
    {
        return view('platform.settings', [
            'settings' => [
                'platform_default_locale' => AppSetting::getValue('platform_default_locale', config('app.locale', 'es')),
            ],
            'localeLabels' => config('app.locale_labels', ['es' => 'Español', 'en' => 'English']),
        ]);
    }

    public function update(UpdatePlatformSettingsRequest $request): RedirectResponse
    {
        AppSetting::putValue('platform_default_locale', $request->validated('platform_default_locale'));

        return redirect()
            ->route('platform.settings.edit')
            ->with('status', __('ui.platform_settings_saved'));
    }
}
