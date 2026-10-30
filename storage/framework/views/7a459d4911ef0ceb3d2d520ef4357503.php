
<?php $__env->startSection('Styles'); ?>
	<style>th{text-align:center;}</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Contents'); ?>
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<div class="pull-left">
							<input type="month" id="periode" value="<?php echo e($periode); ?>">	
							<select id="category" name="category" style="height:27px;">
								<option value="payroll">Payroll</option>
								<option value="overtime">Overtime</option>
								<option value="thr">THR</option>
								<option value="bonus">Bonus</option>
							</select>
						</div>
						<div class="pull-right">
							<a href="<?php echo e($site); ?>/Summary/<?php echo e($periode); ?>/<?php echo e($department); ?>/<?php echo e($tipe); ?>" id="template" class="btn btn-default btn-md"><i class="fa fa-angle-double-left"></i> &nbsp;Kembali</a>
						</div>
					</div>
					<div class="box-body" style="overflow: auto;">
						<table id="tables" class="table table-bordered" style="min-width:100%;">
							<thead>
								<tr>
									<th style="width:30px;">No<br>&nbsp;</th>
									<th style="width:100px;">NIK<br>&nbsp;</th>
									<th >NAME<br>&nbsp;</th>
									<th style="width:70px;">DEPT<br>&nbsp;</th>
									<th style="width:70px;">POSITION<br>&nbsp;</th>
									<th style="width:70px;">STATUS<br>&nbsp;</th>
									<th style="width:70px;">CATEGORY<br>&nbsp;</th>
									<th style="width:70px;">ADDITIONAL<br><small style="font-weight:normal;">A</small></th>
									<th style="width:70px;">EARN<br><small style="font-weight:normal;">B</small></th>
									<th style="width:70px;">SUM EARN<br><small style="font-weight:normal;">C=A+B</small></th>
									<th style="width:70px;">PERSEN<br><small style="font-weight:normal;">D</small></th>
									<th style="width:70px;">PPH21<br><small style="font-weight:normal;">E=CxD</small></th>
									<th style="width:70px;">PAID<br><small style="font-weight:normal;">F</small></th>
									<th style="width:70px;">BALANCE<br><small style="font-weight:normal;">G=E-F</small></th>
								</tr>
							</thead>
							<tbody>
							<?php $no=0;?>
							<?php $__currentLoopData = $tb1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td>
										<?php
											$no++;
											$add=$dt1->monthly_salary+$dt1->prev_amount;
										?>
										<?php echo e($no); ?>

									</td>
									<td><?php echo e($dt1->NIK); ?></td>
									<td><?php echo e($dt1->nama_karyawan); ?></td>
									<td><?php echo e($dt1->department); ?></td>
									<td><?php echo e($dt1->jabatan); ?></td>
									<td><?php echo e($dt1->status_pajak); ?></td>
									<td><?php echo e($dt1->kategori); ?></td>
									<td><?php echo number_format($add,0);?></td>
									<td><?php echo number_format($dt1->current_amount,0);?></td>
									<td><?php echo number_format($dt1->cumulative_amount,0);?></td>
									<td><?php echo number_format($dt1->prosentase,0);?>%</td>
									<td><?php echo number_format($dt1->tax_amount,0);?></td>
									<td><?php echo number_format($dt1->previous_payment,0);?></td>
									<td><?php echo number_format($dt1->balance_amount,0);?></td>
								</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>

			</div>
		</div>
	</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
	<script>
		$('body').on("change","#periode",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			var tipe='<?php echo $tipe;?>'
			if(periode=='') var periode=0;
			window.location.href="<?php echo e($site); ?>/TaxTerMonthly/"+periode+"/"+department+"/"+tipe+"/payroll";
		});
		$('body').on("change","#idDepartment",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			var tipe='<?php echo $tipe;?>'
			if(periode=='') var periode=0;
			window.location.href="<?php echo e($site); ?>/TaxTerMonthly/"+periode+"/"+department+"/"+tipe+"/payroll";
		});
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);

	</script>
	<script>
		$(document).ready(function(){
		$('[data-toggle="popover"]').popover();
		});
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/payroll', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\payroll\salary_tax_termonthly.blade.php ENDPATH**/ ?>