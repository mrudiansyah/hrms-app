

<?php $__env->startSection('Contents'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Activity</small></h1>
    </section>
    <section class="content">
        <?php echo $__env->make('qcc._navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div style="height:15px;"></div>
        <?php echo $__env->make('qcc._messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-tasks text-blue"></i> Team QCC</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr class="bg-gray">
                            <th>Team</th>
                            <th>Tema Periode</th>
                            <th style="width:220px;">Tanggal Periode</th>
                            <th style="width:90px;" class="text-center">Anggota</th>
                            <th style="width:150px;" class="text-center">Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $teamPeriods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teamPeriod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><strong><?php echo e($teamPeriod->group_name); ?></strong></td>
                                <td><?php echo e($teamPeriod->sugested_thema); ?></td>
                                <td>
                                    <?php echo e(\Illuminate\Support\Carbon::parse($teamPeriod->periode_start)->format('d/m/Y')); ?>

                                    – <?php echo e(\Illuminate\Support\Carbon::parse($teamPeriod->periode_finish)->format('d/m/Y')); ?>

                                </td>
                                <td class="text-center"><?php echo e($teamPeriod->member_count); ?></td>
                                <td class="text-center">
                                    <form action="<?php echo e(url('/qcc_activity/'.$teamPeriod->id.'/open')); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            <i class="fa fa-eye"></i> Show Activity
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted" style="padding:30px;">
                                    <i class="fa fa-tasks fa-2x"></i>
                                    <p style="margin:10px 0 0;">
                                        <?php echo e($isCommittee ? 'Belum ada team yang terdaftar pada periode QCC.' : 'Anda belum terdaftar sebagai anggota team QCC.'); ?>

                                    </p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/qcc/activities.blade.php ENDPATH**/ ?>