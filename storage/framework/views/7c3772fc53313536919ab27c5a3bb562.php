<div class="qcc-navigation">
    <a href="<?php echo e(url('/qcc_team')); ?>" class="btn btn-sm <?php echo e(request()->is('qcc_team') ? 'btn-primary' : 'btn-default'); ?>">
        <i class="fa fa-users"></i> Master Team
    </a>
    <a href="<?php echo e(url('/qcc_team/periode')); ?>" class="btn btn-sm <?php echo e(request()->is('qcc_team/periode') ? 'btn-primary' : 'btn-default'); ?>">
        <i class="fa fa-calendar"></i> Periode Team
    </a>
</div>
<?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/qcc/_team_navigation.blade.php ENDPATH**/ ?>