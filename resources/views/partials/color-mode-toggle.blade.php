<!-- Color Mode Toggle -->
<div class="color-mode-toggle" x-data="{ colorMenuOpen: false }">
    <button 
        @click="colorMenuOpen = !colorMenuOpen"
        class="color-mode-btn shadow-lg"
        title="Badili Mwonekano"
    >
        <i class="fas fa-palette text-sm"></i>
    </button>
    
    <div 
        x-show="colorMenuOpen" 
        @click.away="colorMenuOpen = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="color-mode-menu"
    >
        <div 
            class="color-mode-option"
            @click="changeColorMode('default'); colorMenuOpen = false"
        >
            <div class="color-mode-indicator default"></div>
            <span class="text-sm">Rangi Za Kawaida</span>
        </div>
        <div 
            class="color-mode-option"
            @click="changeColorMode('dark'); colorMenuOpen = false"
        >
            <div class="color-mode-indicator dark"></div>
            <span class="text-sm">Giza</span>
        </div>
        <div 
            class="color-mode-option"
            @click="changeColorMode('light'); colorMenuOpen = false"
        >
            <div class="color-mode-indicator light"></div>
            <span class="text-sm">Mwanga</span>
        </div>
    </div>
</div>