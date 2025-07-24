<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Configuration; // Importa tu modelo Configuration
use Illuminate\Support\Facades\Route; // Para verificar la ruta actual
use Illuminate\Support\Facades\Auth;

class CheckInstallationStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Define the route to redirect if the application is not installed.
        $installationRouteName = 'configuration.index';

        //  If we're on the configuration page or a page that doesn't require installation, 
        // allow the request to continue.
        if ($request->routeIs($installationRouteName) ||
            $request->routeIs('configuration.store') ||
            $request->routeIs('login') ||
            $request->routeIs('register') ||
            $request->routeIs('password.request') ||
            $request->routeIs('password.reset')) {
            return $next($request);
        }

        $userId = Auth::id();

        // Check if the 'installed' parameter exists with value '1' in the 'configuration' table.
        $isInstalled = Configuration::where('parameter', 'installed')
                                    ->where('value', '1')
                                    ->where('user_id', $userId)
                                    ->exists();

        // If not installed, redirect to the configuration page.
        if (!$isInstalled) {
            return redirect()->route($installationRouteName);
        }

        // If installed, allow the request to continue to the next layer.
        return $next($request);
    }
}