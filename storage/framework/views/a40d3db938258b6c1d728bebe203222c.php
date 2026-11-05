

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
                                <th>Department</th>
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
                                        <?php if($canManageTeams): ?>
                                            <?php if($canManageMembers): ?>
                                                <form action="<?php echo e(url('/qcc_team_periode/toggle')); ?>" method="POST">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="id_qcc_periode" value="<?php echo e($selectedPeriod->id); ?>">
                                                    <input type="hidden" name="id_qcc_team" value="<?php echo e($team->id); ?>">
                                                    <input type="hidden" name="registered" value="<?php echo e($teamPeriod ? 0 : 1); ?>">
                                                    <input type="checkbox" aria-label="Daftarkan <?php echo e($team->group_name); ?> pada periode ini"
                                                        <?php if($teamPeriod): echo 'checked'; endif; ?>
                                                        onchange="if (!this.checked && !confirm('Hapus team beserta semua anggotanya dari periode ini?')) { this.checked = true; return; } this.form.elements.registered.value = this.checked ? 1 : 0; this.form.submit();">
                                                </form>
                                            <?php else: ?>
                                                <input type="checkbox" disabled <?php if($teamPeriod): echo 'checked'; endif; ?> aria-label="<?php echo e($teamPeriod ? 'Terdaftar' : 'Belum terdaftar'); ?>">
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <i class="fa <?php echo e($teamPeriod ? 'fa-check text-success' : 'fa-minus text-muted'); ?>"
                                                aria-label="<?php echo e($teamPeriod ? 'Terdaftar' : 'Belum terdaftar'); ?>"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($team->group_name); ?></td>
                                    <td><?php echo e($team->department ?: '-'); ?></td>
                                    <td><?php echo e($team->registered_at ? \Illuminate\Support\Carbon::parse($team->registered_at)->format('d/m/Y') : '-'); ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-default btn-xs" type="button" data-toggle="modal" data-target="#<?php echo e($detailsId); ?>">
                                            <i class="fa fa-users"></i> Lihat Anggota
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="5" class="text-center text-muted">Belum ada team QCC.</td></tr>
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
                            <div class="modal-body" style="min-height: 300px;">
                                <?php if(!$teamPeriod): ?>
                                    <p class="text-muted">Centang team terlebih dahulu untuk mendaftarkan team pada periode ini dan mengelola anggotanya.</p>
                                <?php else: ?>
                                    <?php if(!$canManageMembers): ?>
                                        <div class="alert alert-info">
                                            Periode terkunci. Hanya committee yang dapat menambah atau menghapus anggota.
                                        </div>
                                    <?php endif; ?>
                                    <div class="qcc-member-feedback alert" role="status" aria-live="polite" style="display:none;"></div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Karyawan</th>
                                                    <th>Posisi Team</th>
                                                    <?php if($canManageMembers): ?>
                                                        <th style="width:145px;">Aksi</th>
                                                    <?php endif; ?>
                                                </tr>
                                            </thead>
                                            <tbody class="qcc-member-list">
                                                <?php $__currentLoopData = $members->get($teamPeriod->id, collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e($member->NIK); ?> - <?php echo e($member->employee_name ?? 'Karyawan tidak ditemukan'); ?></td>
                                                        <td><?php echo e($member->position_name ?? '-'); ?></td>
                                                        <?php if($canManageMembers): ?>
                                                            <td>
                                                                <form action="<?php echo e(url('/qcc_team_member/'.$member->id)); ?>" method="POST" style="display:inline;">
                                                                    <?php echo csrf_field(); ?>
                                                                    <?php echo method_field('DELETE'); ?>
                                                                    <button class="btn btn-danger btn-xs" type="submit" onclick="return confirm('Hapus anggota ini?');"><i class="fa fa-trash"></i> Hapus</button>
                                                                </form>
                                                            </td>
                                                        <?php endif; ?>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($canManageMembers): ?>
                                                    <?php ($addMemberFormId = 'add-member-' . $teamPeriod->id); ?>
                                                    <tr>
                                                        <td>
                                                            <form id="<?php echo e($addMemberFormId); ?>" class="qcc-add-member-form" action="<?php echo e(url('/qcc_team_periode/'.$teamPeriod->id.'/member')); ?>" method="POST">
                                                                <?php echo csrf_field(); ?>
                                                            </form>
                                                            <select class="form-control selectpicker qcc-employee-select" name="id_employee" form="<?php echo e($addMemberFormId); ?>" aria-label="Pilih karyawan" data-live-search="true" data-width="100%" data-container="#<?php echo e($detailsId); ?>" data-size="10" required>
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
                                                <?php endif; ?>
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

<?php $__env->startSection('Scripts'); ?>
<script>
    (function ($) {
        $('.qcc-employee-select').selectpicker();

        document.querySelectorAll('.qcc-add-member-form').forEach(function (form) {
            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                var modal = form.closest('.modal');
                var feedback = modal.querySelector('.qcc-member-feedback');
                var employeeSelect = modal.querySelector('select[name="id_employee"]');
                var positionSelect = modal.querySelector('select[name="id_position_team"]');
                var submitButton = modal.querySelector('button[form="' + form.id + '"]');

                if (form.dataset.submitting === 'true') {
                    return;
                }

                form.dataset.submitting = 'true';
                submitButton.disabled = true;
                feedback.style.display = 'none';

                try {
                    var response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new FormData(form)
                    });
                    var result = {};

                    try {
                        result = await response.json();
                    } catch (error) {
                        result = {};
                    }

                    if (!response.ok) {
                        var validationMessages = result.errors
                            ? Object.values(result.errors).flat().join(' ')
                            : '';
                        throw new Error(validationMessages || result.message || 'Anggota gagal ditambahkan.');
                    }

                    var row = document.createElement('tr');
                    var employeeCell = document.createElement('td');
                    var positionCell = document.createElement('td');
                    var actionCell = document.createElement('td');
                    var employeeName = result.member.employee_name || 'Karyawan tidak ditemukan';
                    employeeCell.textContent = (result.member.NIK || '') + ' - ' + employeeName;
                    positionCell.textContent = result.member.position_name || '-';

                    var deleteForm = document.createElement('form');
                    deleteForm.action = '<?php echo e(url('/qcc_team_member')); ?>/' + encodeURIComponent(result.member.id);
                    deleteForm.method = 'POST';
                    deleteForm.style.display = 'inline';

                    var csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = form.querySelector('input[name="_token"]').value;
                    deleteForm.appendChild(csrfToken);

                    var method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'DELETE';
                    deleteForm.appendChild(method);

                    var deleteButton = document.createElement('button');
                    deleteButton.className = 'btn btn-danger btn-xs';
                    deleteButton.type = 'submit';
                    deleteButton.innerHTML = '<i class="fa fa-trash"></i> Hapus';
                    deleteButton.addEventListener('click', function (clickEvent) {
                        if (!confirm('Hapus anggota ini?')) {
                            clickEvent.preventDefault();
                        }
                    });
                    deleteForm.appendChild(deleteButton);
                    actionCell.appendChild(deleteForm);
                    row.appendChild(employeeCell);
                    row.appendChild(positionCell);
                    row.appendChild(actionCell);

                    var memberList = modal.querySelector('.qcc-member-list');
                    memberList.insertBefore(row, memberList.lastElementChild);
                    employeeSelect.value = '';
                    positionSelect.value = '';
                    $(employeeSelect).selectpicker('refresh');
                    feedback.className = 'qcc-member-feedback alert alert-success';
                    feedback.textContent = result.message;
                    feedback.style.display = 'block';
                } catch (error) {
                    feedback.className = 'qcc-member-feedback alert alert-danger';
                    feedback.textContent = error.message || 'Anggota gagal ditambahkan.';
                    feedback.style.display = 'block';
                } finally {
                    form.dataset.submitting = 'false';
                    submitButton.disabled = false;
                }
            });
        });
    })(jQuery_3_2_1);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/qcc/teams_period.blade.php ENDPATH**/ ?>