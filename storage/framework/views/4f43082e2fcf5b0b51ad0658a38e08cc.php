<style>
	html{
		margin:30px;
		font-family: Arial, Helvetica, sans-serif;
		font-size:10px;
	}
	th{
		font-weight:bold;
		border:1px solid #000;
	}
	td{
		border:1px solid #000;
		text-align:center;
		font-size:9px;
		padding:3px;
	}
	.polos{
		border:0px;
		text-align:left;
		font-size:9px;
	}
	.polos2{
		border:0px;
		text-align:left;
		font-size:9px;
	}
	.absolute {
		position: absolute;
	}		
	.relative {
		position: relative;
		height:80px;
	}

</style>

<?php $__currentLoopData = $tb_absens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<table style="width:100%;" cellspacing="0">
	<tr>
		<td colspan="3" style="text-align:center;padding:15px;">
			<img src="<?php echo e(public_path('gambar/logosai.png')); ?>" style="width:210px;">
		</td>
		<td colspan="11" style="text-align:center;font-size:16px;font-weight:bold;">DATA PRESENSI<br>&<br>ABSENSI KARYAWAN</td>
		<td colspan="4" rowspan="2" style="text-align:center;padding:10px;">
			ABSENSI RATE
			<div style="text-align:left;padding:10px 20px;">
				Rumus Absensi Rate:<br>
				<img src="<?php echo e(public_path('gambar/rumus_absensirate.png')); ?>" style="width:100px;"><br>
				A = Absensi Rate<br>
				B = Total Jumlah ABsensi<br>
				C = Total Hari Kerja<br>
				D = Total Jumlah Karyawan
			</div>
			Parameter:
			<table style="width:100%" cellspacing="0">
				<tr>
					<td style="font-size:16px;text-align:center;" class="polos">
						<?php 
							$x=(($dts->present_plan*$dts->total_employee)-$dts->total_absen)/($dts->present_plan*$dts->total_employee)*100;
							if($x>='0.98')$hasil='Baik';
							else $hasil='Buruk';
						?>
						<?php echo e($hasil); ?>

					</td>
					<td class="polos">
						98%-100% <br> &lt; 98%
					</td>
					<td class="polos">
						: Baik<br>
						: Buruk
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="3" style="padding:10px 20px;text-align:left;">
			<table style="width:100%" cellspacing="0">
				<tr><td style="width:125px;" class="polos2">NAMA DIVISI</td><td class="polos2">: <?php echo e($dts->dept_code); ?></td></tr>
				<tr><td class="polos2">PERIODE BULAN</td><td class="polos2">: <?php echo strtoupper(date('M - Y',strtotime($dts->start)));?></td></tr>
				<tr><td class="polos2">TOTAL ABSENSI</td><td class="polos2">: <?php echo e($dts->total_absen); ?></td></tr>
				<tr><td class="polos2">TOTAL HARI KERJA</td><td class="polos2">: <?php echo e($dts->present_plan); ?></td></tr>
				<tr><td class="polos2">TOTAL JUMLAH KARYAWAN</td><td class="polos2">: <?php echo e($dts->total_employee); ?></td></tr>
				<tr>
					<td class="polos2">ABSENSI RATE BULAI INI</td><td class="polos2">
						
					: <?php echo e(number_format($x,2)); ?>%
					</td>
				</tr>
			</table>
		</td>
		<td colspan="3"><i>Diketahui & diterima oleh</i><br><br><br>Ttd & Tgl<br><br><br><br><br></td>
		<td colspan="3">
			<i>Diketahui oleh</i><br><br><br>
			<table style="width:100%" cellspacing="5">
				<tr>
					<td style="text-align:center;width:100%;" class="polos relative">
						<?php 
							$ttd_app=public_path('approval/122.png');
							$confirm=public_path('approval/approved.png');
							$filename = public_path('approval/122.png');
							if (file_exists($filename)){?>
								<br><br><img src='<?php echo e($ttd_app); ?>' class="absolute" style="width:140px;top:-10px;left:30px;">
							<?php }else{?>
								<br><br><br><img src='<?php echo e($confirm); ?>' style='height:80px;' class="absolute">
							<?php }
						?>
						<br><br><br><b>Ristiyono</b><br>Dept.Head
					</td>
					<!-- <td style="text-align:center;" class="polos relative">
						<?php 
							//$ttd_app=public_path('approval/1640.png');
							//$confirm=public_path('approval/approved.png');
							//$filename = public_path('approval/1640.png');
							//if (file_exists($filename)){?>
								<br><br><br><img src='<?php echo e($ttd_app); ?>' class="absolute" style="width:100px;top:-10px;left:20px;">
							<?php //}else{?>
								<br><br><br><img src='<?php echo e($confirm); ?>' class="absolute" style="width:100px;top:-30px;left:20px;">
							<?php //}
						?>
						<br><br><b>Springer Andrew</b><br>Asst.Dept.Head
					</td> -->
				</tr>
			</table>
		</td>
		<td colspan="5">
			<i>Disiapkan oleh</i><br><br><br>
			<table style="width:100%" cellspacing="5">
				<tr>
					<td style="text-align:center;width:50%;" class="polos relative">
						<?php 
							$ttd_app=public_path('approval/97.png');
							$confirm=public_path('approval/approved.png');
							$filename = public_path('approval/97.png');
							if (file_exists($filename)){?>
								<br><br><br><img src='<?php echo e($ttd_app); ?>' class="absolute" style="width:100px;top:-10px;left:0px;">
							<?php }else{?>
								<br><br><br><img src='<?php echo e($confirm); ?>' style='height:80px;' class="absolute">
							<?php }
						?>
						<br><br><br><b>Nova Triana</b><br>Sect.Head
					</td>
					<td style="text-align:center;" class="polos relative">
						<?php 
							$ttd_app=public_path('approval/101.png');
							$confirm=public_path('approval/approved.png');
							$filename = public_path('approval/101.png');
							if (file_exists($filename)){?>
								<br><br><br><img src='<?php echo e($ttd_app); ?>' class="absolute" style="width:100px;top:-10px;left:0px;">
							<?php }else{?>
								<br><br><br><img src='<?php echo e($confirm); ?>' class="absolute" style="width:60px;top:-30px;left:20px;">
							<?php }
						?>
						<br><br><br><b>Angella P</b><br>Pers. Affair
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<th rowspan="2">NO</th>
		<th rowspan="2" style="width:120px;">NAMA LENGKAP</th>
		<th rowspan="2" style="width:70px;">NIK</th>
		<th colspan="6">DATA PERSENTASE KEHADIRAN & KETEPATAN WAKTU</th>
		<th colspan="2">TERLAMBAT</th>
		<th colspan="2">IJIN</th>
		<th rowspan="2">CUTI</th>
		<th colspan="3">KETERANGAN TIDAK HADIR</th>
		<th rowspan="2">TOTAL</th>
	</tr>
	<tr>
		<th>Kehadiran<br>100%</th>
		<th>Realisasi</th>
		<th>%Kehadiran</th>
		<th>Ketepatan<br>Waktu 100%</th>
		<th>Realisasi</th>
		<th>%Ketepatan<br>Waktu</th>
		<th>X</th>
		<th>Menit</th>
		<th>1/2 Hari</th>
		<th>Keluar<br>(Menit)</th>
		<th>Sakit</th>
		<th>Ijin</th>
		<th>Alpa</th>
	</tr>
	<?php 
		$no=0;
		$hrate=0;
		$drate=0;
		$kali=0;
		$telat=0;
		$stengah=0;
		$keluar=0;
		$cuti=0;
		$sakit=0;
		$izin=0;
		$alpa=0;
		$total=0;
		$jabatan='';
	?>
	<?php $__currentLoopData = $tb_absen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	<?php 
		$hrate=$hrate+$dt->hour_rate;
		$drate=$drate+$dt->present_rate;
		$kali=$kali+$dt->terlambat;
		$telat=$telat+$dt->terlambat_minutes;
		// $stengah=$stengah+$dt->setengah_minutes;
		$stengah=$stengah+($dt->setengah_minutes/240);
		$keluar=$keluar+$dt->keluar_minutes;
		$cuti=$cuti+$dt->cuti;
		$sakit=$sakit+$dt->sakit;
		$izin=$izin+$dt->izin;
		$alpa=$alpa+$dt->alpa;
		$total=$total+$dt->absent;
	?>
	<?php if($dt->jabatan!=$jabatan){$jabatan=$dt->jabatan;?>
		<tr><td colspan="18" style="text-align:left;background:#000;color:#FFF;font-weight:bold;padding-left:10px;font-size:12px;"><?php echo e(strtoupper($dt->jabatan)); ?></td></tr>
	<?php }?>
	
	<tr>
		<td><?php $no++;echo $no;?></td>
		<td style="text-align:left;"><?php echo e(strtoupper($dt->employee_name)); ?></td>
		<td><?php echo e($dt->NIK); ?></td>
		<td><?php echo e($dt->present_plan); ?></td>
		<td><?php echo e($dt->present_actual); ?></td>
		<td><?php echo e($dt->present_rate); ?>%</td>
		<td><?php echo e($dt->hour_plan); ?></td>
		<td><?php echo e($dt->hour_actual); ?></td>
		<td><?php echo e($dt->hour_rate); ?>%</td>
		<td><?php echo e($dt->terlambat); ?></td>
		<td><?php echo e($dt->terlambat_minutes); ?></td>
		<td>
			<?php $banyak=$dt->setengah_minutes/240;?>
			<?php echo e($banyak); ?>

		</td>
		<td><?php echo e($dt->keluar_minutes); ?></td>
		<td><?php echo e($dt->cuti); ?></td>
		<td><?php echo e($dt->sakit); ?></td>
		<td><?php echo e($dt->izin); ?></td>
		<td><?php echo e($dt->alpa); ?></td>
		<td><?php echo e($dt->absent); ?></td>
	</tr>
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	<tr style="background:#CCC;">
		<td colspan="3">TOTAL <?php echo e($dts->divisi); ?>: <?php echo e($no); ?> Emplopyees</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><?php $drates=$drate/$no;echo number_format($x,2);?>%</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><?php $hrates=$hrate/$no;echo number_format($hrates,2);?>%</td>
		<td><?php echo e($kali); ?></td>
		<td><?php echo e($telat); ?></td>
		<td><?php echo e($stengah); ?></td>
		<td><?php echo e($keluar); ?></td>
		<td><?php echo e($cuti); ?></td>
		<td><?php echo e($sakit); ?></td>
		<td><?php echo e($izin); ?></td>
		<td><?php echo e($alpa); ?></td>
		<td><?php echo e($total); ?></td>
	</tr>
</table>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\user\m_absency\absencyrate.blade.php ENDPATH**/ ?>