@php
    $currentRoute = request()->route()->getName();
    $navItems = [
        ['route' => 'admin.dashboard', 'icon' => '<i class="fas fa-chart-line me-2"></i>', 'label' => 'Makampuni', 'match' => 'admin/dashboard'],
        ['route' => 'admin.dashboard', 'icon' => '<i class="fas fa-lock me-2"></i>', 'label' => 'Neno la Siri', 'match' => 'admin/nenosiri*'],
        ['route' => 'logout', 'icon' => '<i class="fas fa-power-off me-2"></i>', 'label' => 'Toka', 'logout' => true],
    ];
@endphp

@foreach($navItems as $item)
    @if(isset($item['logout']) && $item['logout'])
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                class="sidebar-item flex items-center px-4 py-3 rounded-xl transition-all duration-200 w-full hover:bg-green-700">
                {!! $item['icon'] !!} <span x-show="sidebarOpen" x-transition class="ml-3 font-medium">{{ $item['label'] }}</span>
            </button>
        </form>
    @else
        <a href="{{ route($item['route']) }}" 
            class="sidebar-item flex items-center px-4 py-3 rounded-xl transition-all duration-200" 
            :class="[sidebarOpen ? 'justify-start' : 'justify-center', 
                '{{ request()->is($item['match']) ? 'active-nav-item bg-green-700' : 'hover:bg-green-700' }}']">
            {!! $item['icon'] !!}
            <span x-show="sidebarOpen" x-transition class="ml-3 font-medium">{{ $item['label'] }}</span>
        </a>
    @endif
@endforeach
