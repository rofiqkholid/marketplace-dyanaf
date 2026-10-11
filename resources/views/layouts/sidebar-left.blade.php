<div class="contents"
     x-data="{ mobileOpen: false }"
     @toggle-mobile-sidebar.window="mobileOpen = !mobileOpen"
     @keydown.escape.window="mobileOpen = false">

    <!-- Mobile Overlay Backdrop (Only visible < lg breakpoint) -->
    <div x-show="mobileOpen" 
         x-cloak 
         @click="mobileOpen = false" 
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 z-[9998] lg:hidden"
         style="display: none;">
    </div>

    <!-- Mobile Slide-over Drawer Panel (Only visible < lg breakpoint) -->
    <div x-show="mobileOpen" 
         x-cloak 
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 z-[9999] w-72 bg-[#f8fafc] border-r border-slate-300 flex flex-col justify-between p-4 h-full overflow-hidden lg:hidden"
         style="display: none;">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-3 shrink-0">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('image/dyanaf-logo-circle.png') }}" alt="Dyanaf Logo" class="w-7 h-7 object-contain">
                <span class="text-sm font-bold text-slate-800 font-['Outfit']">Menu Navigasi</span>
            </div>
            <button @click="mobileOpen = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <!-- Mobile Scrollable Content (Isolated Scroll) -->
        <div id="mobile-sidebar-scroll" class="space-y-5 overflow-y-auto flex-1 pr-1 overscroll-contain">
            <nav class="space-y-1">
                @foreach($mainMenus as $menu)
                    @php
                        $targetUrl = $menu->is_maintenance ? route('maintenance', ['page' => $menu->name]) : $menu->url;
                        $isActive = ($menu->url === '/' && request()->is('/')) || ($menu->url !== '#' && $menu->url !== '/' && (request()->is(trim($menu->url, '/')) || request()->fullUrlIs(url($menu->url))));
                    @endphp
                    <a href="{{ $targetUrl }}" class="flex items-center space-x-3 px-3.5 py-2 text-xs {{ $isActive ? 'font-bold text-blue-600' : 'font-medium text-slate-600 hover:text-slate-900' }} hover:bg-blue-50 rounded-none transition-colors">
                        @if($menu->icon)
                            <i class="{{ $menu->icon }}"></i>
                        @endif
                        <span>{{ $menu->name }}</span>
                    </a>
                @endforeach
            </nav>

            @foreach($sectionMenus as $section)
                <div>
                    <div class="text-xs font-bold text-slate-500 mb-2 px-1">{{ $section->name }}</div>
                    <nav class="space-y-1">
                        @foreach($section->children as $child)
                            @php
                                $childTargetUrl = $child->is_maintenance ? route('maintenance', ['page' => $child->name]) : $child->url;
                                $isChildActive = ($child->url === '/' && request()->is('/')) || ($child->url !== '#' && $child->url !== '/' && (request()->is(trim($child->url, '/')) || request()->fullUrlIs(url($child->url))));
                            @endphp
                            @php
                                $isExternal = !$child->is_maintenance && (str_starts_with($child->url, 'http://') || str_starts_with($child->url, 'https://'));
                            @endphp
                            <a href="{{ $childTargetUrl }}" 
                               @if($isExternal) target="_blank" rel="noopener noreferrer" @endif
                               class="flex items-center justify-between px-3.5 py-1.5 text-xs {{ $isChildActive ? 'font-bold text-blue-600' : 'text-slate-600 hover:text-slate-900' }} hover:bg-blue-50 rounded-none transition-colors">
                                <div class="flex items-center space-x-3">
                                    @if($child->icon)
                                        <i class="{{ $child->icon }}"></i>
                                    @endif
                                    <span>{{ $child->name }}</span>
                                </div>
                                @if($section->name === 'Categories')
                                    <i class="fa-solid fa-chevron-right text-[9px] text-slate-400"></i>
                                @endif
                            </a>
                        @endforeach
                    </nav>
                </div>
            @endforeach
        </div>

        <!-- Fixed Bottom User Info Card in Mobile Drawer -->
        <div class="pt-3 border-t border-slate-300/80 mt-3 shrink-0">
            @auth
                <div class="flex items-center justify-between px-3 py-2 bg-[#eef2f6] border border-slate-300/70 rounded-none gap-2">
                    <div class="flex items-center space-x-3 min-w-0 flex-1">
                        <div class="relative shrink-0">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" 
                                     alt="{{ Auth::user()->name }}" 
                                     referrerpolicy="no-referrer" 
                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=1e293b&background=e2e8f0';" 
                                     class="w-8 h-8 rounded-full object-cover border border-slate-300">
                            @else
                                <div class="w-8 h-8 rounded-full bg-slate-300 text-slate-600 flex items-center justify-center font-bold text-xs">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                            @endif
                            <span class="absolute bottom-0 right-0 w-2 h-2 bg-emerald-500 border border-white rounded-full"></span>
                        </div>
                        <div class="overflow-hidden leading-tight min-w-0 flex-1">
                            <div class="text-xs font-bold text-[#1e293b] font-['Outfit'] truncate">{{ Auth::user()->name }}</div>
                            <div class="text-[11px] text-slate-500 truncate mt-0.5">{{ Auth::user()->google_id ?? 'No Google ID' }}</div>
                        </div>
                    </div>
                    
                    <!-- Red Logout Button -->
                    <form action="{{ route('logout') }}" method="POST" class="shrink-0 flex items-center">
                        @csrf
                        <button type="submit" 
                                class="p-1.5 text-rose-600 hover:text-rose-700 transition-colors cursor-pointer" 
                                title="Keluar / Logout">
                            <i class="fa-solid fa-right-from-bracket text-sm"></i>
                        </button>
                    </form>
                </div>
            @else
                <div class="flex items-center space-x-3 px-3 py-2 bg-[#eef2f6] border border-slate-300/70 rounded-none">
                    <div class="relative shrink-0">
                        <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-400 border border-slate-300 flex items-center justify-center font-bold text-xs">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <span class="absolute bottom-0 right-0 w-2 h-2 bg-slate-400 border border-white rounded-full"></span>
                    </div>
                    <div class="overflow-hidden leading-tight">
                        <div class="text-xs font-bold text-[#1e293b] font-['Outfit']">Guest</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Guest Account</div>
                    </div>
                </div>
            @endauth
        </div>
    </div>

    <!-- Desktop Docked Sidebar (Hidden on mobile < lg, Docked on desktop lg+) -->
    <aside class="w-72 bg-[#f8fafc] border-r border-slate-300/80 shrink-0 hidden lg:flex flex-col justify-between h-[calc(100vh-4rem)] sticky top-16 p-3.5 rounded-none">
        <!-- Scrollable Menu Items Container (Isolated Scroll) -->
        <div id="desktop-sidebar-scroll" class="space-y-5 overflow-y-auto flex-1 pr-1 overscroll-contain">
            <!-- Main Navigation Links (Standalone Items) -->
            <nav class="space-y-1">
                @foreach($mainMenus as $menu)
                    @php
                        $targetUrl = $menu->is_maintenance ? route('maintenance', ['page' => $menu->name]) : $menu->url;
                        $isActive = ($menu->url === '/' && request()->is('/')) || ($menu->url !== '#' && $menu->url !== '/' && (request()->is(trim($menu->url, '/')) || request()->fullUrlIs(url($menu->url))));
                    @endphp
                    <a href="{{ $targetUrl }}" class="flex items-center space-x-3 px-3.5 py-2 text-xs {{ $isActive ? 'active-sidebar-item font-bold text-blue-600' : 'font-medium text-slate-600 hover:text-slate-900' }} hover:bg-blue-50 rounded-none transition-colors">
                        @if($menu->icon)
                            <i class="{{ $menu->icon }}"></i>
                        @endif
                        <span>{{ $menu->name }}</span>
                    </a>
                @endforeach
            </nav>

            <!-- Level 1 Menu Sections (Categories, Quick Actions, App Settings, etc.) & Level 2 Items -->
            @foreach($sectionMenus as $section)
                <div>
                    <!-- Level 1 Label -->
                    <div class="text-xs font-bold text-slate-500 mb-2 px-1">{{ $section->name }}</div>
                    
                    <!-- Level 2 Submenus -->
                    <nav class="space-y-1">
                        @foreach($section->children as $child)
                            @php
                                $childTargetUrl = $child->is_maintenance ? route('maintenance', ['page' => $child->name]) : $child->url;
                                $isChildActive = ($child->url === '/' && request()->is('/')) || ($child->url !== '#' && $child->url !== '/' && (request()->is(trim($child->url, '/')) || request()->fullUrlIs(url($child->url))));
                            @endphp
                            @php
                                $isExternal = !$child->is_maintenance && (str_starts_with($child->url, 'http://') || str_starts_with($child->url, 'https://'));
                            @endphp
                            <a href="{{ $childTargetUrl }}" 
                               @if($isExternal) target="_blank" rel="noopener noreferrer" @endif
                               class="flex items-center justify-between px-3.5 py-1.5 text-xs {{ $isChildActive ? 'active-sidebar-item font-bold text-blue-600' : 'text-slate-600 hover:text-slate-900' }} hover:bg-blue-50 rounded-none transition-colors">
                                <div class="flex items-center space-x-3">
                                    @if($child->icon)
                                        <i class="{{ $child->icon }}"></i>
                                    @endif
                                    <span>{{ $child->name }}</span>
                                </div>
                                @if($section->name === 'Categories')
                                    <i class="fa-solid fa-chevron-right text-[9px] text-slate-400"></i>
                                @endif
                            </a>
                        @endforeach
                    </nav>
                </div>
            @endforeach
        </div>

        <!-- Fixed Bottom User Info Card (Pinned at Bottom) -->
        <div class="pt-3 border-t border-slate-300/80 mt-3 shrink-0">
            @auth
                <div class="flex items-center justify-between px-3 py-2 bg-[#eef2f6] border border-slate-300/70 rounded-none gap-2">
                    <div class="flex items-center space-x-3 min-w-0 flex-1">
                        <div class="relative shrink-0">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" 
                                     alt="{{ Auth::user()->name }}" 
                                     referrerpolicy="no-referrer" 
                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=1e293b&background=e2e8f0';" 
                                     class="w-8 h-8 rounded-full object-cover border border-slate-300">
                            @else
                                <div class="w-8 h-8 rounded-full bg-slate-300 text-slate-600 flex items-center justify-center font-bold text-xs">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                            @endif
                            <span class="absolute bottom-0 right-0 w-2 h-2 bg-emerald-500 border border-white rounded-full"></span>
                        </div>
                        <div class="overflow-hidden leading-tight min-w-0 flex-1">
                            <div class="text-xs font-bold text-[#1e293b] font-['Outfit'] truncate">{{ Auth::user()->name }}</div>
                            <div class="text-[11px] text-slate-500 truncate mt-0.5">{{ Auth::user()->google_id ?? 'No Google ID' }}</div>
                        </div>
                    </div>
                    
                    <!-- Red Logout Button -->
                    <form action="{{ route('logout') }}" method="POST" class="shrink-0 flex items-center">
                        @csrf
                        <button type="submit" 
                                class="p-1.5 text-rose-600 hover:text-rose-700 transition-colors cursor-pointer" 
                                title="Keluar / Logout">
                            <i class="fa-solid fa-right-from-bracket text-sm"></i>
                        </button>
                    </form>
                </div>
            @else
                <div class="flex items-center space-x-3 px-3 py-2 bg-[#eef2f6] border border-slate-300/70 rounded-none">
                    <div class="relative shrink-0">
                        <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-400 border border-slate-300 flex items-center justify-center font-bold text-xs">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <span class="absolute bottom-0 right-0 w-2 h-2 bg-slate-400 border border-white rounded-full"></span>
                    </div>
                    <div class="overflow-hidden leading-tight">
                        <div class="text-xs font-bold text-[#1e293b] font-['Outfit']">Guest</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Guest Account</div>
                    </div>
                </div>
            @endauth
        </div>
    </aside>

    <!-- Sidebar Scroll Persistence Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var sidebars = [
                { id: 'desktop-sidebar-scroll', key: 'desktop-sidebar-scroll-pos' },
                { id: 'mobile-sidebar-scroll', key: 'mobile-sidebar-scroll-pos' }
            ];

            sidebars.forEach(function(item) {
                var el = document.getElementById(item.id);
                if (!el) return;

                // 1. Pulihkan posisi scroll yang tersimpan
                var saved = sessionStorage.getItem(item.key);
                if (saved !== null) {
                    el.scrollTop = parseInt(saved, 10);
                }

                // 2. Jika ada menu yang aktif, pastikan posisinya berada di view area
                var activeItem = el.querySelector('.active-sidebar-item');
                if (activeItem) {
                    var elRect = el.getBoundingClientRect();
                    var itemRect = activeItem.getBoundingClientRect();
                    // Jika posisi menu aktif di luar pandangan (terlalu bawah atau terlalu atas), scroll ke posisinya
                    if (itemRect.top < elRect.top || itemRect.bottom > elRect.bottom) {
                        activeItem.scrollIntoView({ block: 'nearest' });
                    }
                }

                // 3. Simpan posisi scroll secara real-time saat pengguna scroll
                el.addEventListener('scroll', function() {
                    sessionStorage.setItem(item.key, el.scrollTop);
                }, { passive: true });
            });

            // 4. Pastikan juga posisi tersimpan tepat saat klik link navigasi apa pun
            document.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', function() {
                    sidebars.forEach(function(item) {
                        var el = document.getElementById(item.id);
                        if (el) {
                            sessionStorage.setItem(item.key, el.scrollTop);
                        }
                    });
                });
            });
        });
    </script>
</div>
