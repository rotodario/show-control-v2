<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePlatformMailSettingsRequest;
use App\Support\PlatformMailSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlatformMailController extends Controller
{
    public function edit(PlatformMailSettings $platformMailSettings): View
    {
        return view('platform.mail', [
            'settings' => $platformMailSettings->all(),
        ]);
    }

    public function update(UpdatePlatformMailSettingsRequest $request, PlatformMailSettings $platformMailSettings): RedirectResponse
    {
        $platformMailSettings->save($request->validated());

        return redirect()
            ->route('platform.mail.edit')
            ->with('status', 'Correo global actualizado.');
    }
}
