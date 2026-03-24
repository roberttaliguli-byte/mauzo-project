@php
    use Illuminate\Support\Facades\Auth;

    $currentRoute = request()->route()?->getName();
    $navItems = [];

    /* =========================
       Menu Definitions - Optimized for mobile
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
       Guard & Role Resolution with Uwezo Support
    ========================= */

    if (Auth::guard('mfanyakazi')->check()) {
        $employee = Auth::guard('mfanyakazi')->user();
        
        // Check if employee has full access (uwezo mkubwa)
        if (isset($employee->uwezo) && $employee->uwezo === 'mkubwa') {
            $navItems = $bossNavItems; // Full access like boss
        } else {
            $navItems = $mfanyakaziNavItems; // Limited access
        }
    } elseif (Auth::check()) {
        $user = Auth::user();
        if (in_array($user->role ?? null, ['boss', 'admin'], true)) {
            $navItems = $bossNavItems;
        }
    }
@endphp

{{-- =========================
     Sidebar Navigation - No Transparency, Proper Alignment
========================= --}}

<div class="flex flex-col h-full">
    {{-- Dashboard Section (for Boss or Mkubwa Employee) --}}
    @if((Auth::check() && Auth::user()?->role === 'boss') || 
         (Auth::guard('mfanyakazi')->check() && Auth::guard('mfanyakazi')->user()->uwezo === 'mkubwa'))
        <div class="mb-1">
            <a href="{{ route('dashboard') }}"
               class="sidebar-item flex items-center px-3 py-3 sm:px-4 sm:py-3 rounded-lg transition-all duration-200
                      {{ $currentRoute === 'dashboard' ? 'active-nav-item' : 'hover:bg-green-700/20' }}"
               @click="window.innerWidth < 1024 ? closeSidebar() : null">
                <span class="text-base sm:text-lg md:text-xl flex-shrink-0">🏠</span>
                <span x-show="sidebarOpen || window.innerWidth >= 1024" 
                      class="ml-3 sm:ml-4 font-medium text-sm sm:text-base truncate">
                    Dashboard
                </span>
                <span x-show="!(sidebarOpen || window.innerWidth >= 1024)" 
                      x-cloak
                      class="ml-2 text-xs font-medium truncate">
                    Dash
                </span>
            </a>
        </div>
        
        {{-- Divider - SOLID --}}
        <div class="border-t border-white/20 my-2"></div>
    @endif

    {{-- Main Menu Items --}}
    <div class="flex-1 space-y-1">
        @foreach ($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="sidebar-item flex items-center px-3 py-3 sm:px-4 sm:py-3 rounded-lg transition-all duration-200
                      {{ $currentRoute === $item['route'] ? 'active-nav-item' : 'hover:bg-green-700/20' }}"
               @click="window.innerWidth < 1024 ? closeSidebar() : null">
                {{-- Icon with consistent sizing --}}
                <span class="text-base sm:text-lg md:text-xl flex-shrink-0">{{ $item['icon'] }}</span>
                
                {{-- Full label - shown when sidebar is open or on desktop --}}
                <span x-show="sidebarOpen || window.innerWidth >= 1024" 
                      class="ml-3 sm:ml-4 font-medium text-sm sm:text-base truncate">
                    {{ $item['label'] }}
                </span>
                
                {{-- Short label - only shown on mobile when sidebar is collapsed --}}
                <span x-show="!(sidebarOpen || window.innerWidth >= 1024)" 
                      x-cloak
                      class="ml-2 text-xs font-medium truncate">
                    {{ $item['short'] ?? $item['label'] }}
                </span>
                
                {{-- Active indicator --}}
                @if($currentRoute === $item['route'])
                    <span class="ml-auto w-1.5 h-1.5 sm:w-2 sm:h-2 bg-white rounded-full"></span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Mobile only: Logout button at bottom --}}
    <div class="mt-auto pt-3 border-t border-white/20 lg:hidden">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="sidebar-item flex items-center px-3 py-3 sm:px-4 rounded-lg transition-all duration-200 hover:bg-red-700/50 w-full text-left"
                    @click="closeSidebar()">
                <span class="text-base sm:text-lg">🚪</span>
                <span x-show="sidebarOpen" 
                      class="ml-3 sm:ml-4 font-medium text-sm sm:text-base">
                    Toka
                </span>
                <span x-show="!sidebarOpen" 
                      x-cloak
                      class="ml-2 text-xs font-medium">
                    Toka
                </span>
            </button>
        </form>
    </div>
</div>

{{-- Optional: Add custom CSS for better mobile spacing --}}
@push('styles')
<style>
    /* Better touch targets for mobile */
    @media (max-width: 640px) {
        .sidebar-item {
            min-height: 48px;
            margin: 2px 0;
        }
    }
    
    /* Smooth transitions */
    .sidebar-item {
        transition: all 0.2s ease-in-out;
    }
    
    /* Better spacing for collapsed sidebar */
    @media (max-width: 1023px) {
        [x-cloak] {
            display: none !important;
        }
    }
</style>
@endpush