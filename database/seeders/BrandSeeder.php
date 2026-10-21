<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'slug' => 'netflix',
                'name' => 'Netflix',
                'category' => 'Streaming & Entertainment',
                'logo' => 'assets/logo/Netflix_2015_logo.svg',
                'description' => 'Akses streaming film & series original Netflix kualitas Ultra HD 4K tanpa hambatan dengan garansi penuh selama masa aktif.',
                'packages' => [
                    // Paket Sharing
                    [
                        'title' => 'Netflix Sharing (1 Bulan)',
                        'desc' => 'Profil bersama dengan pembeli lain dilengkapi PIN. Khusus penggunaan HP / Tab.',
                        'price' => 38000,
                        'original_price' => 45000,
                        'features' => [
                            'Profil bersama dengan pembeli lain dilengkapi PIN',
                            'Rawan screen limit',
                            'Hanya login 1 device',
                            'KHUSUS Penggunaan HP/Tab',
                            'Disarankan menonton metode offline agar tidak rawan batas layar'
                        ]
                    ],
                    [
                        'title' => 'Netflix Sharing (2 Bulan)',
                        'desc' => 'Profil bersama dengan pembeli lain dilengkapi PIN. Khusus penggunaan HP / Tab durasi 2 bulan hemat.',
                        'price' => 63000,
                        'original_price' => 75000,
                        'features' => [
                            'Profil bersama dengan pembeli lain dilengkapi PIN',
                            'Rawan screen limit',
                            'Hanya login 1 device',
                            'KHUSUS Penggunaan HP/Tab',
                            'Disarankan menonton metode offline agar tidak rawan batas layar'
                        ]
                    ],
                    [
                        'title' => 'Netflix Sharing (3 Bulan)',
                        'desc' => 'Profil bersama dengan pembeli lain dilengkapi PIN. Khusus penggunaan HP / Tab durasi 3 bulan paling ekonomis.',
                        'price' => 91000,
                        'original_price' => 110000,
                        'features' => [
                            'Profil bersama dengan pembeli lain dilengkapi PIN',
                            'Rawan screen limit',
                            'Hanya login 1 device',
                            'KHUSUS Penggunaan HP/Tab',
                            'Disarankan menonton metode offline agar tidak rawan batas layar'
                        ]
                    ],

                    // Paket Private
                    [
                        'title' => 'Netflix Private (1 Bulan)',
                        'desc' => 'Profil pribadi dengan PIN. Minim screen limit, cocok untuk HP, Tab, Laptop, PC, & Smart TV.',
                        'price' => 55000,
                        'original_price' => 65000,
                        'features' => [
                            'Profil pribadi dengan PIN',
                            'Minim screen limit',
                            'Disarankan utk pengguna HP/Tab/Laptop/PC',
                            'Bisa di Smart TV dgn risiko screen limit lebih besar dari paket VIP'
                        ]
                    ],
                    [
                        'title' => 'Netflix Private (2 Bulan)',
                        'desc' => 'Profil pribadi dengan PIN durasi 2 bulan. Minim screen limit untuk semua device.',
                        'price' => 95000,
                        'original_price' => 115000,
                        'features' => [
                            'Profil pribadi dengan PIN',
                            'Minim screen limit',
                            'Disarankan utk pengguna HP/Tab/Laptop/PC',
                            'Bisa di Smart TV dgn risiko screen limit lebih besar dari paket VIP'
                        ]
                    ],
                    [
                        'title' => 'Netflix Private (3 Bulan)',
                        'desc' => 'Profil pribadi dengan PIN durasi 3 bulan hemat panjang. Bebas nonton di berbagai device.',
                        'price' => 120000,
                        'original_price' => 150000,
                        'features' => [
                            'Profil pribadi dengan PIN',
                            'Minim screen limit',
                            'Disarankan utk pengguna HP/Tab/Laptop/PC',
                            'Bisa di Smart TV dgn risiko screen limit lebih besar dari paket VIP'
                        ]
                    ],

                    // Paket VIP
                    [
                        'title' => 'Netflix VIP (1 Bulan)',
                        'desc' => 'Profil pribadi dengan PIN. AMAN screen limit, exclusive untuk Smart TV & bisa perpanjang.',
                        'price' => 69000,
                        'original_price' => 85000,
                        'features' => [
                            'Profil pribadi dengan PIN',
                            'AMAN screen limit',
                            'Exclusive utk pengguna TV supaya AMAN screen limit',
                            'Bisa perpanjang dgn email sama / transfer profil'
                        ]
                    ],
                    [
                        'title' => 'Netflix VIP (2 Bulan)',
                        'desc' => 'Profil pribadi dengan PIN durasi 2 bulan. Bebas screen limit khusus Smart TV dan semua device.',
                        'price' => 79000,
                        'original_price' => 99000,
                        'features' => [
                            'Profil pribadi dengan PIN',
                            'AMAN screen limit',
                            'Exclusive utk pengguna TV supaya AMAN screen limit',
                            'Bisa perpanjang dgn email sama / transfer profil'
                        ]
                    ],
                    [
                        'title' => 'Netflix VIP (3 Bulan)',
                        'desc' => 'Profil pribadi dengan PIN durasi 3 bulan terbaik. Paling nyaman & stabil untuk nonton di TV.',
                        'price' => 135000,
                        'original_price' => 165000,
                        'features' => [
                            'Profil pribadi dengan PIN',
                            'AMAN screen limit',
                            'Exclusive utk pengguna TV supaya AMAN screen limit',
                            'Bisa perpanjang dgn email sama / transfer profil'
                        ]
                    ],
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'chatgpt',
                'name' => 'ChatGPT Plus',
                'category' => 'AI & Productivity',
                'logo' => 'assets/logo/OpenAI_logo_2025_(symbol).svg',
                'description' => 'Akses OpenAI ChatGPT Plus dengan model GPT-4o, pembuatan gambar DALL-E 3, analisis data advance, dan respons kilat.',
                'packages' => [
                    [
                        'title' => 'ChatGPT Plus Sharing (1 Bulan)',
                        'desc' => 'Akses model GPT-4o, browsing web, dan upload dokumen dengan kuota bersama terkelola.',
                        'price' => 60000,
                        'original_price' => 85000,
                        'features' => ['Akses Model GPT-4o', 'Browsing & Data Analysis', 'Generate Gambar DALL-E 3', 'Garansi 30 Hari']
                    ],
                    [
                        'title' => 'ChatGPT Plus Private (1 Bulan)',
                        'desc' => 'Akun Plus pribadi dengan email sendiri/fresh tanpa batasan sharing limit.',
                        'price' => 320000,
                        'original_price' => 350000,
                        'features' => ['Akun Pribadi 100%', 'Bebas Custom GPTs', 'Prioritas Speed Tertinggi', 'Full Garansi Resmi']
                    ],
                    [
                        'title' => 'ChatGPT Team Workspace',
                        'desc' => 'Fitur admin workspace, data tidak digunakan untuk training OpenAI.',
                        'price' => 450000,
                        'original_price' => 500000,
                        'features' => ['Workspace Khusus Tim', 'Privacy Data Terjamin', 'Higher Message Limits', 'Dukungan Setup Admin']
                    ]
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'claude',
                'name' => 'Claude Pro',
                'category' => 'AI & Coding Intelligence',
                'logo' => 'assets/logo/Claude_AI_logo.svg',
                'description' => 'Akses Anthropic Claude 3.5 Sonnet terbaik untuk coding, penulisan artikel analitis, dan pemrosesan dokumen ratusan halaman.',
                'packages' => [
                    [
                        'title' => 'Claude Pro Sharing (1 Bulan)',
                        'desc' => 'Akses Claude 3.5 Sonnet & Claude 3 Opus dengan fitur Artifacts interaktif.',
                        'price' => 65000,
                        'original_price' => 90000,
                        'features' => ['Model Claude 3.5 Sonnet', 'Fitur Artifacts Coding', 'Analisis Dokumen PDF Besar', 'Garansi Penuh']
                    ],
                    [
                        'title' => 'Claude Pro Private (1 Bulan)',
                        'desc' => 'Akun eksklusif private untuk performa programming dan penulisan intensif.',
                        'price' => 330000,
                        'original_price' => 360000,
                        'features' => ['100% Akun Milik Sendiri', 'Limit Penggunaan 5x Lebih Banyak', 'Akses Fitur Terbaru Prioritas', 'Garansi 30 Hari']
                    ]
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'gemini',
                'name' => 'Google Gemini Advanced',
                'category' => 'AI & Cloud Storage',
                'logo' => 'assets/logo/Google_Gemini_logo.svg',
                'description' => 'Kombinasi model AI tercanggih Google Gemini 1.5 Pro dengan konteks 1 juta token plus Google One Cloud Storage 2TB.',
                'packages' => [
                    [
                        'title' => 'Gemini Pro 5TB (3 Bulan)',
                        'desc' => 'Metode cara kerjanya invite ke family link dan bisa menikmati Gemini Pro Plan 5TB.',
                        'price' => 50000,
                        'original_price' => 75000,
                        'features' => [
                            'Gemini PRO',
                            'Include Cloud Storage 5TB',
                            'Include Nano Banana & VEO 3 (kebutuhan foto & video)',
                            'Include 1K Kredit G. Flow / Bulan',
                            'Include NotebookLM',
                            'Dan lain-lain'
                        ]
                    ],
                    [
                        'title' => 'Gemini Pro 5TB (6 Bulan)',
                        'desc' => 'Metode cara kerjanya invite ke family link dan bisa menikmati Gemini Pro Plan 5TB.',
                        'price' => 100000,
                        'original_price' => 135000,
                        'features' => [
                            'Gemini PRO',
                            'Include Cloud Storage 5TB',
                            'Include Nano Banana & VEO 3 (kebutuhan foto & video)',
                            'Include 1K Kredit G. Flow / Bulan',
                            'Include NotebookLM',
                            'Dan lain-lain'
                        ]
                    ],
                    [
                        'title' => 'Gemini Pro 5TB (12 Bulan / 1 Tahun)',
                        'desc' => 'Metode cara kerjanya invite ke family link dan bisa menikmati Gemini Pro Plan 5TB.',
                        'price' => 130000,
                        'original_price' => 180000,
                        'features' => [
                            'Gemini PRO',
                            'Include Cloud Storage 5TB',
                            'Include Nano Banana & VEO 3 (kebutuhan foto & video)',
                            'Include 1K Kredit G. Flow / Bulan',
                            'Include NotebookLM',
                            'Dan lain-lain'
                        ]
                    ],
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'spotify',
                'name' => 'Spotify Premium',
                'category' => 'Music & Audio',
                'logo' => 'assets/logo/2024_Spotify_Logo.svg',
                'description' => 'Dengarkan lagu dan podcast favorit tanpa iklan, download lagu sepuasnya untuk mode offline, dan kualitas audio maksimal.',
                'packages' => [
                    [
                        'title' => 'Spotify Premium Individual (1 Bulan)',
                        'desc' => 'Bebas iklan, download lagu offline, kualitas suara tinggi, bisa perpanjang.',
                        'price' => 22000,
                        'original_price' => 30000,
                        'features' => ['Bebas Iklan Selamanya', 'Download Offline Music', 'Bisa Akun Pribadi', 'Garansi Penuh']
                    ],
                    [
                        'title' => 'Spotify Premium Plan (3 Bulan)',
                        'desc' => 'Paket hemat 3 bulan nonstop tanpa khawatir terputus di tengah jalan.',
                        'price' => 55000,
                        'original_price' => 75000,
                        'features' => ['Aktif 90 Hari Penuh', 'Kualitas Suara Master/Hi-Fi', 'Support Semua Perangkat', 'Full Garansi']
                    ],
                    [
                        'title' => 'Spotify Premium Family Invite (1 Bulan)',
                        'desc' => 'Invite resmi ke paket keluarga, aman dan langsung aktif ke email Anda.',
                        'price' => 18000,
                        'original_price' => 25000,
                        'features' => ['Proses 5 Menit Jadi', 'Pakai Akun Sendiri', 'Garansi Re-invite Jika Drop', 'No VPN Required']
                    ]
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'canva',
                'name' => 'Software Design Bisnis Plan',
                'category' => 'Graphic & Visual Design',
                'logo' => 'assets/logo/Canva_logo.svg',
                'description' => 'Akses 100+ juta foto, video, audio stok premium, fitur Magic Studio AI, resize instan, dan hapus background 1-klik.',
                'packages' => [
                    [
                        'title' => 'Canva Bisnis',
                        'desc' => 'Akses penuh fitur Canva Pro selama 3 bulan via invite email pribadi.',
                        'price' => 30000,
                        'original_price' => 45000,
                        'features' => [
                            'Akses Semua Template & Elemen Pro',
                            'Hapus Background Foto & Video 1-Klik',
                            'Magic Studio AI Design Tools',
                            'Resize Desain Instan Tanpa Batas',
                            'Invite Email Pribadi (Aman & Legal)',
                            'Garansi Penuh Selama Durasi'
                        ]
                    ],
                    [
                        'title' => 'Canva Pro (6 Bulan)',
                        'desc' => 'Akses penuh fitur Canva Pro selama 6 bulan via invite email pribadi.',
                        'price' => 50000,
                        'original_price' => 75000,
                        'features' => [
                            'Akses Semua Template & Elemen Pro',
                            'Hapus Background Foto & Video 1-Klik',
                            'Magic Studio AI Design Tools',
                            'Resize Desain Instan Tanpa Batas',
                            'Invite Email Pribadi (Aman & Legal)',
                            'Garansi Penuh Selama Durasi'
                        ]
                    ],
                    [
                        'title' => 'Canva Pro (1 Tahun)',
                        'desc' => 'Akses penuh fitur Canva Pro selama 1 tahun penuh via invite email pribadi.',
                        'price' => 110000,
                        'original_price' => 150000,
                        'features' => [
                            'Akses Semua Template & Elemen Pro',
                            'Hapus Background Foto & Video 1-Klik',
                            'Magic Studio AI Design Tools',
                            'Resize Desain Instan Tanpa Batas',
                            'Invite Email Pribadi (Aman & Legal)',
                            'Garansi Penuh Selama Durasi'
                        ]
                    ],
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'instagram',
                'name' => 'Instagram Growth & Booster',
                'category' => 'Social Media Management',
                'logo' => 'assets/logo/Instagram_logo_2022.svg',
                'description' => 'Layanan optimasi profil Instagram, peningkatan interaksi autentik, followers aktif Indonesia, dan desain feed profesional.',
                'packages' => [
                    [
                        'title' => 'Paket Followers Aktif Indonesia (1.000 Follower)',
                        'desc' => 'Meningkatkan kredibilitas akun bisnis atau kreator Anda dengan akun riil berkualitas.',
                        'price' => 65000,
                        'original_price' => 90000,
                        'features' => ['Akun Riil Berkualitas', 'Proses Cepat & Alami', 'Tanpa Butuh Password', 'Garansi Refill 30 Hari']
                    ],
                    [
                        'title' => 'Paket Likes & Impression Postingan',
                        'desc' => 'Booster algoritma Reels dan Feed agar berpeluang masuk Explore Instagram.',
                        'price' => 35000,
                        'original_price' => 50000,
                        'features' => ['Support Post Feed & Reels', 'Meningkatkan Reach Konten', 'Cukup Kirimkan Link', 'Proses Instan']
                    ],
                    [
                        'title' => 'Jasa Desain Feed Instagram (9 Grid Carousel)',
                        'desc' => 'Desain konten feed estetik, selaras dengan identitas brand dan copywriting menarik.',
                        'price' => 150000,
                        'original_price' => 220000,
                        'features' => ['9 Template Desain Eksklusif', 'Revisi hingga Puas', 'File High-Res Siap Upload', 'Source File Editable']
                    ]
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'copilot',
                'name' => 'GitHub Copilot / VS Code AI',
                'category' => 'Developer & Coding Tools',
                'logo' => 'assets/logo/Visual_Studio_Code_1.35_icon.svg',
                'description' => 'AI coding assistant terbaik terintegrasi langsung di editor Visual Studio Code, JetBrains, dan Neovim untuk mempercepat ngoding.',
                'packages' => [
                    [
                        'title' => 'GitHub Copilot Individual (1 Bulan)',
                        'desc' => 'Autocompletion baris kode cerdas, Copilot Chat di editor, dan dokumentasi instan.',
                        'price' => 55000,
                        'original_price' => 85000,
                        'features' => ['Copilot Code Completion', 'Copilot Chat in Editor', 'Support Semua Bahasa Coding', 'Garansi 30 Hari']
                    ],
                    [
                        'title' => 'GitHub Copilot Pro / Edu (1 Tahun)',
                        'desc' => 'Masa aktif panjang 1 tahun untuk pelajar, mahasiswa, dan developer freelance.',
                        'price' => 220000,
                        'original_price' => 320000,
                        'features' => ['Aktif 1 Tahun Penuh', 'Koneksi ke Akun GitHub Anda', 'Debugging & Unit Test AI', 'Garansi Penuh']
                    ]
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'figma',
                'name' => 'Figma Professional',
                'category' => 'UI/UX & Product Design',
                'logo' => 'assets/logo/figma-logo.svg',
                'description' => 'Akses Figma Professional tanpa batas file, riwayat versi tanpa batasan hari, kolaborasi tim tak terbatas, dan fitur dev mode.',
                'packages' => [
                    [
                        'title' => 'Figma Pro Education / Pro (1 Tahun)',
                        'desc' => 'Unlimited file desain, unlimited project, akses penuh Dev Mode dan shared libraries.',
                        'price' => 75000,
                        'original_price' => 120000,
                        'features' => ['Unlimited Figma Projects', 'Fitur Dev Mode untuk Koding', 'Unlimited Version History', 'Garansi 1 Tahun']
                    ],
                    [
                        'title' => 'Figma Pro Team License (Private)',
                        'desc' => 'Lisensi tim khusus studio desain Anda dengan manajemen perizinan terpusat.',
                        'price' => 280000,
                        'original_price' => 380000,
                        'features' => ['Full Control Admin', 'Design System Shared Library', 'Private Plugins & Widgets', 'Support Konsultasi Setup']
                    ]
                ],
                'is_active' => true,
            ],
        ];

        foreach ($brands as $brandData) {
            $slug = strtolower(trim($brandData['slug']));
            $existing = Brand::whereRaw('LOWER(slug) = ?', [$slug])->first();

            if ($existing) {
                // KUNCI: Pertahankan stok dan tanggal restok yang sudah diinput oleh admin
                $currentStock = $existing->stock;
                $currentRestockDate = $existing->restock_date;

                $existing->update([
                    'name' => $brandData['name'],
                    'category' => $brandData['category'],
                    'logo' => $brandData['logo'],
                    'description' => $brandData['description'],
                    'packages' => $brandData['packages'],
                    'is_active' => $brandData['is_active'] ?? true,
                    'stock' => $currentStock,
                    'restock_date' => $currentRestockDate,
                ]);
            } else {
                // Hanya jika benar-benar brand baru yang belum ada di database
                $brandData['stock'] = 0;
                Brand::create($brandData);
            }
        }
    }
}
