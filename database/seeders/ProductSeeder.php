<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate existing products to replace catalog
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $products = [
            [
                'name' => 'Makalah (Tanpa Materi)',
                'category' => 'Daily Assignments Assistance',
                'price' => 100000,
                'stock' => 99,
                'description' => 'Complete academic paper writing from research, writing, analysis, to references and formatting.',
                'image' => '/image/thumnail/banner-makalah.webp',
                'is_active' => true,
            ],
            [
                'name' => 'Makalah (Ada Materi)',
                'category' => 'Daily Assignments Assistance',
                'price' => 70000,
                'stock' => 99,
                'description' => 'Systematic writing and formatting of papers from materials you provide.',
                'image' => '/image/thumnail/banner-joki-tugas.webp',
                'is_active' => true,
            ],
            [
                'name' => 'Jurnal Ilmiah',
                'category' => 'Daily Assignments Assistance',
                'price' => 130000,
                'stock' => 99,
                'description' => 'Writing and formatting scientific journal articles following academic templates.',
                'image' => '/image/thumnail/banner-jurnal.webp',
                'is_active' => true,
            ],
            [
                'name' => 'CV Kreatif',
                'category' => 'Daily Assignments Assistance',
                'price' => 25000,
                'stock' => 99,
                'description' => 'Attractive and aesthetic CV design suitable for creative, media, and communication fields.',
                'image' => '/image/thumnail/banner-cv.webp',
                'is_active' => true,
            ],
            [
                'name' => 'CV ATS-Friendly',
                'category' => 'Daily Assignments Assistance',
                'price' => 60000,
                'stock' => 99,
                'description' => 'Standardized ATS format CV to boost your chances in company document screening.',
                'image' => '/image/thumnail/banner-cv-ats.webp',
                'is_active' => true,
            ],
            [
                'name' => 'Surat Lamaran Kerja',
                'category' => 'Daily Assignments Assistance',
                'price' => 20000,
                'stock' => 99,
                'description' => 'Professional and persuasive cover letters tailored to your target job position.',
                'image' => '/image/thumnail/banner-lamaran.webp',
                'is_active' => true,
            ],
            [
                'name' => 'Gabung & Rapikan PDF',
                'category' => 'Daily Assignments Assistance',
                'price' => 10000,
                'stock' => 99,
                'description' => 'Merging, page ordering, and file compression of application documents into 1 PDF.',
                'image' => '/image/thumnail/banner-gabung-pdf.webp',
                'is_active' => true,
            ],
            [
                'name' => 'Web Statis (Profile / Portfolio)',
                'category' => 'Software',
                'price' => 600000,
                'stock' => 99,
                'description' => 'Elegant, fast, and responsive company profile or personal portfolio website.',
                'image' => '/image/thumnail/banner-web-statis.webp',
                'is_active' => true,
            ],
            [
                'name' => 'Web Dinamis (Business / Custom System)',
                'category' => 'Software',
                'price' => 1500000,
                'stock' => 99,
                'description' => 'Complex business websites with database, admin dashboard, and custom features.',
                'image' => '/image/thumnail/banner-web-dinamis.webp',
                'is_active' => true,
            ],
            [
                'name' => 'Desain Grafis & Promo',
                'category' => 'Graphic Design',
                'price' => 100000,
                'stock' => 99,
                'description' => 'Logo design, banners, posters, social media feeds, and business branding assets.',
                'image' => '/image/thumnail/banner-desain-grafis.webp',
                'is_active' => true,
            ],
            [
                'name' => 'Data Entry & Spreadsheet',
                'category' => 'Daily Assignments Assistance',
                'price' => 150000,
                'stock' => 99,
                'description' => 'Accurate data input and processing for Excel, Google Sheets, or databases.',
                'image' => '/image/thumnail/banner-data-entry.webp',
                'is_active' => true,
            ],
            [
                'name' => 'Jasa Ketik Word',
                'category' => 'Daily Assignments Assistance',
                'price' => 100000,
                'stock' => 99,
                'description' => 'Fast document typing and formatting in Word according to writing standards.',
                'image' => '/image/thumnail/banner-ketik-word.webp',
                'is_active' => true,
            ],
            [
                'name' => 'Jasa Excel & Analisis Data',
                'category' => 'Daily Assignments Assistance',
                'price' => 130000,
                'stock' => 99,
                'description' => 'Complex formulas, functions, pivot tables, and Excel data analysis.',
                'image' => '/image/thumnail/banner-ketik-exel.webp',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
