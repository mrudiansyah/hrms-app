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

@foreach($tb_absens as $dts)
<table style="width:100%;" cellspacing="0">
	<tr>
		<td colspan="3" style="text-align:center;padding:15px;">
			<img src="{{ public_path('gambar/logosai.png') }}" style="width:210px;">
		</td>
		<td colspan="11" style="text-align:center;font-size:16px;font-weight:bold;">DATA PRESENSI<br>&<br>ABSENSI KARYAWAN</td>
		<td colspan="4" rowspan="2" style="text-align:center;padding:10px;">
			ABSENSI RATE
			<div style="text-align:left;padding:10px 20px;">
				Rumus Absensi Rate:<br>
				<img src="{{ public_path('gambar/rumus_absensirate.png') }}" style="width:100px;"><br>
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
						{{$hasil}}
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
				<tr><td style="width:125px;" class="polos2">NAMA DIVISI</td><td class="polos2">: {{$dts->dept_code}}</td></tr>
				<tr><td class="polos2">PERIODE BULAN</td><td class="polos2">: <?php echo strtoupper(date('M - Y',strtotime($dts->start)));?></td></tr>
				<tr><td class="polos2">TOTAL ABSENSI</td><td class="polos2">: {{$dts->total_absen}}</td></tr>
				<tr><td class="polos2">TOTAL HARI KERJA</td><td class="polos2">: {{$dts->present_plan}}</td></tr>
				<tr><td class="polos2">TOTAL JUMLAH KARYAWAN</td><td class="polos2">: {{$dts->total_employee}}</td></tr>
				<tr>
					<td class="polos2">ABSENSI RATE BULAI INI</td><td class="polos2">
						
					: {{number_format($x,2)}}%
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
								<br><br><img src='{{ $ttd_app }}' class="absolute" style="width:140px;top:-10px;left:30px;">
							<?php }else{?>
								<br><br><br><img src='{{ $confirm }}' style='height:80px;' class="absolute">
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
								<br><br><br><img src='{{ $ttd_app }}' class="absolute" style="width:100px;top:-10px;left:20px;">
							<?php //}else{?>
								<br><br><br><img src='{{ $confirm }}' class="absolute" style="width:100px;top:-30px;left:20px;">
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
								<br><br><br><img src='{{ $ttd_app }}' class="absolute" style="width:100px;top:-10px;left:0px;">
							<?php }else{?>
								<br><br><br><img src='{{ $confirm }}' style='height:80px;' class="absolute">
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
								<br><br><br><img src='{{ $ttd_app }}' class="absolute" style="width:100px;top:-10px;left:0px;">
							<?php }else{?>
								<br><br><br><img src='{{ $confirm }}' class="absolute" style="width:60px;top:-30px;left:20px;">
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
	@foreach($tb_absen as $dt)
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
		<tr><td colspan="18" style="text-align:left;background:#000;color:#FFF;font-weight:bold;padding-left:10px;font-size:12px;">{{strtoupper($dt->jabatan)}}</td></tr>
	<?php }?>
	
	<tr>
		<td><?php $no++;echo $no;?></td>
		<td style="text-align:left;">{{strtoupper($dt->employee_name)}}</td>
		<td>{{$dt->NIK}}</td>
		<td>{{$dt->present_plan}}</td>
		<td>{{$dt->present_actual}}</td>
		<td>{{$dt->present_rate}}%</td>
		<td>{{$dt->hour_plan}}</td>
		<td>{{$dt->hour_actual}}</td>
		<td>{{$dt->hour_rate}}%</td>
		<td>{{$dt->terlambat}}</td>
		<td>{{$dt->terlambat_minutes}}</td>
		<td>
			<?php $banyak=$dt->setengah_minutes/240;?>
			{{$banyak}}
		</td>
		<td>{{$dt->keluar_minutes}}</td>
		<td>{{$dt->cuti}}</td>
		<td>{{$dt->sakit}}</td>
		<td>{{$dt->izin}}</td>
		<td>{{$dt->alpa}}</td>
		<td>{{$dt->absent}}</td>
	</tr>
	@endforeach
	<tr style="background:#CCC;">
		<td colspan="3">TOTAL {{$dts->divisi}}: {{$no}} Emplopyees</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><?php $drates=$drate/$no;echo number_format($x,2);?>%</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><?php $hrates=$hrate/$no;echo number_format($hrates,2);?>%</td>
		<td>{{$kali}}</td>
		<td>{{$telat}}</td>
		<td>{{$stengah}}</td>
		<td>{{$keluar}}</td>
		<td>{{$cuti}}</td>
		<td>{{$sakit}}</td>
		<td>{{$izin}}</td>
		<td>{{$alpa}}</td>
		<td>{{$total}}</td>
	</tr>
</table>
@endforeach
