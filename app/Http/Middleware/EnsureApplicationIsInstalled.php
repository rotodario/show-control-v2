<?php

namespace App\Http\Middleware;

use App\Support\InstallationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApplicationIsInstalled
{
    public function __construct(private readonly InstallationService $installationService)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->installationService->isInstalled()) {
            return $next($request);
        }

        if ($request->routeIs('install.*')) {
            return $next($request);
        }

        return redirect()->route('install.index');
    }
}
