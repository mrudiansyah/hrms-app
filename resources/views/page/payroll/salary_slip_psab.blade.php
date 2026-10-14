<style>
	html{
		margin:20px;
		font-family: Arial, Helvetica, sans-serif;
		font-size:8px;
	}
	.bingkai{
		border:1px solid #000;
	}

</style>
@foreach($tb_salary_summary_employee as $dt)
<?php 
	$total_salary=$dt->upah_psab*$dt->aktual_kerja;
?>
<table style="width:100%;" cellspacing="0">
	<tr>
		<td class="bingkai" style="padding:7px;">
			<table style="width:100%;" cellspacing="0">
				<tr>
					<td style="width:120px;"><img src="{{ asset('public/gambar/logosai.png') }}" style="width:100%;"></td>
					<td style="text-align:center;">
						<b style="font-size:14px;">SLIP GAJI PSAB</b><br>
						<label>PT SUMMIT ADYAWINSA INDONESIA</label><br>
						<label>Tahun {{$thn}}</label>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center;">
						<table style="width:100%;">
							<tr>
								<td style="width:15%">&nbsp;</td>
								<td style="text-align:center; padding:0px;border:1px solid #C00;color:#F00;font-size:14px;">R A H A S I A</td>
								<td style="width:15%">&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="height:5px;"></td>
	</tr>
	<tr>
		<td class="bingkai">
			<table style="width:100%;">
				<tr>
					<td style="width:170px;">PERIODE PEMBAYARAN</td>
					<td style="text-align:center;width:5px;">:</td>
					<td>{{$periode_text}}</td>
				</tr>
				<tr>
					<td>DIBAYAR TANGGAL</td>
					<td style="text-align:center;">:</td>
					<td><?php echo date('d F Y',strtotime($dt->tanggal_pembayaran));?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="bingkai">
			<table style="width:100%;">
				<tr>
					<td style="width:170px;">NAMA KARYAWAN</td>
					<td style="text-align:center;width:5px;">:</td>
					<td>{{$dt->nama_karyawan}}</td>
				</tr>
				<tr>
					<td>NIK</td>
					<td style="text-align:center;">:</td>
					<td>{{$dt->NIK}}</td>
				</tr>
				<tr>
					<td>SUB.DEPT./DEPT./DIV/DIREKTORAT</td>
					<td style="text-align:center;">:</td>
					<td>{{$dt->dept_code}}</td>
				</tr>
				<tr>
					<td>JABATAN</td>
					<td style="text-align:center;">:</td>
					<td>Pegawai Harian Lepas</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="bingkai" style="padding:10px 5px;">
			<table style="width:100%;">
				<tr>
					<td style="padding-left:5px;">KOMPONEN UPAH</td>
					<td colspan="4">&nbsp;</td>
				</tr>
				<tr>
					<td style="padding-left:15px;"> - Upah Harian</td>
					<td style="text-align:center;width:5px;">:</td>
					<td style="text-align:right;width:45px;"><?php echo number_format($dt->upah_psab,0);?></td>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td style="padding-left:15px;"> - Jumlah Kehadiran</td>
					<td style="text-align:center;width:5px;">:</td>
					<td style="text-align:right;width:45px;"><?php echo number_format($dt->aktual_kerja,0);?></td>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td style="padding-left:15px;"> - Total Salary</td>
					<td style="text-align:center;width:5px;">:</td>
					<td style="width:30px;">&nbsp;</td>
					<td style="width:45px;text-align:right;"><?php echo number_format($total_salary,0);?></td>
				</tr>
				<tr>
					<td style="padding-left:5px;">RAPEL</td>
					<td style="text-align:center;width:5px;">:</td>
					<td style="width:30px;">&nbsp;</td>
					<td style="width:45px;text-align:right;"><?php echo number_format($dt->rapel,0);?></td>
				</tr>
				<tr>
					<td colspan="5">&nbsp;</td>
				</tr>
				<tr>
					<td style="padding-left:5px;"><b>TOTAL DITERIMA</b></td>
					<td style="text-align:center;width:5px;">:</td>
					<td style="width:30px;">&nbsp;</td>
					<td style="width:45px;text-align:right;"><b><?php echo number_format($dt->pendapatan_netto,0);?></b></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="bingkai" style="background: #000;color:#FFF;text-align:center;font-weight:bold;">LEGALISASI</td>
	</tr>
	<tr>
		<td class="bingkai">
			<table style="width:100%;">
				<tr>
					<td style="text-align:center;width:50%;">Dibayar oleh,<br><br><br><br>Payroll Adm</td>
					<td style="text-align:center;width:50%;">Diterima oleh,<br><br><br><br>{{$dt->nama_karyawan}}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="bingkai" style="background:#F00;color:#FFF;text-align:center;font-weight:bold;">CATATAN PENTING</td>
	</tr>
	<tr>
		<td class="bingkai" style="text-align:center;color:#C00;font-weight:bold;">
			Data gaji dalam slip ini bersifat RAHASIA. Setiap karyawan WAJIB menyimpan dengan baik & seaman mungkin slip ini. DILARANG KERAS membuang slip ini dengan sembarangan. Pelanggaran terhadap ketentuan ini akan dikenai sanksi SP.
		</td>
	</tr>
</table>
@endforeach
