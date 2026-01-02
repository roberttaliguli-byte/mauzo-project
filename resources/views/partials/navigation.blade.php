@php
    use Illuminate\Support\Facades\Auth;

    $currentRoute = request()->route()?->getName();
    $navItems = [];

    /* =========================
       Menu Definitions
    ========================= */

    $bossNavItems = [
        ['route' => 'mauzo.index', 'icon' => '🛒', 'label' => 'Mauzo'],
        ['route' => 'madeni.index', 'icon' => '💳', 'label' => 'Madeni'],
        ['route' => 'matumizi.index', 'icon' => '💰', 'label' => 'Matumizi'],
        ['route' => 'bidhaa.index', 'icon' => '📦', 'label' => 'Bidhaa'],
        ['route' => 'manunuzi.index', 'icon' => '🚚', 'label' => 'Manunuzi'],
        ['route' => 'wafanyakazi.index', 'icon' => '👔', 'label' => 'Wafanyakazi'],
        ['route' => 'masaplaya.index', 'icon' => '🏆', 'label' => 'Masaplaya'],
        ['route' => 'wateja.index', 'icon' => '👥', 'label' => 'Wateja'],
        ['route' => 'uchambuzi.index', 'icon' => '📊', 'label' => 'Uchambuzi'],
    ];

    $mfanyakaziNavItems = [
        ['route' => 'mauzo.index', 'icon' => '🛒', 'label' => 'Mauzo'],
        ['route' => 'madeni.index', 'icon' => '💳', 'label' => 'Madeni'],
        ['route' => 'matumizi.index', 'icon' => '💰', 'label' => 'Matumizi'],
        ['route' => 'bidhaa.index', 'icon' => '📦', 'label' => 'Bidhaa'],
        ['route' => 'wateja.index', 'icon' => '👥', 'label' => 'Wateja'],
    ];

    /* =========================
       Guard & Role Resolution
    ========================= */

    if (Auth::guard('mfanyakazi')->check()) {
        // Employee logged in
        $navItems = $mfanyakaziNavItems;

    } elseif (Auth::check()) {
        // Web guard (boss / admin)
        $user = Auth::user();

        if (in_array($user->role ?? null, ['boss', 'admin'], true)) {
            $navItems = $bossNavItems;
        }
    }
@endphp

{{-- =========================
     Sidebar Navigation
========================= --}}

{{-- Boss Dashboard (ONLY for boss, never mfanyakazi) --}}
@if (Auth::check() && Auth::user()?->role === 'boss')
    <a href="{{ route('dashboard') }}"
       class="sidebar-item flex items-center px-4 py-3 rounded-xl transition-all duration-200
              {{ $currentRoute === 'dashboard' ? 'active-nav-item bg-green-700' : 'hover:bg-green-700' }}">
        <span class="text-lg">🏠</span>
        <span x-show="sidebarOpen" x-transition class="ml-3 font-medium">
            Dashboard
        </span>
    </a>
@endif

{{-- Shared Menu --}}
@foreach ($navItems as $item)
    <a href="{{ route($item['route']) }}"
       class="sidebar-item flex items-center px-4 py-3 rounded-xl transition-all duration-200
              {{ $currentRoute === $item['route'] ? 'active-nav-item bg-green-700' : 'hover:bg-green-700' }}">
        <span class="text-lg">{{ $item['icon'] }}</span>
        <span x-show="sidebarOpen" x-transition class="ml-3 font-medium">
            {{ $item['label'] }}
        </span>
    </a>
@endforeach
