
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
					<div class="box-header">
						<i class="fa fa-file-text-o"></i>Employee List
						<div class="box-tools pull-right">
							<button class="btn btn-primary btn-md create-modal" data-id="" data-id_employee="" data-id_machine="" data-id_group="">
								<i class="fa fa-user-plus"></i> &nbsp;Add New
							</button>
						</div>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12">
								&nbsp;
							</div>
						</div>
						<table id="tables" class="table table-hover">
							<thead>
								<tr>
									<th>No</th>
									<th>NIK</th>
									<th>Employee Name</th>
									<th>Shift</th>
									<th>Department</th>
									<th>Line</th>
									<th>Machine</th>
									<th>Group Competence</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								<?php $__currentLoopData = $data['tb_employee_mc']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td>
										<?php $no++;echo $no;?>
									</td>
									<td><?php echo e($dt->NIK); ?></td>
									<td><?php echo e($dt->employee_name); ?></td>
									<td><?php echo e($dt->shift_code); ?></td>
									<td><?php echo e($dt->dept_code); ?></td>
									<td><?php echo e($dt->resource_group_id); ?></td>
									<td><?php echo e($dt->resource_label); ?></td>
									<td><?php echo e($dt->group_name); ?></td>
									<td>
										<div class="pull-left">
											<button class="btn btn-info btn-xs" onclick="window.open('/EMS/SkillMatric/EmployeeCompetence/<?php echo e($dt->id_employee); ?>/0', '_blank')">
												<i class="fa fa-list-ol"></i>
											</button>
											<button class="btn btn-warning btn-xs create-modal" data-id_tabel="<?php echo e($dt->id); ?>" data-id_employee="<?php echo e($dt->id_employee); ?>" data-id_machine="<?php echo e($dt->id_machine); ?>" data-id_group="<?php echo e($dt->id_group); ?>">
												<i class="fa fa-edit"></i>
											</button>
											<button class="btn btn-danger btn-xs delete-modal" data-delid="<?php echo e($dt->id); ?>" data-delid2="tb_employee_mc" data-delname="<?php echo e($dt->employee_name); ?>">
												<i class="fa fa-trash"></i>
											</button>
										</div>
									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
					<div class="box-footer">
						&nbsp;
					</div>
				</div>

			</div>
		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->
	<div class="modal fade" id="modal-delete">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Delete Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Delete : <b id="delname"></b> ?
					<input type="hidden" id="delid">
					<input type="hidden" id="delid2">
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
	<div class="modal fade" id="modal-create">
		<div class="modal-dialog box box-primary" style="width:300px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Form Employee Job</h4>
					<input type="hidden" name="id_tabel" id="id_tabel" class="form-control">
				</div>
				<div class="modal-body">
					<form role="form" action="" method="post">
						<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
						<?php echo e(csrf_field()); ?>

						<div class="box-body">
							<div class="form-group">
								<label>Employee</label>
								<select name="id_employee" class="form-control" id="id_employee">
									<option value=""></option>
									<?php $__currentLoopData = $data['tb_employees']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($dt2->id); ?>"><?php echo e($dt2->employee_name); ?> <?php echo e($dt2->NIK); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
							</div>
							<div class="form-group">
								<label>Machine</label>
								<select name="id_machine" class="form-control" id="id_machine">
									<option value=""></option>
									<?php $__currentLoopData = $data['tb_machine']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt3): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($dt3->id); ?>"><?php echo e($dt3->resource_id); ?> (<?php echo e($dt3->resource_group_id); ?>)</option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
							</div>
							<div class="form-group">
								<label>Group Competence</label>
								<select name="id_group" class="form-control" id="id_group">
									<option value=""></option>
									<?php $__currentLoopData = $data['tb_group']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt3): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($dt3->id); ?>"><?php echo e($dt3->group_name); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
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
		$(document).on('click', '.create-modal', function() {
            $("#id_tabel").val($(this).data('id_tabel'));
            $("#id_employee").val($(this).data('id_employee'));
            $("#id_machine").val($(this).data('id_machine'));
            $("#id_group").val($(this).data('id_group'));
			$('#modal-create').modal('show');
		});

		$('.modal-footer').on('click', '.simpan', function() {
            var a=$("#id_tabel").val();
			var b=$("#id_employee").val();
			var c=$("#id_machine").val();
			var d=$("#id_group").val();
            var datas = {
                id:a,
				id_employee:b,
				id_machine:c,
				id_group:d
            }
			$.ajaxSetup({
				type:"POST",
				url: "<?php echo e($site); ?>/SkillMatric/MainJob/Save",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:datas,
				success: function(respond){
					//alert(respond);
					location.reload();
				}
			})
		});
		// Delete Data
		$(document).on('click', '.delete-modal', function() {
			$('#delid').val($(this).data('delid'));
			$('#delname').text($(this).data('delname'));
			$('#delid2').val($(this).data('delid2'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#delid').val();
			var y=$('#delid2').val();
            var datas = {
                id:x,
				tabel:y
            }
			$.ajaxSetup({
				type:"POST",
				url: "<?php echo e($site); ?>/SkillMatric/Delete/",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:datas,
				success: function(respond){
					//alert(respond);
					location.reload();
				}
			})
		});
	</script>
	<script>
		$(document).ready(function() {
			var table = $('#tables').DataTable({
			'paging'      : true,
			'lengthChange': false,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
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
	
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts/memo', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\skill_matric\main_job.blade.php ENDPATH**/ ?>