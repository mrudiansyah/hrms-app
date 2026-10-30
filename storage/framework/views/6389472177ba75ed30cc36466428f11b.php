
<?php $__env->startSection('Contents'); ?>
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<h3 class="box-title"><?php echo e($juduls); ?> - <?php echo e($periode); ?></h3>
						<div class="pull-right">
                            <a href="<?php echo e($site); ?>/SummaryOther/Refresh/<?php echo e($periode); ?>" class="btn btn-primary btn-md" id="refresh"><i class="fa fa-refresh"></i> &nbsp;Refresh</a>
						</div>
					</div>
					<div class="box-body">
						<table id="tables" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>NO</th>
									<th>NIK</th>
									<th>EMPLOYEE_NAME</th>
                                    <th>DAYS</th>
                                    <th>SALARY/DAY</th>
                                    <th>RAPEL</th>
                                    <th>TOTAL_AMOUNT</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0; $grand_total=0;?>
								<?php $__currentLoopData = $tb_salary_summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr>
										<td><?php echo e(++$no); ?></td>
										<td><?php echo e($dt->NIK ?? ''); ?></td>
										<td><?php echo e($dt->employee_name ?? ''); ?></td>
										<td><?php echo e($dt->days ?? '0'); ?></td>
                                        <td style="text-align:right;"><?php echo e(isset($dt->salary_perday) ? number_format($dt->salary_perday, 0) : '0'); ?></td>
                                        <td style="text-align:right;"><?php echo e(isset($dt->rapel) ? number_format($dt->rapel, 0) : '0'); ?></td>
                                        <td style="text-align:right;"><?php echo e(isset($dt->total_amount) ? number_format($dt->total_amount, 0) : '0'); ?></td>
									</tr>
                                    <?php $grand_total += ($dt->total_amount ?? 0); ?>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" style="text-align:right;">GRAND TOTAL</th>
                                    <th style="text-align:right;"><?php echo e(number_format($grand_total, 0)); ?></th>
                                </tr>
                            </tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/payroll_other', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\payroll\summary_other.blade.php ENDPATH**/ ?>