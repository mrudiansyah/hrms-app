

<?php $__env->startSection('Contents'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Periode Team</small></h1>
    </section>
    <section class="content">
        <?php echo $__env->make('qcc._team_navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div style="height:15px;"></div>
        <?php echo $__env->make('qcc._messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Pilih Periode QCC</h3>
            </div>
            <div class="box-body">
                <form action="<?php echo e(url('/qcc_team/periode')); ?>" method="GET" class="form-inline">
                    <div class="form-group">
                        <label for="periode_id">Periode</label>
                        <select class="form-control" id="periode_id" name="periode_id" required>
                            <option value="">Pilih periode QCC</option>
                            <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $period): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($period->id); ?>" <?php if($selectedPeriod && (int) $selectedPeriod->id === (int) $period->id): echo 'selected'; endif; ?>>
                                    <?php echo e($period->sugested_thema); ?>

                                    (<?php echo e(\Illuminate\Support\Carbon::parse($period->periode_start)->format('d/m/Y')); ?> - <?php echo e(\Illuminate\Support\Carbon::parse($period->periode_finish)->format('d/m/Y')); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Tampilkan</button>
                </form>
            </div>
        </div>

        <?php if($selectedPeriod): ?>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Daftar Team untuk: <?php echo e($selectedPeriod->sugested_thema); ?></h3>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="bg-gray">
                                <th style="width:70px;" class="text-center">Terdaftar</th>
                                <th>Nama Team</th>
                                <th style="width:180px;">Tanggal Registrasi</th>
                                <th style="width:120px;" class="text-center">Anggota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $teams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php ($teamPeriod = $teamPeriods->get($team->id)); ?>
                                <?php ($detailsId = 'team-members-' . $team->id); ?>
                                <tr>
                                    <td class="text-center">
                                        <form action="<?php echo e(url('/qcc_team_periode/toggle')); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="id_qcc_periode" value="<?php echo e($selectedPeriod->id); ?>">
                                            <input type="hidden" name="id_qcc_team" value="<?php echo e($team->id); ?>">
                                            <input type="hidden" name="registered" value="<?php echo e($teamPeriod ? 0 : 1); ?>">
                                            <input type="checkbox" aria-label="Daftarkan <?php echo e($team->group_name); ?> pada periode ini"
                                                <?php if($teamPeriod): echo 'checked'; endif; ?>
                                                onchange="if (!this.checked && !confirm('Hapus team beserta semua anggotanya dari periode ini?')) { this.checked = true; return; } this.form.elements.registered.value = this.checked ? 1 : 0; this.form.submit();">
                                        </form>
                                    </td>
                                    <td><?php echo e($team->group_name); ?></td>
                                    <td><?php echo e($team->registered_at ? \Illuminate\Support\Carbon::parse($team->registered_at)->format('d/m/Y') : '-'); ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-default btn-xs" type="button" data-toggle="modal" data-target="#<?php echo e($detailsId); ?>">
                                            <i class="fa fa-users"></i> Lihat Anggota
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="4" class="text-center text-muted">Belum ada team QCC.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php $__currentLoopData = $teams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php ($teamPeriod = $teamPeriods->get($team->id)); ?>
                <?php ($detailsId = 'team-members-' . $team->id); ?>
                <div class="modal fade" id="<?php echo e($detailsId); ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo e($detailsId); ?>-title">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="<?php echo e($detailsId); ?>-title">
                                    Anggota Team <?php echo e($team->group_name); ?> - <?php echo e($selectedPeriod->sugested_thema); ?>

                                </h4>
                            </div>
                            <div class="modal-body">
                                <?php if(!$teamPeriod): ?>
                                    <p class="text-muted">Centang team terlebih dahulu untuk mendaftarkan team pada periode ini dan mengelola anggotanya.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Karyawan</th>
                                                    <th>Posisi Team</th>
                                                    <th style="width:145px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $members->get($teamPeriod->id, collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e($member->NIK); ?> - <?php echo e($member->employee_name ?? 'Karyawan tidak ditemukan'); ?></td>
                                                        <td><?php echo e($member->position_name ?? '-'); ?></td>
                                                        <td>
                                                            <form action="<?php echo e(url('/qcc_team_member/'.$member->id)); ?>" method="POST" style="display:inline;">
                                                                <?php echo csrf_field(); ?>
                                                                <?php echo method_field('DELETE'); ?>
                                                                <button class="btn btn-danger btn-xs" type="submit" onclick="return confirm('Hapus anggota ini?');"><i class="fa fa-trash"></i> Hapus</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php ($addMemberFormId = 'add-member-' . $teamPeriod->id); ?>
                                                <tr>
                                                    <td>
                                                        <form id="<?php echo e($addMemberFormId); ?>" action="<?php echo e(url('/qcc_team_periode/'.$teamPeriod->id.'/member')); ?>" method="POST">
                                                            <?php echo csrf_field(); ?>
                                                        </form>
                                                        <select class="form-control" name="id_employee" form="<?php echo e($addMemberFormId); ?>" aria-label="Pilih karyawan" required>
                                                            <option value="">Pilih karyawan</option>
                                                            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($employee->id); ?>"><?php echo e($employee->NIK); ?> - <?php echo e($employee->employee_name); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control" name="id_position_team" form="<?php echo e($addMemberFormId); ?>" aria-label="Pilih posisi team" required>
                                                            <option value="">Pilih posisi team</option>
                                                            <?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($position->id); ?>"><?php echo e($position->position_name); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-success btn-sm" type="submit" form="<?php echo e($addMemberFormId); ?>">
                                                            <i class="fa fa-plus"></i> Tambah
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/qcc/teams_period.blade.php ENDPATH**/ ?>