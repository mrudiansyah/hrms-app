
<?php $__env->startSection('Contents'); ?>
	<?php $periode_sekarang=date('Y-m');?>
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-body">
						<div class="col-xs-12 col-md-12 col-lg-12">
							<div class="box-header">
								&nbsp;
								<?php if($periode_sekarang>=$periode){?>
									<?php if (request()->user()->hasRole('root')||request()->user()->hasRole('payroll')){?>
										<button type="button" class="btn btn-primary btn-md import-modal" id="import"><i class="fa fa-upload"></i> &nbsp;Import</button>
										<button title="Reset" type="button" class="reset-modal btn btn-danger btn-md" data-resetid="<?php echo e($periode); ?>" data-resetname="<?php echo e($periode); ?>" id="reset"><i class="fa fa-trash"></i> Reset</button>
										<button class="btn btn-primary btn-md formentry" data-idworkentry="" data-periode="<?php echo e($periode); ?>" data-idemployee="" data-workingday="0" data-shift="0" data-absen="0"><i class="fa fa-plus"></i> &nbsp;Add One</button>
									<?php }?>
								<?php }?>
								<div class="pull-left">
									<input type="month" id="periode" class="form-control" value="<?php echo e($periode); ?>">	
								</div>
								<div class="pull-right">
									<!-- <a href="<?php echo e($site); ?>/WorkEntry/Detail/<?php echo e($periode); ?>/<?php echo e($department); ?>" id="detail" class="btn btn-info btn-md"><i class="fa fa-folder-o"></i> &nbsp;Detail</a> -->
									<!-- <a href="<?php echo e($site); ?>/Summary/<?php echo e($periode); ?>/<?php echo e($department); ?>/<?php echo e($tipe); ?>" class="btn btn-success btn-md"><i class="fa fa-book"></i> &nbsp;Salary Summary</a> -->
								</div>
							</div>
							<div class="box-body">
								<table id="tables" class="table table-bordered">
									<thead>
										<tr>
											<th>No</th>
											<th>NIK</th>
											<th>EMPLOYEE NAME</th>
											<th>DEPARTMENT</th>
											<th>POSITION</th>
											<th>TRANSPORT</th>
											<?php if($tipe=='SAI'||$tipe=='MAGANG'){?>
												<th>SHIFT</th>
												<th>ABSENT</th>
											<?php }?>
											<th>WE1</th>
											<th>WE2</th>
											<th>SHIFT1</th>
											<th>SHIFT2</th>
											<th>ABSEN1</th>
											<th>ABSEN2</th>
											<th>&nbsp;</th>
										</tr>
									</thead>
									<tbody>
										<?php $no=0;$max=0;?>
										<?php $__currentLoopData = $tb_work_entry; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<?php if($dt->import_status=='1')$max=1;?>
											<tr>
												<td><?php $no++;echo $no;?></td>
												<td><?php echo e($dt->NIK); ?></td>
												<td><?php echo e($dt->nama_karyawan); ?></td>
												<td><?php echo e($dt->nama_bagian); ?></td>
												<td><?php echo e($dt->kode_jabatan); ?></td>
												<td><?php echo e($dt->aktual_kerja); ?></td>
												<?php if($tipe=='SAI'||$tipe=='MAGANG'){?>
													<td><?php echo e($dt->aktual_shift); ?></td>
													<td><?php echo e($dt->absen); ?></td>
												<?php }?>
												<td><?php echo e($dt->ak_tua); ?></td>
												<td><?php echo e($dt->ak_muda); ?></td>
												<td><?php echo e($dt->as_tua); ?></td>
												<td><?php echo e($dt->as_muda); ?></td>
												<td><?php echo e($dt->ab_tua); ?></td>
												<td><?php echo e($dt->ab_muda); ?></td>
												<td>
													<?php if (request()->user()->hasRole('root')||request()->user()->hasRole('payroll')){?>
														<button type="button" class="btn btn-primary btn-xs formentry" data-idworkentry="<?php echo e($dt->id); ?>" data-periode="<?php echo e($periode); ?>" data-idemployee="<?php echo e($dt->id_employee); ?>" data-namakaryawan="<?php echo e($dt->nama_karyawan); ?>" data-workingday="<?php echo number_format($dt->aktual_kerja,0);?>" data-shift="<?php echo number_format($dt->aktual_shift,0);?>" data-absen="<?php echo number_format($dt->absen,0);?>"><i class="fa fa-edit"></i></button>
														<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="<?php echo e($dt->id); ?>" data-delname="<?php echo e($dt->nama_karyawan); ?>"><i class="fa fa-trash"></i></button>
													<?php }?>
												</td>
											</tr>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</tbody>

								</table>
								<input type="hidden" id="jumlah" value="<?php echo e($no); ?>">
								<input type="hidden" id="maksimum" value="<?php echo e($max); ?>">
							</div>
							<!-- /.box-body -->
						</div>
				
					</div>
				</div>
			</div>
		</div>
	</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Modals'); ?>
	<div class="modal fade" id="modal-import">
		<div class="modal-dialog box box-primary" style="width:400px;">
				<div class="modal-content">
					<form method="post" action="<?php echo e($site); ?>/WorkEntry/Import" enctype="multipart/form-data">
						<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Import File Excel</h4>
						</div>
						<div class="modal-body">
							<?php echo e(csrf_field()); ?>

							<label></label>
							<div class="form-group">
								<input type="file" name="file" required="required">
							</div>
						</div>
						<div class="modal-footer">
							<a href="<?php echo e($site); ?>/Template/WorkEntry/<?php echo e($periode); ?>/<?php echo e($tipe); ?>" id="template" target="_blank" class="btn btn-default btn-md"><i class="fa fa-download"></i> &nbsp;Template</a>
							<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary pull-left">Import</button>
						</div>
					</form>		
				</div>

				<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
    </div>
	<div class="modal fade" id="modal-reset">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Confirmation</h4>
				</div>
				<div class="modal-body">
					<b id="aksesText">Reset </b> <b id="resetname"></b> ?
					<input type="hidden" id="resetid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left reset" data-dismiss="modal">Yes, Delete</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-form">
		<div class="modal-dialog box box-primary" style="width:350px;">
			<div class="modal-content">
					<form>
					<?php echo e(csrf_field()); ?>

						<div class="modal-header">	
							FORM WORK ENTRY
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<input type="hidden" id="idWorkentry" class="form-control">
							<div class="form-group">
								<label>Periode</label>
								<input type="month" id="periodeWork" class="form-control" disabled>
							</div>
							<div class="form-group opsitambah">
								<label>Employee</label>
								<!-- <select id="idKaryawan" class="form-control selectpicker" data-live-search="true"> -->
								<select id="idemployeeShow" class="form-control selectpicker" data-live-search="true">
									<option value=""></option>
									<?php $__currentLoopData = $tb_employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($dt2->id_employee); ?>"><?php echo e($dt2->nama_karyawan); ?> (<?php echo e($dt2->NIK); ?>)</option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

								</select>
							</div>
							<div class="form-group opsiedit">
								<label>Employee</label>
								<input type="text" id="namakaryawan" class="form-control" disabled>
							</div>
							<div class="form-group">
								<label>Work Entry</label>
								<input type="number" id="workentry" class="form-control" onclick="select();">
							</div>
							<div class="form-group">
								<label>Work Shift</label>
								<input type="number" id="workshift" class="form-control" onclick="select();">
							</div>
							<div class="form-group">
								<label>Absent</label>
								<input type="number" id="absent" class="form-control" onclick="select();">
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
							<button type="button" class="btn btn-primary pull-right" id="simpan" data-dismiss="modal">Save</button>
						</div>
					</form>
			</div>

			<!-- /.modal-content -->
		</div>
	<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-delete">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Confirmation</h4>
				</div>
				<div class="modal-body">
					<b id="aksesText">Delete </b> <b id="delname"></b> ?
					<input type="hidden" id="delid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left delete" data-dismiss="modal">Yes, Delete</button>
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
		$( document ).ready(function() {
			$('#idDepartment').val(<?php echo e($department); ?>);
			var jumlah = document.getElementById('jumlah').value;
			if(jumlah>0){
				document.getElementById("import").disabled = true;
				var element = document.getElementById("template");
  				element.classList.add("disabled");
				document.getElementById("reset").disabled = false;
			}else{
				document.getElementById("import").disabled = false;
				var element = document.getElementById("template");
				element.classList.remove("disabled");
				document.getElementById("reset").disabled = true;
			}
			var maksimum= document.getElementById('maksimum').value;
			if(maksimum>0){
				var element = document.getElementById("detail");
  				element.classList.add("disabled");
			}else{
				var element = document.getElementById("detail");
  				element.classList.remove("disabled");
			}
		});
		$(document).on('click', '.import-modal', function() {
			$('#modal-import').modal('show');
		});
		$('body').on("change","#periode",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			var tipe='<?php echo $tipe;?>'
			if(periode=='') var periode=0;
			window.location.href="<?php echo e($site); ?>/WorkEntry/"+periode+"/"+department+"/"+tipe;
		});
		$('body').on("change","#idDepartment",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			var tipe='<?php echo $tipe;?>'
			if(periode=='') var periode=0;
			window.location.href="<?php echo e($site); ?>/WorkEntry/"+periode+"/"+department+"/"+tipe;
		});
	</script>
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.reset-modal', function() {
			$('#resetid').val($(this).data('resetid'));
			$('#resetname').text($(this).data('resetname'));
			$('#aksesText').text($(this).data('akses'));
			$('#modal-reset').modal('show');
		});
		$('.modal-footer').on('click', '.reset', function() {
			var x=$('#resetid').val();
			var tipe='<?php echo $tipe;?>'
			window.location.href='<?php echo e($site); ?>/Reset/WorkEntry/'+x+'/'+tipe;
		});
	</script>
	<script>
 		$(document).on('click', '.formentry', function() {
			$('#idWorkentry').val($(this).data('idworkentry'));
			$('#periodeWork').val($(this).data('periode'));
			//$('#idemployee').val($(this).data('idemployee'));
			$('#namakaryawan').val($(this).data('namakaryawan'));
			$('#workentry').val($(this).data('workingday'));
			$('#workshift').val($(this).data('shift'));
			$('#absent').val($(this).data('absen'));
			var kondisi=$(this).data('idemployee');
			if(kondisi>0){
				$('.opsiedit').show();
				$('.opsitambah').hide();
			}else{
				$('.opsiedit').hide();
				$('.opsitambah').show();
			}
			$('#modal-form').modal('show');
		});
		$(document).on('click', '#simpan', function() {
			var idworkentry=$('#idWorkentry').val();
			var periodework=$('#periodeWork').val();
			var idemployee=$('#idemployeeShow').val();
			var workentry=$('#workentry').val();
			var workshift=$('#workshift').val();
			var absent=$('#absent').val();
			var tipe="<?php echo e($tipe); ?>";

			$.ajaxSetup({
				type:"POST",
				url: "<?php echo e($site); ?>/WorkEntry/Simpan",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:{tipe:tipe,idworkentry:idworkentry,periodework:periodework,idemployee:idemployee,workentry:workentry,workshift:workshift,absent:absent},
				success: function(respond){
					alert(respond);
					if(respond=='Success'){
						location.reload();
					}
				}
			})
		});
		$(document).on('click', '.delete-modal', function() {
			$('#delid').val($(this).data('delid'));
			$('#delname').text($(this).data('delname'));
			$('#aksesText').text($(this).data('akses'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#delid').val();
			window.location.href='<?php echo e($site); ?>/WorkEntry/Delete/'+x;
		});
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/payroll', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/payroll/work_entry.blade.php ENDPATH**/ ?>