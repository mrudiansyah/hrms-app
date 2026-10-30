
<?php $__env->startSection('Contents'); ?>
	<section class="content">
		<div class="row">
			<div class="col-xs-8">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<div class="pull-left">
							<button type="button" class="btn btn-primary btn-md edit-modal"><i class="fa fa-edit"></i> &nbsp;Update SLPJ</button>
						</div>
					</div>
					<div class="box-body">
						<table id="tables" class="table table-bordered">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th>NIK</th>
									<th>EMPLOYEE_NAME</th>
									<th>DEPT</th>
									<th>SLPJ</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;$item='';?>
								<?php $__currentLoopData = $tb_salaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php $no++;?>
									<tr>
										<td><?php echo e($no); ?></td>
										<td><?php echo e($dt->NIK); ?></td>
										<td><?php echo e($dt->employee_name); ?></td>
										<td><?php echo e($dt->dept_code); ?></td>
										<td><?php echo e($dt->slpj); ?></td>
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
    <div class="modal fade" id="modal-edit">
		<div class="modal-dialog" style="width:300px;">
			<div class="modal-content">
				<form>
					<input type="hidden" id="idSalaryContract">
					<?php echo e(csrf_field()); ?>

					<div class="modal-body">
						<div class="box box-primary box-solid" style="border:0px;">
							<div class="box-header" style="padding:5px 10px;font-size:20px;">
								<label id="Judul">List DPK</label>
							</div>
							<div class="row" style="padding-top:20px;">
								<div class="col-lg-12 col-md-12 col-xs-12" style="border-right:1px solid #EEE;">
									<div class="box-body" style="padding:10px 0px;">
										<div class="form-group">
											<label>Employee</label>
											<select id="idKaryawan" class="form-control selectpicker" data-live-search="true">
												<option value=""></option>
												<?php $__currentLoopData = $tb_employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($dt2->id); ?>"><?php echo strtoupper($dt2->employee_name)." (".$dt2->NIK." ".strtoupper($dt2->dept_code).")";?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

											</select>
										</div>
										<div class="form-group">
											<label>SLPJ</label>
											<input type="number" id="slpj" class="form-control" style="text-align:right;" onclick="select();">
										</div>
									</div>
								</div>
							</div>
							<div class="box-footer">
								<button type="button" class="btn btn-success pull-right" id="simpan">Save</button>
								<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
							</div>
						</div>
					</div>
				</form>		
			</div>
		</div>
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
	<!-- page script Tabel-->
	<script type="text/javascript">
		$(document).on('click', '.edit-modal', function() {
			$('#modal-edit').modal('show');
		});
		$(document).on('click', '#simpan', function() {
			$.ajaxSetup({
				type:"POST",
				url: "<?php echo e($site); ?>/DPKSLPJ/Submit",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			var idemployee=$('#idKaryawan').val();
			var slpj=$('#slpj').val();
			
			$.ajax({
				data:{idemployee:idemployee,slpj:slpj},
				success: function(respond){
					//alert(respond);
					if(respond=='Sukses'){
						location.reload();
					}else{
						alert("Gagal");
					}
				}
			})
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
		$(document).on('click', '.delete-modal', function() {
			$('#delid').val($(this).data('delid'));
			$('#delname').text($(this).data('delname'));
			$('#aksesText').text($(this).data('akses'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#delid').val();
			//window.location.href='<?php echo e($site); ?>/ContractDelete/'+x;
		});
	</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/dpk', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\payroll\dpk_slpj.blade.php ENDPATH**/ ?>