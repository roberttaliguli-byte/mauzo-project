@php
    $currentRoute = request()->route()->getName();
    $navItems = [
        ['route' => 'dashboard', 'icon' => '🏠', 'label' => 'Dashboard'],
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
@endphp

@foreach($navItems as $item)
    <a href="{{ route($item['route']) }}" 
       class="sidebar-item flex items-center px-4 py-3 rounded-xl transition-all duration-200" 
       :class="[
           sidebarOpen ? 'justify-start' : 'justify-center',
           '{{ $currentRoute === $item['route'] ? 'active-nav-item bg-green-700' : 'hover:bg-green-700' }}'
       ]">
        <span class="text-lg">{{ $item['icon'] }}</span>
        <span x-show="sidebarOpen" x-transition class="ml-3 font-medium">{{ $item['label'] }}</span>
    </a>
@endforeach