
<?php $__env->startSection('Styles'); ?>
	<style>th{text-align:center;}</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Contents'); ?>
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<div class="pull-left">
							<input type="month" id="periode" class="form-control" value="<?php echo e($periode); ?>">	
						</div>
						<div class="pull-right">
							<a href="<?php echo e($site); ?>/Summary/<?php echo e($periode); ?>/<?php echo e($department); ?>/<?php echo e($tipe); ?>" id="template" class="btn btn-default btn-md"><i class="fa fa-angle-double-left"></i> &nbsp;Kembali</a>
							<a href="<?php echo e($site); ?>/ConditionalTax/<?php echo e($periode); ?>/<?php echo e($department); ?>/<?php echo e($tipe); ?>" id="template" class="btn btn-info btn-md">Conditional Tax</a>
						</div>
					</div>
					<div class="box-body" style="overflow: auto;">
						<table id="tables" class="table table-bordered" style="min-width:100%;">
							<thead>
								<tr>
									<th style="width:30px;">No<br>&nbsp;</th>
									<th style="width:100px;">NIK<br>&nbsp;</th>
									<th >NAME<br>&nbsp;</th>
									<th style="width:70px;">DEPT<br>&nbsp;</th>
									<th style="width:70px;">STATUS<br>&nbsp;</th>
									<th style="width:70px;">SALARY<br><small style="font-weight:normal;">A</small></th>
									<th style="width:70px;" title="TRANSPORT + SHIFT">ALLOWANCE<br><small style="font-weight:normal;">B</small></th>
									<th style="width:70px;">RAPEL<br><small style="font-weight:normal;">D</small></th>
									<th style="width:70px;">JKK/JKM/BPJS<br><small style="font-weight:normal;">E</small></th>
									<th style="width:70px;">JHT/JP<br><small style="font-weight:normal;">F</small></th>
									<th style="width:70px;">ABSENT<br><small style="font-weight:normal;">G</small></th>
									<th style="width:70px;">MONTHY_SALARY<br><small style="font-weight:normal;">H=(ABCDE)-(FG)</small></th>
									<th style="width:70px;">KATEGORY<br><small style="font-weight:normal;">I</small></th>
									<th style="width:70px;">TER<br><small style="font-weight:normal;">J</small></th>
									<th style="width:70px;">PPh21<br><small style="font-weight:normal;">K=H*TER</small></th>
									<!--
									<th style="width:50px;">YEARLY_SALARY<br><small style="font-weight:normal;">N=Hx<?php echo e($sisa_bulan); ?></small></th>
									<th style="width:50px;">PREV_SALARY<br><small style="font-weight:normal;">O</small></th>
									<th style="width:50px;">BRUTO<br><small style="font-weight:normal;">I=N+O</small></th>
									<th style="width:50px;">POSITION
										<br><small style="font-weight:normal;">J=Ix<?php echo number_format($biaya_jabatan_p,0);?>% <i style="cursor:pointer;" class="fa fa-info-circle" title="Max: <?php echo number_format($biaya_jabatan_l,0);?>" style="color:blue;"></i></small>
									</th>
									<th style="width:50px;">
										PTKP
										<br>
										<small style="font-weight:normal;">
											K
											<a style="cursor:pointer;" data-placement="bottom" data-html="true" data-toggle="popover" title="<i class='fa fa-file-text-o'></i> List PTKP" data-content="
											<?php 
												foreach($tb_pph21_status as $dt){
													if(strlen($dt->status_pajak)==2)$tambahan="&nbsp;&nbsp;";else $tambahan="";
													echo $dt->status_pajak.$tambahan." :".number_format($dt->ptkp,0)."<br>";
												}
											?>
											"><i class="fa fa-info-circle"></i></a>
										</small>
									</th>
									<th style="width:50px;">PKP<br><small style="font-weight:normal;">L=(I-J)-K</small></th>
									<th style="width:50px;">PPH21<br>
										<small style="font-weight:normal;">
											M&nbsp;<i style="cursor:pointer;" class="fa fa-info-circle" id="pajak" style="color:blue;"></i>
										</small>
									</th>
									-->
								</tr>
							</thead>
							<tbody>
							<?php $no=0;?>
							<?php $__currentLoopData = $tb_salary_tax; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td>
										<?php
											$no++;
										?>
										<?php echo e($no); ?>

									</td>
									<td><?php echo e($dt->NIK); ?></td>
									<td><?php echo e($dt->nama_karyawan); ?></td>
									<td><?php echo e($dt->dept_code); ?></td>
									<td><?php echo e($dt->statuspajak); ?></td>
									<td><?php echo number_format($dt->total_salary,0);?></td>
									<td title="Transport:<?php echo e($dt->tunjangan_transport); ?> Shift:<?php echo e($dt->tunjangan_shift); ?>"><?php $allowance=$dt->tunjangan_transport+$dt->tunjangan_shift;echo number_format($allowance,0);?></td>
									<td>
										<div class="pull-left" id="rapel<?php echo e($dt->id); ?>"><?php echo number_format($dt->rapel,0);?></div>
									</td>
									<td><?php $jkk_jkm_bpjs=$dt->jaminan_kecelakaan_kerja+$dt->jaminan_kematian+$dt->kesehatan;echo number_format($jkk_jkm_bpjs,0);?></td>
									<td style="color:blue;"><?php $jht_jp=$dt->jaminan_hari_tua+$dt->jaminan_pensiun;echo number_format($jht_jp,0);?></td>
									<td style="color:blue;"><?php echo number_format($dt->ketidakhadiran,0);?></td>
									<td><?php echo number_format($dt->gaji,0);?></td>
									<td><?php echo $dt->kategori;?></td>
									<td><?php echo number_format($dt->ter,2);?></td>
									<td><?php echo number_format($dt->pph21_ter,2);?></td>
									<!--
									<td><?php $now_bruto=$dt->penghasilan_bruto-$dt->penghasilan_sebelumnya;echo number_format($now_bruto,0);?></td>
									<td><?php echo number_format($dt->penghasilan_sebelumnya,0);?></td>
									<td><?php echo number_format($dt->penghasilan_bruto,0);?></td>
									<td style="color:blue;"><?php echo number_format($dt->biaya_jabatan,0);?></td>
									<td><?php echo number_format($dt->ptkp,0);?></td>
									<td style="text-align:right;"><?php if($dt->pkp>0)echo number_format($dt->pkp,0);?></td>
									<td style="text-align:right;"><?php if($dt->pph21_salary>0)echo number_format($dt->pph21_salary,0);?></td>
									-->
								</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>

			</div>
		</div>
	</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Modals'); ?>
	<div class="modal fade" id="modal-tax">
	<div class="modal-dialog box box-primary" style="width:300px;">
		<div class="modal-content">
			<div class="modal-header">	
				<b>PROSENTASE PPH21 / <?php echo e($sisa_bulan); ?></b>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<?php 
					foreach($tb_pph21 as $dt){
						$bawah=number_format($dt->batas_bawah,0);
						$atas=number_format($dt->batas_atas,0);
						if($dt->id==1)echo "<div class='row'><div class='col-xs-8'> <= ".$atas." </div><div class='col-xs-4'>:".number_format($dt->prosentase,0)."%</div></div>";
						else if($dt->id==4)echo "<div class='row'><div class='col-xs-8'> > ".$bawah." </div><div class='col-xs-4'>:".number_format($dt->prosentase,0)."%</div></div>";
						else echo "<div class='row'><div class='col-xs-8'>".$bawah." s/d ".$atas." </div><div class='col-xs-4'>:".number_format($dt->prosentase,0)."%</div></div>";
					}
				?>
			</div>
		</div>

		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
	</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
	<script>
		$('body').on("change","#periode",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			var tipe='<?php echo $tipe;?>'
			if(periode=='') var periode=0;
			window.location.href="<?php echo e($site); ?>/Tax/"+periode+"/"+department+"/"+tipe;
		});
		$('body').on("change","#idDepartment",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			var tipe='<?php echo $tipe;?>'
			if(periode=='') var periode=0;
			window.location.href="<?php echo e($site); ?>/Tax/"+periode+"/"+department+"/"+tipe;
		});
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
 		$(document).on('click', '#pajak', function() {
			$('#modal-tax').modal('show');
		});

	</script>
	<script>
		$(document).ready(function(){
		$('[data-toggle="popover"]').popover();
		});
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/payroll', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\payroll\salary_tax.blade.php ENDPATH**/ ?>