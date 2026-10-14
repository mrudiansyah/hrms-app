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
@foreach($tb_slip as $dt)
	<table style="width:100%;" cellspacing="0">
		<tr>
			<td class="bingkai" style="padding:7px;">
				<table style="width:100%;" cellspacing="0">
					<tr>
						<td style="width:120px;"><img src="{{ asset('public/gambar/logosai.png') }}" style="width:100%;"></td>
						<td style="text-align:center;">
							<b style="font-size:14px;">SLIP GAJI KARYAWAN</b><br>
							<label>PT SUMMIT ADYAWINSA INDONESIA</label><br>
							<label>Tahun <?php echo date('Y',strtotime($dt->Finish_Kontrak));?></label>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center;">
							<table style="width:100%;">
								<tr>
									<td style="width:15%">&nbsp;</td>
									<td style="text-align:center; padding:5px;border:1px solid #C00;color:#F00;font-size:18px;">R A H A S I A</td>
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
						<td style="width:170px;">PERIODE KONTRAK</td>
						<td style="text-align:center;width:5px;">:</td>
						<td><?php echo date('F Y',strtotime($dt->Finish_Kontrak));?></td>
					</tr>
					<tr>
						<td>DIBAYAR TANGGAL</td>
						<td style="text-align:center;">:</td>
						<td><?php echo date('d F Y',strtotime($dt->pay_date));?></td>
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
						<td>{{$dt->Nama}}</td>
					</tr>
					<tr>
						<td>NIK</td>
						<td style="text-align:center;">:</td>
						<td>{{$dt->NIK}}</td>
					</tr>
					<tr>
						<td>SUB.DEPT./DEPT./DIV/DIREKTORAT</td>
						<td style="text-align:center;">:</td>
						<td>{{$dt->Bagian}}</td>
					</tr>
					<tr>
						<td>JABATAN</td>
						<td style="text-align:center;">:</td>
						<td>{{$dt->Jabatan}}</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="bingkai" style="padding:10px 5px;">
				<table style="width:100%;">
					<tr>
						<td style="width:170px;">PENDAPATAN</td>
						<td colspan="4">&nbsp;</td>
					</tr>
					<tr>
						<td style="padding-left:5px;">I. KOMPONEN UPAH</td>
						<td colspan="4">&nbsp;</td>
					</tr>
					<tr>
						<td style="padding-left:15px;"> 1. Upah Pokok</td>
						<td style="text-align:center;width:5px;">:</td>
						<td style="text-align:right;width:45px;"><?php echo number_format($dt->Upah_Pokok,0);?></td>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td style="padding-left:15px;">2. Tunjangan Tetap</td>
						<td colspan="4">&nbsp;</td>
					</tr>
					<tr>
						<td style="padding-left:25px;">2.1. Tunjangan Jabatan</td>
						<td style="text-align:center;">:</td>
						<td style="text-align:right;"><?php echo number_format($dt->Tunjangan_Jabatan,0);?></td>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td style="padding-left:15px;">3. Tunjangan Tidak Tetap</td>
						<td colspan="4">&nbsp;</td>
					</tr>
					<tr>
						<td style="padding-left:25px;">3.1. Tunjangan Prestasi Tahunan</td>
						<td style="text-align:center;">:</td>
						<td style="text-align:right;"><?php echo number_format($dt->Tunjangan_Prestasi,0);?></td>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td style="padding-left:25px;">3.2. Tunjangan Skill</td>
						<td style="text-align:center;">:</td>
						<td style="text-align:right;"><?php echo number_format($dt->Tunjangan_Skill,0);?></td>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td style="padding-left:15px;">4. PPH21</td>
						<td colspan="4">&nbsp;</td>
					</tr>
					<tr>
						<td style="padding-left:25px;">4.1. Insentif PPh21</td>
						<td style="text-align:center;">:</td>
						<td style="text-align:right;"><?php echo number_format($dt->PPH21_Insentif,0);?></td>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td style="padding-left:5px;">Total Pendapatan Kotor</td>
						<td style="text-align:center;">:</td>
						<td colspan="2">&nbsp;</td>
						<td style="text-align:right;padding-right:10px;"><?php echo number_format($dt->Total_Pendapatan,0);?></td>
					</tr>
				</table>
				<table style="width:100%;">
					<tr>
						<td style="width:170px;padding-left:5px;">KOMPENSASI</td>
						<td colspan="4">&nbsp;</td>
					</tr>
					<tr>
						<td style="padding-left:15px;">1. Masa Kontrak</td>
						<td style="text-align:center;width:5px">:</td>
						<td style="width:45px;"><?php echo date('d-M-y',strtotime($dt->Start_Kontrak));?></td>
						<td style="width:30px;text-align:center;">s/d</td>
						<td><?php echo date('d-M-y',strtotime($dt->Finish_Kontrak));?></td>
					</tr>
					<tr>
						<td style="padding-left:15px;">2. Kompensasi</td>
						<td style="text-align:center;">:</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td style="text-align:right;padding-right:10px;"><?php echo number_format($dt->Kompensasi,0);?></td>
					</tr>
					<tr>
						<td style="padding-left:15px;">3. Pajak/PPh21</td>
						<td style="text-align:center;">:</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td style="text-align:right;padding-right:10px;"><?php echo number_format($dt->PPH21,0);?></td>
					</tr>
					<tr>
						<td colspan="5">&nbsp;</td>
					</tr>
					<tr>
						<td style="padding-left:5px;"><b>Total Pendapatan Kompensasi</b></td>
						<td style="text-align:center;">:</td>
						<td colspan="2">&nbsp;</td>
						<td style="text-align:right;padding-right:10px;border-bottom:1px double #000;"><b><?php echo number_format($dt->Total_Kompensasi,0);?></b></td>
					</tr>
					<tr>
						<td colspan="5" style="background: #FFF;height:150px;">&nbsp;</td>
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
						<td style="text-align:center;width:50%;">Diterima oleh,<br><br><br><br>{{$dt->Nama}}</td>
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

<script>
	$(function () {
		$('#table2').DataTable({
		'paging'      : false,
		'lengthChange': false,
		'searching'   : false,
		'ordering'    : false,
		'info'        : false,
		"pageLength"  : 10,
		'autoWidth'   : true,
		})
	})
	$(function () {
		$('#table3').DataTable({
		'paging'      : true,
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : false,
		'info'        : true,
		"pageLength"  : 5,
		'autoWidth'   : false,
		})
	})
</script>
