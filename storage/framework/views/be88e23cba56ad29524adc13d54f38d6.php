<style>
	html{
		margin:40px;
	}
	ol li{
		padding-bottom:5px;
	}
	.tabelot th{
		text-align:center;
		font-weight:normal;
		font-size:8px;
	}
	.tabelot td{
		text-align:center;
		font-size:10px;
	}
	.absolute {
		position: absolute;
		width:110px;
		left:0px;
		top:-10px;
	}		
	.relative {
		left:0px;
		position: relative;
		height:20px;
	}	
	.page-break {
    page-break-after: always;
}
</style>
<?php if($tb_overtime_detail_count > 0 ) { ?>
	<?php $__currentLoopData = $tb_overtime; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	<?php $adminspl=$dt->admin;?>
				<table style="width:100%;" border="1" cellspacing="0" class="tabelot">
					<tr>
						<td colspan="4" style="padding:5px;"><img src="<?php echo e(base_path()); ?>/public/gambar/logosai.png" style="width:160px;"></td>
						<td colspan="3" style="text-align:center;font-size:12px;"><b><u>OVERTIME (OT) ORDER FORM</u></b><br><i>Surat Perintah Lembur  ( SPL )</i></td>
						<td colspan="6" style="width:220px;text-align:left;">
							<table style="width:100%;" border="0">
								<tr>
									<td style="width:80px;text-align:left;">Nompr OT/SPL</td>
									<td title="<?php echo e($dt->id_overtime); ?>" style="text-align:left;">: <?php echo e($dt->id_overtime); ?></td>
								</tr>
								<tr>
									<td style="text-align:left;">Divisi</td>
									<td style="text-align:left;">: <?php echo e($dt->dept_name); ?></td>
								</tr>
								<tr>
									<td style="width:80px;text-align:left;">Day, Date/Hari, Tgl</td>
									<td style="text-align:left;">: <?php echo date('l,d F Y',strtotime($dt->ot_date));?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="13" style="border:1px solid #000;padding:5px;text-align:left;">
							<ol style="font-size:9px;">
								<li>Except permanent overtime, all overtime planing must be requested and conducted by using or based on current overtime order form Kecuali lembur tetap semua rencana pelaksanaan kerja lembur wajib dimohonkan serta dilaksanakan dengan menggunakan atau berdasarkan Surat Perintah Lembur yang berlaku.</li>
								<li>Overtime with no overtime order form, will not be paid  Lembur yang tidak menggunakan Surat Perintah Lembur tidak akan dibayar.</li>
								<li>Planning of overtime must be submitted to HRD Departement, not more than 1 (one) hour before overtime is conducted  Rencana  pelaksanaan  lembur  wajib  diajukan  selambat - lambatnya 1 (satu) jam sebelum lembur tersebut dilaksanakan</li>
								<li>In emergency cases, overtime order can be submitted 1 x 24 hours after overtime coducted Dalam keadaan darurat dimana ketentuan no. 3 tidak dapat dilaksanakan, maka Surat Perintah Lembur dapat dibuat dan diajukan paling lambat 1 x 24 jam setelah lembur dilaksanakan.</li>
								<li>If break the provisions no. 3 & 4 then cost of overtime will not be paid Apabila melanggar ketentuan no 3 & 4, maka biaya lembur tidak akan dibayar.</li>
							</ol>
						</td>
					</tr>
					<tr>
						<th rowspan="2" style="width:20px;font-size:10px;"><b>NO</b><br><i>No</i></th>
						<th rowspan="2" style="width:90px;font-size:10px;"><b>NAME</b><br><i>Name</i></th>
						<th rowspan="2" style="width:60px;font-size:10px;"><b>NIK</b><br><i>NIK</i></th>
						<th rowspan="2" style="width:60px;font-size:9px;"><b>DIV/SUB DIV</b><br><i>Div. & Sub Div.</i></th>
						<th rowspan="2" style="font-size:9px;"><b>REASON/TARGET</b><br><i>Alasan & Hasil Lembur yang harus dicapai</i></th>
						<th colspan="3"><b>OVER TIME PLANNING (Rencana)</b></th>
						<th colspan="3"><b>OVER TIME ACTUAL (Kenyataan)</b></th>
						<th rowspan="2" style="width:30px;font-size:7px;"><b>ACT HOURS</b><br><i>Realisasi Jam OT</i></th>
						<th rowspan="2" style="width:30px;font-size:7px;"><b>OT CONV.</b><br><i>Konfersi Jam OT</i></th>
					</tr>
					<tr>
						<th style="width:30px;font-size:7px;"><b>START</b><br><i>mulai Jam</i></th>
						<th style="width:30px;font-size:7px;"><b>FINISH</b><br><i>selesai Jam</i></th>
						<th style="width:40px;font-size:7px;"><b>EMP.<br>SIGNED</b><br><i>tth Karyawan</i></th>
						<th style="width:30px;font-size:7px;"><b>START</b><br><i>mulai Jam</i></th>
						<th style="width:30px;font-size:7px;"><b>FINISH</b><br><i>selesai Jam</i></th>
						<th style="width:40px;font-size:7px;"><b>EMP.<br>SIGNED</b><br><i>tth Karyawan</i></th>
					</tr>
					<?php $no=0;$hours_acum=0;$convertion_acum=0;?>
					<?php $__currentLoopData = $tb_overtime_detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php $no++;$hours_acum=$hours_acum+$dt2->hours_act;$convertion_acum=$convertion_acum+$dt2->hours_convertion;?>
					<tr>
						<td style="font-size:10px;padding:3px;"><?php echo e($no); ?></td>
						<td style="text-align:left;padding:4px"><?php echo strtoupper($dt2->employee_name);?></td>
						<td style="font-size:10px;"><?php echo e($dt2->NIK); ?></td>
						<td style="font-size:8px;"><?php echo e($dt2->dept_code); ?></td>
						<td style="font-size:9px;text-align:left;padding:4px;"><?php echo e($dt2->reason); ?></td>
						<td><?php echo date('H:i',strtotime($dt2->start_plan));?></td>
						<td><?php echo date('H:i',strtotime($dt2->finish_plan));?></td>
						<td><?php if($dt2->sign_before==1||$dt2->sign_before==3)echo "Confirm";?></td>
						<td><?php if($dt2->sign_after==0)echo "";else echo date('H:i',strtotime($dt2->start_act));?></td>
						<td><?php if($dt2->sign_after==0)echo "";else echo date('H:i',strtotime($dt2->finish_act));?></td>
						<td><?php if($dt2->sign_after==1||$dt2->sign_after==3)echo "Confirm";?></td>
						<td><?php if($dt2->sign_after==0)echo "";else echo number_format($dt2->hours_act,2);?></td>
						<td><?php if($dt2->sign_after==0)echo "";else echo number_format($dt2->hours_convertion,2);?></td>
					</tr>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
					<?php $batas=10; if($no<$batas){for($i=$no+1;$i<=$no+5;$i++){?>
					<tr>
						<td style="font-size:12px;padding:3px;"><?php echo e($i); ?></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<?php }}?>
					<tr>
						<td style="border-right:0px;" colspan="3">&nbsp;</td>
						<td style="border-left:0px;border-right:0px;"><input type="text" value="<?php echo e($no); ?>" style="width:50px;height:20px;font-size:16px;"></td>
						<td colspan="1" style="border-left:0px;text-align:left;">Person/s (<i>Orang</i>)</td>
						<td colspan="4">&nbsp;</td>
						<td colspan="2" style="font-size:9px;"><b>Total OT Hours</b><br><i>Total Jam Lembur</i></td>
						<td style="font-size:14px;"><?php if($dt2->status==0)echo "";else echo number_format($hours_acum,'2');?></td>
						<td style="font-size:14px;"><?php if($dt2->status==0)echo "";else echo number_format($convertion_acum,'2');?></td>
					</tr>
					<tr>
						<td colspan="7" style="padding:3px;">
							Overtime plan legalized / Pengesahan Rencana Lembur
						</td>
						<td colspan="6">
							Overtime realization legalized / Pengesahan Realisasi Lembur
						</td>
					</tr>
					<tr>
						<td colspan="7" style="padding:10px;">
							<table style="width:100%;" border="0">
								<tr>
									<td style="width:25%;">Order by<br>Diperintah oleh</td>
									<td style="width:25%;">
										<?php if($dt->disetujui!='')echo "Approved by<br>Disetujui oleh";?>
									</td>
									<td style="width:25%;">
										<?php if($dt->diketahui!='')echo "Seen by<br>Diketahui oleh";?>
									</td>
									<td style="width:25%;">Seen by<br>Diketahui oleh</td>
								</tr>
								<tr>
									<td style="height:30px;" class="relative">
										<?php 
											$reject=base_path().'/public/approval/rejected.png';
											if($dt->status_diperintah=='1'){
												$ttd_app=base_path().'/public/approval/'.$dt->diperintah.'.png';
												$confirm=base_path().'/public/approval/confirm.png';
												$filename = 'c:/xampp/htdocs/public/approval/'.$dt->diperintah.'.png';
												if (file_exists($filename)){?>
													<img src='<?php echo e($ttd_app); ?>' class="absolute">
												<?php }else{?>
													<img src='<?php echo e($confirm); ?>' style='width:100px;height:40px;'>
												<?php }
											}elseif($dt->status_diperintah=='3'){?>
												<?php $lock=base_path().'/public/approval/lock.png';?>
												<?php if($dt->autoCancel==1): ?>
													<!-- <img src='<?php echo e($lock); ?>' style='width:100px;height:40px;'> -->
													Locked
												<?php else: ?>
													<img src='<?php echo e($reject); ?>' style='width:100px;height:40px;'>
												<?php endif; ?>
											<?php }
										?>
									</td>
									<td class="relative">
										<?php 
											if($dt->status_disetujui=='1'){
												$ttd_app=base_path().'/public/approval/'.$dt->disetujui.'.png';
												$confirm=base_path().'/public/approval/confirm.png';
												$reject=base_path().'/public/approval/rejected.png';
												$filename = 'c:/xampp/htdocs/public/approval/'.$dt->disetujui.'.png';
												if (file_exists($filename)){?>
													<img src='<?php echo e($ttd_app); ?>' class="absolute">
												<?php }else{?>
													<img src='<?php echo e($confirm); ?>' style='width:100px;height:40px;'>
												<?php }
											}elseif($dt->status_disetujui=='3'){?>
												<img src='<?php echo e($reject); ?>' style='width:100px;height:40px;'>
											<?php }
										?>
									</td>
									<td class="relative">
										<?php 
											if($dt->status_diketahui=='1'){
												$ttd_app=base_path().'/public/approval/'.$dt->diketahui.'.png';
												$confirm=base_path().'/public/approval/confirm.png';
												$reject=base_path().'/public/approval/rejected.png';
												$filename = 'c:/xampp/htdocs/public/approval/'.$dt->diketahui.'.png';
												if (file_exists($filename)){?>
													<img src='<?php echo e($ttd_app); ?>' class="absolute">
												<?php }else{?>
													<img src='<?php echo e($confirm); ?>' style='width:100px;height:40px;'>
												<?php }
											}elseif($dt->status_diketahui=='3'){?>
												<img src='<?php echo e($reject); ?>' style='width:100px;height:40px;'>
											<?php }
										?>
									</td>
									<td class="relative">
										<?php if($dt->dicatat!=''){?>
											<?php $ttd_dik=base_path().'/public/approval/'.$dt->dicatat.'.png';?>
											<?php if($dt->status_dicatat=='1'){?><img src="<?php echo e($ttd_dik); ?>" class="absolute"><?php }?>
											<?php if($dt->status_dicatat=='3'){?><img src="<?php echo e(base_path()); ?>/public/approval/rejected.png" style="width:100px;height:40px;"><?php }?>
										<?php }?>
									</td>
								</tr>
								<tr>
									<td>
										<u><?php if($dt->date_diperintah!='')echo date('d-M-Y H:i',strtotime($dt->date_diperintah));?></u><br>
										<b><?php echo e($diperintah); ?></b><br>(<?php echo e($diperintah_j); ?>)
									</td>
									<td>
										<?php if($dt->disetujui!=''){?>
											<u><?php if($dt->date_disetujui!='')echo date('d-M-Y H:i',strtotime($dt->date_disetujui));?></u><br>
											<b><?php echo e($disetujui); ?></b><br> (<?php echo e($disetujui_j); ?>)
										<?php }?>
									</td>
									<td>
										<?php if($dt->diketahui!=''){?>
											<u><?php if($dt->date_diketahui!='')echo date('d-M-Y H:i',strtotime($dt->date_diketahui));?></u><br>
											<b><?php echo e($diketahui); ?></b><br>(<?php echo e($diketahui_j); ?>)
										<?php }?>
									</td>
									<td>
										<u><?php if($dt->date_dicatat!='')echo date('d-M-Y H:i',strtotime($dt->date_dicatat));?></u><br>
										<b><?php echo e($dicatat); ?></b><br>(<?php echo e($dicatat_j); ?>)
									</td>
								</tr>
							</table>
						</td>
						<td colspan="6">
							<table style="width:100%;" border="0">
								<tr>
									<td style="width:50%;">Checked & Approved by<br>Diperiksa & Disetujui</td>
									<td style="width:50%;">Recorded & Paid by<br>Dicatat & Dibayar oleh</td>
								</tr>
								<tr>
									<td style="height:30px;" class="relative">
										<?php 
											if($dt->status_approve=='1'){
												$ttd_app=base_path().'/public/approval/'.$dt->approve.'.png';
												$confirm=base_path().'/public/approval/confirm.png';
												$reject=base_path().'/public/approval/rejected.png';
												$filename = 'c:/xampp/htdocs/public/approval/'.$dt->approve.'.png';
												if (file_exists($filename)){?>
													<img src='<?php echo e($ttd_app); ?>' class="absolute">
												<?php }else{?>
													<img src='<?php echo e($confirm); ?>' style='width:100px;height:40px;'>
												<?php }
											}elseif($dt->status_approve=='3'){?>
												<img src='<?php echo e($reject); ?>' style='width:100px;height:40px;'>
											<?php }
										?>
									</td>
									<td class="relative">
										<?php 
											if($dt->status_paid=='1'){
												$ttd_app=base_path().'/public/approval/'.$dt->paid.'.png';
												$confirm=base_path().'/public/approval/confirm.png';
												$reject=base_path().'/public/approval/rejected.png';
												$filename = 'c:/xampp/htdocs/public/approval/'.$dt->paid.'.png';
												if (file_exists($filename)){?>
													<img src='<?php echo e($ttd_app); ?>' class="absolute">
												<?php }else{?>
													<img src='<?php echo e($confirm); ?>' style='width:100px;height:40px;'>
												<?php }
											}elseif($dt->status_paid=='3'){?>
												<img src='<?php echo e($reject); ?>' style='width:100px;height:40px;'>
											<?php }
										?>
									</td>
								</tr>
								<tr>
									<td>
										<u><?php if($dt->date_approve!='')echo date('d-M-Y H:i',strtotime($dt->date_approve));?></u><br>
										<b><?php echo e($approve); ?></b><br>(<?php echo e($approve_j); ?>)
									</td>
									<td>
										<u><?php if($dt->date_paid!='')echo date('d-M-Y H:i',strtotime($dt->date_paid));?></u><br>
										<b><?php echo e($paid); ?></b><br>(Administration)
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="13" style="border:1px solid #000;padding:5px;text-align:center;">
							<div style="font-size:14px;padding:5px;border:0px solid #000;">
								"SPL ini dibuat berdasarkan kesepakatan dan persetujuan kedua belah pihak"
							</div>
							<div style="font-size:8px;padding:5px;border:0px solid #000;">
								1. Original (White/Putih : Finance/Cashier   2. Copy 1 (Blue/Biru) : Relateddivision / Bagian yg lembur   3. Copy 2 (Green/Hijau) : Personnel / Payroll   4. Copy 3 (Yellow/Kuning) : Security / Keamanan 												
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="13" style="border:1px solid #000;padding:5px;text-align:left;">
							<div style="font-size:10px;padding:5px;border:0px;">
								<label>CREATED BY <?php echo e($adminspl); ?> </label>
								<ul>
									<li></li>
									<?php $__currentLoopData = $tb_pesan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<li><?php echo e($dt->penulis); ?>: <?php echo e($dt->pesan); ?></li>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</ul>
								
							</div>
						</td>
					</tr>
				</table>
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	<?php } if($dpk_count > 0 ){	?>
		<?php if($tb_overtime_detail_count > 0 ) { ?>
			<div class="page-break"></div>
		<?php } ?>
	<?php $__currentLoopData = $tb_overtime; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	<table style="width:100%;" border="1" cellspacing="0" class="tabelot">
		<tr>
        <td colspan="4" style="padding:5px;"><img src="<?php echo e(base_path()); ?>/public/gambar/logosai.png" style="width:160px;"></td>
			<td colspan="3" style="text-align:center;font-size:12px;"><b><u>OVERTIME (OT) ORDER FORM</u></b><br><i>Surat Perintah Lembur  ( SPL )</i></td>
			<td colspan="6" style="width:220px;text-align:left;">
				<table style="width:100%">
					<tr>
						<td style="width:80px;text-align:left;">Nompr OT/SPL</td>
						<td title="<?php echo e($dt->id_overtime); ?>" style="text-align:left;">: <?php echo e($dt->id_overtime); ?></td>
					</tr>
					<tr>
						<td style="text-align:left;">Divisi</td>
						<td style="text-align:left;">: <?php echo e($dt->dept_name); ?></td>
					</tr>
					<tr>
						<td style="width:80px;text-align:left;">Day, Date/Hari, Tgl</td>
						<td style="text-align:left;">: <?php echo date('l,d F Y',strtotime($dt->ot_date));?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="13" style="border:1px solid #000;padding:5px;text-align:left;">
				<ol style="font-size:9px;">
					<li>Except permanent overtime, all overtime planing must be requested and conducted by using or based on current overtime order form Kecuali lembur tetap semua rencana pelaksanaan kerja lembur wajib dimohonkan serta dilaksanakan dengan menggunakan atau berdasarkan Surat Perintah Lembur yang berlaku.</li>
					<li>Overtime with no overtime order form, will not be paid  Lembur yang tidak menggunakan Surat Perintah Lembur tidak akan dibayar.</li>
					<li>Planning of overtime must be submitted to HRD Departement, not more than 1 (one) hour before overtime is conducted  Rencana  pelaksanaan  lembur  wajib  diajukan  selambat - lambatnya 1 (satu) jam sebelum lembur tersebut dilaksanakan</li>
					<li>In emergency cases, overtime order can be submitted 1 x 24 hours after overtime coducted Dalam keadaan darurat dimana ketentuan no. 3 tidak dapat dilaksanakan, maka Surat Perintah Lembur dapat dibuat dan diajukan paling lambat 1 x 24 jam setelah lembur dilaksanakan.</li>
					<li>If break the provisions no. 3 & 4 then cost of overtime will not be paid Apabila melanggar ketentuan no 3 & 4, maka biaya lembur tidak akan dibayar.</li>
				</ol>
			</td>
		</tr>
		<tr>
			<th rowspan="2" style="width:20px;font-size:10px;"><b>NO</b><br><i>No</i></th>
			<th rowspan="2" style="width:90px;font-size:10px;"><b>NAME</b><br><i>Name</i></th>
			<th rowspan="2" style="width:60px;font-size:10px;"><b>NIK</b><br><i>NIK</i></th>
			<th rowspan="2" style="width:60px;font-size:9px;"><b>DIV/SUB DIV</b><br><i>Div. & Sub Div.</i></th>
			<th rowspan="2" style="font-size:9px;"><b>REASON/TARGET</b><br><i>Alasan & Hasil Lembur yang harus dicapai</i></th>
			<th colspan="3"><b>OVER TIME PLANNING (Rencana)</b></th>
			<th colspan="3"><b>OVER TIME ACTUAL (Kenyataan)</b></th>
			<th rowspan="2" style="width:30px;font-size:7px;"><b>ACT HOURS</b><br><i>Realisasi Jam OT</i></th>
			<th rowspan="2" style="width:30px;font-size:7px;"><b>OT CONV.</b><br><i>Konfersi Jam OT</i></th>
		</tr>
		<tr>
			<th style="width:30px;font-size:7px;"><b>START</b><br><i>mulai Jam</i></th>
			<th style="width:30px;font-size:7px;"><b>FINISH</b><br><i>selesai Jam</i></th>
			<th style="width:40px;font-size:7px;"><b>EMP.<br>SIGNED</b><br><i>tth Karyawan</i></th>
			<th style="width:30px;font-size:7px;"><b>START</b><br><i>mulai Jam</i></th>
			<th style="width:30px;font-size:7px;"><b>FINISH</b><br><i>selesai Jam</i></th>
			<th style="width:40px;font-size:7px;"><b>EMP.<br>SIGNED</b><br><i>tth Karyawan</i></th>
		</tr>
		<?php $no=0;$hours_acum=0;$convertion_acum=0;?>
		<?php $__currentLoopData = $tb_emp_dpk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<?php $no++;$hours_acum=$hours_acum+$dt2->hours_act;$convertion_acum=$convertion_acum+$dt2->hours_convertion;?>
		<tr>
			<td style="font-size:10px;padding:3px;"><?php echo e($no); ?></td>
			<td style="text-align:left;padding:4px"><?php echo strtoupper($dt2->employee_name);?></td>
			<td style="font-size:10px;"><?php echo e($dt2->NIK); ?></td>
			<td style="font-size:8px;"><?php echo e($dt2->dept_code); ?></td>
			<td style="font-size:9px;text-align:left;padding:4px;"><?php echo e($dt2->reason); ?></td>
			<td><?php echo date('H:i',strtotime($dt2->start_plan));?></td>
			<td><?php echo date('H:i',strtotime($dt2->finish_plan));?></td>
			<td><?php if($dt2->sign_before==1||$dt2->sign_before==3)echo "Confirm";?></td>
			<td><?php if($dt2->sign_after==0)echo "";else echo date('H:i',strtotime($dt2->start_act));?></td>
			<td><?php if($dt2->sign_after==0)echo "";else echo date('H:i',strtotime($dt2->finish_act));?></td>
			<td><?php if($dt2->sign_after==1||$dt2->sign_after==3)echo "Confirm";?></td>
			<td><?php if($dt2->sign_after==0)echo "";else echo number_format($dt2->hours_act,2);?></td>
			<td><?php if($dt2->sign_after==0)echo "";else echo number_format($dt2->hours_convertion,2);?></td>
		</tr>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
		<?php $batas=10; if($no<$batas){for($i=$no+1;$i<=$no+5;$i++){?>
		<tr>
			<td style="font-size:12px;padding:3px;"><?php echo e($i); ?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<?php }}?>
		<tr>
			<td style="border-right:0px;" colspan="3">&nbsp;</td>
			<td style="border-left:0px;border-right:0px;"><input type="text" value="<?php echo e($no); ?>" style="width:50px;height:20px;font-size:16px;"></td>
			<td colspan="1" style="border-left:0px;text-align:left;">Person/s (<i>Orang</i>)</td>
			<td colspan="4">&nbsp;</td>
			<td colspan="2" style="font-size:9px;"><b>Total OT Hours</b><br><i>Total Jam Lembur</i></td>
			<td style="font-size:14px;"><?php if($dt2->status==0)echo "";else echo number_format($hours_acum,'2');?></td>
			<td style="font-size:14px;"><?php if($dt2->status==0)echo "";else echo number_format($convertion_acum,'2');?></td>
		</tr>
		<tr>
			<td colspan="7" style="padding:3px;">
				Overtime plan legalized / Pengesahan Rencana Lembur
			</td>
			<td colspan="6">
				Overtime realization legalized / Pengesahan Realisasi Lembur
			</td>
		</tr>
		<tr>
			<td colspan="7" style="padding:10px;">
				<table style="width:100%;">
					<tr>
						<td style="width:25%;">Order by<br>Diperintah oleh</td>
						<td style="width:25%;">
							<?php if($dt->disetujui!='')echo "Approved by<br>Disetujui oleh";?>
						</td>
						<td style="width:25%;">
							<?php if($dt->diketahui!='')echo "Seen by<br>Diketahui oleh";?>
						</td>
						<td style="width:25%;">Seen by<br>Diketahui oleh</td>
					</tr>
					<tr>
						<td style="height:30px;" class="relative">
							<?php 
								$reject=base_path().'/public/approval/rejected.png';
								if($dt->status_diperintah=='1'){
									$ttd_app=base_path().'/public/approval/'.$dt->diperintah.'.png';
									$confirm=base_path().'/public/approval/confirm.png';
									$filename = 'c:/xampp/htdocs/public/approval/'.$dt->diperintah.'.png';
									if (file_exists($filename)){?>
										<img src='<?php echo e($ttd_app); ?>' class="absolute">
									<?php }else{?>
										<img src='<?php echo e($confirm); ?>' style='width:100px;height:40px;'>
									<?php }
								}elseif($dt->status_diperintah=='3'){?>
									<img src='<?php echo e($reject); ?>' style='width:100px;height:40px;'>
								<?php }
							?>
						</td>
						<td class="relative">
							<?php 
								if($dt->status_disetujui=='1'){
									$ttd_app=base_path().'/public/approval/'.$dt->disetujui.'.png';
									$confirm=base_path().'/public/approval/confirm.png';
									$reject=base_path().'/public/approval/rejected.png';
									$filename = 'c:/xampp/htdocs/public/approval/'.$dt->disetujui.'.png';
									if (file_exists($filename)){?>
										<img src='<?php echo e($ttd_app); ?>' class="absolute">
									<?php }else{?>
										<img src='<?php echo e($confirm); ?>' style='width:100px;height:40px;'>
									<?php }
								}elseif($dt->status_disetujui=='3'){?>
									<img src='<?php echo e($reject); ?>' style='width:100px;height:40px;'>
								<?php }
							?>
						</td>
						<td class="relative">
							<?php 
								if($dt->status_diketahui=='1'){
									$ttd_app=base_path().'/public/approval/'.$dt->diketahui.'.png';
									$confirm=base_path().'/public/approval/confirm.png';
									$reject=base_path().'/public/approval/rejected.png';
									$filename = 'c:/xampp/htdocs/public/approval/'.$dt->diketahui.'.png';
									if (file_exists($filename)){?>
										<img src='<?php echo e($ttd_app); ?>' class="absolute">
									<?php }else{?>
										<img src='<?php echo e($confirm); ?>' style='width:100px;height:40px;'>
									<?php }
								}elseif($dt->status_diketahui=='3'){?>
									<img src='<?php echo e($reject); ?>' style='width:100px;height:40px;'>
								<?php }
							?>
						</td>
						<td class="relative">
							<?php if($dt->dicatat!=''){?>
								<?php $ttd_dik=base_path().'/public/approval/'.$dt->dicatat.'.png';?>
								<?php if($dt->status_dicatat=='1'){?><img src="<?php echo e($ttd_dik); ?>" class="absolute"><?php }?>
								<?php if($dt->status_dicatat=='3'){?><img src="<?php echo e(base_path()); ?>/public/approval/rejected.png" style="width:100px;height:40px;"><?php }?>
							<?php }?>
						</td>
					</tr>
					<tr>
						<td>
							<u><?php if($dt->date_diperintah!='')echo date('d-M-Y H:i',strtotime($dt->date_diperintah));?></u><br>
							<b><?php echo e($diperintah); ?></b><br>(<?php echo e($diperintah_j); ?>)
						</td>
						<td>
							<?php if($dt->disetujui!=''){?>
								<u><?php if($dt->date_disetujui!='')echo date('d-M-Y H:i',strtotime($dt->date_disetujui));?></u><br>
								<b><?php echo e($disetujui); ?></b><br> (<?php echo e($disetujui_j); ?>)
							<?php }?>
						</td>
						<td>
							<?php if($dt->diketahui!=''){?>
								<u><?php if($dt->date_diketahui!='')echo date('d-M-Y H:i',strtotime($dt->date_diketahui));?></u><br>
								<b><?php echo e($diketahui); ?></b><br>(<?php echo e($diketahui_j); ?>)
							<?php }?>
						</td>
						<td>
							<u><?php if($dt->date_dicatat!='')echo date('d-M-Y H:i',strtotime($dt->date_dicatat));?></u><br>
							<b><?php echo e($dicatat); ?></b><br>(<?php echo e($dicatat_j); ?>)
						</td>
					</tr>
				</table>
			</td>
			<td colspan="6">
				<table style="width:100%;">
					<tr>
						<td style="width:50%;">Checked & Approved by<br>Diperiksa & Disetujui</td>
						<td style="width:50%;">Recorded & Paid by<br>Dicatat & Dibayar oleh</td>
					</tr>
					<tr>
						<td style="height:30px;" class="relative">
							<?php 
								if($dt->status_approve=='1'){
									$ttd_app=base_path().'/public/approval/'.$dt->approve.'.png';
									$confirm=base_path().'/public/approval/confirm.png';
									$reject=base_path().'/public/approval/rejected.png';
									$filename = 'c:/xampp/htdocs/public/approval/'.$dt->approve.'.png';
									if (file_exists($filename)){?>
										<img src='<?php echo e($ttd_app); ?>' class="absolute">
									<?php }else{?>
										<img src='<?php echo e($confirm); ?>' style='width:100px;height:40px;'>
									<?php }
								}elseif($dt->status_approve=='3'){?>
									<img src='<?php echo e($reject); ?>' style='width:100px;height:40px;'>
								<?php }
							?>
						</td>
						<td class="relative">
							<?php 
								if($dt->status_paid=='1'){
									$ttd_app=base_path().'/public/approval/'.$dt->paid.'.png';
									$confirm=base_path().'/public/approval/confirm.png';
									$reject=base_path().'/public/approval/rejected.png';
									$filename = 'c:/xampp/htdocs/public/approval/'.$dt->paid.'.png';
									if (file_exists($filename)){?>
										<img src='<?php echo e($ttd_app); ?>' class="absolute">
									<?php }else{?>
										<img src='<?php echo e($confirm); ?>' style='width:100px;height:40px;'>
									<?php }
								}elseif($dt->status_paid=='3'){?>
									<img src='<?php echo e($reject); ?>' style='width:100px;height:40px;'>
								<?php }
							?>
						</td>
					</tr>
					<tr>
						<td>
							<u><?php if($dt->date_approve!='')echo date('d-M-Y H:i',strtotime($dt->date_approve));?></u><br>
							<b><?php echo e($approve); ?></b><br>(<?php echo e($approve_j); ?>)
						</td>
						<td>
							<u><?php if($dt->date_paid!='')echo date('d-M-Y H:i',strtotime($dt->date_paid));?></u><br>
							<b><?php echo e($paid); ?></b><br>(Administration)
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="13" style="border:1px solid #000;padding:5px;text-align:left;">
				<div style="font-size:8px;padding:5px;border:0px solid #000;">
					1. Original (White/Putih : Finance/Cashier   2. Copy 1 (Blue/Biru) : Relateddivision / Bagian yg lembur   3. Copy 2 (Green/Hijau) : Personnel / Payroll   4. Copy 3 (Yellow/Kuning) : Security / Keamanan 												
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="13" style="border:1px solid #000;padding:5px;text-align:left;">
				<div style="font-size:10px;padding:5px;border:0px;">
					<label>Notes <?php echo e($adminspl); ?>:</label>
					<ul>
						<li><?php echo e($adminspl); ?></li>
						<?php $__currentLoopData = $tb_pesan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<li><?php echo e($dt->penulis); ?>: <?php echo e($dt->pesan); ?></li>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</ul>
					
				</div>
			</td>
		</tr>
	</table>
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	<?php } ?>

<?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/m_overtime/printviewspl.blade.php ENDPATH**/ ?>