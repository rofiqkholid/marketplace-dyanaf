<aside x-data class="w-80 bg-[#f8fafc] border-l border-slate-300/80 shrink-0 hidden xl:block h-[calc(100vh-4rem)] sticky top-16 p-4 overflow-y-auto space-y-5 rounded-none overscroll-contain">

    <!-- Tools Section -->
    <div class="px-1 space-y-3">
        <div class="text-xs font-bold text-slate-700">Alat & Utilitas</div>
        
        @php
            $isGuest = !Auth::check();
            $userPoints = Auth::check() ? Auth::user()->dyanaf_points : 0;
            
            // 10 Fitur Tools dengan alokasi pemakaian poin (min 5, max 20)
            $toolItems = [
                ['name' => 'Gabung PDF', 'icon' => 'fa-solid fa-file-pdf text-[11px] text-red-500', 'cost' => 10],
                ['name' => 'JPG to PDF', 'icon' => 'fa-solid fa-file-image text-[11px] text-blue-500', 'cost' => 5],
                ['name' => 'PNG to PDF', 'icon' => 'fa-regular fa-file-image text-[11px] text-emerald-500', 'cost' => 5],
                ['name' => 'JPG to PNG', 'icon' => 'fa-solid fa-image text-[11px] text-amber-500', 'cost' => 5],
                ['name' => 'PNG to SVG', 'icon' => 'fa-solid fa-bezier-curve text-[11px] text-purple-500', 'cost' => 15],
                ['name' => 'Docx to PDF', 'icon' => 'fa-solid fa-file-word text-[11px] text-blue-600', 'cost' => 10],
                ['name' => 'Kompres PDF', 'icon' => 'fa-solid fa-file-zipper text-[11px] text-rose-500', 'cost' => 10],
                ['name' => 'PDF to Word', 'icon' => 'fa-solid fa-file-lines text-[11px] text-teal-600', 'cost' => 15],
                ['name' => 'Pisahkan PDF', 'icon' => 'fa-solid fa-scissors text-[11px] text-indigo-500', 'cost' => 10],
                ['name' => 'Hapus BG', 'icon' => 'fa-solid fa-wand-magic-sparkles text-[11px] text-cyan-500', 'cost' => 20],
            ];
        @endphp

        <div class="grid grid-cols-2 gap-2">
            @foreach($toolItems as $tool)
                @php
                    $isInsufficient = !$isGuest && ($userPoints < $tool['cost']);
                @endphp
                <a href="#" 
                   @if($isGuest)
                       onclick="event.preventDefault(); window.dispatchEvent(new CustomEvent('open-modal-information', { 
                           detail: { 
                               title: 'Login Diperlukan', 
                               message: 'Fitur <strong>{{ $tool['name'] }}</strong> membutuhkan <strong>{{ $tool['cost'] }} Dyanaf Poin</strong>.<br><br>Silakan login terlebih dahulu untuk mengakses fitur ini.', 
                               type: 'login' 
                           } 
                       }));"
                   @elseif($isInsufficient)
                       onclick="event.preventDefault(); window.dispatchEvent(new CustomEvent('open-modal-information', { 
                           detail: { 
                               title: 'Dyanaf Poin Tidak Cukup', 
                               message: 'Untuk menggunakan fitur <strong>{{ $tool['name'] }}</strong> dibutuhkan <strong>{{ $tool['cost'] }} Dyanaf Poin</strong>.<br><br><div class=\'inline-block p-3 bg-amber-50/70 border border-amber-200/80 text-slate-700 text-left w-full max-w-lg mx-auto\'><div class=\'flex items-start space-x-2.5\'><i class=\'fa-solid fa-lightbulb text-amber-500 text-base mt-0.5 shrink-0\'></i><div><strong class=\'text-slate-800 text-xs block mb-0.5\'>Cara Mendapatkan Dyanaf Poin:</strong><p class=\'text-xs text-slate-600 leading-relaxed\'>Poin diperoleh secara otomatis setiap kali Anda melakukan <strong>order atau pembelian produk</strong> di Dyanaf Creative.</p></div></div></div>', 
                               type: 'insufficient_points' 
                           } 
                       }));"
                   @else
                       onclick="event.preventDefault(); window.dispatchEvent(new CustomEvent('open-modal-information', { 
                           detail: { 
                               title: 'Fitur Segera Tersedia', 
                               message: 'Fitur <strong>{{ $tool['name'] }}</strong> saat ini sedang dalam tahap pengembangan dan akan segera aktif.<br><br>Saldo Poin Anda mencukupi, dan poin Anda <strong>belum dipotong</strong>. Nantikan peluncuran resminya segera!', 
                               type: 'coming_soon' 
                           } 
                       }));"
                   @endif
                   class="flex items-center space-x-2 p-2 rounded-none bg-white hover:bg-blue-50 text-slate-700 hover:text-blue-600 transition-colors border border-slate-300/80 cursor-pointer">
                    <i class="{{ $tool['icon'] }}"></i>
                    <span class="text-[11px] font-semibold truncate">{{ $tool['name'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Store Location & Google Maps Section -->
    <div class="px-1 space-y-3 pt-3 border-t border-slate-300/80">
        <div class="text-xs font-bold text-slate-800">Lokasi Toko Dyanaf</div>
        
        <!-- Google Maps Embed -->
        <div class="w-full h-44 rounded-none overflow-hidden border border-slate-300/80 relative">
            <iframe 
                src="https://maps.google.com/maps?q=-6.294750,107.294611&hl=id&z=16&output=embed" 
                class="w-full h-full border-0" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

        <!-- Coordinates Badge & Google Maps Link -->
        <a href="https://www.google.com/maps?q=-6.294750,107.294611" target="_blank" class="flex items-center justify-between p-2 bg-white border border-slate-300/80 text-[11px] font-normal text-slate-700">
            <span>6°17'41.1"S 107°17'40.6"E</span>
            <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400"></i>
        </a>
    </div>

    <!-- Store Contact Details -->
    <div class="space-y-4 px-1 pt-4 mt-3 border-t border-slate-300/80">
        <div class="space-y-3.5">
            <!-- Website -->
            <a href="https://dyanaf.id" target="_blank" class="block">
                <div class="text-[11px] text-slate-500 font-normal">Website Resmi</div>
                <div class="text-xs font-normal text-slate-800 truncate">dyanaf.id</div>
            </a>

            <!-- Phone / WhatsApp -->
            <a href="https://wa.me/6285881721193" target="_blank" class="block">
                <div class="text-[11px] text-slate-500 font-normal">Telepon / WhatsApp</div>
                <div class="text-xs font-normal text-slate-800 truncate">085881721193</div>
            </a>

            <!-- Email -->
            <a href="mailto:support@dyanaf.id" class="block">
                <div class="text-[11px] text-slate-500 font-normal">Email Support</div>
                <div class="text-xs font-normal text-slate-800 truncate">support@dyanaf.id</div>
            </a>

            <!-- Social Media (TikTok, Threads, Instagram) -->
            <div class="space-y-1.5 pt-0.5">
                <div class="text-[11px] text-slate-500 font-normal">Media Sosial</div>
                <div class="space-y-1">
                    <a href="https://www.tiktok.com/@dyanaf.creative" target="_blank" class="flex items-center space-x-2 text-xs text-slate-800">
                        <i class="fa-brands fa-tiktok text-slate-500 w-4 text-center"></i>
                        <span class="truncate">@dyanaf.creative</span>
                    </a>
                    <a href="https://www.threads.net/@dyanaf.creative" target="_blank" class="flex items-center space-x-2 text-xs text-slate-800">
                        <i class="fa-brands fa-threads text-slate-500 w-4 text-center"></i>
                        <span class="truncate">@dyanaf.creative</span>
                    </a>
                    <a href="https://www.instagram.com/dyanaf.creative" target="_blank" class="flex items-center space-x-2 text-xs text-slate-800">
                        <i class="fa-brands fa-instagram text-slate-500 w-4 text-center"></i>
                        <span class="truncate">@dyanaf.creative</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Full Address Box -->
        <div class="space-y-1 pt-1">
            <div class="text-[11px] font-normal text-slate-500">Alamat Lengkap Toko</div>
            <p class="text-[11px] font-normal text-slate-800 leading-relaxed">
                Lingkungan Bubulak Sauyunan, RT.4/RW.8, Kelurahan Tanjung Pura (Rumah No. 20), KARAWANG BARAT, KAB. KARAWANG, JAWA BARAT, ID, 41311
            </p>
        </div>
    </div>

</aside>
