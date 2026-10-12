@props([
    'paginator',
    'perPageOptions' => [10, 50, 100],
])

@php
    $currentPage = $paginator->currentPage();
    $lastPage = max(1, $paginator->lastPage());
    $total = $paginator->total();
    $firstItem = $paginator->firstItem() ?? 0;
    $lastItem = $paginator->lastItem() ?? 0;
    $currentPerPage = (int) ($paginator->perPage() ?? 10);

    // Build smart page numbers list with ellipsis
    $pageList = [];
    if ($lastPage <= 7) {
        for ($i = 1; $i <= $lastPage; $i++) {
            $pageList[] = $i;
        }
    } else {
        if ($currentPage <= 4) {
            for ($i = 1; $i <= 5; $i++) {
                $pageList[] = $i;
            }
            $pageList[] = '...';
            $pageList[] = $lastPage;
        } elseif ($currentPage >= $lastPage - 3) {
            $pageList[] = 1;
            $pageList[] = '...';
            for ($i = $lastPage - 4; $i <= $lastPage; $i++) {
                $pageList[] = $i;
            }
        } else {
            $pageList[] = 1;
            $pageList[] = '...';
            for ($i = $currentPage - 1; $i <= $currentPage + 1; $i++) {
                $pageList[] = $i;
            }
            $pageList[] = '...';
            $pageList[] = $lastPage;
        }
    }
@endphp

<!-- Table Footer: Pagination & Per-Page Controls (Serverside AJAX SPA) -->
<div class="p-3 bg-slate-50/80 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-600">
    <div class="flex flex-wrap items-center gap-3">
        <div class="flex items-center space-x-2">
            <span class="text-slate-500 font-medium">Tampilkan:</span>
            <select 
                onchange="window.navigateTable(this.value, this.closest('.table-card-container') || 'table-card-container')" 
                class="bg-white border border-slate-300 text-slate-800 text-xs px-2.5 py-1.5 focus:outline-none focus:border-blue-500 cursor-pointer font-medium hover:border-slate-400 transition-colors">
                @foreach($perPageOptions as $option)
                    <option 
                        value="{{ request()->fullUrlWithQuery(['per_page' => $option, 'page' => 1]) }}"
                        {{ $currentPerPage === (int)$option ? 'selected' : '' }}>
                        {{ $option }} data
                    </option>
                @endforeach
            </select>
        </div>

        <div class="text-slate-500 border-l border-slate-200 pl-3">
            Menampilkan 
            <span class="font-bold text-slate-800">{{ $firstItem }}</span> 
            sampai 
            <span class="font-bold text-slate-800">{{ $lastItem }}</span> 
            dari 
            <span class="font-bold text-slate-800">{{ $total }}</span> 
            data
        </div>
    </div>

    <!-- Pagination Buttons -->
    <div class="flex items-center space-x-1 self-center sm:self-auto">
        <!-- Tombol Prev -->
        @if($paginator->onFirstPage() || $total === 0)
            <button type="button" disabled class="inline-flex items-center justify-center px-2.5 py-1.5 border text-xs opacity-40 cursor-not-allowed bg-slate-50 text-slate-400 border-slate-200">
                <i class="fa-solid fa-chevron-left text-[10px] mr-1"></i>
                <span>Prev</span>
            </button>
        @else
            <button type="button" 
               onclick="window.navigateTable('{{ $paginator->previousPageUrl() }}', this.closest('.table-card-container') || 'table-card-container')"
               class="inline-flex items-center justify-center px-2.5 py-1.5 border text-xs bg-white hover:bg-slate-100 text-slate-700 hover:text-slate-900 border-slate-300 cursor-pointer transition-colors"
               title="Halaman Sebelumnya">
                <i class="fa-solid fa-chevron-left text-[10px] mr-1"></i>
                <span>Prev</span>
            </button>
        @endif

        <!-- Page Number Buttons -->
        @foreach($pageList as $p)
            @if($p === '...')
                <span class="px-2 py-1 text-xs text-slate-400">...</span>
            @elseif($p == $currentPage)
                <span class="min-w-[30px] h-[28px] flex items-center justify-center border text-xs bg-blue-600 text-white border-blue-600 font-bold">
                    {{ $p }}
                </span>
            @else
                <button type="button" 
                   onclick="window.navigateTable('{{ $paginator->url($p) }}', this.closest('.table-card-container') || 'table-card-container')"
                   class="min-w-[30px] h-[28px] flex items-center justify-center border text-xs bg-white hover:bg-slate-100 text-slate-700 border-slate-300 font-medium cursor-pointer transition-colors">
                    {{ $p }}
                </button>
            @endif
        @endforeach

        <!-- Tombol Next -->
        @if(!$paginator->hasMorePages() || $total === 0)
            <button type="button" disabled class="inline-flex items-center justify-center px-2.5 py-1.5 border text-xs opacity-40 cursor-not-allowed bg-slate-50 text-slate-400 border-slate-200">
                <span>Next</span>
                <i class="fa-solid fa-chevron-right text-[10px] ml-1"></i>
            </button>
        @else
            <button type="button" 
               onclick="window.navigateTable('{{ $paginator->nextPageUrl() }}', this.closest('.table-card-container') || 'table-card-container')"
               class="inline-flex items-center justify-center px-2.5 py-1.5 border text-xs bg-white hover:bg-slate-100 text-slate-700 hover:text-slate-900 border-slate-300 cursor-pointer transition-colors"
               title="Halaman Selanjutnya">
                <span>Next</span>
                <i class="fa-solid fa-chevron-right text-[10px] ml-1"></i>
            </button>
        @endif
    </div>
