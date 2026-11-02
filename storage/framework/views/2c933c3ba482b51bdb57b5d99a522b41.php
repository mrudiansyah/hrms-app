

<?php $__env->startSection('Contents'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Periode</small></h1>
    </section>
    <section class="content">
        <?php echo $__env->make('qcc._navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div style="height:15px;"></div>
        <?php echo $__env->make('qcc._messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Tambah Periode QCC</h3>
            </div>
            <form action="<?php echo e(url('/qcc_perriode')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sugested_thema">Tema QCC</label>
                                <input class="form-control" id="sugested_thema" name="sugested_thema"
                                    value="<?php echo e(old('sugested_thema')); ?>" maxlength="255" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="periode_start">Periode Start</label>
                                <input class="form-control" type="date" id="periode_start" name="periode_start"
                                    value="<?php echo e(old('periode_start')); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="periode_finish">Periode Finish</label>
                                <input class="form-control" type="date" id="periode_finish" name="periode_finish"
                                    value="<?php echo e(old('periode_finish')); ?>" required>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted">
                        Saat periode dibuat, sistem membuat 8 schedule berurutan dengan durasi 2/1/3/3/6/3/2/1 minggu.
                        Jadwal yang melewati periode finish dibatasi ke tanggal finish; step sisanya ditempatkan pada tanggal itu.
                    </p>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-plus"></i> Buat Periode dan Schedule</button>
                    <a class="btn btn-default" href="<?php echo e(url('/qcc_step')); ?>">Atur Master Steps</a>
                </div>
            </form>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Daftar Periode</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr class="bg-gray">
                            <th>Tema</th>
                            <th style="width:130px;">Start</th>
                            <th style="width:130px;">Finish</th>
                            <th style="width:100px;">Schedule</th>
                            <th style="width:250px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $period): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php ($formId = 'update-period-' . $period->id); ?>
                            <tr>
                                <td>
                                    <input class="form-control" name="sugested_thema" form="<?php echo e($formId); ?>"
                                        value="<?php echo e($period->sugested_thema); ?>" maxlength="255" required>
                                </td>
                                <td>
                                    <input class="form-control" type="date" name="periode_start" form="<?php echo e($formId); ?>"
                                        value="<?php echo e(\Illuminate\Support\Carbon::parse($period->periode_start)->format('Y-m-d')); ?>" required>
                                </td>
                                <td>
                                    <input class="form-control" type="date" name="periode_finish" form="<?php echo e($formId); ?>"
                                        value="<?php echo e(\Illuminate\Support\Carbon::parse($period->periode_finish)->format('Y-m-d')); ?>" required>
                                </td>
                                <td><?php echo e($scheduleCounts[$period->id] ?? 0); ?> / 8</td>
                                <td>
                                    <form id="<?php echo e($formId); ?>" action="<?php echo e(url('/qcc_perriode/' . $period->id)); ?>" method="POST" style="display:inline;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <button class="btn btn-primary btn-sm" type="submit" title="Simpan perubahan">
                                            <i class="fa fa-save"></i>
                                        </button>
                                    </form>
                                    <a class="btn btn-info btn-sm"
                                        href="<?php echo e(url('/qcc_schedule?periode_id=' . $period->id)); ?>">
                                        <i class="fa fa-calendar"></i> Schedule
                                    </a>
                                    <form action="<?php echo e(url('/qcc_perriode/' . $period->id)); ?>" method="POST" style="display:inline;"
                                        onsubmit="return confirm('Hapus periode ini beserta semua schedule?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-danger btn-sm" type="submit" title="Hapus periode">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="5" class="text-center">Belum ada periode QCC.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/qcc/periods.blade.php ENDPATH**/ ?>