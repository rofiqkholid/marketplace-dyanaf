<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dyanaf Marketplace</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('image/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('image/apple-touch-icon.png') }}">
    
    <!-- Vite Assets (Tailwind CSS, Outfit Fonts & Font Awesome Icons bundle offline) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f0f4f8] text-slate-800 font-sans antialiased min-h-screen selection:bg-blue-500 selection:text-white">

    <!-- Global Page Transition Loader (Immediately rendered first) -->
    @include('components.loader')

    <!-- Top Navbar -->
    @include('components.navbar')

    <!-- Main Container (Full Width, Sidebars Docked to Left & Right Edges) -->
    <div class="flex w-full pt-16 min-h-[calc(100vh-4rem)]">
        <!-- Left Sidebar -->
        @include('layouts.sidebar-left')

        <!-- Center Main Content -->
        <main class="flex-1 p-4 md:p-6 w-full">
            @yield('content')
        </main>

        <!-- Right Sidebar (Rendered on marketplace homepage, maintenance & error pages) -->
        @if(request()->is('/') || request()->is('maintenance') || !empty($exception))
            @include('layouts.sidebar-right')
        @endif
    </div>

    <!-- Global Condition Modals -->
    <x-modal-condition />
    <x-modal-information />

    <!-- Global Toast Alert Notifications (Bottom-Right Floating) -->
    @include('components.alert')

    @stack('scripts')
</body>
</html>
