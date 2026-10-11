<header x-data class="fixed top-0 left-0 right-0 z-50 h-16 bg-[#f8fafc] border-b border-slate-300/80 px-2 sm:px-4 flex items-center justify-between rounded-none gap-2">
    <!-- Brand Logo & Mobile Toggle -->
    <div class="flex items-center space-x-2 shrink-0">
        <button type="button"
                @click="$dispatch('toggle-mobile-sidebar')"
                class="lg:hidden text-slate-600 hover:text-slate-900 p-1.5 focus:outline-none cursor-pointer"
                aria-label="Toggle Mobile Sidebar">
            <i class="fa-solid fa-bars text-sm"></i>
        </button>
        <a href="/" class="flex items-center space-x-2 group">
            <img src="{{ asset('image/dyanaf-logo-circle.png') }}" alt="Dyanaf Logo" class="hidden sm:block w-8 h-8 sm:w-9 sm:h-9 object-contain rounded-none group-hover:scale-105 transition-transform">
            <span class="text-base sm:text-xl font-bold text-[#1e293b] font-['Outfit'] whitespace-nowrap">
                <span class="sm:hidden">Dyanaf</span>
                <span class="hidden sm:inline">Dyanaf Creative</span>
            </span>
        </a>
    </div>

    <!-- Center Search Bar (Desktop Only) -->
    <div class="hidden md:block flex-1 min-w-0 max-w-2xl px-1 sm:px-4">
        <div class="relative flex items-center">
            <i class="fa-solid fa-magnifying-glass absolute left-3 text-slate-400 text-xs sm:text-sm"></i>
            <input type="text" 
                   placeholder="Search..." 
                   class="w-full bg-[#eef2f6] text-slate-800 text-xs sm:text-sm placeholder-slate-400 pl-8 sm:pl-10 pr-2 sm:pr-4 py-1.5 sm:py-2 rounded-full border border-slate-300/80 focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition-all">
        </div>
    </div>

    <!-- Right Actions -->
    <div class="flex items-center space-x-2 sm:space-x-3 shrink-0 ml-auto">
        <!-- Real-time Date & Clock Widget (Desktop awal dengan jam digital, Mobile dengan singkatan hari & tanggal) -->
        @php
            $initialFullDate = now()->locale('id')->translatedFormat('l, j F Y');
            $initialTime = now()->format('H:i:s');
            $initialShortDate = now()->locale('id')->translatedFormat('D, d/m/y');
        @endphp
        <div x-data="{
            fullDateStr: '{{ $initialFullDate }}',
            timeStr: '{{ $initialTime }}',
            shortDateStr: '{{ $initialShortDate }}',
            updateClock() {
                const now = new Date();
                const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                this.fullDateStr = now.toLocaleDateString('id-ID', options);
                this.timeStr = now.toTimeString().split(' ')[0];
                
                const dayName = now.toLocaleDateString('id-ID', { weekday: 'short' });
                const dd = String(now.getDate()).padStart(2, '0');
                const mm = String(now.getMonth() + 1).padStart(2, '0');
                const yy = String(now.getFullYear()).slice(-2);
                this.shortDateStr = `${dayName}, ${dd}/${mm}/${yy}`;
            }
        }" x-init="updateClock(); setInterval(() => updateClock(), 1000)" class="flex items-center text-[#1e293b] font-['Outfit'] pr-1.5 sm:pr-3 shrink-0">
            <!-- Mobile View: Tampilkan format singkatan hari & tanggal (Sun, 11/10/26) -->
            <span class="md:hidden text-sm sm:text-base font-normal text-slate-700 tracking-tight whitespace-nowrap" x-text="shortDateStr">{{ $initialShortDate }}</span>

            <!-- Desktop View: Format awal (Hari, Tanggal Lengkap | Jam:Menit:Detik) -->
            <div class="hidden md:flex items-center space-x-3 lg:space-x-4">
                <span class="text-base md:text-xl font-normal text-slate-800 shrink-0" x-text="fullDateStr">{{ $initialFullDate }}</span>
                <span class="h-6 border-r border-slate-300 shrink-0"></span>
                <span class="text-lg md:text-2xl font-normal text-slate-800 tabular-nums w-[6.5rem] inline-block text-center shrink-0" x-text="timeStr">{{ $initialTime }}</span>
            </div>
        </div>

        @auth
            <!-- Dyanaf Points Energy Badge in Header (Enlarged with More Padding) -->
            <div class="inline-flex items-center space-x-1.5 sm:space-x-2 px-3 sm:px-4 py-1 sm:py-1.5 bg-gradient-to-r from-purple-600 via-fuchsia-600 to-rose-500 text-white text-xs sm:text-sm font-bold rounded-full shrink-0 border border-white/20" title="Saldo Dyanaf Poin: {{ Auth::user()->dyanaf_points }}">
                <i class="fa-solid fa-bolt text-xs sm:text-sm text-amber-300"></i>
                <span class="tabular-nums tracking-wide">{{ Auth::user()->dyanaf_points }}</span>
            </div>

            <!-- Logged In User Info Dropdown -->
            <div x-data="{ open: false }" @click.outside="open = false" class="relative shrink-0">
                <!-- User Trigger Button -->
                <button @click="open = !open" class="flex items-center space-x-2 sm:space-x-3 text-right hover:opacity-90 transition-opacity focus:outline-none cursor-pointer px-1 py-1">
                    <div class="hidden sm:flex flex-col items-end justify-center leading-tight max-w-[180px]">
                        <span class="text-sm font-bold text-[#1e293b] font-['Outfit'] truncate w-full text-right">{{ Auth::user()->name }}</span>
                        <span class="text-xs text-slate-500 font-normal truncate w-full text-right">{{ Auth::user()->email }}</span>
                    </div>
                    <div class="relative shrink-0 w-8 h-8 sm:w-10 sm:h-10">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" 
                                 alt="{{ Auth::user()->name }}" 
                                 referrerpolicy="no-referrer" 
                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=1e293b&background=e2e8f0';" 
                                 class="w-full h-full rounded-full object-cover border border-slate-200">
                        @else
                            <div class="w-full h-full rounded-full bg-slate-300 text-slate-600 flex items-center justify-center font-bold text-sm sm:text-base">
                                <i class="fa-solid fa-user"></i>
                            </div>
                        @endif
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                    </div>
                </button>

                <!-- Dropdown Menu Box -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-1.5 w-56 bg-white border border-slate-300 rounded-none z-50 overflow-hidden"
                     style="display: none;">
                    
                    <!-- Dropdown Header -->
                    <div class="px-4 py-2.5 border-b border-slate-200 bg-white">
                        <div class="text-xs font-bold text-[#1e293b] font-['Outfit'] truncate">{{ Auth::user()->name }}</div>
                        <div class="text-[11px] text-slate-500 truncate mt-0.5">{{ Auth::user()->email }}</div>
                    </div>

                    <!-- Links Section -->
                    <div class="py-1 border-b border-slate-200">
                        <a href="#" class="flex items-center space-x-3 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                            <i class="fa-solid fa-user text-slate-500 w-4 text-center text-xs"></i>
                            <span>Profile</span>
                        </a>
                        <a href="#" class="flex items-center space-x-3 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                            <i class="fa-solid fa-gear text-slate-500 w-4 text-center text-xs"></i>
                            <span>Settings</span>
                        </a>
                    </div>

                    <!-- Logout Action Section -->
                    <div class="py-1">
                        <button type="button" @click="open = false; $dispatch('open-logout-modal')" class="w-full flex items-center space-x-3 px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors text-left cursor-pointer">
                            <i class="fa-solid fa-right-from-bracket text-rose-600 w-4 text-center text-xs"></i>
                            <span>Logout</span>
                        </button>
                    </div>
                </div>
            </div>
        @else
            <!-- Guest User Info Dropdown -->
            <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                <!-- Guest Trigger Button -->
                <button @click="open = !open" class="flex items-center space-x-2 sm:space-x-3 text-right hover:opacity-90 transition-opacity focus:outline-none cursor-pointer px-1 py-1">
                    <div class="hidden sm:flex flex-col items-end justify-center leading-tight">
                        <span class="text-sm font-bold text-[#1e293b] font-['Outfit']">Guest</span>
                        <span class="text-xs text-slate-500 font-normal">Not logged in</span>
                    </div>
                    <div class="relative shrink-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-slate-200 text-slate-500 border border-slate-300 flex items-center justify-center font-bold text-sm sm:text-base">
                            <i class="fa-solid fa-user text-slate-400"></i>
                        </div>
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-slate-400 border-2 border-white rounded-full"></span>
                    </div>
                </button>

                <!-- Guest Dropdown Menu Box -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-1.5 w-60 bg-white border border-slate-300 rounded-none z-50 overflow-hidden"
                     style="display: none;">
                    
                    <!-- Guest Dropdown Header -->
                    <div class="px-4 py-2.5 border-b border-slate-200 bg-white">
                        <div class="text-xs font-bold text-[#1e293b] font-['Outfit']">Guest Account</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Please login to access your account</div>
                    </div>

                    <!-- Login Action Section -->
                    <div class="p-3 bg-slate-50/50">
                        <a href="{{ route('auth.google') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-2.5 px-4 rounded-none flex items-center justify-center space-x-2 transition-colors active:scale-[0.98]">
                            <!-- Google 4-Color Official Logo SVG -->
                            <svg class="w-4 h-4 shrink-0 bg-white p-0.5 rounded-sm" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                            </svg>
                            <span>Login with Google</span>
                        </a>
                    </div>
                </div>
            </div>
        @endauth
    </div>
</header>
