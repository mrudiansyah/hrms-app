
<?php $__env->startSection('Contents'); ?>
	<?php $periode_sekarang=date('Y-m');?>
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<?php if(!isset($is_pdf)): ?>
						<div class="pull-left">
							<input type="month" id="periode" class="form-control" value="<?php echo e($periode); ?>">	
						</div>
						<div class="pull-right">
							<a href="<?php echo e($site); ?>/Summary/<?php echo e($periode); ?>/<?php echo e($department); ?>/<?php echo e($tipe); ?>" class="btn btn-default btn-md"><i class="fa fa-angle-double-left"></i> &nbsp;Back</a>
							<button class="btn btn-success btn-md" onclick="exportTableToExcel('table_summary', 'Salary_Summary_<?php echo e($tipe); ?>_<?php echo e($periode); ?>')"><i class="fa fa-file-excel-o"></i> &nbsp;Save as Excel</button>
							<a href="<?php echo e($site); ?>/SummaryDownloadPDF/<?php echo e($periode); ?>/<?php echo e($department); ?>/<?php echo e($tipe); ?>" class="btn btn-danger btn-md" target="_blank"><i class="fa fa-file-pdf-o"></i> &nbsp;Download PDF</a>
						</div>
						<?php endif; ?>
					</div>
					<div class="box-body" style="overflow: auto;">
						<?php if(isset($is_pdf)): ?>
						<div style="text-align: center; margin-bottom: 10px;">
							<h3 style="margin: 0; font-weight: bold; font-size: 20px;">PT. SUMMIT ADYAWINSA INDONESIA</h3>
							<h4 style="margin: 5px 0; font-weight: bold; font-size: 16px;">RECAPITULATION EMPLOYEE <?php if($tipe=='MAGANG')echo "APPRENTICESHIP ";?> SALARY</h4>
							<h4 style="margin: 0; font-weight: bold; font-size: 16px;">PERIOD <?php echo e(strtoupper(date('F, Y', strtotime($periode . '-01')))); ?></h4>
						</div>
						<?php endif; ?>
						<table id="table_summary" class="table table-bordered table-striped" style="min-width:100%; margin-bottom: 30px;">
							<thead>
								<tr style="background-color: #f4b084;">
									<th>No</th>
									<th>NAME</th>
									<th>NIK</th>
									<th>JOIN_DATE</th>
									<th>DEPT</th>
									<th>LEVEL</th>
									<th>CC_CODE</th>
									<th>STATUS</th>
									<th>REKENING</th>
									<th>BASIC_SALARY</th>
									<th>TUNJ_JABATAN</th>
									<th>TUNJ_PRESTASI_TAHUNAN</th>
									<!-- <th>TUNJ_SKILL</th> -->
									<th>TOTAL_SALARY</th>
									<th>RAPEL</th>
									<th>SHIFT_&_TRANS</th>
									<th>TOTAL_REC</th>
									<th>SAL_&_ALLW</th>
									<th>PPH21</th>
									<th>ABS_D</th>
									<th>ABS_A</th>
									<th>TAX_UPMT</th>
									<th title="Potongan Finance">OTHERS</th>
									<th>UNION</th>
									<th>KOPERASI</th>
									<th>BPJS_KES</th>
									<th>JHT</th>
									<th>PENSIUN</th>
									<th>TOTAL_DED</th>
									<th>PAID</th>
								</tr>
							</thead>
							<tbody>
							<?php 
								$global_no=0;
								$total_grand_pokok=0; $total_grand_jab=0; $total_grand_masa=0; $total_grand_skill=0;
								$total_grand_sal=0; $total_grand_rapel=0; $total_grand_transhift=0; $total_grand_receive=0; 
								$total_grand_bruto=0; $total_grand_tax=0; $total_grand_ded=0; $total_grand_netto=0; 
							?>
							<?php $__currentLoopData = $tb_salary_summary->groupBy('kode_bagian'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept => $dept_group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr style="background-color: #3c8dbc; color: #fff;">
									<td colspan="29" style="font-weight: bold; font-size: 10px;">Department: <?php echo e($dept); ?></td>
								</tr>
								<?php 
									$gd_bruto=0; $gd_tax=0; $gd_ded=0; $gd_netto=0; $gd_pokok=0; $gd_jab=0; $gd_masa=0; $gd_skill=0; $gd_sal=0;
								?>
								<?php $__currentLoopData = $dept_group->groupBy('cc_code'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cc => $cc_group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php $c_pokok=0; $c_jab=0; $c_masa=0;$c_skill=0; $c_sal=0; $c_bruto=0; $c_tax=0; $c_ded=0; $c_netto=0; ?>
									<?php $__currentLoopData = $cc_group; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr>
											<td>
												<?php
													$global_no++;
													$kes=$dt->bpjs_kesehatan;
													$tk=$dt->jaminan_pensiun+$dt->jaminan_hari_tua;
													$potongans=$dt->lain_lain+$dt->standby_oncall+$dt->seragam+$dt->pants;
													$total_deduction=$dt->pph21_ter+$dt->ketidakhadiran+$dt->pph21_kurang_bayar+$potongans+$dt->serikat+$dt->koperasi+$kes+$tk;
													$jml_salary=$dt->total_salary+$dt->upah_harian;
													$transhift=$dt->tunjangan_shift+$dt->tunjangan_transport;
													$salary_allowance=$jml_salary+$transhift+$dt->rapel;
													$total_receive=$salary_allowance+$dt->jaminan_kecelakaan_kerja+$dt->jaminan_kematian+$dt->kesehatan;
													
													$c_pokok+=$dt->upah_pokok; $c_jab+=$dt->tunjangan_jabatan; $c_masa+=$dt->tunjangan_prestasi_tahunan;$c_skill+=$dt->tunjangan_skill;
													$c_sal+=$jml_salary; $c_bruto+=$dt->pendapatan_bruto; $c_tax+=$dt->pph21_ter;
													$c_ded+=$total_deduction; $c_netto+=$dt->pendapatan_netto;

													$gd_pokok+=$dt->upah_pokok; $gd_jab+=$dt->tunjangan_jabatan; $gd_masa+=$dt->tunjangan_prestasi_tahunan; $gd_skill+=$dt->tunjangan_skill;
													$gd_sal+=$jml_salary; $gd_bruto+=$dt->pendapatan_bruto; $gd_tax+=$dt->pph21_ter;
													$gd_ded+=$total_deduction; $gd_netto+=$dt->pendapatan_netto;

													$total_grand_pokok+=$dt->upah_pokok; $total_grand_jab+=$dt->tunjangan_jabatan; $total_grand_masa+=$dt->tunjangan_prestasi_tahunan; $total_grand_skill+=$dt->tunjangan_skill;
													$total_grand_sal+=$jml_salary; $total_grand_rapel+=$dt->rapel; 
													$total_grand_transhift+=$transhift; $total_grand_receive+=$total_receive;
													$total_grand_bruto+=$dt->pendapatan_bruto; $total_grand_tax+=$dt->pph21_ter;
													$total_grand_ded+=$total_deduction; $total_grand_netto+=$dt->pendapatan_netto;
												?>
												<?php echo e($global_no); ?>

											</td>
											<td><?php echo e($dt->nama_karyawan); ?></td>
											<td><?php echo e($dt->NIK); ?></td>
											<td><?php echo e(date('d-M-Y',strtotime($dt->tanggal_kontrak))); ?></td>
											<td><?php echo e($dt->kode_bagian); ?></td>
											<td><?php echo e($dt->jabatan); ?></td>
											<td><?php echo e($dt->cc_code); ?></td>
											<td><?php echo e($dt->statuspajak); ?></td>
											<td><?php echo e($dt->nomor_rekening); ?></td>
											<td><?php echo e(number_format($dt->upah_pokok,0)); ?></td>
											<td><?php echo e(number_format($dt->tunjangan_jabatan,0)); ?></td>
											<td><?php echo e(number_format($dt->tunjangan_prestasi_tahunan,0)); ?></td>
											<!-- <td><?php echo e($dt->tunjangan_skill); ?></td> -->
											<td style="text-align:right;"><?php echo e(number_format($jml_salary,0)); ?></td>
											<td style="text-align:right;">
												<div class="pull-left" id="rapel<?php echo e($dt->id); ?>"><?php echo e(number_format($dt->rapel,0)); ?></div>
											</td>
											<td style="text-align:right;"><?php echo e(number_format($transhift,0)); ?></td>
											<td style="text-align:right;"><?php echo e(number_format($total_receive,0)); ?></td>
											<td style="text-align:right;"><?php echo e(number_format($salary_allowance,0)); ?></td>
											<td style="color:red; text-align:right;"><?php echo e(number_format($dt->pph21_ter,0)); ?></td>
											<td><?php echo e($dt->absen); ?></td>
											<td style="color:red; text-align:right;"><?php echo e(number_format($dt->ketidakhadiran,0)); ?></td>
											<td style="color:red; text-align:right;"><?php echo e(number_format($dt->pph21_kurang_bayar,0)); ?></td>
											<td style="color:red; text-align:right;"><?php echo e(number_format($potongans,0)); ?></td>
											<td style="color:red; text-align:right;"><?php echo e(number_format($dt->serikat,0)); ?></td>
											<td style="color:red; text-align:right;"><?php echo e(number_format($dt->koperasi,0)); ?></td>
											<td style="color:red; text-align:right;"><?php echo e(number_format($kes,0)); ?></td>
											<td style="color:red; text-align:right;"><?php echo e(number_format($dt->jaminan_hari_tua,0)); ?></td>
											<td style="color:red; text-align:right;"><?php echo e(number_format($dt->jaminan_pensiun,0)); ?></td>
											<td style="color:red; text-align:right;"><?php echo e(number_format($total_deduction,0)); ?></td>
											<td style="text-align:right;"><b id="netto<?php echo e($dt->id); ?>"><?php echo e(number_format($dt->pendapatan_netto,0)); ?></b></td>
										</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<tr style="background-color: #eee; font-weight: bold;">
										<td colspan="8" class="text-left">Cost Center: <?php echo e($cc); ?></td>
										<td>&nbsp;</td>
										<td><?php echo e(number_format($c_pokok,0)); ?></td>
										<td><?php echo e(number_format($c_jab,0)); ?></td>
										<td><?php echo e(number_format($c_masa,0)); ?></td>
										<!-- <td><?php echo e(number_format($c_skill,0)); ?></td> -->
										<td style="text-align:right;"><?php echo e(number_format($c_sal,0)); ?></td>
										<td colspan="3"></td>
										<td style="text-align:right;"><?php echo e(number_format($c_bruto,0)); ?></td>
										<td style="text-align:right;"><?php echo e(number_format($c_tax,0)); ?></td>
										<td colspan="9"></td>
										<td style="text-align:right;"><?php echo e(number_format($c_ded,0)); ?></td>
										<td style="text-align:right;"><?php echo e(number_format($c_netto,0)); ?></td>
									</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								<tr style="background-color: #d2d6de; font-weight: bold;">
									<td colspan="8" class="text-right">TOTAL <?php echo e($dept); ?></td>
									<td>&nbsp;</td>
									<td><?php echo e(number_format($gd_pokok,0)); ?></td>
									<td><?php echo e(number_format($gd_jab,0)); ?></td>
									<td><?php echo e(number_format($gd_masa,0)); ?></td>
									<!-- <td><?php echo e(number_format($gd_skill,0)); ?></td> -->
									<td style="text-align:right;"><?php echo e(number_format($gd_sal,0)); ?></td>
									<td colspan="3"></td>
									<td style="text-align:right;"><?php echo e(number_format($gd_bruto,0)); ?></td>
									<td style="text-align:right;"><?php echo e(number_format($gd_tax,0)); ?></td>
									<td colspan="9"></td>
									<td style="text-align:right;"><?php echo e(number_format($gd_ded,0)); ?></td>
									<td style="text-align:right;"><?php echo e(number_format($gd_netto,0)); ?></td>
								</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<tr style="background-color: #f4b084; font-weight: bold;">
								<td colspan="8" class="text-right">GRAND TOTAL</td>
								<td>&nbsp;</td>
								<td><?php echo e(number_format($total_grand_pokok,0)); ?></td>
								<td><?php echo e(number_format($total_grand_jab,0)); ?></td>
								<td><?php echo e(number_format($total_grand_masa,0)); ?></td>
								<!-- <td><?php echo e(number_format($total_grand_skill,0)); ?></td> -->
								<td style="text-align:right;"><?php echo e(number_format($total_grand_sal, 0)); ?></td>
								<td style="text-align:right;"><?php echo e(number_format($total_grand_rapel, 0)); ?></td>
								<td style="text-align:right;"><?php echo e(number_format($total_grand_transhift, 0)); ?></td>
								<td style="text-align:right;"><?php echo e(number_format($total_grand_receive, 0)); ?></td>
								<td style="text-align:right;"><?php echo e(number_format($total_grand_bruto, 0)); ?></td>
								<td style="text-align:right;"><?php echo e(number_format($total_grand_tax, 0)); ?></td>
								<td colspan="9"></td>
								<td style="text-align:right;"><?php echo e(number_format($total_grand_ded, 0)); ?></td>
								<td style="text-align:right;"><?php echo e(number_format($total_grand_netto, 0)); ?></td>
							</tr>
							</tbody>
						</table>
						<div style="font-size:12px;">
							<?php if($tipe=='MANAGEMENT'): ?>
							<table style="width:35%;" border="1" cellspacing="0" cellpadding="3" align="center">
								<tr>
									<td colspan="2" style="text-align:center;">Approved by</td>
								</tr>
								<tr>
									<td style="text-align:center;vertical-align:middle;height:60px;" class="relative">
										&nbsp;
									</td>
									<td style="text-align:center;vertical-align:middle;">
										&nbsp;
									</td>
								</tr>
								<tr>
									<th style="width:180px;text-align:center;vertical-align:bottom;">NOVA TRIANA NOFIANTI</th>
									<th style="width:180px;text-align:center;vertical-align:bottom;">RISTIYONO</th>
								</tr>
								<tr>
									<td style="text-align:center;">Section Head</td>
									<td style="text-align:center;">Dept.Head HRGA</td>
								</tr>
							</table>
							<?php else: ?>
								<table style="width:35%;" border="1" cellspacing="0" cellpadding="3" align="center">
									<tr>
										<td colspan="3" style="text-align:center;">Approved by</td>
									</tr>
									<tr>
										<td style="text-align:center;vertical-align:middle;height:50px;" class="relative">
											&nbsp;
										</td>
										<td style="text-align:center;vertical-align:middle;">
											&nbsp;
										</td>
										<td style="text-align:center;vertical-align:middle;">
											&nbsp;
										</td>
									</tr>
									<tr>
										<th style="width:180px;text-align:center;vertical-align:bottom;">ANGELLA PUSPA DEWI P</th>
										<th style="width:180px;text-align:center;vertical-align:bottom;">NOVA TRIANA NOFIANTI</th>
										<th style="width:180px;text-align:center;vertical-align:bottom;">RISTIYONO</th>
									</tr>
									<tr>
										<td style="text-align:center;">Specialist</td>
										<td style="text-align:center;">Section Head</td>
										<td style="text-align:center;">Dept.Head HRGA</td>
									</tr>
								</table>
							<?php endif; ?>
						</div>

					</div>
					<!-- /.box-body -->
				</div>

			</div>
		</div>
	</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Modals'); ?>
	<?php if(!isset($is_pdf)): ?>
	<div class="modal fade" id="modal-sumber">
		<div class="modal-dialog box box-primary" style="width:300px;">
			<div class="modal-content">
					<form>
					<?php echo e(csrf_field()); ?>

						<div class="modal-header">	
							Update Rapel
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label>Rapel Amount</label>
								<input type="hidden" id="idSummary" class="form-control">
								<input type="text" id="rapelAmount" class="form-control" onclick="select();">
							</div>
							<div class="form-group">
								<label>Remark</label>
								<textarea id="description" class="form-control"></textarea>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Batal</button>
							<button type="button" class="btn btn-primary pull-right" id="simpansummary" data-dismiss="modal">Simpan</button>
						</div>
					</form>
			</div>

			<!-- /.modal-content -->
		</div>
	</div>
	<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
	<?php if(!isset($is_pdf)): ?>
	<script>
		$('body').on("change","#periode",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			var tipe='<?php echo $tipe;?>'
			if(periode=='') var periode=0;
			window.location.href="<?php echo e($site); ?>/Summary/"+periode+"/"+department+"/"+tipe;
		});
		$('body').on("change","#idDepartment",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			var tipe='<?php echo $tipe;?>'
			if(periode=='') var periode=0;
			window.location.href="<?php echo e($site); ?>/Summary/"+periode+"/"+department+"/"+tipe;
		});
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
 		$(document).on('click', '#rapel', function() {
			$('#idSummary').val($(this).data('idsummary'));
			$('#rapelAmount').val($(this).data('rapelamount'));
			var angka = $('#rapelAmount').val();
			formatRupiahS(angka,'rapelAmount');
			$('#modal-sumber').modal('show');
		});
		$(document).on('keyup', '#rapelAmount', function() {
			var biaya = document.getElementById('rapelAmount');
			biaya.addEventListener('keyup', function(e){
				biaya.value = formatRupiah(this.value, '');
			});
		});
		$(document).on('click', '#simpansummary', function() {
			var x=$('#idSummary').val();
			var y = $('#rapelAmount').val().replace(/\./g,'');
			//var y=$('#rapelAmount').val();
			var z=$('#description').val();
			$.ajaxSetup({
				type:"POST",
				url: "<?php echo e($site); ?>/Summary/Rapel",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:{id_summary:x,rapel:y,description:z},
				success: function(respond){
					if(respond=='Sukses'){
						location.reload();
					}else{
						alert("Gagal");
					}
				}
			})
		});
		function formatRupiah(angka, prefix){
			var number_string = angka.replace(/[^,\d]/g, '').toString(),
			split   		= number_string.split(','),
			sisa     		= split[0].length % 3,
			rupiah     		= split[0].substr(0, sisa),
			ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);
 
			if(ribuan){
				separator = sisa ? '.' : '';
				rupiah += separator + ribuan.join('.');
			}
 
			rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
			return prefix == undefined ? rupiah : (rupiah ? '' + rupiah : '');
		}
		function formatRupiahS(angka,pemilik){
			var number_string = angka.replace(/[^,\d]/g, '').toString(),
			split   		= number_string.split(','),
			sisa     		= split[0].length % 3,
			rupiah     		= split[0].substr(0, sisa),
			ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);
 
			if(ribuan){
				separator = sisa ? '.' : '';
				rupiah += separator + ribuan.join('.');
			}
 
			rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
			$('#'+pemilik).val(rupiah);
		}

	</script>
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.delete-modal', function() {
			$('#delid').val($(this).data('delid'));
			$('#delname').text($(this).data('delname'));
			$('#aksesText').text($(this).data('akses'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#delid').val();
			window.location.href='<?php echo e($site); ?>/ContractDelete/'+x;
		});

		function exportTableToExcel(tableID, filename = ''){
			var tableSelect = document.getElementById(tableID);
			var tableHTML = tableSelect.outerHTML;
			var blob = new Blob([tableHTML], {
				type: "application/vnd.ms-excel"
			});
			var url = URL.createObjectURL(blob);
			var downloadLink = document.createElement("a");
			filename = filename ? filename + '.xls' : 'excel_data.xls';
			downloadLink.href = url;
			downloadLink.download = filename;
			downloadLink.click();
			URL.revokeObjectURL(url);
		}
	</script>
	<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(isset($is_pdf) ? 'layouts/pdf' : 'layouts/payroll', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\payroll\salary_summary_pdf.blade.php ENDPATH**/ ?>