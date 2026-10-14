<?php
    $currentLevel = isset($level) ? $level : 1;
    $hasChildren = !empty($node['children']) && count($node['children']) > 0;
    // Show top 2 rows: Level 1 children (Level 2) are shown, Level 2 children (Level 3+) are collapsed by default
    $isCollapsed = $currentLevel >= 2;
?>

<li>
    <!-- Employee Card Box -->
    <div class="node-box" data-id="<?php echo e($node['id']); ?>" data-name="<?php echo e($node['employee_name']); ?>"
        data-nik="<?php echo e($node['NIK']); ?>" data-dept="<?php echo e($node['dept_code'] ?? '-'); ?>"
        data-position="<?php echo e($node['position_name'] ?? '-'); ?>">

        <!-- Portrait Rectangle Avatar Icon -->
        <!-- <div class="node-avatar-box">
            <i class="fa fa-user"></i>
        </div> -->

        <!-- Employee Name -->
        <div class="node-name" title="<?php echo e($node['employee_name']); ?>">
            <?php echo e($node['employee_name']); ?>

        </div>

        <!-- NIK & Dept Code -->
        <div class="node-badges">
            <span class="label label-default" style="font-size: 10px;"><?php echo e($node['NIK']); ?></span>
            <?php if(!empty($node['dept_code'])): ?>
                <span class="node-badge-dept"><?php echo e($node['dept_code']); ?></span>
            <?php endif; ?>
        </div>

        <!-- Position Name -->
        <div class="node-position" title="<?php echo e($node['position_name'] ?? '-'); ?>">
            <?php echo e($node['position_name'] ?? '-'); ?>

        </div>

        <!-- Subordinate Counts -->
        <div class="sub-counts">
            <div class="count-item" title="Jumlah bawahan langsung">
                <span class="count-number badge-direct"><?php echo e($node['direct_count']); ?></span>
                <span class="count-label">Direct</span>
            </div>
            <div class="count-item" title="Jumlah seluruh bawahan di bawah struktur">
                <span class="count-number badge-total"><?php echo e($node['total_count']); ?></span>
                <span class="count-label">Total Sub</span>
            </div>
        </div>
    </div>

    <!-- Toggle Button Wrapper with Connector Line -->
    <?php if($hasChildren): ?>
        <div class="btn-toggle-wrapper">
            <button type="button" class="btn btn-default btn-xs tree-toggle-btn" title="Klik untuk Buka/Tutup Bawahan">
                <i class="fa <?php echo e($isCollapsed ? 'fa-plus' : 'fa-minus'); ?>"></i> <?php echo e(count($node['children'])); ?> Bawahan
            </button>
        </div>

        <!-- Recursive Children -->
        <ul class="<?php echo e($isCollapsed ? 'hidden-branch' : ''); ?>">
            <?php $__currentLoopData = $node['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $childNode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('page.admin.structure.node', ['node' => $childNode, 'level' => $currentLevel + 1], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    <?php endif; ?>
</li><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/structure/node.blade.php ENDPATH**/ ?>