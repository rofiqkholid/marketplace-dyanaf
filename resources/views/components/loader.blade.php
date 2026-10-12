<!-- Inline Critical Styles so Loader renders instantly before any external CSS is downloaded -->
<style id="loader-critical-css">
    #global-page-loader {
        position: fixed;
        inset: 0;
        z-index: 99999;
        background-color: #f0f4f8;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: opacity 0.5s ease, visibility 0.5s ease;
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }
    .mascot-container-box {
        background-color: #ffdd44;
        border: 2px solid #1a1a1a;
        box-shadow: 5px 5px 0px 0px #1a1a1a;
        border-radius: 1.1rem;
        padding: 1rem 1.25rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        max-width: 210px;
        width: 75%;
        transition: opacity 0.5s ease;
        opacity: 1;
    }
    .mascot-svg {
        width: 100%;
        max-width: 155px;
        height: auto;
    }
    @media (min-width: 640px) {
        .mascot-container-box {
            border: 2.5px solid #1a1a1a;
            box-shadow: 8px 8px 0px 0px #1a1a1a;
            border-radius: 1.5rem;
            padding: 1.5rem 1.75rem;
            gap: 1rem;
            max-width: 320px;
            width: 90%;
        }
        .mascot-svg {
            max-width: 220px;
        }
    }
    #global-page-loader.fade-out {
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }
    #global-page-loader.fade-out .mascot-container-box {
        opacity: 0 !important;
    }
    @keyframes mascot-pop {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.22);
        }
    }
    .animate-mascot-1, .animate-mascot-2, .animate-mascot-3 {
        transform-box: fill-box;
        transform-origin: center;
        animation: mascot-pop 0.6s infinite ease-in-out;
    }
    .animate-mascot-1 { animation-delay: 0s; }
    .animate-mascot-2 { animation-delay: 0.2s; }
    .animate-mascot-3 { animation-delay: 0.4s; }
</style>

<!-- Global Page Loading Screen Overlay (Renders immediately at top of <body>) -->
<div id="global-page-loader">
    <div class="mascot-container-box">
        <svg class="mascot-svg" viewBox="0 0 320 150" fill="none">
            <!-- Mascot 1 (Teal Bear/Cat) -->
            <g transform="translate(40, 30)">
                <g class="animate-mascot-1">
                    <ellipse cx="45" cy="55" rx="35" ry="30" fill="#8be4d6" stroke="#1a1a1a" stroke-width="3"/>
                    <path d="M 20 30 L 10 5 L 35 25 Z" fill="#8be4d6" stroke="#1a1a1a" stroke-width="3"/>
                    <path d="M 70 30 L 80 5 L 55 25 Z" fill="#8be4d6" stroke="#1a1a1a" stroke-width="3"/>
                    <circle cx="33" cy="50" r="4" fill="#1a1a1a"/>
                    <circle cx="57" cy="50" r="4" fill="#1a1a1a"/>
                    <circle cx="23" cy="58" r="4" fill="#ff7f9d"/>
                    <circle cx="67" cy="58" r="4" fill="#ff7f9d"/>
                    <path d="M 40 58 Q 45 64 50 58" stroke="#1a1a1a" stroke-width="3" stroke-linecap="round" fill="none"/>
                </g>
            </g>
            <!-- Mascot 2 (Orange Bear) -->
            <g transform="translate(110, 15)">
                <g class="animate-mascot-2">
                    <ellipse cx="50" cy="65" rx="40" ry="35" fill="#f7a027" stroke="#1a1a1a" stroke-width="3"/>
                    <circle cx="20" cy="35" r="14" fill="#f7a027" stroke="#1a1a1a" stroke-width="3"/>
                    <circle cx="80" cy="35" r="14" fill="#f7a027" stroke="#1a1a1a" stroke-width="3"/>
                    <ellipse cx="38" cy="60" rx="4" ry="6" fill="#1a1a1a"/>
                    <ellipse cx="62" cy="60" rx="4" ry="6" fill="#1a1a1a"/>
                    <ellipse cx="50" cy="70" rx="10" ry="7" fill="#ffffff" stroke="#1a1a1a" stroke-width="2"/>
                    <path d="M 50 67 L 50 71 Q 50 75 46 75 M 50 71 Q 50 75 54 75" stroke="#1a1a1a" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                </g>
            </g>
            <!-- Mascot 3 (Yellow Bunny) -->
            <g transform="translate(190, 25)">
                <g class="animate-mascot-3">
                    <ellipse cx="40" cy="60" rx="30" ry="28" fill="#ffdd44" stroke="#1a1a1a" stroke-width="3"/>
                    <path d="M 25 35 C 15 10 30 -5 35 30" fill="#ffdd44" stroke="#1a1a1a" stroke-width="3"/>
                    <path d="M 55 35 C 65 10 50 -5 45 30" fill="#ffdd44" stroke="#1a1a1a" stroke-width="3"/>
                    <circle cx="30" cy="55" r="3.5" fill="#1a1a1a"/>
                    <circle cx="50" cy="55" r="3.5" fill="#1a1a1a"/>
                    <path d="M 36 63 Q 40 67 44 63" stroke="#1a1a1a" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                </g>
            </g>
        </svg>
    </div>
</div>

<script>
    (function() {
        const loader = document.getElementById('global-page-loader');
        if (!loader) return;

        let isHidden = false;

        function hideLoader() {
            if (isHidden) return;
            isHidden = true;
            // Delay 300ms so user has time to see the ready transition smoothly
            setTimeout(function() {
                loader.classList.add('fade-out');
            }, 300);
        }

        function showLoader() {
            isHidden = false;
            loader.classList.remove('fade-out');
        }

        // Wait strictly until window has finished loading ALL resources (images, fonts, scripts)
        if (document.readyState === 'complete') {
            hideLoader();
        } else {
            window.addEventListener('load', hideLoader);
        }

        // Handle browser bfcache (Back / Forward navigation)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                hideLoader();
            }
        });
    })();
</script>
