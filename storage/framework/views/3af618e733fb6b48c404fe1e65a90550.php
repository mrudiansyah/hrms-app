
<?php $__env->startSection('Contents'); ?>
<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<h3 class="box-title">Compensation (Contract)</h3>
						<div class="pull-right" style="margin-left: 10px; display: flex; align-items: center;">
							<input type="month" id="periode" class="form-control" value="<?php echo e($periode); ?>">	
							&nbsp;&nbsp;
							<a href="<?php echo e($site); ?>/CompensationUpdate/<?php echo e($periode); ?>" class="btn btn-primary btn-md"><i class="fa fa-refresh"></i> &nbsp;Update</a>
							&nbsp;&nbsp;
							<a href="<?php echo e($site); ?>/CompensationExcel/<?php echo e($periode); ?>" class="btn btn-success btn-md"><i class="fa fa-file-excel-o"></i> &nbsp;Excel</a>
						</div>
					</div>
					<div class="box-body" style="overflow: auto;">

						<table id="tables" class="table table-hover">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th>NIK</th>
									<th>EMPLOYEE_NAME</th>
									<th>DEPARTMENT</th>
									<th>ACTION</th>
									<th>PERIODE</th>
									<th>MONTH</th>
									<th>SALARY</th>
									<th>AMOUNT</th>
									<th>PPH21</th>
									<th>RECEIVED</th>
									<th>ACTION</th>
								</tr>
							</thead>
							<tbody>
							<?php $no=0;?>
							<?php $__currentLoopData = $tb_kompensasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td><?php echo e($dt->NIK); ?></td>
									<td><?php echo e($dt->Nama); ?></td>
									<td><?php echo e($dt->Bagian); ?></td>
									<td><?php echo e($dt->contract_name); ?></td>
									<td><?php echo date('d-M-y',strtotime($dt->Start_Kontrak)).' ~ '.date('d-M-y',strtotime($dt->Finish_Kontrak));?></td>
									<td><?php echo e($dt->Jumlah_Bulan); ?></td>
									<td><?php echo number_format($dt->Total_Pendapatan,0);?></td>
									<td><?php echo number_format($dt->Kompensasi,0);?></td>
									<td><?php echo number_format($dt->PPH21,0);?></td>
									<td><?php echo number_format($dt->Total_Kompensasi,0);?></td>
									<td>
										<div class="pull-right">
											<?php if($dt->send_mail==0){?><a href="<?php echo e($site); ?>/SendMailCompensation/<?php echo e($dt->id); ?>"><button type="button" class="btn btn-info btn-xs"><i class="fa fa-envelope-o"></i></button></a><?php }?>
											<a href="<?php echo e($site); ?>/Compensation/Slip/<?php echo e($dt->id); ?>" target="_blank"><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></button></a>
											<!-- <form action="<?php echo e($site); ?>/CompensationDelete/<?php echo e($dt->id); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
												<?php echo csrf_field(); ?>
												<button type="submit" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
											</form> -->
										</div>
									</td>
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
			var periode=document.getElementById('periode').value;
			if(periode=='') var periode=0;
			window.location.href="<?php echo e($site); ?>/Compensation/"+periode;
		});
	</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts/payroll_payments', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\payroll\salary_compensation.blade.php ENDPATH**/ ?>