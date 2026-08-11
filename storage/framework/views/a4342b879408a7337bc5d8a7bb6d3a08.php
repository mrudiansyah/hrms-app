
<?php $__env->startSection('Contents'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<div class="content-wrapper">
    <section class="content-header">
        <h1>Adyawinsa Group <small>Tambah Karyawan Baru</small></h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo e(url('/adyawinsa-group')); ?>"><i class="fa fa-users"></i> Employee List</a></li>
            <li class="active">Tambah Karyawan</li>
        </ol>
    </section>

    <section class="content">
    <div class="row">
        <div class="col-md-10">

            <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h4><i class="icon fa fa-ban"></i> Validasi Gagal!</h4>
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($err); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo e(session('error')); ?>

            </div>
            <?php endif; ?>

            <form action="<?php echo e(url('/adyawinsa-group')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-user"></i> Data Utama</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>NIK / No. Karyawan <span class="text-red">*</span></label>
                                <input type="text" name="employee_number" class="form-control"
                                    value="<?php echo e(old('employee_number')); ?>" placeholder="Contoh: EMP-001" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Gender <span class="text-red">*</span></label>
                                <select name="gender" class="form-control" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="Male" <?php echo e(old('gender') == 'Male' ? 'selected' : ''); ?>>Laki-laki</option>
                                    <option value="Female" <?php echo e(old('gender') == 'Female' ? 'selected' : ''); ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Tanggal Bergabung <span class="text-red">*</span></label>
                                <input type="date" name="start_date" class="form-control"
                                    value="<?php echo e(old('start_date')); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Nama Lengkap <span class="text-red">*</span></label>
                                <input type="text" name="full_name" class="form-control"
                                    value="<?php echo e(old('full_name')); ?>" placeholder="Nama lengkap karyawan" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Email <span class="text-red">*</span></label>
                                <input type="email" name="email" class="form-control"
                                    value="<?php echo e(old('email')); ?>" placeholder="contoh@email.com" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Department <span class="text-red">*</span></label>
                                <select name="department_id" class="form-control select2" required>
                                    <option value="">-- Pilih Department --</option>
                                    <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($dept->id); ?>" <?php echo e(old('department_id') == $dept->id ? 'selected' : ''); ?>>
                                        <?php echo e($dept->dept_name); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Jabatan / Posisi <span class="text-red">*</span></label>
                                <select name="job_position_id" class="form-control select2" required>
                                    <option value="">-- Pilih Posisi --</option>
                                    <?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($pos->id); ?>" <?php echo e(old('job_position_id') == $pos->id ? 'selected' : ''); ?>>
                                        <?php echo e($pos->position_name); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-id-card-o"></i> Data Pribadi</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Tempat Lahir</label>
                                <input type="text" name="place_of_birth" class="form-control"
                                    value="<?php echo e(old('place_of_birth')); ?>" placeholder="Kota kelahiran">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Tanggal Lahir</label>
                                <input type="date" name="date_of_birth" class="form-control"
                                    value="<?php echo e(old('date_of_birth')); ?>">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Agama</label>
                                <select name="religion" class="form-control">
                                    <option value="">-- Pilih --</option>
                                    <?php $__currentLoopData = ['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ag); ?>" <?php echo e(old('religion') == $ag ? 'selected' : ''); ?>><?php echo e($ag); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>Golongan Darah</label>
                                <select name="blood_type" class="form-control">
                                    <option value="">-- Pilih --</option>
                                    <?php $__currentLoopData = ['A','B','AB','O']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($bt); ?>" <?php echo e(old('blood_type') == $bt ? 'selected' : ''); ?>><?php echo e($bt); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>No. Telepon</label>
                                <input type="text" name="phone_number" class="form-control"
                                    value="<?php echo e(old('phone_number')); ?>" placeholder="08xx-xxxx-xxxx">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Nama Ibu Kandung</label>
                                <input type="text" name="mother_maiden_name" class="form-control"
                                    value="<?php echo e(old('mother_maiden_name')); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>No. KTP</label>
                                <input type="text" name="national_id_number" class="form-control"
                                    value="<?php echo e(old('national_id_number')); ?>" placeholder="16 digit NIK KTP">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>No. Kartu Keluarga</label>
                                <input type="text" name="family_card_number" class="form-control"
                                    value="<?php echo e(old('family_card_number')); ?>">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>No. NPWP</label>
                                <input type="text" name="tax_id_number" class="form-control"
                                    value="<?php echo e(old('tax_id_number')); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <a href="<?php echo e(url('/adyawinsa-group')); ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('Scripts'); ?>
<script>
    $(document).ready(function() {
        $('.select2').select2({ width: '100%' });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/adyawinsa_group/create.blade.php ENDPATH**/ ?>