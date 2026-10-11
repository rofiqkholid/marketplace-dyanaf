<?php

namespace App\Http\Middleware;

use App\Models\Menu;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuMaintenanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for maintenance page itself, auth, static assets, and master management
        if ($request->is('maintenance', 'maintenance/*', 'auth/*', 'logout', 'up')) {
            return $next($request);
        }

        // Pemilik can always access to preview and manage
        if (Auth::check() && Auth::user()->role === 'pemilik') {
            return $next($request);
        }

        $currentPath = '/' . ltrim($request->path(), '/');

        // Check if there is any active menu with is_maintenance = true matching current URL
        $maintenanceMenu = Menu::where('is_active', true)
            ->where('is_maintenance', true)
            ->get()
            ->first(function ($menu) use ($currentPath, $request) {
                if (empty($menu->url) || $menu->url === '#') {
                    return false;
                }

                $menuPath = '/' . ltrim(parse_url($menu->url, PHP_URL_PATH) ?? '', '/');

                if ($menuPath === '/' && $currentPath === '/') {
                    return true;
                }

                if ($menuPath !== '/' && ($currentPath === $menuPath || $request->is(ltrim($menuPath, '/') . '/*'))) {
                    return true;
                }

                return false;
            });

        if ($maintenanceMenu) {
            return redirect()->route('maintenance', ['page' => $maintenanceMenu->name]);
        }

        return $next($request);
    }
}
