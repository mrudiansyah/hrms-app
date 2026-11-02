

<?php $__env->startSection('Contents'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Master Team</small></h1>
    </section>
    <section class="content">
        <?php echo $__env->make('qcc._team_navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div style="height:15px;"></div>
        <?php echo $__env->make('qcc._messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-users text-blue"></i> Daftar Team</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add-team-modal">
                        <i class="fa fa-plus"></i> Tambah Team
                    </button>
                </div>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr class="bg-gray">
                            <th style="width:75px;">ID</th>
                            <th>Nama Team</th>
                            <th style="width:190px;">Tanggal Registrasi</th>
                            <th style="width:150px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $teams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php ($modalId = 'edit-team-' . $team->id); ?>
                            <tr>
                                <td><span class="text-muted">#<?php echo e($team->id); ?></span></td>
                                <td><strong><?php echo e($team->group_name); ?></strong></td>
                                <td>
                                    <i class="fa fa-calendar text-muted"></i>
                                    <?php echo e($team->registered_at ? \Illuminate\Support\Carbon::parse($team->registered_at)->format('d/m/Y') : '-'); ?>

                                </td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-xs" type="button" data-toggle="modal" data-target="#<?php echo e($modalId); ?>" title="Edit team">
                                        <i class="fa fa-pencil"></i> Edit
                                    </button>
                                    <form action="<?php echo e(url('/qcc_team/'.$team->id)); ?>" method="POST" style="display:inline;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-danger btn-xs" type="submit" onclick="return confirm('Hapus team ini? Team yang terdaftar pada periode tidak dapat dihapus.');" title="Hapus team">
                                            <i class="fa fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted" style="padding:30px;">
                                    <i class="fa fa-users fa-2x"></i>
                                    <p style="margin:10px 0 0;">Belum ada team QCC. Klik <strong>Tambah Team</strong> untuk memulai.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($teams->isNotEmpty()): ?>
                <div class="box-footer text-muted">
                    <i class="fa fa-info-circle"></i> Total <?php echo e($teams->count()); ?> team terdaftar.
                </div>
            <?php endif; ?>
        </div>

        <div class="modal fade" id="add-team-modal" tabindex="-1" role="dialog" aria-labelledby="add-team-modal-title">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="<?php echo e(url('/qcc_team')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="form_context" value="add-team-modal">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="add-team-modal-title"><i class="fa fa-users"></i> Tambah Team</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="add_group_name">Nama Team</label>
                                <input class="form-control" id="add_group_name" name="group_name"
                                    value="<?php echo e(old('form_context') === 'add-team-modal' ? old('group_name') : ''); ?>"
                                    placeholder="Contoh: Productivity Up" required maxlength="255">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label for="add_registered_at">Tanggal Registrasi</label>
                                <input class="form-control" type="date" id="add_registered_at" name="registered_at"
                                    value="<?php echo e(old('form_context') === 'add-team-modal' ? old('registered_at') : now()->toDateString()); ?>" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                            <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Simpan Team</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php $__currentLoopData = $teams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php ($modalId = 'edit-team-' . $team->id); ?>
            <?php ($editingThisTeam = old('form_context') === $modalId); ?>
            <div class="modal fade" id="<?php echo e($modalId); ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo e($modalId); ?>-title">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form action="<?php echo e(url('/qcc_team/'.$team->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="form_context" value="<?php echo e($modalId); ?>">
                            <div class="modal-header bg-yellow">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="<?php echo e($modalId); ?>-title"><i class="fa fa-pencil"></i> Edit Team</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="group_name_<?php echo e($team->id); ?>">Nama Team</label>
                                    <input class="form-control" id="group_name_<?php echo e($team->id); ?>" name="group_name"
                                        value="<?php echo e($editingThisTeam ? old('group_name') : $team->group_name); ?>" required maxlength="255">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label for="registered_at_<?php echo e($team->id); ?>">Tanggal Registrasi</label>
                                    <input class="form-control" type="date" id="registered_at_<?php echo e($team->id); ?>" name="registered_at"
                                        value="<?php echo e($editingThisTeam ? old('registered_at') : ($team->registered_at ? \Illuminate\Support\Carbon::parse($team->registered_at)->format('Y-m-d') : '')); ?>" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                <button class="btn btn-warning" type="submit"><i class="fa fa-save"></i> Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('Scripts'); ?>
    <?php if($errors->any()): ?>
        <script>
            $(function () {
                var formContext = <?php echo json_encode(old('form_context'), 15, 512) ?>;
                var modal = formContext ? document.getElementById(formContext) : null;
                if (modal) {
                    $(modal).modal('show');
                }
            });
        </script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/qcc/teams_master.blade.php ENDPATH**/ ?>