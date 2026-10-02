<style>
	html{
		margin:40px;
	}
	ol li{
		padding-bottom:5px;
	}
	.tabela th{
		text-align:center;
		font-weight:normal;
		font-size:10px;
		border:1px solid #000;
		padding:2px;
	}
	.tabela td{
		font-family: Arial, Helvetica, sans-serif;
		text-align:center;
		font-size:10px;
		border:1px solid #000;
		padding:2px;
	}
	.absolute {
		position: absolute;
		width:150px;
		left:-5px;
		top:0px;
	}		
	.relative {
		left:0px;
		position: relative;
		height:100px;
	}
	.bordered td{
		border:1px solid #000;
	}	
	.ttd th{
		border:1px solid #000;
		vertical-align:middle;
		text-align:center;
		font-size:9px;
		height:20px;
	}	
	.ttd td{
		border:1px solid #000;
		vertical-align:bottom;
		font-size:9px;
		text-align:center;
	}	

</style>
<?php $ksk=0;?>
<?php $__currentLoopData = $tb_ksk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php if($ksk==0){$ksk++;?>
<table style="width:100%;" cellspacing="0" class="tabela">
	<tr>
		<td style="width:15%;border:0px;"><img src="<?php echo e(base_path()); ?>/public/gambar/logosai.png" style="width:150px;"></td>
		<td style="border:0px;width:85%;">
			PT  SUMMIT ADYAWINSA INDONESIA<br>
			<label style="font-size:12px;">EMPLOYEE STATUS CONFIRMATION</label><br>
			<?php echo e($row->no_ksk); ?>

		</td>
	</tr>
	<tr><td colspan="2" style="border:0px;">&nbsp;</td></tr>
	<tr>
		<td style="text-align:left;border:0px;" colspan="1">DIVISI / DEPT</td>
		<td style="text-align:left;border:0px;" colspan="1">: <?php echo e($row->dept_name); ?></td>
	</tr>
	<tr>
		<td style="text-align:left;border:0px;" colspan="1"><?php echo strtoupper($row->pic_pos);?></td>
		<td style="text-align:left;border:0px;" colspan="1">: <?php echo e($row->manager_name); ?></td>
	</tr>
	<tr><td colspan="2" style="border:0px;">&nbsp;</td></tr>
	<tr>
		<td colspan="2" style="border:0px;">
			<table cellspacing="0" class="tabela">
				<tr><td colspan="16" style="text-align:left;border:0px;"><b>A. EMPLOYEE INFORMATION</b></td></tr>
				<tr>
					<td rowspan="3" style="width:5px;">NO</td>
					<td rowspan="3" style="width:100px;">ID NUMBER</td>
					<td rowspan="3" style="width:150px;">NAME</td>
					<td rowspan="3" style="width:70px;">JOIN DATE</td>
					<td rowspan="3" style="width:90px;">DEPT</td>
					<td rowspan="3" style="width:70px;">POSITION</td>
					<td rowspan="3" style="width:40px;">WARNING LETTER</td>
					<td colspan="5" style="width:70px;">ATTENDANCE</td>
					<td colspan="2" style="width:70px;">DATE OF CONTRACT</td>
					<td rowspan="3" style="width:70px;">Performance Appraisal</td>
					<td rowspan="3" style="width:70px;">TOTAL OF CONTRACT DURATION</td>
				</tr>
				<tr>
					<td rowspan="2" style="width:30px;">SICK</td>
					<td rowspan="2" style="width:30px;">PERMIT</td>
					<td rowspan="2" style="width:30px;">ALPA</td>
					<td colspan="2" style="width:30px;">LATE</td>
					<td rowspan="2" style="width:50px;">START</td>
					<td rowspan="2" style="width:50px;">END</td>
				</tr>
				<tr>
					<td style="width:40px;">TOTAL</td>
					<td style="width:40px;">MIUTE</td>
				</tr>
				<?php $no=0;?>
				<?php $__currentLoopData = $tb_ksk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php $bulan=$dt->months%12;$tahun=($dt->months-$bulan)/12;?>
					<tr>
						<td><?php $no++;echo $no;?></td>
						<td><?php echo e($dt->NIK); ?></td>
						<td style="text-align:left;"><?php echo e($dt->employee_name); ?></td>
						<td><?php echo date('d-M-y',strtotime($dt->join_date));?></td>
						<td><?php echo e($dt->dept_code); ?></td>
						<td><?php echo e($dt->position_name); ?></td>
						<td><?php if($dt->warning_letter=='')echo "-";else echo $dt->warning_letter;?></td>
						<td><?php echo e($dt->sick); ?></td>
						<td><?php echo e($dt->permit); ?></td>
						<td><?php echo e($dt->alpa); ?></td>
						<td><?php echo e($dt->late); ?></td>
						<td><?php echo e($dt->minutes); ?></td>
						<td><?php echo date('d-M-y',strtotime($dt->first_contract));?></td>
						<td><?php echo date('d-M-y',strtotime($dt->finish_contract));?></td>
						<td><?php echo e($dt->performance); ?></td>
						<td><?php if($tahun>0)echo $tahun.' Tahun<br>';if($bulan>0)echo $bulan.' Bulan';?></td>
					</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

			</table>
		</td>
	</tr>
	<tr><td colspan="2" style="border:0px;">&nbsp;</td></tr>
	<tr>
		<td colspan="2" style="border:0px;">
			<table cellspacing="0" class="tabela">
				<tr><td colspan="7" style="text-align:left;border:0px;"><b>B. EMPLOYEE CONFIRMATION</b></td></tr>
				<tr>
					<td style="width:5px;">NO</td>
					<td style="width:100px;">ID NUMBER</td>
					<td style="width:150px;">NAME</td>
					<td style="width:150px;">DIRECT SPV</td>
					<td style="width:80px;">EXTEND</td>
					<td style="width:80px;">NOT EXTEND</td>
					<td style="width:80px;">PERMANENCY</td>
					<td style="width:80px;">PKWT</td>
					<td style="width:70px;">MONTHs<br>(contract)</td>
					<td style="width:190px;">REASON</td>
				</tr>
				<?php $no=0;?>
				<?php $__currentLoopData = $tb_ksk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr>
						<td><?php $no++;echo $no;?></td>
						<td><?php echo e($dt->NIK); ?></td>
						<td style="text-align:left;"><?php echo e($dt->employee_name); ?></td>
						<td><?php echo e($dt->approvalname1); ?></td>
						<td><?php if($dt->judge=='EXTEND'){?><img src="<?php echo e(base_path()); ?>/public/gambar/checklist.png" style="width:15px;"><?php }?></td>
						<td><?php if($dt->judge=='NOT EXTEND'){?><img src="<?php echo e(base_path()); ?>/public/gambar/checklist.png" style="width:15px;"><?php }?></td>
						<td><?php if($dt->judge=='PERMANENCY'){?><img src="<?php echo e(base_path()); ?>/public/gambar/checklist.png" style="width:15px;"><?php }?></td>
						<td><?php if($dt->judge=='PKWT'){?><img src="<?php echo e(base_path()); ?>/public/gambar/checklist.png" style="width:15px;"><?php }?></td>
						<td><?php if($dt->judge=='EXTEND'||$dt->judge=='PKWT')echo $dt->next_contract." months";?></td>
						<td><?php echo $dt->reason;?></td>
					</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</table>
		</td>
	</tr>
	<tr><td colspan="2" style="border:0px;">&nbsp;</td></tr>
	<tr>
		<td colspan="2" style="border:0px;">
			<table cellspacing="0" class="tabela">
				<tr><td colspan="4" style="text-align:left;border:0px;"><b>C. INFORMATION OF QUOTA MANPOWER AT HRGA</b></td></tr>
				<tr>
					<td style="width:70px;">&nbsp;</td>
					<td style="width:70px;">Permanent</td>
					<td style="width:70px;">Contract</td>
					<td style="width:70px;">Magang</td>
				</tr>
				<tr>
					<td>Target</td>
					<td><?php echo e($row->permanent_target); ?></td>
					<td><?php echo e($row->contract_target); ?></td>
					<td><?php echo e($row->magang_target); ?></td>
				</tr>
				<tr>
					<td>Actual</td>
					<td><?php echo e($row->permanent_actual); ?></td>
					<td><?php echo e($row->contract_actual); ?></td>
					<td><?php echo e($row->magang_actual); ?></td>
				</tr>
				<tr>
					<td>Remain</td>
					<td><?php echo e($row->permanent_remain); ?></td>
					<td><?php echo e($row->contract_remain); ?></td>
					<td><?php echo e($row->magang_remain); ?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td colspan="2" style="border:0px;">&nbsp;</td></tr>
	<tr><td colspan="2" style="border:0px;">&nbsp;</td></tr>
	<tr>
		<td colspan="2" style="border:0px;">
			<table cellspacing="0" class="tabela">
				<tr><td style="border:0px;width:20px;">&nbsp;</td><td colspan="15" style="text-align:left;border:0px;width:600px;">NOTE:</td></tr>
				<tr><td style="border:0px;">&nbsp;</td><td colspan="15" style="text-align:left;border:0px;">1. (*) Dept. Head need to fill in the related column with a check mark (<img src="<?php echo e(base_path()); ?>/public/gambar/checklist.png" style="width:15px;">) after assessing attendances, performance appraisals and employees skill matrix.</td></tr>
				<tr><td style="border:0px;">&nbsp;</td><td colspan="15" style="text-align:left;border:0px;">2. Please return this form by at 10th of each month</td></tr>
				<tr><td style="border:0px;">&nbsp;</td><td colspan="15" style="text-align:left;border:0px;">3. The check mark (<img src="<?php echo e(base_path()); ?>/public/gambar/checklist.png" style="width:15px;">) in related column will become HRGA direction to do the follow up action in processing the status of related employees.</td></tr>
				<tr><td style="border:0px;">&nbsp;</td><td colspan="15" style="text-align:left;border:0px;">4. Every single decision should be followed by reason and attachment.</td></tr>
			</table>
		</td>
	</tr>
	<tr><td colspan="2" style="border:0px;">&nbsp;</td></tr>
	<tr><td colspan="2" style="border:0px;">&nbsp;</td></tr>
	<tr>
		<td colspan="2" style="border:0px;">
			<?php $panjang=$jml_approval*100;?>
			<table class="ttd" cellspacing="0" cellpadding="0" align="center">
				<tr>
					<th width="<?php echo e($panjang); ?>" style="padding:2px;" colspan="<?php echo e($jml_approval); ?>">RECEIVED & RECOMMENDED BY,</th>
					<th width="90" style="padding:2px;">ACCEPTED BACK BY,</th>
					<th width="90" style="padding:2px;">CHECKED BY,</th>
					<th width="90" style="padding:2px;">APPROVED BY,</th>
				</tr>
				<tr>
					<?php if($row->approvalname1!=''){?>
						<td>
							<?php if($row->approval1_date!=''): ?>
								<?php echo e($row->approval1_date); ?>

							<?php endif; ?>
						</td>
					<?php }?>
					<?php if($row->approvalname2!=''){?>
						<td>
							<?php if($row->approval2_date!=''): ?>
								<?php echo e($row->approval2_date); ?>

							<?php else: ?> 
                                &nbsp;
							<?php endif; ?>
						</td>
					<?php }?>
					<?php if($row->approvalname3!=''){?>
						<td>
							<?php if($row->approval3_date!=''): ?>
								<?php echo e($row->approval3_date); ?>

							<?php else: ?> &nbsp;
							<?php endif; ?>
						</td>
					<?php }?>
					<?php if($row->approvalname4!=''){?>
						<td>
							<?php if($row->approval4_date!=''): ?>
								<?php echo e($row->approval4_date); ?>

							<?php else: ?> &nbsp;
							<?php endif; ?>
						</td>
					<?php }?>
					<?php if($row->approvalname5!=''){?>
						<td>
							<?php if($row->approval5_date!=''): ?>
								<?php echo e($row->approval5_date); ?>

							<?php else: ?> &nbsp;
							<?php endif; ?>
						</td>
					<?php }?>
					<?php if($row->approvalname6!=''){?>
						<td>
							<?php if($row->approval6_date!=''): ?>
								<?php echo e($row->approval6_date); ?>

							<?php else: ?> &nbsp;
							<?php endif; ?>
						</td>
					<?php }?>
					<td>
						<?php if($row->legalize1_date!=''): ?>
							<?php echo e($row->legalize1_date); ?>

						<?php else: ?> &nbsp;
						<?php endif; ?>
					</td>
					<td>
						<?php if($row->legalize2_date!=''): ?>
							<?php echo e($row->legalize2_date); ?>

						<?php else: ?> &nbsp;
						<?php endif; ?>
					</td>
					<td>
						<?php if($row->legalize3_date!=''): ?>
							<?php echo e($row->legalize3_date); ?>

						<?php else: ?> &nbsp;
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<?php if($row->approvalname1!=''){?>
						<td style="border-top:0px;" class="relative">
                            <?php
                                $ttd_app='/public/approval/'.$row->approval1.'.png';
                                $confirm='/public/approval/confirm.png';
                            ?>
                            <?php if($row->approval1_status=='1'): ?>
                                <img src="<?php echo e(base_path()); ?><?php echo e($confirm); ?>" style="width:100px;">
                            <?php endif; ?>
                            <br><br>
							<u><?php echo e($row->approvalname1); ?></u><br>
							<?php if($row->approval1_date!=''): ?>
                                <?php echo e($pos[1]); ?>

                            <?php else: ?> 
                            &nbsp;
							<?php endif; ?>
						</td>
					<?php }?>
					<?php if($row->approvalname2!=''){?>
						<td style="border-top:0px;" class="relative">
                            <?php
                                $ttd_app='/public/approval/'.$row->approval2.'.png';
                                $confirm='/public/approval/confirm.png';
                            ?>
                            <?php if($row->approval2_status=='1'): ?>
                                <img src="<?php echo e(base_path()); ?><?php echo e($confirm); ?>" style="width:100px;">
                            <?php endif; ?>
                            <br><br>
							<u><?php echo e($row->approvalname2); ?></u><br>
							<?php if($row->approval2_date!=''): ?>
                                <?php echo e($pos[2]); ?>

							<?php else: ?> &nbsp;
							<?php endif; ?>
						</td>
					<?php }?>
					<?php if($row->approvalname3!=''){?>
						<td style="border-top:0px;" class="relative">
                            <?php
                                $ttd_app='/public/approval/'.$row->approval3.'.png';
                                $confirm='/public/approval/confirm.png';
                            ?>
                            <?php if($row->approval3_status=='1'): ?>
                                <img src="<?php echo e(base_path()); ?><?php echo e($confirm); ?>" style="width:100px;">
                            <?php endif; ?>
                            <br><br>
							<u><?php echo e($row->approvalname3); ?></u><br>
							<?php if($row->approval3_date!=''): ?>
                                <?php echo e($pos[3]); ?>

							<?php else: ?> &nbsp;
							<?php endif; ?>
						</td>
					<?php }?>
					<?php if($row->approvalname4!=''){?>
						<td style="border-top:0px;" class="relative">
                            <?php
                                $ttd_app='/public/approval/'.$row->approval4.'.png';
                                $confirm='/public/approval/confirm.png';
                            ?>
                            <?php if($row->approval4_status=='1'): ?>
                                <img src="<?php echo e(base_path()); ?><?php echo e($confirm); ?>" style="width:100px;">
                            <?php endif; ?>
                            <br><br>
							<u><?php echo e($row->approvalname4); ?></u><br>
							<?php if($row->approval4_date!=''): ?>
                                <?php echo e($pos[4]); ?>

							<?php else: ?> &nbsp;
							<?php endif; ?>
						</td>
					<?php }?>
					<?php if($row->approvalname5!=''){?>
						<td style="border-top:0px;" class="relative">
                            <?php
                                $ttd_app='/public/approval/'.$row->approval5.'.png';
                                $confirm='/public/approval/confirm.png';
                            ?>
                            <?php if($row->approval5_status=='1'): ?>
                                <img src="<?php echo e(base_path()); ?><?php echo e($confirm); ?>" style="width:100px;">
                            <?php endif; ?>
                            <br><br>
							<u><?php echo e($row->approvalname5); ?></u><br>
							<?php if($row->approval5_date!=''): ?>
                                <?php echo e($pos[5]); ?>

							<?php else: ?> &nbsp;
							<?php endif; ?>
						</td>
					<?php }?>
					<?php if($row->approvalname6!=''){?>
						<td style="border-top:0px;" class="relative">
                            <?php
                                $ttd_app='/public/approval/'.$row->approval6.'.png';
                                $confirm='/public/approval/confirm.png';
                            ?>
                            <?php if($row->approval6_status=='1'): ?>
                                <img src="<?php echo e(base_path()); ?><?php echo e($confirm); ?>" style="width:100px;">
                            <?php endif; ?>
                            <br><br>
							<u><?php echo e($row->approvalname6); ?></u><br>
							<?php if($row->approval6_date!=''): ?>
								<?php echo e($pos[6]); ?>

							<?php else: ?> &nbsp;
							<?php endif; ?>
						</td>
					<?php }?>
					<td style="border-top:0px;" class="relative">
                            <?php
                                $ttd_app='/public/approval/'.$row->legalize1.'.png';
                                $confirm='/public/approval/confirm.png';
                            ?>
                            <?php if($row->legalize1_status=='1'): ?>
                                <img src="<?php echo e(base_path()); ?><?php echo e($confirm); ?>" style="width:100px;">
                            <?php endif; ?>
                            <br><br>
						<u><?php echo e($row->legalizename1); ?></u><br>
						<?php if($row->legalize1_date!=''): ?>
                            <?php echo e($pos[7]); ?>

						<?php else: ?> &nbsp;
						<?php endif; ?>
					</td>
					<td style="border-top:0px;" class="relative">
                            <?php
                                $ttd_app='/public/approval/'.$row->legalize2.'.png';
                                $confirm='/public/approval/confirm.png';
                            ?>
                            <?php if($row->legalize2_status=='1'): ?>
                                <img src="<?php echo e(base_path()); ?><?php echo e($confirm); ?>" style="width:100px;">
                            <?php endif; ?>
                            <br><br>
						<u><?php echo e($row->legalizename2); ?></u><br>
						<?php if($row->legalize2_date!=''): ?>
							<?php echo e($pos[8]); ?>

						<?php else: ?> &nbsp;
						<?php endif; ?>
					</td>
					<td style="border-top:0px;" class="relative">
                            <?php
                                $ttd_app='/public/approval/'.$row->legalize3.'.png';
                                $confirm='/public/approval/confirm.png';
                            ?>
                            <?php if($row->legalize3_status=='1'): ?>
                                <img src="<?php echo e(base_path()); ?><?php echo e($confirm); ?>" style="width:100px;">
                            <?php endif; ?>
                            <br><br>
						<u><?php echo e($row->legalizename3); ?></u><br>
						<?php if($row->legalize3_date!=''): ?>
                            <?php echo e($pos[9]); ?>

						<?php else: ?> &nbsp;
						<?php endif; ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php }?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/m_employee/ksk_preview.blade.php ENDPATH**/ ?>