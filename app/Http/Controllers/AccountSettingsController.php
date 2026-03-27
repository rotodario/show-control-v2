<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserAlertSettingsRequest;
use App\Http\Requests\UpdateUserPdfSettingsRequest;
use App\Models\UserAlertSetting;
use App\Models\UserPdfSetting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccountSettingsController extends Controller
{
    public function profile(Request $request): View
    {
        return view('account.profile', [
            'user' => $request->user(),
            'accountSection' => 'profile',
        ]);
    }

    public function alerts(Request $request): View
    {
        return view('account.alerts', [
            'user' => $request->user(),
            'settings' => $request->user()?->alertSettings()->firstOrNew(),
            'accountSection' => 'alerts',
        ]);
    }

    public function updateAlerts(UpdateUserAlertSettingsRequest $request): RedirectResponse
    {
        UserAlertSetting::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->validated(),
        );

        return redirect()
            ->route('account.alerts')
            ->with('status', 'alert-settings-updated');
    }

    public function pdf(Request $request): View
    {
        return view('account.pdf', [
            'user' => $request->user(),
            'settings' => $request->user()?->pdfSettings()->firstOrNew(),
            'accountSection' => 'pdf',
        ]);
    }

    public function updatePdf(UpdateUserPdfSettingsRequest $request): RedirectResponse
    {
        UserPdfSetting::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->validated(),
        );

        return redirect()
            ->route('account.pdf')
            ->with('status', 'pdf-settings-updated');
    }

    public function preferences(Request $request): View
    {
        return view('account.preferences', [
            'user' => $request->user(),
            'accountSection' => 'preferences',
        ]);
    }
}
