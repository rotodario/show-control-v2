<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePlatformUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlatformUserController extends Controller
{
    public function index(): View
    {
        return view('platform.users.index', [
            'users' => User::query()
                ->with('roles')
                ->withCount(['tours', 'shows'])
                ->orderBy('name')
                ->get(),
            'assignableRoles' => [
                'admin' => 'Admin',
                'super_admin' => 'Super Admin',
            ],
        ]);
    }

    public function update(UpdatePlatformUserRequest $request, User $user): RedirectResponse
    {
        $actor = $request->user();
        $targetRole = $request->validated('role');
        $targetActive = (bool) $request->validated('is_active');

        if ($actor->is($user) && ! $targetActive) {
            return redirect()
                ->route('platform.users.index')
                ->with('platform_error', __('ui.platform_users_cannot_deactivate_self'));
        }

        $currentSuperAdminCount = User::role('super_admin')->count();

        if (
            $user->hasRole('super_admin')
            && $currentSuperAdminCount <= 1
            && ($targetRole !== 'super_admin' || ! $targetActive)
        ) {
            return redirect()
                ->route('platform.users.index')
                ->with('platform_error', __('ui.platform_users_last_super_admin'));
        }

        $user->syncRoles([$targetRole]);
        $user->update([
            'is_active' => $targetActive,
        ]);

        return redirect()
            ->route('platform.users.index')
            ->with('platform_status', __('ui.platform_users_saved'));
    }
}
