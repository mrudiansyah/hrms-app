
<?php $__env->startSection('Contents'); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Payroll
                <small>Summary Assignment</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Payroll Summary</li>
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

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-ban"></i> Error!</h4>
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-12">
                    <!-- Filter Box -->
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-filter"></i> Filter Periode</h3>
                        </div>
                        <div class="box-body">
                            <form action="<?php echo e(url('/payroll/capture_assignment')); ?>" method="GET" class="form-inline" style="display: inline-block;">
                                <div class="form-group">
                                    <label for="start">Start Date: </label>
                                    <input type="date" name="start" id="start" class="form-control" value="<?php echo e($start); ?>">
                                </div>
                                <div class="form-group" style="margin-left: 10px;">
                                    <label for="end">End Date: </label>
                                    <input type="date" name="end" id="end" class="form-control" value="<?php echo e($end); ?>">
                                </div>
                                <button type="submit" class="btn btn-primary" style="margin-left: 10px;">
                                    <i class="fa fa-search"></i> Filter
                                </button>
                            </form>

                            <form action="<?php echo e(url('/payroll/save_summary_assignment')); ?>" method="POST" style="display: inline-block; margin-left: 10px;">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="start" value="<?php echo e($start); ?>">
                                <input type="hidden" name="end" value="<?php echo e($end); ?>">
                                <button type="submit" class="btn btn-success" onclick="return confirm('Apakah anda yakin ingin menyimpan data ke table summary?')">
                                    <i class="fa fa-save"></i> Capture
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Data Box -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-list"></i> Summary Overtime (<?php echo e($start); ?> - <?php echo e($end); ?>)</h3>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="payroll-table" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NIK</th>
                                            <th>Name</th>
                                            <th>Dept</th>
                                            <th>Position</th>
                                            <th>Total_Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 0; ?>
                                        <?php $__currentLoopData = $tb1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php $no++;
                                                echo $no; ?></td>
                                                <td><?php echo e($dt->NIK); ?></td>
                                                <td><?php echo e($dt->employee_name); ?></td>
                                                <td><?php echo e($dt->dept_code); ?></td>
                                                <td><?php echo e($dt->position_name); ?></td>
                                                <td><?php echo e(number_format($dt->total_amount,0)); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('Scripts'); ?>
    <script>
        $(document).ready(function() {
            $('#payroll-table').DataTable({
                'paging'      : true,
                'lengthChange': true,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : false,
                "pageLength"  : 10,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'print'
                ]
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\payroll\capture_assignment.blade.php ENDPATH**/ ?>