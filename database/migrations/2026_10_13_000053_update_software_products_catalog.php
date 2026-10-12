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
        // Hapus produk lama jika ada
        \Illuminate\Support\Facades\DB::table('products')
            ->whereIn('name', [
                'Web Statis (Profile / Portfolio)',
                'Web Dinamis (Business / Custom System)',
            ])
            ->delete();

        // Tambahkan produk baru: Website Landing Page
        \Illuminate\Support\Facades\DB::table('products')->updateOrInsert(
            ['name' => 'Website Landing Page'],
            [
                'category' => 'Software',
                'price' => 1500000,
                'stock' => 99,
                'description' => "Cocok untuk company profile, promosi produk, serta personal branding modern.\n• Gratis Domain Custom (.com / .id) 1 Tahun\n• Perpanjangan Rp 1.000.000 / tahun\n• Desain Responsive & Fast Loading",
                'image' => '/image/thumnail/banner-web-statis.webp',
                'is_active' => true,
                'is_trending' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Tambahkan produk baru: Web Operasional & POS
        \Illuminate\Support\Facades\DB::table('products')->updateOrInsert(
            ['name' => 'Web Operasional & POS'],
            [
                'category' => 'Software',
                'price' => 8000000,
                'stock' => 99,
                'description' => "Sistem manajemen gudang, antrian, & Point of Sale (POS) untuk Cafe, toko retail, dll.\n• Gratis Domain Custom (.com / .id) 1 Tahun\n• Perpanjangan Rp 1.000.000 / tahun\n• Gratis Maintenance 30 Hari (Go Live)\n• Fitur Inventaris, Stok, & Laporan Penjualan",
                'image' => '/image/thumnail/banner-web-dinamis.webp',
                'is_active' => true,
                'is_trending' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('products')
            ->whereIn('name', [
                'Website Landing Page',
                'Web Operasional & POS',
            ])
            ->delete();

        \Illuminate\Support\Facades\DB::table('products')->insert([
            [
                'name' => 'Web Statis (Profile / Portfolio)',
                'category' => 'Software',
                'price' => 600000,
                'stock' => 99,
                'description' => 'Elegant, fast, and responsive company profile or personal portfolio website.',
                'image' => '/image/thumnail/banner-web-statis.webp',
                'is_active' => true,
                'is_trending' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Web Dinamis (Business / Custom System)',
                'category' => 'Software',
                'price' => 1500000,
                'stock' => 99,
                'description' => 'Complex business websites with database, admin dashboard, and custom features.',
                'image' => '/image/thumnail/banner-web-dinamis.webp',
                'is_active' => true,
                'is_trending' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
};
