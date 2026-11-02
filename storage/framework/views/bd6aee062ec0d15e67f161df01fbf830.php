<div class="qcc-navigation">
    <a href="<?php echo e(url('/qcc_step')); ?>" class="btn btn-sm <?php echo e(request()->is('qcc_step*') ? 'btn-primary' : 'btn-default'); ?>">
        <i class="fa fa-list-ol"></i> Master Steps
    </a>
    <a href="<?php echo e(url('/qcc_perriode')); ?>" class="btn btn-sm <?php echo e(request()->is('qcc_perriode*') ? 'btn-primary' : 'btn-default'); ?>">
        <i class="fa fa-calendar"></i> QCC Periode
    </a>
    <a href="<?php echo e(url('/qcc_schedule')); ?>" class="btn btn-sm <?php echo e(request()->is('qcc_schedule*') ? 'btn-primary' : 'btn-default'); ?>">
        <i class="fa fa-calendar-check-o"></i> Schedule
    </a>
</div>
<?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/qcc/_navigation.blade.php ENDPATH**/ ?>