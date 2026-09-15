<style>
	html{
		margin:25px 20px 5px 20px;
	}
	td{
		font-family: Arial, Helvetica, sans-serif;
	}
	.seling{
		font-family: Arial, Helvetica, sans-serif;
		font-size:10px;
	}
	.kiri{
		text-align:left;
	}
	.tengah{
		text-align:center;
	}
	.kanan{
		text-align:right;
	}
	.judul{
		font-size:24px;
		font-weight: bold;
	}
	.besar{
		font-size:16px;
	}
	.sedang{
		font-size:10px;
	}
	.kecil{
		font-size:5px;
	}
	.garis{
		border-bottom:1px solid #999;
		font-family: "Courier", "Times", "serif";
		font-size:12px;
	}
	.ttd{
		border-bottom:1px solid #999;
		font-family: "Courier", "Times", "serif";
		font-size:9px;
	}
</style>
<?php $__currentLoopData = $tb_izin; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div style="border:1px solid #000;padding:5px 20px;">
	<?php
		if($dt->category=='A'){
			$nomor='001';
			$judul='SURAT IZIN TIDAK MASUK KERJA';
			$konten='MOHON IZIN UNTUK TIDAK MASUK KERJA PADA:';
		}
		if($dt->category=='B'){
			$nomor='002';
			$judul='SURAT IZIN 1/2 HARI KERJA';
			$konten='MOHON IZIN UNTUK TIDAK MELANJUTKAN PEKERJAAN:';
		}
		if($dt->category=='C'){
			$nomor='003';
			$judul='SURAT IZIN KELUAR PABRIK';
			$konten='MOHON IZIN UNTUK MENINGGALKAN PEKERJAAN:';
		}
		if($dt->category=='D'){
			$nomor='004';
			$judul='SURAT IZIN UNTUK BEROBAT';
			$konten='MOHON IZIN UNTUK BEROBAT KARENA SAKIT DENGAN:';
		}
	?>
	<table style="width:100%;">
		<tr>
			<td colspan="2" class="sedang">PT. SUMMIT ADYAWINSA INDONESIA</td>
			<td colspan="2" class="kecil kanan">NO.DOC: <?php echo e($nomor); ?>/SAI-OHCD/PERS/2014</td>
		</tr>
		<tr><td colspan="4" class="besar"><label class="judul"><?php echo e($dt->category); ?></label> <?php echo e($judul); ?></td></tr>
		<tr>
			<td style="width:40%;">&nbsp;</td>
			<td style="width:30%;">&nbsp;</td>
			<td style="width:15%;">&nbsp;</td>
			<td style="width:15%;">&nbsp;</td>
		</tr>
		<tr>
			<td class="sedang kanan">TANGGAL</td>
			<td colspan="3" class="sedang garis">: <?php echo strtoupper(date('d F Y',strtotime($dt->apply_date)));?></td>
		</tr>
		<tr>
			<td class="sedang">NAMA</td>
			<td colspan="3" class="sedang garis">: <?php echo e($dt->employee_name); ?></td>
		</tr>
		<tr>
			<td class="sedang">NIK</td>
			<td colspan="3" class="sedang garis">: <?php echo e($dt->NIK); ?></td>
		</tr>
		<tr>
			<td class="sedang">BAGIAN</td>
			<td colspan="3" class="sedang garis">: <?php echo e($dt->dept_name); ?></td>
		</tr>
		<tr><td colspan="4">&nbsp;</td></tr>
		<tr><td colspan="4" class="sedang"><?php echo e($konten); ?></td></tr>
		<?php if($dt->category=='A'){?>
			<tr>
				<td class="sedang">TANGGAL</td>
				<td colspan="3" class="sedang garis">: <?php echo date('d F Y',strtotime($dt->apply_date));?></td>
			</tr>
			<tr>
				<td class="sedang">KEPERLUAN</td>
				<td colspan="3" class="sedang garis">: <?php echo e($dt->keperluan); ?></td>
			</tr>
		<?php }?>
		<?php if($dt->category=='B'){?>
			<tr>
				<td class="sedang">DARI PUKUL</td>
				<td colspan="2" class="sedang garis">: <?php echo date('H:i',strtotime($dt->start_izin));?></td>
				<td class="sedang">WIB</td>
			</tr>
			<tr>
				<td class="sedang">KEPERLUAN</td>
				<td colspan="3" class="sedang garis">: <?php echo e($dt->keperluan); ?></td>
			</tr>
		<?php }?>
		<?php if($dt->category=='C'){?>
			<tr>
				<td class="sedang">DARI PUKUL</td>
				<td colspan="2" class="sedang garis">: <?php echo date('H:i',strtotime($dt->start_izin))." <label class='seling'>S/D</label> ".date('H:i',strtotime($dt->finish_izin));?></td>
				<td class="sedang">WIB</td>
			</tr>
			<tr>
				<td class="sedang">KEPERLUAN</td>
				<td colspan="3" class="sedang garis">: <?php echo e($dt->keperluan); ?></td>
			</tr>
		<?php }?>
		<?php if($dt->category=='D'){?>
			<tr>
				<td class="sedang">KELUHAN</td>
				<td colspan="3" class="sedang garis">: <?php echo substr($dt->keluhan, 0, 20);if(strlen($dt->keluhan)>20)echo "...";?></td>
			</tr>
			<tr>
				<td class="sedang">BEROBAT_KE</td>
				<td colspan="3" class="sedang garis">: <?php echo e($dt->berobat_ke); ?></td>
			</tr>
			<tr>
				<td class="sedang">DARI PUKUL</td>
				<td colspan="2" class="sedang garis">: <?php echo date('H:i',strtotime($dt->start_izin))." <label class='seling'>S/D</label> ".date('H:i',strtotime($dt->finish_izin));?></td>
				<td class="sedang">WIB</td>
			</tr>
		<?php }?>
		<tr><td colspan="4" style="height:20px;">&nbsp;</td></tr>
		<tr>
			<td class="sedang tengah">DISETUJUI OLEH</td>
			<td>&nbsp;</td>
			<td colspan="2" class="sedang tengah">DIAJUKAN OLEH,<br>KARYAWAN YBS</td>
		</tr>
		<tr>
			<td class="tengah">
				<?php if($dt->status_disetujui==1){
					$ttd_app=base_path().'/public/approval/'.$dt->disetujui.'.png';
					$confirm=base_path().'/public/approval/confirm.png';
					$reject=base_path().'/public/approval/rejected.png';
					$filename = 'c:/xampp/htdocs/EMS/public/approval/'.$dt->disetujui.'.png';
					if (file_exists($filename)){?>
						<img src='<?php echo e($ttd_app); ?>' style='height:30px'>
					<?php }else{?>
						<img src='<?php echo e($confirm); ?>' style='width:50px;'>
					<?php }
				}elseif($dt->status_disetujui=='3'){?>
					<img src='<?php echo e($reject); ?>' style='width:70px;'>
				<?php }?>
			</td>
			<td style="height: 25px;">&nbsp;</td>
			<td colspan="2" class="tengah">
				<?php 
					$ttd_app=base_path().'/public/approval/'.$dt->id_employee.'.png';
					$confirm=base_path().'/public/approval/confirm.png';
					$reject=base_path().'/public/approval/rejected.png';
					$filename = 'c:/xampp/htdocs/EMS/public/approval/'.$dt->id_employee.'.png';
					if (file_exists($filename)){?>
						<img src='<?php echo e($ttd_app); ?>' style='height:30px'>
					<?php }else{?>
						<img src='<?php echo e($confirm); ?>' style='width:50px;'>
					<?php }
				?>
			</td>
		</tr>
		<tr>
			<td class="sedang tengah ttd">
				<?php echo '('.substr($dt->nama_atasan, 0, 20).')';?>
			</td>
			<td>&nbsp;</td>
			<td colspan="2" class="sedang tengah ttd">
				<?php echo '('.substr($dt->employee_name, 0, 20).')';?>
			</td>
		</tr>
		<tr><td colspan="4" class="sedang tengah" style="padding-top:20px;">DIKETAHUI OLEH,</td></tr>
		<tr>
			<td class="sedang tengah">PERSONALIA</td>
			<td>&nbsp;</td>
			<td colspan="2" class="sedang tengah">SCURITY</td>
		</tr>
		<tr>
			<td class="tengah">
				<?php if($dt->status_personalia==1){
					$ttd_app=base_path().'/public/approval/'.$dt->personalia.'.png';
					$confirm=base_path().'/public/approval/confirm.png';
					$reject=base_path().'/public/approval/rejected.png';
					$filename = 'c:/xampp/htdocs/EMS/public/approval/'.$dt->personalia.'.png';
					if (file_exists($filename)){?>
						<img src='<?php echo e($ttd_app); ?>' style='height:30px;'>
					<?php }else{?>
						<img src='<?php echo e($confirm); ?>' style='width:50px;'>
					<?php }
				}elseif($dt->status_personalia=='3'){?>
					<img src='<?php echo e($reject); ?>' style='width:50px;'>
				<?php }?>
			</td>
			<td style="height: 25px;">&nbsp;</td>
			<td colspan="2" class="tengah">
				<?php if($dt->status_scurity==1){
					$ttd_app=base_path().'/public/approval/'.$dt->scurity.'.png';
					$confirm=base_path().'/public/approval/confirm.png';
					$reject=base_path().'/public/approval/rejected.png';
					$filename = 'c:/xampp/htdocs/EMS/public/approval/'.$dt->scurity.'.png';
					if (file_exists($filename)){?>
						<img src='<?php echo e($ttd_app); ?>' style='height:30px;'>
					<?php }else{?>
						<img src='<?php echo e($confirm); ?>' style='width:50px;'>
					<?php }
				}elseif($dt->status_personalia=='3'){?>
					<img src='<?php echo e($reject); ?>' style='width:50px;'>
				<?php }?>
			</td>
		</tr>
		<tr>
			<td class="sedang tengah ttd">
				<?php 
					if($dt->nama_personalia=='')echo "(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)";
					else echo '('.substr($dt->nama_personalia, 0, 13).')';
				?>
			</td>
			<td>&nbsp;</td>
			<td colspan="2" class="sedang tengah ttd">
				<?php 
					if($dt->nama_scurity=='')echo "(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)";
					else echo '('.substr($dt->nama_scurity, 0, 13).')';
				?>
			</td>
		</tr>
	</table>
	<div class="besar" style="text-align:center;color:#F00;background:#EEE;padding:3px 0px;font-size:11px;"><?php if($dt->category=='C'||$dt->category=='D')echo "Harap lapor Security saat masuk kembali ke PT.SAI";?></div>
</div>
<div class="sedang">Created by: <?php echo e($dt->admin); ?> <?php echo e($dt->created_at); ?></div>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\admin\m_permit\previewizin.blade.php ENDPATH**/ ?>