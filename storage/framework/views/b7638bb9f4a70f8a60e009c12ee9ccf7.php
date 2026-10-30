
<?php $__env->startSection('Contents'); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Employee Status Renewal
                <small>Detail Pembaharuan Status Karyawan</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo e(route('renewal.index')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="<?php echo e(route('renewal.index')); ?>">Renewal</a></li>
                <li class="active">Detail</li>
            </ol>
        </section>

        <section class="content">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Success!</h4>
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                <i class="fa fa-file-text"></i> Detail Employee Status Renewal
                            </h3>
                            <div class="box-tools pull-right">
                                <a href="<?php echo e(route('renewal.edit', $renewal->id)); ?>" class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a href="<?php echo e(route('renewal.print', $renewal->id)); ?>" class="btn btn-default btn-sm" target="_blank">
                                    <i class="fa fa-print"></i> Print
                                </a>
                                <a href="<?php echo e(route('renewal.index')); ?>" class="btn btn-primary btn-sm">
                                    <i class="fa fa-list"></i> Back to List
                                </a>
                            </div>
                        </div>
                        <div class="box-body">
                            <!-- Informasi Karyawan -->
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-user"></i> Data Karyawan</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>NIK</label>
                                            <p class="form-control-static"><?php echo e($renewal->nik ?? '-'); ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Nama Karyawan</label>
                                            <p class="form-control-static"><?php echo e($renewal->name ?? '-'); ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Tanggal Bergabung</label>
                                            <p class="form-control-static"><?php echo e($renewal->date_of_join ? date('d/m/Y', strtotime($renewal->date_of_join)) : '-'); ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Masa Kerja</label>
                                            <p class="form-control-static"><?php echo e($renewal->work_period ?? '-'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kondisi Terakhir/Sekarang -->
                            <div class="box box-info">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-history"></i> Last Condition (Kondisi Terakhir/Sekarang)</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>POSITION / Jabatan</label>
                                            <p class="form-control-static"><?php echo e($renewal->last_position ?? '-'); ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label>GRADE / Golongan</label>
                                            <p class="form-control-static"><?php echo e($renewal->last_grade ?? '-'); ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label>DIV./SUB DIV. / Divisi/Sub.Divisi</label>
                                            <p class="form-control-static"><?php echo e($renewal->last_division ?? '-'); ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label>SALARY / Gaji</label>
                                            <p class="form-control-static">Rp <?php echo e(number_format($renewal->last_salary ?? 0, 0, ',', '.')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($renewal->last_others): ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>OTHERS / Lain-lain</label>
                                            <p class="form-control-static"><?php echo e($renewal->last_others ?? '-'); ?></p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Kondisi Baru -->
                            <div class="box box-success">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-exchange"></i> New Condition (Kondisi Baru)</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>POSITION / Jabatan</label>
                                            <p class="form-control-static"><strong><?php echo e($renewal->new_position ?? '-'); ?></strong></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label>GRADE / Golongan</label>
                                            <p class="form-control-static"><strong><?php echo e($renewal->new_grade ?? '-'); ?></strong></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label>DIV./SUB DIV. / Divisi/Sub.Divisi</label>
                                            <p class="form-control-static"><strong><?php echo e($renewal->new_division ?? '-'); ?></strong></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label>SALARY / Gaji</label>
                                            <p class="form-control-static"><strong>Rp <?php echo e(number_format($renewal->new_salary ?? 0, 0, ',', '.')); ?></strong></p>
                                        </div>
                                    </div>
                                    <?php if($renewal->new_others): ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>OTHERS / Lain-lain</label>
                                            <p class="form-control-static"><?php echo e($renewal->new_others ?? '-'); ?></p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Alasan dan Efektif -->
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="box box-warning">
                                        <div class="box-header with-border">
                                            <h3 class="box-title"><i class="fa fa-question-circle"></i> Reasons (Alasan Perubahan)</h3>
                                        </div>
                                        <div class="box-body">
                                            <p class="form-control-static"><?php echo e($renewal->reasons ?? '-'); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="box box-danger">
                                        <div class="box-header with-border">
                                            <h3 class="box-title"><i class="fa fa-calendar"></i> Effective From</h3>
                                        </div>
                                        <div class="box-body">
                                            <p class="form-control-static">
                                                <strong><?php echo e($renewal->effective_from ? date('d/m/Y', strtotime($renewal->effective_from)) : '-'); ?></strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Approval Signatures -->
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-check-circle"></i> Approval Signatures</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body" style="display: none;">
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
                                </div>
                            </div>

                            <!-- Audit Info -->
                            <div class="box box-default collapsed-box">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-info-circle"></i> Audit Information</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Created By</label>
                                            <p class="form-control-static"><?php echo e($renewal->created_by ?? '-'); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Created At</label>
                                            <p class="form-control-static"><?php echo e($renewal->created_at ? date('d/m/Y H:i:s', strtotime($renewal->created_at)) : '-'); ?></p>
                                        </div>
                                    </div>
                                    <?php if($renewal->updated_at): ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Updated At</label>
                                            <p class="form-control-static"><?php echo e(date('d/m/Y H:i:s', strtotime($renewal->updated_at))); ?></p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <div class="pull-right">
                                <a href="<?php echo e(route('renewal.edit', $renewal->id)); ?>" class="btn btn-warning">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a href="<?php echo e(route('renewal.print', $renewal->id)); ?>" class="btn btn-default" target="_blank">
                                    <i class="fa fa-print"></i> Print
                                </a>
                                <a href="<?php echo e(route('renewal.index')); ?>" class="btn btn-primary">
                                    <i class="fa fa-list"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\renewal\show.blade.php ENDPATH**/ ?>