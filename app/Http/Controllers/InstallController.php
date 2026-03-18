<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\InstallationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InstallController extends Controller
{
    public function index(InstallationService $installationService): View|RedirectResponse
    {
        if ($installationService->isInstalled()) {
            return redirect()->route(Auth::check() ? 'dashboard' : 'login');
        }

        return view('install.index', [
            'requirements' => $installationService->requirements(),
            'canInstall' => $installationService->canInstall(),
        ]);
    }

    public function store(Request $request, InstallationService $installationService): RedirectResponse
    {
        if ($installationService->isInstalled()) {
            return redirect()->route('login');
        }

        abort_unless($installationService->canInstall(), 422);

        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:255'],
            'app_url' => ['required', 'url', 'max:255'],
            'db_host' => ['required', 'string', 'max:255'],
            'db_port' => ['required', 'integer'],
            'db_database' => ['required', 'string', 'max:255'],
            'db_username' => ['required', 'string', 'max:255'],
            'db_password' => ['nullable', 'string', 'max:255'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email:rfc', 'max:255'],
            'admin_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $installationService->install($validated);
        } catch (\Throwable $exception) {
            return back()
                ->withErrors([
                    'install' => 'No se ha podido completar la instalacion. Revisa la conexion a la base de datos y los permisos de escritura.',
                ])
                ->withInput();
        }

        $user = User::query()->where('email', $validated['admin_email'])->first();

        if ($user) {
            Auth::login($user);
        }

        return redirect()
            ->route('dashboard')
            ->with('status', 'Instalacion completada correctamente.');
    }
}
