
<?php $__env->startSection('Contents'); ?>
	<?php $periode_sekarang=date('Y-m');?>
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<div class="pull-left">
							<input type="month" id="periode" class="form-control" value="<?php echo e($periode); ?>">	
						</div>
						<div class="pull-right">
							<a href="<?php echo e($site); ?>/WorkEntry/<?php echo e($periode); ?>/<?php echo e($department); ?>/<?php echo e($tipe); ?>" class="btn btn-default btn-md"><i class="fa fa-angle-double-left"></i> &nbsp;Back</a>
							<?php if($periode_sekarang>=$periode){?>
								<!-- <a href="<?php echo e($site); ?>/SummaryUpdate/<?php echo e($periode); ?>/<?php echo e($department); ?>/<?php echo e($tipe); ?>" class="btn btn-warning btn-md"><i class="fa fa-refresh"></i> &nbsp;Refresh</a> -->
								<a href="<?php echo e($site); ?>/SummaryUpdateTer/<?php echo e($periode); ?>/<?php echo e($department); ?>/<?php echo e($tipe); ?>" class="btn btn-warning btn-md"><i class="fa fa-refresh"></i> &nbsp;Refresh</a>
								<a href="<?php echo e($site); ?>/SummaryReset/<?php echo e($periode); ?>/<?php echo e($tipe); ?>" class="btn btn-danger btn-md"><i class="fa fa-trash"></i> &nbsp;Clear</a>
							<?php }?>
							<!-- <a href="<?php echo e($site); ?>/Tax/<?php echo e($periode); ?>/<?php echo e($department); ?>/<?php echo e($tipe); ?>" class="btn btn-info btn-md"><i class="fa fa-dollar"></i> &nbsp;PPh21</a> -->
							<a href="<?php echo e($site); ?>/TaxTerMonthly/<?php echo e($periode); ?>/<?php echo e($department); ?>/<?php echo e($tipe); ?>/payroll" class="btn btn-info btn-md"><i class="fa fa-dollar"></i> &nbsp;PPh21</a>
							<a href="<?php echo e($site); ?>/SummaryExcel/<?php echo e($periode); ?>/<?php echo e($department); ?>/<?php echo e($tipe); ?>" class="btn btn-success btn-md"><i class="fa fa-file-excel-o"></i> &nbsp;Excel</a>
							<a href="<?php echo e($site); ?>/SendMail/<?php echo e($periode); ?>/<?php echo e($department); ?>/<?php echo e($tipe); ?>/tb_salary_summary" class="btn btn-primary btn-md"><i class="fa fa-upload"></i> &nbsp;Distribute Slip</a>
						</div>
					</div>
					<div class="box-body" style="overflow: auto;">
						<?php if($tipe=='PSAB'){?>
							<table id="tables" class="table table-bordered" style="min-width:100%;">
								<thead>
									<tr>
										<th style="width:30px;">No</th>
										<th style="width:120px;">NIK</th>
										<th style="width:240px;">EMPLOYEE</th>
										<th style="width:120px;">DEPARTMENT</th>
										<th style="width:120px;">SALARY/DAY</th>
										<th style="width:120px;">WORKING DAY</th>
										<th style="width:90px;">ATTENDANCE</th>
										<th style="width:120px;">TOTAL SALARY</th>
										<th style="width:90px;">RAPEL</th>
										<th style="width:120px;">PAID</th>
										<th>&nbsp;</th>
									</tr>
								</thead>
								<tbody>
								<?php $no=0;?>
								<?php $__currentLoopData = $tb_salary_summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr>
										<td>
											<?php
												$no++;
												$total_salary=$dt->upah_psab*$dt->aktual_kerja;
											?>
											<?php echo e($no); ?>

										</td>
										<td><?php echo e($dt->NIK); ?></td>
										<td><?php echo e($dt->nama_karyawan); ?></td>
										<td><?php echo e($dt->kode_bagian); ?></td>
										<td><?php echo number_format($dt->upah_psab,0);?></td>
										<td><?php echo e($hari_kerja_psab); ?></td>
										<td><?php echo e($dt->aktual_kerja); ?></td>
										<td><?php echo number_format($total_salary,0);?></td>
										<td>
											<div class="pull-left" id="rapel<?php echo e($dt->id); ?>"><?php echo number_format($dt->rapel,0);?></div>
											<div class="pull-right">
												<i class="fa fa-edit" data-idsummary="<?php echo e($dt->id); ?>" data-rapelamount="<?php echo e($dt->rapel); ?>" data-netto="<?php echo e($dt->pendapatan_netto); ?>" style="cursor:pointer;" id="rapel"></i>											
											</div>
										</td>
										<td style="text-align:right;"><b id="netto<?php echo e($dt->id); ?>"><?php echo number_format($dt->pendapatan_netto,0);?></b></td>
										<td style="text-align:right;">
											<a href="<?php echo e($site); ?>/PaySlip/<?php echo e($periode); ?>/<?php echo e($dt->tipe_kontrak); ?>/<?php echo e($dt->id_employee); ?>" target="_blank"><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></button></a>
										</td>
									</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</tbody>
							</table>
						<?php }elseif($tipe=='PKL'){?>
							<table id="tables" class="table table-bordered" style="min-width:100%;">
								<thead>
									<tr>
										<th style="width:30px;">No</th>
										<th style="width:120px;">NIK</th>
										<th style="width:240px;">EMPLOYEE</th>
										<th style="width:120px;">DEPARTMENT</th>
										<th style="width:120px;">ALLOWANCE/DAY</th>
										<th style="width:120px;">WORKING DAY</th>
										<th style="width:90px;">ATTENDANCE</th>
										<th style="width:120px;">TOTAL ALLOWANCE</th>
										<th style="width:90px;">RAPEL</th>
										<th style="width:120px;">PAID</th>
										<th>&nbsp;</th>
									</tr>
								</thead>
								<tbody>
								<?php $no=0;?>
								<?php $__currentLoopData = $tb_salary_summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr>
										<td>
											<?php
												$no++;
												$total_salary=$dt->tunjangan_pkl*$dt->aktual_kerja;
											?>
											<?php echo e($no); ?>

										</td>
										<td><?php echo e($dt->NIK); ?></td>
										<td><?php echo e($dt->nama_karyawan); ?></td>
										<td><?php echo e($dt->kode_bagian); ?></td>
										<td><?php echo number_format($dt->tunjangan_pkl,0);?></td>
										<td><?php echo e($hari_kerja_psab); ?></td>
										<td><?php echo e($dt->aktual_kerja); ?></td>
										<td><?php echo number_format($total_salary,0);?></td>
										<td>
											<div class="pull-left" id="rapel<?php echo e($dt->id); ?>"><?php echo number_format($dt->rapel,0);?></div>
											<div class="pull-right">
												<i class="fa fa-edit" data-idsummary="<?php echo e($dt->id); ?>" data-rapelamount="<?php echo e($dt->rapel); ?>" data-netto="<?php echo e($dt->pendapatan_netto); ?>" style="cursor:pointer;" id="rapel"></i>											
											</div>
										</td>
										<td style="text-align:right;"><b id="netto<?php echo e($dt->id); ?>"><?php echo number_format($dt->pendapatan_netto,0);?></b></td>
										<td style="text-align:right;">
											<a href="<?php echo e($site); ?>/PaySlip/<?php echo e($periode); ?>/<?php echo e($dt->tipe_kontrak); ?>/<?php echo e($dt->id_employee); ?>" target="_blank"><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></button></a>
										</td>
									</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</tbody>
							</table>
						<?php }else{?>
							<table id="tables" class="table table-bordered" style="min-width:100%;">
								<thead>
									<tr>
										<th style="width:30px;">No</th>
										<th style="width:100px;">NIK</th>
										<th style="min-width:200px;">NAME</th>
										<th style="width:70px;">JOIN_DATE</th>
										<th style="width:70px;">DEPT</th>
										<th style="width:70px;">CC_CODE</th>
										<th style="width:70px;">LEVEL</th>
										<th style="width:70px;">STATUS</th>
										<th style="width:70px;">POKOK</th>
										<th style="width:70px;">JABATAN</th>
										<th style="width:70px;">MASA_KERJA</th>
										<th style="width:70px;">SALARY</th>
										<th style="width:70px;" >SHIFT</th>
										<th style="width:70px;" >TRANSPORT</th>
										<th style="width:70px;">RAPEL</th>
										<th style="width:70px;">PENDAPATAN</th>
										<th style="width:70px;">PPH21</th>
										<th style="width:70px;">ABSENT_DAY</th>
										<th style="width:70px;">ABSENT_AMT</th>
										<th style="width:70px;">TAX_UNDERPMT</th>
										<th style="width:70px;" title="Potongan Finance">OTHERS</th>
										<th style="width:70px;">UNION</th>
										<th style="width:70px;">KOPERASI</th>
										<th style="width:70px;">BPJS_KES</th>
										<th style="width:70px;">JHT</th>
										<th style="width:70px;">PENSIUN</th>
										<th style="width:70px;">TOTAL_DEDUCTION</th>
										<th style="width:70px;">PAID</th>
										<th style="width:60px;">&nbsp;</th>
									</tr>
								</thead>
								<tbody>
								<?php $no=0;?>
								<?php $__currentLoopData = $tb_salary_summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr>
										<td>
											<?php
												$no++;
												$kes=$dt->bpjs_kesehatan;
												$tk=$dt->jaminan_pensiun+$dt->jaminan_hari_tua;
												$jumlah=$dt->bpjs_kesehatan+$dt->jaminan_pensiun+$dt->jaminan_hari_tua;
												$potongans=$dt->lain_lain+$dt->standby_oncall+$dt->seragam+$dt->pants;
												$total_deduction=$dt->pph21_ter+$dt->ketidakhadiran+$dt->pph21_kurang_bayar+$potongans+$dt->serikat+$dt->koperasi+$kes+$tk;
												$judul=number_format($dt->bpjs_kesehatan,0).'/'.number_format($dt->jaminan_pensiun,0).'/'.number_format($dt->jaminan_hari_tua,0);
											?>
											<?php echo e($no); ?>

										</td>
										<td><?php echo e($dt->NIK); ?></td>
										<td><?php echo e($dt->nama_karyawan); ?></td>
										<td><?php echo e(date('d-M-Y',strtotime($dt->tanggal_kontrak))); ?></td>
										<td><?php echo e($dt->kode_bagian); ?></td>
										<td><?php echo e($dt->cc_code); ?></td>
										<td><?php echo e($dt->jabatan); ?></td>
										<td><?php echo e($dt->statuspajak); ?></td>
										<td><?php echo e(number_format($dt->upah_pokok,0)); ?></td>
										<td><?php echo e(number_format($dt->tunjangan_jabatan,0)); ?></td>
										<td><?php echo e(number_format($dt->tunjangan_prestasi_tahunan,0)); ?></td>
										<td><?php $jml_salary=$dt->total_salary+$dt->upah_harian;echo number_format($jml_salary,0);?></td>
										<td title="<?php $transhift=$dt->tunjangan_transport+$dt->tunjangan_shift;echo number_format($dt->tunjangan_transport,0).'+'.number_format($dt->tunjangan_shift,0).'+'.number_format($dt->tunjangan_harian,0);?>"><?php echo number_format($dt->tunjangan_shift,0);?></td>
										<td><?php echo e(number_format($dt->tunjangan_transport,0)); ?></td>
										<td>
											<div class="pull-left" id="rapel<?php echo e($dt->id); ?>"><?php echo number_format($dt->rapel,0);?></div>
											<div class="pull-right">
												<i class="fa fa-edit" data-idsummary="<?php echo e($dt->id); ?>" data-rapelamount="<?php echo e($dt->rapel); ?>" data-netto="<?php echo e($dt->pendapatan_netto); ?>" style="cursor:pointer;" id="rapel"></i>											
											</div>
										</td>
										<td><?php echo number_format($dt->pendapatan_bruto,0);?></td>
										<td style="color:red;"><?php echo number_format($dt->pph21_ter,0);?></td>
										<td><?php echo e($dt->absen); ?></td>
										<td style="color:red;"><?php echo number_format($dt->ketidakhadiran,0);?></td>
										<td style="color:red;"><?php echo number_format($dt->pph21_kurang_bayar,0);?></td>
										<td style="color:red;"><?php echo number_format($potongans,0);?></td>
										<td style="color:red;"><?php echo number_format($dt->serikat,0);?></td>
										<td style="color:red;"><?php echo number_format($dt->koperasi,0);?></td>
										<td style="color:red;" title="<?php echo $judul;?>"><?php echo number_format($kes,0);?></td>
										<td style="color:red;" title="<?php echo $judul;?>"><?php echo number_format($dt->jaminan_hari_tua,0);?></td>
										<td style="color:red;" title="<?php echo $judul;?>"><?php echo number_format($dt->jaminan_pensiun,0);?></td>
										<td style="color:red;" title="<?php echo $judul;?>"><?php echo number_format($total_deduction,0);?></td>
										<td><b id="netto<?php echo e($dt->id); ?>"><?php echo number_format($dt->pendapatan_netto,0);?></b></td>
										<td class="pull-right">
											<a href="<?php echo e($site); ?>/PaySlip/<?php echo e($periode); ?>/<?php echo e($dt->tipe_kontrak); ?>/<?php echo e($dt->id_employee); ?>" target="_blank"><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></button></a>
											<!-- <?php if($dt->mail_status==0){?><a href="<?php echo e($site); ?>/SendMailOne/<?php echo e($dt->id); ?>/tb_salary_summary"><button type="button" class="btn btn-info btn-xs"><i class="fa fa-envelope-o"></i></button></a><?php }?> -->
										</td>
									</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</tbody>
							</table>
						<?php }?>
					</div>
					<!-- /.box-body -->
				</div>

			</div>
		</div>
	</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Modals'); ?>
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
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
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
					location.reload();
					// if(respond=='Sukses'){
					// 	location.reload();
					// }else{
					// 	alert(respond);
					// }
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
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/payroll', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/payroll/salary_summary.blade.php ENDPATH**/ ?>