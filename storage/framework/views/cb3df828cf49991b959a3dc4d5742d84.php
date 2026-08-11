
<?php $__env->startSection('Contents'); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Adyawinsa Group <small>Employee List</small></h1>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-xs-12">

                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <h4><i class="icon fa fa-check"></i> Berhasil!</h4>
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>
                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <h4><i class="icon fa fa-ban"></i> Error!</h4>
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>

                    <div class="box box-primary">
                        <div class="box-header">
                            <i class="fa fa-users"></i>
                            <h3 class="box-title">Daftar Karyawan</h3>
                            <div class="box-tools pull-right">
                                <a href="<?php echo e(url('/adyawinsa-group/create')); ?>" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> Tambah Karyawan
                                </a>
                            </div>
                        </div>
                        <div class="box-body">
                            <table id="tables" class="table table-hover table-bordered table-striped">
                                <thead>
                                    <tr style="background:#d3d8d8ff">
                                        <th style="width:50px;">No</th>
                                        <th>NIK</th>
                                        <th>Nama Lengkap</th>
                                        <th>Gender</th>
                                        <th>Department</th>
                                        <th>Posisi</th>
                                        <th>Tgl Bergabung</th>
                                        <th style="width:90px; text-align:center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($i + 1); ?></td>
                                            <td><?php echo e($emp['employee_number'] ?? '-'); ?></td>
                                            <td><?php echo e($emp['full_name'] ?? '-'); ?></td>
                                            <td><?php echo e($emp['gender'] ?? '-'); ?></td>
                                            <td><?php echo e($emp['department_name'] ?? '-'); ?></td>
                                            <td><?php echo e($emp['position_name'] ?? '-'); ?></td>
                                            <td><?php echo e($emp['start_date'] ?? '-'); ?></td>
                                            <td align="center">
                                                <a href="<?php echo e(url('/adyawinsa-group/' . urlencode($emp['employee_number'] ?? '') . '/edit')); ?>"
                                                    class="btn btn-warning btn-xs" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form
                                                    action="<?php echo e(url('/adyawinsa-group/' . urlencode($emp['employee_number'] ?? ''))); ?>"
                                                    method="POST" style="display:inline;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-danger btn-xs" title="Hapus"
                                                        onclick="return confirm('Yakin ingin menghapus karyawan ini?')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>

                            <?php if(!empty($meta)): ?>
                                <div class="row">
                                    <div class="col-sm-12 text-right">
                                        <small class="text-muted">
                                            Total: <?php echo e($meta['total'] ?? 0); ?> karyawan |
                                            Halaman <?php echo e($meta['page'] ?? 1); ?> dari <?php echo e($meta['last_page'] ?? 1); ?>

                                        </small>
                                        <br>
                                        <?php if(($meta['page'] ?? 1) > 1): ?>
                                            <a href="?page=<?php echo e(($meta['page'] ?? 1) - 1); ?>" class="btn btn-default btn-xs">
                                                <i class="fa fa-chevron-left"></i> Prev
                                            </a>
                                        <?php endif; ?>
                                        <?php if(($meta['page'] ?? 1) < ($meta['last_page'] ?? 1)): ?>
                                            <a href="?page=<?php echo e(($meta['page'] ?? 1) + 1); ?>" class="btn btn-default btn-xs">
                                                Next <i class="fa fa-chevron-right"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('Scripts'); ?>
    <script>
        $(document).ready(function () {
            var table = $('#tables').DataTable({
                'paging': false,
                'lengthChange': false,
                'searching': true,
                'ordering': true,
                'info': false,
                'autoWidth': false,
            });
        });
        window.setTimeout(function () {
            $(".alert").fadeTo(500, 0).slideUp(500, function () { $(this).remove(); });
        }, 3000);
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/adyawinsa_group/index.blade.php ENDPATH**/ ?>