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

            $mainMenus = $menus->filter(fn($menu) => $menu->children->isEmpty());
            $sectionMenus = $menus->filter(fn($menu) => $menu->children->isNotEmpty());

            $view->with([
                'menus' => $menus,
                'mainMenus' => $mainMenus,
                'sectionMenus' => $sectionMenus,
            ]);
        });
    }
}
