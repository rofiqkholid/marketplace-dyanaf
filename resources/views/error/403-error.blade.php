@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex flex-col items-center justify-center text-center p-4">
    <!-- Simple Centered Minimalist 403 View -->
    <div class="max-w-md mx-auto space-y-4">
        
        <!-- Soft Circular Icon -->
        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-slate-200/70 text-slate-400 flex items-center justify-center mx-auto mb-2">
            <i class="fa-solid fa-lock text-2xl sm:text-3xl"></i>
        </div>

        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-['Outfit'] tracking-tight">
                Error 403
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1 font-medium">
                {{ $exception?->getMessage() ?: 'Akses Ditolak. Halaman ini hanya dapat diakses oleh Pemilik.' }}
            </p>
            <p class="text-[11px] text-slate-400 mt-0.5">
                Access Forbidden
            </p>
        </div>

        <!-- Back Button -->
        <div class="pt-2">
            <a href="/" 
               class="inline-flex items-center justify-center space-x-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-none transition-colors shadow-sm cursor-pointer">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Back to Marketplace</span>
            </a>
        </div>

    </div>
</div>
@endsection
