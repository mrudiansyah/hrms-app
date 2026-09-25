<style>
	.kiri{
		text-align:left;
	}
	.tengah{
		text-align:center;
	}
	.vtengah{
		vertical-align:middle;
	}
	.b1{
		border-top:1px solid #000;
	}	
	.b2{
		border-right:1px solid #000;
	}	
	.b3{
		border-bottom:1px solid #000;
	}
	.b4{
		border-left:1px solid #000;
	}
	.b13{
		border-top:1px solid #000;
		border-bottom:1px solid #000;
		border-right:0px;
		border-left:0px;
	}
	.b24{
		border-top:0px;
		border-bottom:0px;
		border-right:1px solid #000;
		border-left:1px solid #000;
	}
	#konten td{
		text-align:center;
	}
	.ttd td{
		text-align:center;
		vertical-align:middle;
	}
	.absolute {
		position: absolute;
		height:80px;
		left:0px;
		max-width:130px;
		top:-10px;
		font-size:10px;
	}		
	.relative {
		left:20px;
		position: relative;
		height:60px;
		font-size:10px;
	}	
	#dokumen td{
		border-bottom:1px solid #000;
	}
</style>
<div style="font-size:14px;">
	<table id="isimemo" width="100%" cellspacing="0" style="border:1px solid #000;">
		<?php $__currentLoopData = $data['table1']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<tr>
			<th class="b3" style="width:200px;padding:3px;"><img src="<?php echo e(asset('public/gambar/logosai.png')); ?>" style="width:90%;"></th>
			<th class="b2 b3 b4" style="text-align:center;vertical-align:middle;font-size:21px;">OVERTIME REQUEST</th>
			<th class="b3" style="width:200px;font-weight:normal;font-size:10px;">
				<table id="dokumen" style="width:100%;" cellspacing="0">
					<tr>
						<td style="width:40%;">No. Dokumen</td>
						<td>: <?php echo e($dt->memo_number); ?></td>
					</tr>
					<tr>
						<td>Department</td>
						<td>: <?php echo e($dt->from_dept); ?></td>
					</tr>
					<tr>
						<td>Tgl. Terbit</td>
						<td>: <?php echo date('d-M-Y',strtotime($dt->created_at));?></td>
					</tr>
					<tr>
						<td style="border-bottom:0px;">Revisi</td>
						<td style="border-bottom:0px;">: -</td>
					</tr>
				</table>
			</th>
		</tr>
		<tr>
			<td colspan="3" style="padding:20px; border-top:0px; border-bottom:0px;">
				<table>
					<tr>
						<td style="width:150px;text-align:left;">Tgl Pengajuan</td>
						<td style="width:10px;">:</td>
						<td>
							<?php echo date('d-M-Y',strtotime($dt->date_information));?>
						</td>
					</tr>
					<tr>
						<td style="text-align:left;">Untuk Department</td>
						<td>:</td>
						<td>
							<?php echo e($dt->to_dept); ?>

						</td>
					</tr>
					<tr>
						<td style="text-align:left;">Penerima<br>&nbsp;</td>
						<td>:<br>&nbsp;</td>
						<td>
							<?php echo e($dt->up_name); ?><br>CC. <?php echo e($dt->cc_name); ?>

						</td>
					</tr>
					<tr>
						<td style="text-align:left;">Tujuan</td>
						<td>:</td>
						<td>
							Request <?php echo e($dt->description); ?>

						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="padding:0px 20px;text-align:left;">
				<p>Dengan Hormat,</p>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="text-indent: 3em;padding:0px 20px;text-align:left;">
				<p><?php echo e($dt->opening_text); ?></p>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="text-indent: 3em;padding:0px 20px;text-align:left;">
				<p><?php echo e($dt->closing_text); ?></p>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="padding:5px 20px;text-align:center;">
				<p>Department yang Mengajukan</p>
				<table width="100%">
					<tr>
						<?php $__currentLoopData = $data['table3']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<?php if($row->approval_group=='1'): ?>
							<td style="text-align:center;">
								<table cellspacing="5" class="ttd" style="width:100%;">
									<tr><td style="text-align:center;"><?php echo e($row->job_approval); ?></td></tr>
									<tr>
										<td style="text-align:center;">
											<p class="relative" style="height:40px;">
												<?php 
												$reject=asset('public/approval/rejected.png');
												if($row->approval_status=='1'){
													$ttd_app=asset('public/approval/'.$row->id_employee.'.png');
													$confirm=asset('public/approval/confirm.png');
													$filename = 'c:/xampp/htdocs/EMS/public/approval/'.$row->id_employee.'.png';
													if (file_exists($filename)){?>
														<img src='<?php echo e($ttd_app); ?>' class="absolute">
													<?php }else{?>
														<img src='<?php echo e($confirm); ?>' style='width:100px;height:40px;'>
													<?php }
												}
												?>
											</p>
										</td>
									</tr>
									<tr>
										<td style="text-align:center;font-size:12px;">
											<?php if($row->approved_date>0)echo date('d-m-Y H:i',strtotime($row->approved_date));else echo "&nbsp;";?><br>
											<?php echo e($row->position); ?><br>
											<?php echo e($row->employee_name); ?>

										</td>
									</tr>
								</table>
							</td>
							<?php endif; ?>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="padding:5px 20px;text-align:center;">
				<p>Department Penerima</p>			
				<table width="100%">
					<tr>
						<?php $__currentLoopData = $data['table3']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<?php if($row->approval_group=='2'): ?>
							<td style="text-align:center;">
								<table cellspacing="10" class="ttd" style="width:100%;">
									<tr><td style="text-align:center;"><?php echo e($row->job_approval); ?></td></tr>
									<tr>
										<td style="text-align:center;">
											<p class="relative" style="height:40px;">
												<?php 
												$reject=asset('public/approval/rejected.png');
												if($row->approval_status=='1'){
													$ttd_app=asset('public/approval/'.$row->id_employee.'.png');
													$confirm=asset('public/approval/confirm.png');
													$filename = 'c:/xampp/htdocs/EMS/public/approval/'.$row->id_employee.'.png';
													if (file_exists($filename)){?>
														<img src='<?php echo e($ttd_app); ?>' class="absolute">
													<?php }else{?>
														<img src='<?php echo e($confirm); ?>' style='width:100px;height:40px;'>
													<?php }
												}
												?>
											</p>
										</td>
									</tr>
									<tr>
										<td style="text-align:center;font-size:12px;">
											<?php if($row->approved_date>0)echo date('d-m-Y H:i',strtotime($row->approved_date));else echo "&nbsp;";?><br>
											<?php echo e($row->position); ?><br>
											<?php echo e($row->employee_name); ?>

										</td>
									</tr>
								</table>
							</td>
							<?php endif; ?>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="padding:5px 20px;text-align:center;">
				<p>Supporting Department</p>
				<table width="100%">
					<tr>
						<?php $__currentLoopData = $data['table3']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<?php if($row->approval_group=='3'): ?>
							<td style="text-align:center;">
								<table cellspacing="10" class="ttd" style="width:100%;">
									<tr><td style="text-align:center;"><?php echo e($row->job_approval); ?></td></tr>
									<tr>
										<td style="text-align:center;">
											<p class="relative" style="height:40px;">
												<?php 
												$reject=asset('public/approval/rejected.png');
												if($row->approval_status=='1'){
													$ttd_app=asset('public/approval/'.$row->id_employee.'.png');
													$confirm=asset('public/approval/confirm.png');
													$filename = 'c:/xampp/htdocs/EMS/public/approval/'.$row->id_employee.'.png';
													if (file_exists($filename)){?>
														<img src='<?php echo e($ttd_app); ?>' class="absolute">
													<?php }else{?>
														<img src='<?php echo e($confirm); ?>' style='width:100px;height:40px;'>
													<?php }
												}
												?>
											</p>
										</td>
									</tr>
									<tr>
										<td style="text-align:center;font-size:12px;">
											<?php if($row->approved_date>0)echo date('d-m-Y H:i',strtotime($row->approved_date));else echo "&nbsp;";?><br>
											<?php echo e($row->position); ?><br>
											<?php echo e($row->employee_name); ?>

										</td>
									</tr>
								</table>
							</td>
							<?php endif; ?>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</tr>
				</table>
			</td>
		</tr>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	</table>
	<table width="100%">
		<tr>
			<td colspan="3" style="padding:5px 20px;width:100%;text-align:center;">
				<p>Lampiran Item Part</p>
				<table id="konten" style="width:100%;" border="1" cellspacing="0" class="kiri">
					<tr style="background:#DDD;">
						<th style="width:30px;height:30px;">No</th>
						<th>Part</th>
						<th>Line</th>
						<th>Process</th>
						<th>Qty</th>
						<th>Reason OT</th>
						<th>Date OT</th>
					</tr>
					<?php $no=0;?>
					<?php $__currentLoopData = $data['table2']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr>
							<td>
								<?php $no++;echo $no;?>
							</td>
							<td style="text-align:left;"><?php echo e($dt2->part_no); ?><br><?php echo e($dt2->part_name); ?></td>
							<td><?php echo e($dt2->lines); ?></td>
							<td><?php echo e($dt2->process); ?></td>
							<td><?php echo e($dt2->plan_qty); ?></td>
							<td style="text-align:left;">
								<?php echo e($dt2->reason_ot); ?>

							</td>
							<td><?php echo e(date('d-M-y',strtotime($dt2->date_ot))); ?><br><b><?php echo e(date('H:i',strtotime($dt2->start_ot))); ?> - <?php echo e(date('H:i',strtotime($dt2->finish_ot))); ?></b></td>
						</tr>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</table>
				
			</td>
		</tr>

	</table>
</div><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/general_memo/tb_memo_ot_pdf.blade.php ENDPATH**/ ?>