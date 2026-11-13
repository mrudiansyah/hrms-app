

<?php $__env->startSection('content'); ?>
<div class="container">
    <h1 class="h3 mb-3">PIN Akses Kandidat</h1>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="alert alert-danger"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('candidate.pins.generate')); ?>" class="mb-4">
        <?php echo csrf_field(); ?>
        <div class="form-check mb-2">
            <input type="checkbox" class="form-check-input" id="skip_email_verification" name="skip_email_verification" value="1">
            <label class="form-check-label" for="skip_email_verification">
                Lewati verifikasi email untuk PIN ini
            </label>
            <small class="form-text text-muted">Gunakan hanya jika kandidat memang diizinkan melanjutkan tanpa verifikasi email.</small>
        </div>
        <button type="submit" class="btn btn-primary">Generate PIN 4 Angka</button>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>PIN</th>
                    <th>Dibuat oleh</th>
                    <th>Dibuat pada</th>
                    <th>Kedaluwarsa</th>
                    <th>Skip verifikasi email</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $pins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php ($active = \Illuminate\Support\Carbon::parse($pin->expired_at)->isFuture()); ?>
                    <tr>
                        <td><?php echo e($pin->id); ?></td>
                        <td><strong><?php echo e($pin->pin_access); ?></strong></td>
                        <td><?php echo e($pin->created_by); ?></td>
                        <td><?php echo e($pin->created_at); ?></td>
                        <td><?php echo e($pin->expired_at); ?></td>
                        <td><?php echo e($pin->skip_email_verification ? 'Ya' : 'Tidak'); ?></td>
                        <td><?php echo e($active ? 'Aktif' : 'Kedaluwarsa'); ?></td>
                        <td>
                            <?php if($active): ?>
                                <form method="POST" action="<?php echo e(route('candidate.pins.expire', $pin->id)); ?>" onsubmit="return confirm('Nonaktifkan PIN ini?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger">Nonaktifkan</button>
                                </form>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="text-center">Belum ada PIN.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/candidate/pins.blade.php ENDPATH**/ ?>