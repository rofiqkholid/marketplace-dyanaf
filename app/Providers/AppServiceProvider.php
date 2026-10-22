<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.sidebar-left', 'components.sidebar-left'], function ($view) {
            $userRole = Auth::check() ? Auth::user()->role : null;

            $menus = Menu::with(['children' => function ($q) use ($userRole) {
                    $q->where('is_active', true)
                      ->where(function ($subQ) use ($userRole) {
                          $subQ->whereNull('role')
                               ->orWhere('role', $userRole);
                      })
                      ->orderBy('order');
                }])
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->where(function ($q) use ($userRole) {
                    $q->whereNull('role')
                      ->orWhere('role', $userRole);
                })
                ->orderBy('order')
                ->get();

            // Decorate helper function for menu presentation logic
            $decorateMenu = function ($menu) {
                $menu->target_url = $menu->is_maintenance 
                    ? route('maintenance', ['page' => $menu->name]) 
                    : $menu->url;

                $menu->is_active_link = ($menu->url === '/' && request()->is('/')) || 
                    ($menu->url !== '#' && $menu->url !== '/' && (request()->is(trim($menu->url, '/')) || request()->fullUrlIs(url($menu->url))));

                $menu->is_external = !$menu->is_maintenance && 
                    (str_starts_with($menu->url, 'http://') || str_starts_with($menu->url, 'https://'));

                return $menu;
            };

            $mainMenus = $menus->filter(fn($menu) => $menu->children->isEmpty())->map($decorateMenu);
            $sectionMenus = $menus->filter(fn($menu) => $menu->children->isNotEmpty())->map(function ($section) use ($decorateMenu) {
                $section->children->transform($decorateMenu);
                return $section;
            });

            $view->with([
                'menus' => $menus,
                'mainMenus' => $mainMenus,
                'sectionMenus' => $sectionMenus,
            ]);
        });
    }
}
