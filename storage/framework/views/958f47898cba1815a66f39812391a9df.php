
<?php $__env->startSection('Contents'); ?>
<?php
//$serverName = "192.168.1.4"; //serverName\instanceName
//$connectionInfo = array( "Database"=>"db_ems_memo", "UID"=>"EMS", "PWD"=>"1nd()n3514572");
//$conn = sqlsrv_connect( $serverName, $connectionInfo);

?>
   <!-- Contents -->
   <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
   <?php $user=Auth::user()->name;?>
    <style>
		#tables th {
		border-top: 1px solid #999;
		border-bottom: 1px solid #999;
		background-color: #2F4F4F;
		color: white;
		}	
        .table1 tr:hover {
		  cursor:pointer;
        }
		#table2 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table2 tbody tr:hover{
			cursor:pointer;
		}
    </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Employee Competence
				<small>&nbsp;</small>
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-xs-12 col-md-12 col-lg-12">
				<div class="box box-primary" style="background:#FFF;">
					<form role="form" action="" method="post">
					<div class="box-header">
						<i class="fa fa-file-text-o"></i>Employee List
						<div class="box-tools pull-right">
							<?php if($data['id_group']!=0): ?>
								<button type="button" class="btn btn-primary pull-left simpan">Save Change</button>
							<?php endif; ?>
						</div>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12 col-md-6 col-lg-4">
								<select class="form-control" id="select_group">
									<option value="<?php echo e($data['id_group']); ?>"><?php echo e($data['group_name']); ?></option>
									<?php $__currentLoopData = $data['tb_group']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<?php if($data['id_group']!=$dt->id): ?>
											<option value="<?php echo e($dt->id); ?>"><?php echo e($dt->group_name); ?></option>
										<?php endif; ?>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
							</div>
							<div class="col-xs-12 col-md-8 col-lg-9">&nbsp;</div>
						</div><br>
						<?php if($data['id_group']!=0): ?>
							<div class="col-lg-7 col-md-12 col-xs-12">
								<table id="tables" class="table table-hover">
									<thead>
										<tr>
											<th>No</th>
											<th>NIK</th>
											<th>Name</th>
											<th>Departement</th>
											<th>Position</th>
										</tr>
									</thead>
									<tbody>
										<?php $no=0;?>
										<?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<?php if($employee->skill_matrix): ?>
												<?php if($employee->skill_matrix->id==$data['id_group']): ?>
													<tr>
														<td><?php $no++;echo $no;?></td>
														<td><?php echo e($employee->NIK); ?></td>
														<td><?php echo e($employee->employee_name); ?></td>
														<td><?php echo e($employee->dept_code); ?></td>
														<td>
															<?php echo e($employee->position_name); ?>

															<div class="form-group pull-right">
																<label>
																	<input type="checkbox" id="c_<?php echo e($dt->id); ?>" class="flat-red" checked>
																</label>
															</div>
														</td>
													</tr>
												<?php endif; ?>
											<?php endif; ?>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</tbody>
								</table>
							</div>
							<div class="col-lg-5 col-md-12 col-xs-12">
								<table id="tables2" class="table table-hover">
									<thead>
										<tr>
											<th>No</th>
											<th>Employee</th>
											<th>Departement</th>
											<th>Position</th>
										</tr>
									</thead>
									<tbody>
										<?php $no=0;?>
										<?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<?php if(!$employee->skill_matrix): ?>
												<tr>
													<td><?php $no++;echo $no;?></td>
													<td><?php echo e($employee->employee_name); ?><br><?php echo e($employee->NIK); ?></td>
													<td><?php echo e($employee->dept_code); ?></td>
													<td>
														<?php echo e($employee->position_name); ?>

														<div class="form-group pull-right">
															<label>
																<input type="checkbox" id="c_<?php echo e($dt->id); ?>" class="flat-red">
															</label>
														</div>
													</td>
												</tr>
											<?php endif; ?>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</tbody>
								</table>
							</div>
						<?php endif; ?>
					</div>
					<!-- /.box-body -->
					<div class="box-footer">
						&nbsp;
					</div>
					</form>
				</div>

			</div>
		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->

<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
	<script>
		$(document).ready(function() {
			var table = $('#tables').DataTable({
			'paging'      : true,
			'lengthChange': false,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 500,
			'autoWidth'   : false,
			"lengthMenu"  : [[5,10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
			"scrollX"     : true
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
		$(document).ready(function() {
			var table = $('#tables2').DataTable({
			'paging'      : true,
			'lengthChange': false,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 500,
			'autoWidth'   : false,
			"lengthMenu"  : [[5,10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
			"scrollX"     : true
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
	<script>
		$('body').on("change","#select_group",function(){
			var select_group=document.getElementById('select_group').value;
			window.location.href="<?php echo e($site); ?>/SkillMatric/Group/"+select_group;
		});
	</script>
	<script type="text/javascript">
		$(document).on('click', '.simpan', function() {
			var id_group = "<?php echo e($data['id_group']); ?>";
			var group_skill = {};
			
			// Kumpulkan semua nilai kompetensi
			<?php foreach($employees as $dt){ ?>
				group_skill["<?php echo e($dt->id); ?>"] = $("#c_<?php echo e($dt->id); ?>").is(':checked') ? 1 : 0;
			<?php } ?>
			
			$.ajax({
				type: "POST",
				url: "<?php echo e($site); ?>/SkillMatric/CompetenceGroupEmployee/Save",
				data: {
					id: id_group,
					group_skill: group_skill
				},
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: function(respond) {
					alert(respond); // Tampilkan response dari server
					//location.reload();
				},
				// error: function(xhr) {
				// 	alert("Error: " + xhr.responseText);
				// }
			});
		});
	</script>
	
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts/memo', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\skill_matric\competence_group_employee.blade.php ENDPATH**/ ?>