</div>

@once
<script>
window.navigateTable = async function(url, target, pushState = true) {
    if (!url) return;
    
    let container = null;
    if (typeof target === 'string') {
        container = document.getElementById(target);
    } else if (target instanceof HTMLElement) {
        container = target;
    }

    if (!container) {
        container = document.getElementById('table-card-container') || document.querySelector('.table-card-container');
    }

    if (!container) {
        window.location.href = url;
        return;
    }

    // Pastikan container memiliki posisi relative untuk overlay
    container.style.position = 'relative';

    // Tampilkan loading kecil di area tabel
    let loader = container.querySelector('.table-loading-overlay');
    if (!loader) {
        loader = document.createElement('div');
        loader.className = 'table-loading-overlay absolute inset-0 bg-white/70 backdrop-blur-[1px] flex items-center justify-center z-30 transition-all';
        loader.innerHTML = `
            <div class="inline-flex items-center space-x-2.5 bg-white border border-slate-300 px-4 py-2 shadow-sm text-xs text-slate-700 font-semibold">
                <i class="fa-solid fa-circle-notch fa-spin text-blue-600 text-sm"></i>
                <span>Memuat data...</span>
            </div>
        `;
        container.appendChild(loader);
    } else {
        loader.style.display = 'flex';
    }

    try {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error('HTTP error ' + response.status);
        }

        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        const newCard = doc.getElementById(container.id) || doc.querySelector('.table-card-container');
        if (newCard) {
            if (pushState) {
                window.history.pushState(null, '', url);
            }
            container.innerHTML = newCard.innerHTML;
            if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                window.Alpine.initTree(container);
            }
        } else {
            window.location.href = url;
        }
    } catch (err) {
        console.error('AJAX table navigation error:', err);
        window.location.href = url;
    } finally {
        const currentLoader = container.querySelector('.table-loading-overlay');
        if (currentLoader) {
            currentLoader.remove();
        }
    }
};

window.addEventListener('popstate', () => {
    const container = document.getElementById('table-card-container') || document.querySelector('.table-card-container');
    if (container) {
        window.navigateTable(window.location.href, container, false);
    }
});
</script>
@endonce
