

<?php $__env->startSection('Contents'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Assessor</small></h1>
    </section>
    <section class="content">
        <?php echo $__env->make('qcc._navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div style="height:15px;"></div>
        <?php echo $__env->make('qcc._messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Pilih Periode dan Kategori</h3>
            </div>
            <div class="box-body">
                <form action="<?php echo e(url('/qcc_assesor')); ?>" method="GET" class="form-inline">
                    <div class="form-group">
                        <label for="assessor-period">Periode</label>
                        <select class="form-control" id="assessor-period" name="periode_id" required>
                            <option value="">Pilih periode</option>
                            <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $period): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($period->id); ?>" <?php if((int) $selectedPeriodId === (int) $period->id): echo 'selected'; endif; ?>>
                                    <?php echo e($period->sugested_thema); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin-left:8px;">
                        <label for="assessor-category">Kategori</label>
                        <select class="form-control" id="assessor-category" name="category_id" required>
                            <option value="">Pilih kategori</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($category->id); ?>" <?php if((int) $selectedCategoryId === (int) $category->id): echo 'selected'; endif; ?>>
                                    <?php echo e($category->asses_category); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Tampilkan</button>
                </form>
            </div>
        </div>

        <?php if($periods->isEmpty() || $categories->isEmpty()): ?>
            <div class="callout callout-warning">
                <p>Tambahkan periode QCC dan kategori assessment sebelum mengelola assessor.</p>
            </div>
        <?php elseif($selectedPeriodId && $selectedCategoryId): ?>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Assessor per Kategori</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-success btn-sm js-assessor-modal" type="button" data-toggle="modal"
                            data-target="#assessorModal" data-create="true">
                            <i class="fa fa-plus"></i> Tambah Assessor
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <p class="text-muted">
                        Periode: <?php echo e($periods->firstWhere('id', $selectedPeriodId)->sugested_thema); ?>

                        | Kategori: <?php echo e($categories->firstWhere('id', $selectedCategoryId)->asses_category); ?>

                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="bg-gray">
                                    <th style="width:60px;">ID</th>
                                    <th style="width:220px;">Nama Assessor</th>
                                    <th>Anggota Assessor</th>
                                    <th style="width:100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $assessors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assessor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php ($members = $membersByAssessor->get($assessor->id, collect())); ?>
                                    <?php ($availableEmployees = $availableEmployeesByAssessor->get($assessor->id, collect())); ?>
                                    <tr>
                                        <td><?php echo e($assessor->id); ?></td>
                                        <td><?php echo e($assessor->assesor_name); ?></td>
                                        <td>
                                            <?php if($members->isNotEmpty()): ?>
                                                <ul class="list-unstyled" style="margin-bottom:8px;">
                                                    <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <li style="margin-bottom:4px;">
                                                            <form action="<?php echo e(url('/qcc_assesor_member/' . $member->id)); ?>" method="POST"
                                                                style="display:inline;" onsubmit="return confirm('Hapus anggota assessor ini?')">
                                                                <?php echo csrf_field(); ?>
                                                                <?php echo method_field('DELETE'); ?>
                                                                <button class="btn btn-default btn-xs" type="submit" title="Hapus anggota">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                            <span>
                                                                <?php if($member->employee_name): ?>
                                                                    <?php echo e($member->employee_name); ?> (<?php echo e($member->NIK); ?>)
                                                                <?php else: ?>
                                                                    Karyawan tidak aktif/tidak ditemukan (#<?php echo e($member->id_employee); ?>)
                                                                <?php endif; ?>
                                                            </span>
                                                        </li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </ul>
                                            <?php else: ?>
                                                <span class="text-muted">Belum ada anggota.</span>
                                            <?php endif; ?>
                                            <?php if($availableEmployees->isNotEmpty()): ?>
                                                <span class="text-muted">Gunakan tombol Tambah Anggota untuk memilih karyawan.</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($availableEmployees->isNotEmpty()): ?>
                                                <button class="btn btn-success btn-xs" title="Tambah Anggota" type="button" data-toggle="modal"
                                                    data-target="#assessorMemberModal-<?php echo e($assessor->id); ?>">
                                                    <i class="fa fa-user-plus"></i>
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">Semua karyawan aktif sudah terdaftar.</span>
                                            <?php endif; ?>
                                            <button class="btn btn-warning btn-xs js-assessor-modal" type="button"
                                                data-toggle="modal" data-target="#assessorModal"
                                                data-id="<?php echo e($assessor->id); ?>"
                                                data-period-id="<?php echo e($assessor->id_qcc_periode); ?>"
                                                data-category-id="<?php echo e($assessor->id_asses_category); ?>"
                                                data-name="<?php echo e($assessor->assesor_name); ?>">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <form action="<?php echo e(url('/qcc_assesor/' . $assessor->id)); ?>" method="POST"
                                                style="display:inline;" onsubmit="return confirm('Hapus assessor? Assessor dengan anggota atau riwayat nilai tidak dapat dihapus.')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button class="btn btn-danger btn-xs" type="submit">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="4" class="text-center text-muted">Belum ada assessor pada periode dan kategori ini.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php $__currentLoopData = $assessors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assessor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php ($availableEmployees = $availableEmployeesByAssessor->get($assessor->id, collect())); ?>
                <?php if($availableEmployees->isNotEmpty()): ?>
                    <div class="modal fade assessor-member-modal" id="assessorMemberModal-<?php echo e($assessor->id); ?>"
                        tabindex="-1" role="dialog" aria-labelledby="assessorMemberModalTitle-<?php echo e($assessor->id); ?>">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="<?php echo e(url('/qcc_assesor/' . $assessor->id . '/member')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="_form_type" value="member">
                                    <input type="hidden" name="_assessor_id" value="<?php echo e($assessor->id); ?>">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h4 class="modal-title" id="assessorMemberModalTitle-<?php echo e($assessor->id); ?>">
                                            Tambah Anggota — <?php echo e($assessor->assesor_name); ?>

                                        </h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="assessor-employee-<?php echo e($assessor->id); ?>">Karyawan aktif</label>
                                            <select class="form-control js-searchable-employee"
                                                id="assessor-employee-<?php echo e($assessor->id); ?>"
                                                name="id_employee" style="width:100%;" required>
                                                <option value="">Cari nama atau NIK karyawan</option>
                                                <?php $__currentLoopData = $availableEmployees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($employee->id); ?>"
                                                        <?php if((string) old('_assessor_id') === (string) $assessor->id
                                                            && (string) old('id_employee') === (string) $employee->id): echo 'selected'; endif; ?>>
                                                        <?php echo e($employee->employee_name); ?> (<?php echo e($employee->NIK); ?>)
                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fa fa-plus"></i> Tambah
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </section>
</div>

<div class="modal fade" id="assessorModal" tabindex="-1" role="dialog" aria-labelledby="assessorModalTitle">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(url('/qcc_assesor')); ?>" data-create-url="<?php echo e(url('/qcc_assesor')); ?>" data-update-url="<?php echo e(url('/qcc_assesor')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" value="PUT" disabled>
                <input type="hidden" name="_form_type" value="assessor">
                <input type="hidden" name="_entity_id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="assessorModalTitle" data-entity="Assessor">Assessor</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="assessor-modal-period">Periode QCC</label>
                        <select class="form-control" id="assessor-modal-period" name="id_qcc_periode" required>
                            <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $period): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($period->id); ?>"><?php echo e($period->sugested_thema); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="assessor-modal-category">Kategori</label>
                        <select class="form-control" id="assessor-modal-category" name="id_asses_category" required>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($category->id); ?>"><?php echo e($category->asses_category); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="assessor-modal-name">Nama assessor / kelompok assessor</label>
                        <input class="form-control" id="assessor-modal-name" name="assesor_name"
                            value="<?php echo e(old('assesor_name')); ?>" maxlength="50" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('Scripts'); ?>
<script>
    $(function () {
        $('.js-assessor-modal').on('click', function () {
            var button = $(this);
            var form = $('#assessorModal form');
            var isEdit = Boolean(button.attr('data-id'));

            form[0].reset();
            form.attr('action', isEdit
                ? form.data('update-url') + '/' + button.attr('data-id')
                : form.data('create-url'));
            form.find('[name="_entity_id"]').val(isEdit ? button.attr('data-id') : '');
            form.find('[name="_method"]').prop('disabled', !isEdit);
            form.find('[name="id_qcc_periode"]').val(button.attr('data-period-id') || '<?php echo e($selectedPeriodId); ?>');
            form.find('[name="id_asses_category"]').val(button.attr('data-category-id') || '<?php echo e($selectedCategoryId); ?>');
            form.find('[name="assesor_name"]').val(button.attr('data-name') || '');
            $('#assessorModalTitle').text((isEdit ? 'Edit ' : 'Tambah ') + 'Assessor');
        });

        $('.assessor-member-modal').on('shown.bs.modal', function () {
            var modal = $(this);
            var select = modal.find('.js-searchable-employee');
            if (!select.hasClass('select2-hidden-accessible')) {
                select.select2({
                    dropdownParent: modal,
                    width: '100%',
                    placeholder: 'Cari nama atau NIK karyawan',
                    language: {
                        noResults: function () {
                            return 'Karyawan tidak ditemukan';
                        },
                        searching: function () {
                            return 'Mencari...';
                        }
                    }
                });
            }
        });

        <?php if($errors->any() && old('_form_type') === 'assessor'): ?>
            var form = $('#assessorModal form');
            var id = <?php echo json_encode(old('_entity_id'), 15, 512) ?>;
            if (id) {
                form.attr('action', form.data('update-url') + '/' + id);
                form.find('[name="_method"]').prop('disabled', false);
            }
            $('#assessorModal').modal('show');
        <?php elseif($errors->any() && old('_form_type') === 'member' && old('_assessor_id')): ?>
            $('#assessorMemberModal-<?php echo e(old('_assessor_id')); ?>').modal('show');
        <?php endif; ?>
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/qcc/assessors.blade.php ENDPATH**/ ?>