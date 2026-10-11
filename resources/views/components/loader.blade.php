<!-- Global Page Loading Screen Overlay -->
<div id="global-page-loader" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-900/40 backdrop-blur-sm transition-opacity duration-300 pointer-events-auto">
    <div class="bg-white/95 border border-slate-200/80 rounded-2xl p-6 sm:p-8 flex flex-col items-center justify-center shadow-2xl space-y-4 max-w-[280px] w-full text-center">
        <!-- Logo with Pulse and Spinner -->
        <div class="relative flex items-center justify-center w-16 h-16">
            <!-- Spinner Ring -->
            <div class="absolute inset-0 rounded-full border-4 border-slate-100 border-t-blue-600 animate-spin"></div>
            <!-- Dyanaf Logo -->
            <img src="{{ asset('image/dyanaf-logo-circle.png') }}" alt="Dyanaf" class="w-10 h-10 object-contain animate-pulse">
        </div>

        <div class="space-y-1">
            <p class="text-sm font-bold text-slate-800 tracking-wide font-['Outfit']">Memuat Halaman...</p>
            <p class="text-[11px] text-slate-500">Mohon tunggu sebentar</p>
        </div>
    </div>
</div>

<script>
    (function() {
        const loader = document.getElementById('global-page-loader');
        if (!loader) return;

        // Hide loader when DOM is ready / loaded
        function hideLoader() {
            loader.classList.add('opacity-0', 'pointer-events-none');
            setTimeout(function() {
                loader.classList.add('hidden');
            }, 300);
        }

        // Show loader on page transition
        function showLoader() {
            loader.classList.remove('hidden', 'opacity-0', 'pointer-events-none');
        }

        if (document.readyState === 'complete') {
            hideLoader();
        } else {
            window.addEventListener('load', hideLoader);
            // Fallback timeout in case load event fires early or gets delayed
            setTimeout(hideLoader, 3000);
        }

        // Handle browser back/forward cache (bfcache)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                hideLoader();
            }
        });

        // Intercept link clicks for page navigation
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;

            const href = link.getAttribute('href');
            const target = link.getAttribute('target');

            // Skip anchor links, javascript, tel/mailto, download, or new tabs
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:') || target === '_blank') {
                return;
            }

            // Skip if meta/ctrl/cmd key is pressed
            if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) {
                return;
            }

            // Check if same origin or external login
            try {
                const url = new URL(link.href, window.location.href);
                // If it's a valid navigation, show loader
                if (url.origin === window.location.origin || url.hostname.includes('google.com') || url.hostname.includes('dyanaf.com')) {
                    showLoader();
                }
            } catch (err) {
                // Ignore invalid URLs
            }
        });

        // Intercept form submissions
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.getAttribute('target') === '_blank') return;
            showLoader();
        });
    })();
</script>
