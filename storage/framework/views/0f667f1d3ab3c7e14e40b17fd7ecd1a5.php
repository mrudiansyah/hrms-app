
<?php $__env->startSection('Contents'); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Payroll
                <small>Assignment Summary</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Assignment Summary</li>
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
                    <!-- Filter Box -->
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-filter"></i> Filter Periode</h3>
                        </div>
                        <div class="box-body">
                            <form action="<?php echo e(url('/payroll/summary_assignment')); ?>" method="GET" class="form-inline" style="display: inline-block;">
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
                                <button type="button" class="btn btn-info" style="margin-left: 5px;" data-toggle="modal" data-target="#modal-import">
                                    <i class="fa fa-file-excel-o"></i> Rapel
                                </button>
                            </form>
                            <form action="<?php echo e(url('/payroll/collect_meals')); ?>" method="POST" style="display: inline-block;">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="start" value="<?php echo e($start); ?>">
                                <input type="hidden" name="end" value="<?php echo e($end); ?>">
                                <button type="submit" class="btn btn-info" style="margin-left: 5px;">
                                    <i class="fa fa-cutlery"></i> Meals
                                </button>
                            </form>&nbsp;&nbsp;
							<a href="<?php echo e(url('/payroll/tax_assignment_excel/'.$start.'/'.$end)); ?>" class="btn btn-success btn-md" style="margin-left: 5px;"><i class="fa fa-file-excel-o"></i> &nbsp;Excel</a>
							<a href="<?php echo e(url('/payroll/distribute_assignment_slip/'.$start.'/'.$end)); ?>" class="btn btn-primary btn-md" style="margin-left: 5px;"><i class="fa fa-envelope"></i> &nbsp;Distribute</a>
                        </div>
                    </div>

                    <!-- Modal Import -->
                    <div class="modal fade" id="modal-import" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-aqua">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Import Rapel</h4>
                                </div>
                                <form action="<?php echo e(url('/payroll/import_rapel')); ?>" method="POST" enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="start" value="<?php echo e($start); ?>">
                                    <input type="hidden" name="end" value="<?php echo e($end); ?>">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="file">Excel File</label>
                                            <input type="file" name="file" id="file" class="form-control" required>
                                            <p class="help-block">
                                                Format: Kolom A (NIK), Kolom B (Periode YYYY-MM), Kolom C (Amount).
                                                <br>
                                                <a href="<?php echo e(url('/payroll/download_format_rapel')); ?>" class="text-primary">
                                                    <i class="fa fa-download"></i> Download Format Excel
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Import Now</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Data Box -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-list"></i> Overtime Summary (<?php echo e($start); ?> - <?php echo e($end); ?>)</h3>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="payroll-table" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Dept</th>
                                            <th>CC_Code</th>
                                            <th>Employee Name</th>
                                            <th>NIK</th>
                                            <th>SLPJ</th>
                                            <th>Hours</th>
                                            <th>Convetion</th>
                                            <th>Amount</th>
                                            <th>Meal</th>
                                            <th>Rapel</th>
                                            <th>Final_Amount</th>
                                            <th>PPh21</th>
                                            <th>Amount</th>
                                            <th>Norek</th>
                                            <th>Total_Paid</th>
                                            <th>Nama</th>
                                            <th>Slip</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $tb1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($index + 1); ?></td>
                                                <td><?php echo e($item->dept_code); ?></td>
                                                <td><?php echo e($item->cc_code); ?></td>
                                                <td><?php echo e($item->employee_name); ?></td>
                                                <td><?php echo e($item->nik); ?></td>
                                                <td><?php echo e(number_format($item->slpj, 0)); ?></td>
                                                <td><?php echo e(number_format($item->hours_act, 2)); ?></td>
                                                <td><?php echo e(number_format($item->hour_convertion, 2)); ?></td>
                                                <td><?php echo e(number_format($item->ot_amount, 0)); ?></td>
                                                <td><?php echo e(number_format($item->meal_amount, 0)); ?></td>
                                                <td><?php echo e(number_format($item->rapel_amount, 0)); ?></td>
                                                <td><?php echo e(number_format($item->gross_amount, 0)); ?></td>
                                                <td><?php echo e(number_format($item->pph21_amount, 0)); ?></td>
                                                <td><?php echo e(number_format($item->net_amount, 0)); ?></td>
                                                <td><?php echo e($item->nomor_rekening); ?></td>
                                                <td><?php echo e(number_format($item->net_amount, 0)); ?></td>
                                                <td><?php echo e($item->employee_name); ?></td>
                                                <td>
                                                    <a href="<?php echo e(url('/payroll/slip/overtime/'.$start.'/'.$end.'/'.$item->id_employee)); ?>" class="btn btn-xs btn-primary"><i class="fa fa-print"></i></a>
                                                </td>
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

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/payroll/summary_assignment.blade.php ENDPATH**/ ?>