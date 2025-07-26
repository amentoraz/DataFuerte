<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Configuration;
use Illuminate\Support\Facades\Route; // To check the current route
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
        //  allow the request to continue IMMEDIATELY.
        if (
            $request->routeIs($installationRouteName) ||
            $request->routeIs('configuration.store') ||
            $request->routeIs('login') || // This should cover GET and POST /
            $request->routeIs('register') ||
            $request->routeIs('password.request') ||
            $request->routeIs('password.reset')
        ) {
            return $next($request); // <--- RETURN HERE to avoid executing the rest of the middleware
        }

        // If the application is not installed, redirect.
        // We only check this if we're not on an exclusion route.
        $isInstalled = Configuration::where('parameter', 'installed')
                                    ->where('value', '1')
                                    ->exists(); // We don't need user_id here initially

        // If not installed AND we're not trying to login or register, redirect.
        if (!$isInstalled) {
            // You can add a condition to ensure you're not on the login route
            // although the first if already should have handled this.
            return redirect()->route($installationRouteName);
        }

        // If installed, or if the exclusion route let us pass, continue.
        return $next($request);
    }
}