<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::table('menus')->where('name', 'Discount')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('menus')->insert([
            'name' => 'Discount',
            'url' => '#',
            'icon' => 'fa-solid fa-tags text-emerald-600 text-sm',
            'order' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
