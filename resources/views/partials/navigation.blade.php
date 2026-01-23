@php
    use Illuminate\Support\Facades\Auth;

    $currentRoute = request()->route()?->getName();
    $navItems = [];

    /* =========================
       Menu Definitions - Optimized for mobile (half screen)
    ========================= */

    $bossNavItems = [
        ['route' => 'mauzo.index', 'icon' => '🛒', 'label' => 'Mauzo', 'short' => 'MZ'],
        ['route' => 'madeni.index', 'icon' => '💳', 'label' => 'Madeni', 'short' => 'DN'],
        ['route' => 'matumizi.index', 'icon' => '💰', 'label' => 'Matumizi', 'short' => 'MT'],
        ['route' => 'bidhaa.index', 'icon' => '📦', 'label' => 'Bidhaa', 'short' => 'BD'],
        ['route' => 'manunuzi.index', 'icon' => '🚚', 'label' => 'Manunuzi', 'short' => 'MN'],
        ['route' => 'wafanyakazi.index', 'icon' => '👔', 'label' => 'Wafanyakazi', 'short' => 'WF'],
        ['route' => 'masaplaya.index', 'icon' => '🏆', 'label' => 'Masaplaya', 'short' => 'MP'],
        ['route' => 'wateja.index', 'icon' => '👥', 'label' => 'Wateja', 'short' => 'WT'],
        ['route' => 'uchambuzi.index', 'icon' => '📊', 'label' => 'Uchambuzi', 'short' => 'UC'],
    ];

    $mfanyakaziNavItems = [
        ['route' => 'mauzo.index', 'icon' => '🛒', 'label' => 'Mauzo', 'short' => 'MZ'],
        ['route' => 'madeni.index', 'icon' => '💳', 'label' => 'Madeni', 'short' => 'DN'],
        ['route' => 'matumizi.index', 'icon' => '💰', 'label' => 'Matumizi', 'short' => 'MT'],
        ['route' => 'bidhaa.index', 'icon' => '📦', 'label' => 'Bidhaa', 'short' => 'BD'],
        ['route' => 'wateja.index', 'icon' => '👥', 'label' => 'Wateja', 'short' => 'WT'],
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
     Sidebar Navigation - Half screen on mobile
========================= --}}

{{-- Boss Dashboard (ONLY for boss, never mfanyakazi) --}}
@if (Auth::check() && Auth::user()?->role === 'boss')
    <a href="{{ route('dashboard') }}"
       class="sidebar-item flex items-center px-2 py-1.5 sm:px-3 sm:py-2 md:px-4 md:py-3 rounded-lg transition-all duration-200
              {{ $currentRoute === 'dashboard' ? 'active-nav-item bg-green-700' : 'hover:bg-green-700' }}"
       @click="window.innerWidth < 1024 ? closeSidebar() : null">
        <span class="text-sm sm:text-base md:text-lg flex-shrink-0">🏠</span>
        <span x-show="sidebarOpen || window.innerWidth >= 1024" 
              x-transition.opacity.duration.200ms
              class="ml-1.5 sm:ml-2 md:ml-3 font-medium text-xs sm:text-sm truncate">
            Dashboard
        </span>
    </a>
@endif

{{-- Shared Menu - Compact for half screen mobile --}}
@foreach ($navItems as $item)
    <a href="{{ route($item['route']) }}"
       class="sidebar-item flex items-center px-2 py-1.5 sm:px-3 sm:py-2 md:px-4 md:py-3 rounded-lg transition-all duration-200
              {{ $currentRoute === $item['route'] ? 'active-nav-item bg-green-700' : 'hover:bg-green-700' }}"
       @click="window.innerWidth < 1024 ? closeSidebar() : null">
        {{-- Icon (smaller on mobile) --}}
        <span class="text-sm sm:text-base md:text-lg flex-shrink-0">{{ $item['icon'] }}</span>
        
        {{-- Label - shown when sidebar is open or on desktop --}}
        <span x-show="sidebarOpen || window.innerWidth >= 1024" 
              x-transition.opacity.duration.200ms
              class="ml-1.5 sm:ml-2 md:ml-3 font-medium text-xs sm:text-sm truncate">
            {{ $item['label'] }}
        </span>
        
        {{-- Short label for mobile when sidebar is collapsed --}}
        <span x-show="!(sidebarOpen || window.innerWidth >= 1024)" 
              x-cloak
              class="ml-1.5 text-xs font-medium truncate">
            {{ $item['short'] ?? $item['label'] }}
        </span>
        
        {{-- Active indicator --}}
        @if($currentRoute === $item['route'])
            <span class="ml-auto w-1.5 h-1.5 sm:w-2 sm:h-2 bg-white rounded-full"></span>
        @endif
    </a>
@endforeach

{{-- Mobile only: Add logout button at bottom --}}
<div class="mt-auto pt-2 border-t border-green-700 lg:hidden">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="sidebar-item flex items-center px-2 py-1.5 sm:px-3 sm:py-2 rounded-lg transition-all duration-200 hover:bg-red-700 w-full text-left"
                @click="closeSidebar()">
            <span class="text-sm sm:text-base">🚪</span>
            <span x-show="sidebarOpen" 
                  x-transition.opacity.duration.200ms
                  class="ml-1.5 sm:ml-2 font-medium text-xs sm:text-sm">
                Toka
            </span>
            <span x-show="!sidebarOpen" 
                  x-cloak
                  class="ml-1.5 text-xs font-medium">
                Toka
            </span>
        </button>
    </form>
</div>