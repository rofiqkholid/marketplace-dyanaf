<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Menu::truncate();

        // Standalone Main Nav Items (Level 1 without children)
        $mainNavs = [
            ['name' => 'Featured', 'url' => '/', 'icon' => 'fa-solid fa-star text-amber-600 text-sm', 'order' => 1],
            ['name' => 'Trending', 'url' => '/trending', 'icon' => 'fa-solid fa-chart-line text-rose-600 text-sm', 'order' => 2],
            ['name' => 'New Arrival', 'url' => '#', 'icon' => 'fa-solid fa-wand-magic-sparkles text-blue-500 text-sm', 'order' => 3],
            ['name' => 'Tracking Order', 'url' => '/tracking-order', 'icon' => 'fa-solid fa-truck-fast text-indigo-600 text-sm', 'order' => 4],
            ['name' => 'Discount', 'url' => '#', 'icon' => 'fa-solid fa-tags text-emerald-600 text-sm', 'order' => 5],
            ['name' => 'All Products', 'url' => '#', 'icon' => 'fa-solid fa-border-all text-cyan-600 text-sm', 'order' => 6],
            ['name' => 'Download Asset', 'url' => '#', 'icon' => 'fa-solid fa-cloud-arrow-down text-purple-600 text-sm', 'order' => 7],
        ];

        foreach ($mainNavs as $nav) {
            Menu::create($nav);
        }

        // Level 1 Section: Categories
        $categoriesHeader = Menu::create([
            'name' => 'Categories',
            'url' => '#',
            'icon' => null,
            'order' => 10,
        ]);

        $categoryItems = [
            ['name' => 'Games', 'url' => '#', 'icon' => 'fa-solid fa-gamepad text-emerald-600 w-4 text-center', 'order' => 1],
            ['name' => 'Software', 'url' => '#', 'icon' => 'fa-solid fa-laptop-code text-cyan-600 w-4 text-center', 'order' => 2],
            ['name' => 'AI Tools', 'url' => '#', 'icon' => 'fa-solid fa-robot text-blue-500 w-4 text-center', 'order' => 3],
            ['name' => 'Gift & Bouquet', 'url' => '#', 'icon' => 'fa-solid fa-gift text-rose-600 w-4 text-center', 'order' => 4],
            ['name' => 'Accounts', 'url' => '#', 'icon' => 'fa-solid fa-user-shield text-amber-600 w-4 text-center', 'order' => 5],
            ['name' => 'Graphic Design', 'url' => '#', 'icon' => 'fa-solid fa-palette text-teal-600 w-4 text-center', 'order' => 6],
        ];

        foreach ($categoryItems as $item) {
            $categoriesHeader->children()->create($item);
        }

        // Level 1 Section: Quick Actions
        $quickActionsHeader = Menu::create([
            'name' => 'Quick Actions',
            'url' => '#',
            'icon' => null,
            'order' => 20,
        ]);

        $quickActionItems = [
            ['name' => 'Rules & Guarantee', 'url' => '#', 'icon' => 'fa-solid fa-shield-halved text-blue-500 w-4 text-center', 'order' => 1],
            ['name' => 'Advices & Guides', 'url' => '#', 'icon' => 'fa-solid fa-book-open text-emerald-600 w-4 text-center', 'order' => 2],
            ['name' => 'Testimonials', 'url' => '#', 'icon' => 'fa-solid fa-quote-left text-amber-500 w-4 text-center', 'order' => 3],
            ['name' => 'Contact Us', 'url' => 'https://wa.me/6285881721193', 'icon' => 'fa-brands fa-whatsapp text-emerald-500 w-4 text-center', 'order' => 4],
        ];

        foreach ($quickActionItems as $item) {
            $quickActionsHeader->children()->create($item);
        }

        // Level 1 Section: App Settings (Restricted to role: pemilik)
        $appSettingsHeader = Menu::create([
            'name' => 'App Settings',
            'url' => '#',
            'icon' => null,
            'role' => 'pemilik',
            'order' => 30,
        ]);

        $appSettingItems = [
            ['name' => 'Brand Master', 'url' => '/brands-master', 'icon' => 'fa-solid fa-tags text-rose-600 w-4 text-center', 'role' => 'pemilik', 'order' => 1],
            ['name' => 'Produk Master', 'url' => '/products-master', 'icon' => 'fa-solid fa-box-open text-amber-600 w-4 text-center', 'role' => 'pemilik', 'order' => 2],
            ['name' => 'User', 'url' => '/users', 'icon' => 'fa-solid fa-users text-blue-600 w-4 text-center', 'role' => 'pemilik', 'order' => 3],
            ['name' => 'Menu', 'url' => '/menus-master', 'icon' => 'fa-solid fa-list-check text-emerald-600 w-4 text-center', 'role' => 'pemilik', 'order' => 4],
            ['name' => 'Create Order', 'url' => '/orders-create', 'icon' => 'fa-solid fa-receipt text-indigo-600 w-4 text-center', 'role' => 'pemilik', 'order' => 5],
        ];

        foreach ($appSettingItems as $item) {
            $appSettingsHeader->children()->create($item);
        }
    }
}
