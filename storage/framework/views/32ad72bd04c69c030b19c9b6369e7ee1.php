

<?php $__env->startSection('Contents'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Konfirmasi Data Profil <small>Karyawan yang belum mengonfirmasi</small></h1>
    </section>

    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Filter</h3>
            </div>
            <form method="GET" action="<?php echo e(url('/ProfileConfirmations')); ?>">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="periode">Periode</label>
                            <select class="form-control" id="periode" name="periode">
                                <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $availablePeriod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($availablePeriod); ?>" <?php if((string) $availablePeriod === $period): echo 'selected'; endif; ?>>
                                        <?php echo e($availablePeriod); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label for="dept_code">Department</label>
                            <select class="form-control" id="dept_code" name="dept_code">
                                <option value="">Semua departemen</option>
                                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($department->dept_code); ?>" <?php if(($filters['dept_code'] ?? '') === (string) $department->dept_code): echo 'selected'; endif; ?>>
                                        <?php echo e($department->dept_code); ?> - <?php echo e($department->dept_name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label for="position_name">Position</label>
                            <select class="form-control" id="position_name" name="position_name">
                                <option value="">Semua posisi</option>
                                <?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($position); ?>" <?php if(($filters['position_name'] ?? '') === (string) $position): echo 'selected'; endif; ?>>
                                        <?php echo e($position); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <a href="<?php echo e(url('/ProfileConfirmations')); ?>" class="btn btn-default">Reset</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search"></i> Terapkan filter
                    </button>
                </div>
            </form>
        </div>

        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <?php echo e(number_format($pendingEmployees->total())); ?> karyawan belum konfirmasi periode <?php echo e($period); ?>

                </h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>NIK</th>
                            <th>Nama Karyawan</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>ID Employee</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $pendingEmployees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($pendingEmployees->firstItem() + $loop->index); ?></td>
                                <td><?php echo e($employee->NIK); ?></td>
                                <td><?php echo e($employee->employee_name); ?></td>
                                <td><?php echo e($employee->dept_code ?? '-'); ?><?php echo e($employee->dept_name ? ' - '.$employee->dept_name : ''); ?></td>
                                <td><?php echo e($employee->position_name ?? '-'); ?></td>
                                <td><?php echo e($employee->id); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center">
                                    Semua karyawan sudah mengonfirmasi untuk periode <?php echo e($period); ?>.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                <?php echo e($pendingEmployees->links('pagination::bootstrap-4')); ?>

            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/profile_refresh_data.blade.php ENDPATH**/ ?>