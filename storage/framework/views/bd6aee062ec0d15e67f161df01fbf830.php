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
    <?php if(request()->user()->hasRole('improvement')): ?>
        <a href="<?php echo e(url('/qcc_assesment')); ?>" class="btn btn-sm <?php echo e(request()->is('qcc_assesment') ? 'btn-primary' : 'btn-default'); ?>">
            <i class="fa fa-list"></i> Assessment
        </a>
    <?php endif; ?>
    <?php if(request()->user()->hasRole('committee')): ?>
        <a href="<?php echo e(url('/qcc_assesor')); ?>" class="btn btn-sm <?php echo e(request()->is('qcc_assesor*') ? 'btn-primary' : 'btn-default'); ?>">
            <i class="fa fa-users"></i> Assessor
        </a>
        <a href="<?php echo e(url('/qcc_assesment_team')); ?>" class="btn btn-sm <?php echo e(request()->is('qcc_assesment_team*') ? 'btn-primary' : 'btn-default'); ?>">
            <i class="fa fa-check-square-o"></i> Penilaian
        </a>
        <a href="<?php echo e(url('/qcc_assesment_summary')); ?>" class="btn btn-sm <?php echo e(request()->is('qcc_assesment_summary*') ? 'btn-primary' : 'btn-default'); ?>">
            <i class="fa fa-bar-chart"></i> Summary Penilaian
        </a>
    <?php endif; ?>
</div>
<?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/qcc/_navigation.blade.php ENDPATH**/ ?>