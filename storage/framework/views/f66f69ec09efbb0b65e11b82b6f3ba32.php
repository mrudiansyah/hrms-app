
<?php $__env->startSection('Contents'); ?>
<div class="content-wrapper">
    <section class="content-header">
		<?php $background_select='#cccccc';?>
		<h1 onclick="">
			<?php echo e($juduls); ?>

			<small><?php if(isset($subjudul))echo $subjudul;else echo 'TMS';?></small>
			<div class="pull-right">
				&nbsp;
			</div>
		</h1>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						&nbsp;
						<div class="pull-left">
							<input type="month" id="periode" class="form-control" value="<?php echo e($periode); ?>" min="202501">
						</div>
						<?php $today=date('Y-m-d');?>
						<div class="pull-right">
							&nbsp;
						</div>
					</div>
					<div class="box-body">
						<div class="pull-left">
							<div class="form-group">
								<label>Legend: </label><br>
								<?php $__currentLoopData = $tb_work_code; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<b class="btn btn-default btn-md" style="background:<?php echo e($dt->background); ?>"><?php echo e($dt->source_check); ?></i></b>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</div>
						</div>
					</div>
					<div class="box-body">
						<table id="tables" class="table table-bordered">
							<thead>
								<tr>
									<th>No.</th>
									<th style="width:120px;">NIK</th>
									<th style="width:180px;">Employee name</th>
									<th style="width:90px;">Dept</th>
									<?php 
										for($i=1;$i<=31;$i++){
											if(strlen($i)==1)$j='0'.$i;
											else $j=$i;
											$tgl=date('Y-m-d',strtotime($periode.'-'.$j));
											if($department>0)
											echo "<th style='padding:0px;text-align:center;vertical-align:middle;'>".$j."</th>";
											else
											echo "<th style='padding:0px;text-align:center;vertical-align:middle;'>".$j."</th>";
										}
									?>
									<th style="width:60px;">Status</th>
									<th>&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<?php 
									$no=0;
									$w5d=0;
									$w6d=0;
								?>
								<?php $__currentLoopData = $tb_work_entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php 
										if($dt->plan_actual=='actual'&&$dt->qty_absent>0){
											if($dt->working_perweek=='5')$w5d++;
											if($dt->working_perweek=='6')$w6d++;
									?>
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td title="<?php echo e($dt->PIN); ?>"><?php echo e($dt->NIK); ?></td>
											<td><?php echo e($dt->nama_karyawan); ?></td>
											<td >
												<?php echo e($dt->department); ?> 
												<?php if(request()->user()->hasRole('hr_access')): ?>
													<button type="button" class="pull-right btn btn-primary btn-xs capture" data-periode="<?php echo e($periode); ?>" data-idemployee="<?php echo e($dt->id_employee); ?>"><i class="fa fa-refresh"></i></button>
												<?php endif; ?>
											</td>
											<?php 
											$alfa=0;
											for($i=1;$i<=31;$i++){
												if(strlen($i)==1)$j='D0'.$i;
												else $j='D'.$i;
												$act=$dt->$j;
												if($act==99)$alfa++;

												//if(strlen($i)==1)$tgl=$dt->periode.'-0'.$i;
												//else $tgl=$dt->periode.'-'.$i;
												
												$status='';
												$remark='';
												$entrycode='';
												$warna=" style='background:#FFF;color:#FFF;padding:0px;margin:0px;'";
												$workCode=$work_code_lookup->get($act);
												if($workCode){
													$warna=" style='background:".$workCode->background.";color:".$workCode->color.";'";
												}
												$checktime=$checktime_lookup->get($dt->id.'|'.$j);
												if($checktime){
													$remark=$checktime->remark;
													$status="";
													if($act=='99')$warna=" style='background:#cccccc;color:#cccccc;'";
													$entrycode=$checktime->entry_code;
												}
												//$entrycode='51';
												echo "<td title='".$remark."' class='checktime-modal' ".$warna." id='no".$no."i".$i."' data-konten='no".$no."i".$i."' data-idworkentry='".$dt->id."' data-namakaryawan='".$dt->nama_karyawan."' data-idcolumn='".$j."' data-remark='".$remark."' data-entrycode='".$entrycode."'>".$status."</td>";
											}?>
											<td>
												<?php if($alfa>0): ?>
													<?php echo e($alfa); ?> Absent
												<?php endif; ?>
											</td>
											<td >			
												<?php
													$PIN=$dt->PIN;
													$lenbadge=strlen($PIN);
													$nullbadge=9-$lenbadge;
													$p='';
													for($q=1;$q<=$nullbadge;$q++){
														$p.='0';
													}
													$badge=$p.$PIN;
												?>
												<a href="/TMSChecktime/<?php echo e($dt->id_employee); ?>/<?php echo e($periode); ?>" title="Info" type="button" class="btn btn-info btn-xs" target="_blank"><i class="fa fa-clock-o"></i></a>
											</td>
										</tr>
									<?php }?>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>

						</table>
						<input type="hidden" id="jumlah" value="<?php echo e($no); ?>">
						<input type="hidden" id="w5d" value="<?php echo e($w5d); ?>">
						<input type="hidden" id="w6d" value="<?php echo e($w6d); ?>">
					</div>
					<!-- /.box-body -->
				</div>
			</div>
		</div>
	</section>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Modals'); ?>
	<div class="modal fade" id="modal-checktime">
		<div class="modal-dialog box box-primary" style="width:300px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Reconcile Work Entry</h4>
				</div>
				<div class="modal-body">
					<form role="form" action="" method="post">
						<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
						<?php echo e(csrf_field()); ?>

						<div class="box-body">
							<div class="form-group">
								<label>Nama</label>
								<input type="hidden" id="idworkentry" class="form-control">
								<input type="hidden" id="idcolumn" class="form-control">
								<input type="hidden" id="idcell" class="form-control">
								<input type="text" id="namakaryawan" class="form-control" disabled>
							</div>
							<div class="form-group">
								<label>Checktime Source</label>
								<select name="group" class="form-control" id="entrycodes">
									<option value=""></option>
									<?php $__currentLoopData = $tb_work_code; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($dt->work_code); ?>"><?php echo e($dt->source_check); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
							</div>
							<div class="form-group">
								<label>Remark</label>
								<textarea class="form-control" id="remark"></textarea>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary pull-left simpan" data-dismiss="modal">Save</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
	<script type="text/javascript">
		$('body').on("change","#periode",function(){
			var department="<?php echo e($department); ?>";
			var periode=document.getElementById('periode').value;
			if(periode=='') var periode=0;
			window.location.href="<?php echo e($site); ?>/MonthlyCheck/"+periode;
		});
	</script>
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<script>
		$(document).ready(function() {
			var w5d="<?php echo e($w5d); ?>";
			var w6d="<?php echo e($w6d); ?>";
			if(w5d>0){
				$('.t5d').show();
			}else{
				$('.t5d').hide();
			} 
			if(w6d>0){
				$('.t6d').show();
			}else{
				$('.t6d').hide();
			} 

			var table = $('#tables').DataTable({
				'paging'      : true,
				'lengthChange': false,
				'searching'   : true,
				'ordering'    : false,
				'info'        : true,
				"pageLength"  : 50,
				'autoWidth'   : false,
				"lengthMenu"  : [[5,10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
				//"scrollX"     : true
			});
			
			new $.fn.dataTable.Buttons( table, {
				//buttons: ['copy', 'excel', 'print']
				buttons: [
				{ extend: 'copyHtml5', footer: true },
				{ extend: 'excelHtml5', footer: true },
				{ extend: 'print', footer: true }
				]
			} );
			
			table.buttons( 0, null ).container().prependTo(
				table.table().container()
			);
		} );


	</script>
	<script type="text/javascript">
		$(document).on('click', '.checktime-modal', function() {
			$('#idworkentry').val($(this).data('idworkentry'));
			$('#idcolumn').val($(this).data('idcolumn'));
			$('#namakaryawan').val($(this).data('namakaryawan'));
			$('#idcell').val($(this).data('konten'));
			$('#remark').val($(this).data('remark'));

			var x=$(this).data('entrycode');
			const selectElement = document.getElementById('entrycodes');
			selectElement.value = x;
			
			periode="<?php echo e($periode); ?>";
			l="<?php echo e($limit_date); ?>";
			y=$(this).data('idcolumn');
			z=y.slice(-2);
			current=periode+'-'+z;
			
			if(x==null){
				$('#modal-checktime').modal('show');
			}else{
				if(current>l){
					$('#modal-checktime').modal('show');
				}else{
					alert('sudah lock payroll');
				}
			}
		});
		$('.modal-footer').on('click', '.simpan', function() {
			var idworkentry=$('#idworkentry').val();
			var idcolumn=$('#idcolumn').val();
			var entrycode=$('#entrycodes').val();
			var remark=$('#remark').val();
			var idcell=$('#idcell').val();

			var background="<?php echo e($background_select); ?>";
			//alert(idcell);
			$.ajaxSetup({
				type:"POST",
				url: "<?php echo e($site); ?>/updateTMS/Actual",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			$.ajax({
				data:{idworkentry:idworkentry,idcolumn:idcolumn,entrycode:entrycode,remark:remark},
				success: function(respond){
					//alert(respond);
					// if(respond=='Sukses'){
					// 	//document.getElementById(idcell).style.backgroundColor= background;
					// 	//document.getElementById(idcell).style.fontColor= background;
					// 	location.reload();
					// }else{
					// 	alert(respond);
					// }
					location.reload();
				}
			})
		});
		$(document).on('click', '.capture', function() {
			var periode=$(this).data('periode');
			var id_employee=$(this).data('idemployee');
			//alert(periode+id_employee);
			$.ajaxSetup({
				type:"POST",
				url: "<?php echo e($site); ?>/updateTMS/ActualAll",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			$.ajax({
				data:{periode:periode,id_employee:id_employee},
				success: function(respond){
					//alert(respond);
					location.reload();
				}
			})
		});
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/tms/monthly_check.blade.php ENDPATH**/ ?>