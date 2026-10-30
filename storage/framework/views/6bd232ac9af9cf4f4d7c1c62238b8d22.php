
<?php $__env->startSection('Contents'); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Payroll
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Journal Recap</li>
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
                    <!-- Data Box -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-list"></i> Recap Journal (<?php echo e($periode); ?>)</h3>
                            <div class="pull-right">
                                <form class="form-inline" style="display:flex; align-items:center; gap:8px;">
                                    <label for="periode" style="margin-bottom:0; white-space:nowrap;">Periode:</label>
                                    <input type="month" name="periode" id="periode" class="form-control input-sm" value="<?php echo e($periode); ?>">
                                </form>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="payroll-table" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Line</th>
                                            <th>GL Account</th>
                                            <th>Description</th>
                                            <th>Description</th>
                                            <th>Debit</th>
                                            <th>Credit</th>
                                            <th>Currency</th>
                                            <th>Currency</th>
                                            <th>Currency</th>
                                            <th>Create Date</th>
                                            <th>Description</th>
                                            <th>Tax Type</th>
                                            <th>Tax Rate</th>
                                            <th>Reporting Module</th>
                                            <th>In/Out</th>
                                            <th>Statistical Amount</th>
                                            <th>Statistical UOM Code</th>
                                            <th>Multi-Company</th>
                                            <th>External Company</th>
                                            <th>External COA Code</th>
                                            <th>External GL Account</th>
                                            <th>CommentText</th>
                                            <th>Post Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $tb_journal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($index + 1); ?></td>
                                                <td><?php echo e($item->account_code); ?>-<?php echo e($item->cc_code); ?></td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td><?php echo e(number_format($item->amount,0)); ?></td>
                                                <td>&nbsp;</td>
                                                <td>IDR</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td><?php echo e($today); ?></td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>0</td>
                                                <td>&nbsp;</td>
                                                <td>FALSE</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
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
	<script type="text/javascript">
		$('body').on("change","#periode",function(){
			var periode=document.getElementById('periode').value;
			window.location.href="/payroll/recap_journal/"+periode;
		});
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\payroll\recap_journal.blade.php ENDPATH**/ ?>