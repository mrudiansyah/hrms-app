

<?php $__env->startSection('Contents'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC Management <small>Master Steps</small></h1>
    </section>
    <section class="content">
        <?php echo $__env->make('qcc._navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div style="height:15px;"></div>
        <?php echo $__env->make('qcc._messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Daftar Step Improvement</h3>
                <p class="text-muted">Urutan 1–8 menentukan durasi default 2, 1, 3, 3, 6, 3, 2, dan 1 minggu.</p>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr class="bg-gray">
                            <th style="width:100px;">Urutan</th>
                            <th>Deskripsi Step</th>
                            <th style="width:180px;">Diperbarui</th>
                            <th style="width:180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php ($formId = 'update-step-' . $step->id); ?>
                            <tr>
                                <td>
                                    <select class="form-control" name="step_sequance" form="<?php echo e($formId); ?>" required>
                                        <?php $__currentLoopData = range(1, 8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sequence): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($sequence); ?>" <?php echo e((int) $step->step_sequance === $sequence ? 'selected' : ''); ?>>
                                                Step <?php echo e($sequence); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control" name="step_description" form="<?php echo e($formId); ?>"
                                        value="<?php echo e($step->step_description); ?>" maxlength="255" required>
                                </td>
                                <td><?php echo e($step->updated_by ?? '-'); ?></td>
                                <td>
                                    <form id="<?php echo e($formId); ?>" action="<?php echo e(url('/qcc_step/' . $step->id)); ?>" method="POST" style="display:inline;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <button class="btn btn-primary btn-sm" type="submit" title="Simpan">
                                            <i class="fa fa-save"></i> Simpan
                                        </button>
                                    </form>
                                    <form action="<?php echo e(url('/qcc_step/' . $step->id)); ?>" method="POST" style="display:inline;"
                                        onsubmit="return confirm('Hapus master step ini?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-danger btn-sm" type="submit" title="Hapus">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php if($steps->count() < 8): ?>
                            <tr>
                                <?php ($createFormId = 'create-qcc-step'); ?>
                                <td>
                                    <select class="form-control" name="step_sequance" form="<?php echo e($createFormId); ?>" required>
                                        <option value="">Pilih urutan</option>
                                        <?php $__currentLoopData = range(1, 8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sequence): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(!$steps->contains(fn ($step) => (int) $step->step_sequance === $sequence)): ?>
                                                <option value="<?php echo e($sequence); ?>" <?php echo e(old('step_sequance') == $sequence ? 'selected' : ''); ?>>
                                                    Step <?php echo e($sequence); ?>

                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control" name="step_description" form="<?php echo e($createFormId); ?>" maxlength="255"
                                        value="<?php echo e(old('step_description')); ?>" placeholder="Nama step improvement" required>
                                </td>
                                <td>-</td>
                                <td>
                                    <form id="<?php echo e($createFormId); ?>" action="<?php echo e(url('/qcc_step')); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-success btn-sm" type="submit">
                                            <i class="fa fa-plus"></i> Tambah
                                        </button>
                                    </form>
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

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/qcc/steps.blade.php ENDPATH**/ ?>