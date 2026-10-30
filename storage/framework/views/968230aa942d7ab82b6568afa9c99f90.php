
<?php $__env->startSection('Contents'); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Employee Status Renewal
                <small>Edit Pembaharuan Status Karyawan</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo e(route('renewal.index')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="<?php echo e(route('renewal.index')); ?>">Renewal</a></li>
                <li><a href="<?php echo e(route('renewal.show', $renewal->id)); ?>">Detail</a></li>
                <li class="active">Edit</li>
            </ol>
        </section>

        <section class="content">
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-ban"></i> Error!</h4>
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-ban"></i> Validation Error!</h4>
                    <ul>
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                <i class="fa fa-edit"></i> Edit Employee Status Renewal
                            </h3>
                            <div class="box-tools pull-right">
                                <a href="<?php echo e(route('renewal.show', $renewal->id)); ?>" class="btn btn-default btn-sm">
                                    <i class="fa fa-arrow-left"></i> Back to Detail
                                </a>
                            </div>
                        </div>
                        <form action="<?php echo e(route('renewal.update', $renewal->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <div class="box-body">
                                <!-- Data Karyawan (Readonly) -->
                                <div class="box box-default collapsed-box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-user"></i> Data Karyawan</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>NIK</label>
                                                    <input type="text" name="nik" class="form-control" 
                                                           value="<?php echo e(old('nik', $renewal->nik)); ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Nama Karyawan</label>
                                                    <input type="text" name="name" class="form-control" 
                                                           value="<?php echo e(old('name', $renewal->name)); ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Tanggal Bergabung</label>
                                                    <input type="date" name="date_of_join" class="form-control" 
                                                           value="<?php echo e(old('date_of_join', $renewal->date_of_join)); ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Masa Kerja</label>
                                                    <input type="text" name="work_period" class="form-control" 
                                                           value="<?php echo e(old('work_period', $renewal->work_period)); ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Posisi Saat Ini (Last Position)</label>
                                                    <input type="text" name="last_position" class="form-control" 
                                                           value="<?php echo e(old('last_position', $renewal->last_position)); ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Grade Saat Ini (Last Grade)</label>
                                                    <input type="text" name="last_grade" class="form-control" 
                                                           value="<?php echo e(old('last_grade', $renewal->last_grade)); ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Data Perubahan (Editable) -->
                                <div class="box box-success">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-exchange"></i> Edit Data Perubahan Status</h3>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Posisi Baru <span class="text-danger">*</span></label>
                                                    <input type="text" name="new_position" class="form-control" 
                                                           value="<?php echo e(old('new_position', $renewal->new_position)); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Grade Baru <span class="text-danger">*</span></label>
                                                    <input type="text" name="new_grade" class="form-control" 
                                                           value="<?php echo e(old('new_grade', $renewal->new_grade)); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Divisi/Sub Divisi Baru <span class="text-danger">*</span></label>
                                                    <input type="text" name="new_division" class="form-control" 
                                                           value="<?php echo e(old('new_division', $renewal->new_division)); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Gaji Baru <span class="text-danger">*</span></label>
                                                    <input type="text" name="new_salary" class="form-control currency" 
                                                           value="<?php echo e(old('new_salary', number_format($renewal->new_salary, 0, ',', '.'))); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Lain-lain (Tunjangan dll)</label>
                                                    <input type="text" name="new_others" class="form-control" 
                                                           value="<?php echo e(old('new_others', $renewal->new_others)); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Alasan Perubahan <span class="text-danger">*</span></label>
                                                    <textarea name="reasons" class="form-control" rows="4" required><?php echo e(old('reasons', $renewal->reasons)); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Berlaku Efektif Mulai <span class="text-danger">*</span></label>
                                                    <input type="date" name="effective_from" class="form-control" 
                                                           value="<?php echo e(old('effective_from', $renewal->effective_from)); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Approval Signatures (Readonly/Info) -->
                                <div class="box box-default collapsed-box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-check-circle"></i> Approval Signatures (Info)</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th width="30%">SUGGESTED BY</th>
                                                        <td width="35%"><?php echo e($renewal->suggested_by_name ?? '-'); ?></td>
                                                        <td width="35%"><?php echo e($renewal->suggested_by_date ? date('d/m/Y', strtotime($renewal->suggested_by_date)) : '-'); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>SUPPORTED BY</th>
                                                        <td><?php echo e($renewal->supported_by_name ?? '-'); ?></td>
                                                        <td><?php echo e($renewal->supported_by_date ? date('d/m/Y', strtotime($renewal->supported_by_date)) : '-'); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>PROPOSED BY</th>
                                                        <td><?php echo e($renewal->proposed_by_name ?? '-'); ?></td>
                                                        <td><?php echo e($renewal->proposed_by_date ? date('d/m/Y', strtotime($renewal->proposed_by_date)) : '-'); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>APPROVED BY</th>
                                                        <td><?php echo e($renewal->approved_by_name ?? '-'); ?></td>
                                                        <td><?php echo e($renewal->approved_by_date ? date('d/m/Y', strtotime($renewal->approved_by_date)) : '-'); ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th width="30%">PERSONNEL NAME</th>
                                                        <td colspan="2"><?php echo e($renewal->personnel_name ?? '-'); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>PERSONNEL DATE</th>
                                                        <td colspan="2"><?php echo e($renewal->personnel_date ? date('d/m/Y', strtotime($renewal->personnel_date)) : '-'); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Kehadiran</th>
                                                        <td colspan="2"><?php echo e($renewal->personnel_attendance ?? '-'); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Ketepatan Waktu</th>
                                                        <td colspan="2"><?php echo e($renewal->personnel_punctuality ?? '-'); ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i> Catatan: Approval signatures hanya dapat diubah melalui proses approval terpisah.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Update
                                </button>
                                <a href="<?php echo e(route('renewal.show', $renewal->id)); ?>" class="btn btn-default">
                                    <i class="fa fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('Scripts'); ?>
    <script>
        $(document).ready(function() {
            // Format currency
            $('.currency').on('keyup', function() {
                var value = $(this).val();
                value = value.replace(/[^0-9]/g, '');
                if (value) {
                    value = parseInt(value).toLocaleString('id-ID');
                    $(this).val(value);
                }
            });

            // Format sebelum submit
            $('form').on('submit', function() {
                $('.currency').each(function() {
                    var value = $(this).val();
                    value = value.replace(/\./g, '');
                    $(this).val(value);
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\renewal\edit.blade.php ENDPATH**/ ?>