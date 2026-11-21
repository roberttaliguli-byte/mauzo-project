<?php if($madeni->count() > 0): ?>
<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-emerald-50">
                <th class="px-4 py-2 text-left font-medium text-emerald-800">Tarehe</th>
                <th class="px-4 py-2 text-left font-medium text-emerald-800">Mkopaji</th>
                <th class="px-4 py-2 text-left font-medium text-emerald-800 hidden md:table-cell">Bidhaa</th>
                <th class="px-4 py-2 text-left font-medium text-emerald-800 hidden md:table-cell">Aina</th>
                <th class="px-4 py-2 text-left font-medium text-emerald-800 hidden md:table-cell">Kipimo</th>
                <th class="px-4 py-2 text-center font-medium text-emerald-800">Idadi</th>
                <th class="px-4 py-2 text-right font-medium text-emerald-800">Deni</th>
                <th class="px-4 py-2 text-right font-medium text-emerald-800">Baki</th>
                <th class="px-4 py-2 text-center font-medium text-emerald-800 print:hidden">Vitendo</th>
            </td>
        </thead>
        <tbody id="debts-tbody" class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $madeni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deni): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="debt-row hover:bg-gray-50" data-debt='<?php echo json_encode($deni, 15, 512) ?>'>
                    <td class="px-4 py-2">
                        <div class="text-sm text-gray-900"><?php echo e(\Carbon\Carbon::parse($deni->created_at)->format('d/m/Y')); ?></div>
                        <div class="text-xs text-gray-500">Kutakiwa: <?php echo e(\Carbon\Carbon::parse($deni->tarehe_malipo)->format('d/m/Y')); ?></div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="font-medium text-gray-900 text-sm"><?php echo e($deni->jina_mkopaji); ?></div>
                        <?php if($deni->simu): ?>
                        <div class="text-xs text-emerald-600"><?php echo e($deni->simu); ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-2 hidden md:table-cell">
                        <span class="text-sm text-gray-700"><?php echo e($deni->bidhaa->jina ?? 'N/A'); ?></span>
                        <?php if($deni->punguzo > 0): ?>
                        <div class="text-xs text-gray-500">Punguzo: <?php echo e(number_format($deni->punguzo, 2)); ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-2 hidden md:table-cell">
                        <span class="text-sm text-gray-600"><?php echo e($deni->bidhaa->aina ?? 'N/A'); ?></span>
                    </td>
                    <td class="px-4 py-2 hidden md:table-cell">
                        <span class="text-sm text-gray-600"><?php echo e($deni->bidhaa->kipimo ?? 'N/A'); ?></span>
                    </td>
                    <td class="px-4 py-2 text-center">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                            <?php echo e($deni->idadi); ?>

                        </span>
                    </td>
                    <td class="px-4 py-2 text-right">
                        <div class="text-sm font-bold text-gray-900"><?php echo e(number_format($deni->jumla, 2)); ?></div>
                    </td>
                    <td class="px-4 py-2 text-right">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold 
                            <?php if($deni->baki <= 0): ?> bg-green-100 text-green-800
                            <?php else: ?> bg-red-100 text-red-800 <?php endif; ?>">
                            <?php echo e(number_format($deni->baki, 2)); ?>

                            <?php if($deni->baki <= 0): ?>
                            <i class="fas fa-check ml-1 text-xs"></i>
                            <?php endif; ?>
                        </span>
                    </td>
                    <td class="px-4 py-2 text-center print:hidden">
                        <div class="flex justify-center space-x-2">
                            <?php if($deni->baki > 0): ?>
                            <button class="pay-debt-btn text-green-600 hover:text-green-800 p-1 rounded-full hover:bg-green-50"
                                    data-id="<?php echo e($deni->id); ?>" title="Lipa Deni">
                                <i class="fas fa-money-bill-wave"></i>
                            </button>
                            <?php endif; ?>
                            
                            
                            <?php if($hasFullAccess): ?>
                                <button class="edit-debt-btn text-amber-600 hover:text-amber-800 p-1 rounded-full hover:bg-amber-50"
                                        data-id="<?php echo e($deni->id); ?>" title="Badili Deni">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="delete-debt-btn text-red-600 hover:text-red-800 p-1 rounded-full hover:bg-red-50"
                                        data-id="<?php echo e($deni->id); ?>" data-name="<?php echo e($deni->jina_mkopaji); ?>" title="Futa Deni">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php else: ?>
                                
                                <button class="text-gray-300 p-1 rounded-full cursor-not-allowed" disabled title="Huna ruhusa ya kubadilisha">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-gray-300 p-1 rounded-full cursor-not-allowed" disabled title="Huna ruhusa ya kufuta">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-hand-holding-usd text-3xl mb-2 text-gray-300"></i>
                        <p>Hakuna madeni yaliyorekodiwa bado</p>
                        <p class="text-xs text-gray-500 mt-1">Anza kwa kuingiza deni jipya</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if($madeni->hasPages()): ?>
<div class="px-4 py-3 border-t border-gray-200">
    <?php echo e($madeni->links()); ?>

</div>
<?php endif; ?>
<?php else: ?>
<div class="p-8 text-center text-gray-500">
    <i class="fas fa-hand-holding-usd text-3xl mb-2 text-gray-300"></i>
    <p>Hakuna madeni yaliyorekodiwa bado</p>
    <p class="text-xs text-gray-500 mt-1">Anza kwa kuingiza deni jipya</p>
</div>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\mauzo-project\resources\views/madeni/partials/debts-table.blade.php ENDPATH**/ ?>