

<?php $__env->startSection('Contents'); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Functionality Test <small>KSK Approval Reminder</small></h1>
            <ol class="breadcrumb">
                <li><a href="/Dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Functionality Test</li>
            </ol>
        </section>

        <section class="content">
            <?php if(session('success')): ?>
                <div class="alert alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
            <?php endif; ?>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Daftar approval KSK yang menunggu</h3>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No. KSK</th>
                                <th>Periode</th>
                                <th>Deadline</th>
                                <th>Employee Name</th>
                                <th>Position Name</th>
                                <th>Nomor Telepon</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php ($employee = $employees->get($record->approver_id)); ?>
                                <tr>
                                    <td><?php echo e($index + 1); ?></td>
                                    <td><?php echo e($record->no_ksk); ?></td>
                                    <td><?php echo e($record->periode); ?></td>
                                    <td><?php echo e($record->dead_line); ?></td>
                                    <td><?php echo e($employee->employee_name ?? '-'); ?></td>
                                    <td><?php echo e($employee->position_name ?? '-'); ?></td>
                                    <td><?php echo e($employee->nomor_telepon ?? '-'); ?></td>
                                    <td>
                                        <?php if(!empty($employee->nomor_telepon)): ?>
                                            <form action="<?php echo e(url('/functionality-test/' . $record->id . '/send-reminder')); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-success btn-sm" title="Kirim reminder WhatsApp">
                                                    <i class="fa fa-send"></i> Kirim
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-default btn-sm" disabled>Nomor kosong</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada approval KSK yang menunggu pada periode belum terkunci.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\functionality_test\ksk_approval_reminders.blade.php ENDPATH**/ ?>