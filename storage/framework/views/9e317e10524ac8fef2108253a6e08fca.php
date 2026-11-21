<?php
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
        ['route' => 'uzalishaji.index', 'icon' => '🏭', 'label' => 'Uzalishaji', 'short' => 'UZ'], // NEW
          ['route' => 'mengineyo.index', 'icon' => '💰', 'label' => 'Mengineyo', 'short' => 'ME'], 
         
    ];

    $mfanyakaziNavItems = [
        ['route' => 'mauzo.index', 'icon' => '🛒', 'label' => 'Mauzo', 'short' => 'MZ'],
        ['route' => 'shift.mine', 'icon' => '🕒', 'label' => 'Shift Yangu', 'short' => 'SY'],
        ['route' => 'madeni.index', 'icon' => '💳', 'label' => 'Madeni', 'short' => 'DN'],
        ['route' => 'matumizi.index', 'icon' => '💰', 'label' => 'Matumizi', 'short' => 'MT'],
        ['route' => 'bidhaa.index', 'icon' => '📦', 'label' => 'Bidhaa', 'short' => 'BD'],
        ['route' => 'manunuzi.index', 'icon' => '🚚', 'label' => 'Manunuzi', 'short' => 'MN'],
        ['route' => 'wateja.index', 'icon' => '👥', 'label' => 'Wateja', 'short' => 'WT'],
         ['route' => 'mengineyo.index', 'icon' => '💰', 'label' => 'Mengineyo', 'short' => 'ME'], 
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
?>



<div class="flex flex-col h-full">
    
    <?php if((Auth::check() && Auth::user()?->role === 'boss') || 
         (Auth::guard('mfanyakazi')->check() && Auth::guard('mfanyakazi')->user()->uwezo === 'mkubwa')): ?>
        <div class="mb-1">
            <a href="<?php echo e(route('dashboard')); ?>"
               class="sidebar-item flex items-center px-3 py-3 sm:px-4 sm:py-3 rounded-lg transition-all duration-200
                      <?php echo e($currentRoute === 'dashboard' ? 'active-nav-item' : 'hover:bg-green-700/20'); ?>"
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
        
        
        <div class="border-t border-white/20 my-2"></div>
    <?php endif; ?>

    
    <div class="flex-1 space-y-1">
        <?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route($item['route'])); ?>"
               class="sidebar-item flex items-center px-3 py-3 sm:px-4 sm:py-3 rounded-lg transition-all duration-200
                      <?php echo e($currentRoute === $item['route'] ? 'active-nav-item' : 'hover:bg-green-700/20'); ?>"
               @click="window.innerWidth < 1024 ? closeSidebar() : null">
                
                <span class="text-base sm:text-lg md:text-xl flex-shrink-0"><?php echo e($item['icon']); ?></span>
                
                
                <span x-show="sidebarOpen || window.innerWidth >= 1024" 
                      class="ml-3 sm:ml-4 font-medium text-sm sm:text-base truncate">
                    <?php echo e($item['label']); ?>

                </span>
                
                
                <span x-show="!(sidebarOpen || window.innerWidth >= 1024)" 
                      x-cloak
                      class="ml-2 text-xs font-medium truncate">
                    <?php echo e($item['short'] ?? $item['label']); ?>

                </span>
                
                
                <?php if($currentRoute === $item['route']): ?>
                    <span class="ml-auto w-1.5 h-1.5 sm:w-2 sm:h-2 bg-white rounded-full"></span>
                <?php endif; ?>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="mt-auto pt-3 border-t border-white/20 lg:hidden">
        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
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


<?php $__env->startPush('styles'); ?>
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
<?php $__env->stopPush(); ?><?php /**PATH C:\xampp\htdocs\mauzo-project\resources\views/partials/navigation.blade.php ENDPATH**/ ?>