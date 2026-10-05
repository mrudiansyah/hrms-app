
<?php $__env->startSection('Contents'); ?>
	<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
	<!-- Contents -->
   	<style>
		#tablesx th {
		border-top: 1px solid #999;
		border-bottom: 1px solid #999;
		background-color: #2F4F4F;
		color: white;
		}	
        .table1 tr:hover {
		  cursor:pointer;
        }
		#tables th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#tables tbody tr:hover{
			cursor:pointer;
		}
		#table2 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table2 tbody tr:hover{
			cursor:pointer;
		}
		#table3 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table3 tbody tr:hover{
			cursor:pointer;
		}
		#table4 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table4 tbody tr:hover{
			cursor:default;
		}
    </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Training
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-lg-6 col-md-12 col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-list"></i>
						<h3 class="box-title">Training Schedule</h3>
						<div class="box-tools pull-right">
							<a href="/Training/Periode/0" title="Schedule" type="button" class="btn btn-primary btn-xs"><i class="fa fa-angle-double-left"></i> &nbsp; Back</a>
						</div>
					</div>
					<div class="box-body" style="height:65px;">
						<div class="pull-right">
							&nbsp;
						</div>
					</div>
					<div class="box-body" style="overflow-x: scroll;">
						<table id="table2" class="table table-hover">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th>Training Name</th>
									<th>Category</th>
									<th>Date</th>
									<th>Time</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								<?php $__currentLoopData = $tb_training_schedule; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td><?php echo e($dt->training_name); ?></td>
									<td><?php echo e($dt->skill_type); ?></td>
									<td><?php echo e($dt->tanggal); ?></td>
									<td>
										<?php 
											echo date('H:i',strtotime($dt->start));
											echo "~";
											echo date('H:i',strtotime($dt->finish));
										?>
									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
				<div class="box box-info" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-folder-o"></i>
						<h3 class="box-title">Supporting Document</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-info btn-xs support"><i class="fa fa-plus"></i> &nbsp;Add Document</button>
						</div>
					</div>
					<div class="box-body" style="overflow-x: scroll;">
						<table id="table3" class="table table-hover">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th>File Name</th>
								</tr>
							</thead>
							<tbody id="supporting">
								<?php $no=0;?>
								<?php $__currentLoopData = $tb_related_document; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td>
										<?php echo e($dt->file_name); ?>

										<div class="pull-right">
											<button title='Delete' type='button' class='deletesupport-modal btn btn-danger btn-xs' data-supportid='<?php echo e($dt->id); ?>' data-supportname='<?php echo e($dt->file_name); ?>'><i class='fa fa-trash'></i></button>
										</div>
									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
				<div class="box box-warning" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-question-circle"></i>
						<h3 class="box-title">Supporting Test</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-warning btn-xs test"><i class="fa fa-plus"></i> &nbsp;Add New</button>
						</div>
					</div>
					<div class="box-body" style="overflow-x: scroll;">
						<table id="table5" class="table table-hover">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th>Test Name</th>
									<th>Passing Grade</th>
								</tr>
							</thead>
							<tbody id="supporting">
								<?php $test=0;?>
								<?php $__currentLoopData = $tb_related_test; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php $test++;echo $no;?></td>
									<td><?php echo e($dt->test_name); ?></td>
									<td>
										<?php echo e($dt->passing_grade); ?>

										<div class="pull-right">
											<button title="Delete" type="button" class="deletetest-modal btn btn-danger btn-xs" data-deltest="<?php echo e($dt->id); ?>" data-delnametest="<?php echo e($dt->test_name); ?>"><i class="fa fa-trash"></i></button>
										</div>
									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
			</div>
			<div class="col-lg-6 col-md-12 col-xs-12">
				<div class="box box-success" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-user"></i>
						<h3 class="box-title">Participants</h3>
						<div class="box-tools pull-right">
							<?php if($test>0): ?>
							<button type="button" class="btn btn-success btn-xs form"><i class="fa fa-plus"></i> &nbsp;Add Participant</button>
							<?php endif; ?>
						</div>
					</div>
					<div class="box-body">
						<div class="pull-right">
							&nbsp;
						</div>
					</div>
					<div class="box-body" style="overflow-x: scroll;">
						<table id="tables" class="table table-hover">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th>NIK</th>
									<th>Name</th>
									<th>Department</th>
									<th>Position</th>
								</tr>
							</thead>
							<tbody id="konten">
								<?php $no=0;?>
								<?php $__currentLoopData = $tb_training_invitation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td><?php echo e($dt->NIK); ?></td>
									<td><?php echo e($dt->nama_karyawan); ?></td>
									<td><?php echo e($dt->department); ?></td>
									<td>
										<?php echo e($dt->jabatan); ?>

										<div class="pull-right">
											<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="<?php echo e($dt->id); ?>" data-delname="<?php echo e($dt->nama_karyawan); ?>"><i class="fa fa-trash"></i></button>
										</div>
									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
			</div>
		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->

	<div class="modal fade" id="modal-form">
		<div class="modal-dialog box box-success" style="width:350px;">
			<div class="modal-content">
					<form>
					<?php echo e(csrf_field()); ?>

						<div class="modal-header">	
							<b>FORM TRAINING PARTICIPANT</b>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<input type="hidden" id="idComponent" class="form-control">
							<div class="form-group">
							<label>Employee</label>
							<select id="idemployee" name="id_employee" class="form-control selectpicker" data-live-search="true">
								<option value=""></option>
								<?php $__currentLoopData = $tb_employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($dt2->id); ?>"><?php echo e($dt2->employee_name); ?> (<?php echo e($dt2->dept_code); ?>)</option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

							</select>
						</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
							<button type="button" class="btn btn-success pull-right" id="simpan" data-dismiss="modal">Select</button>
						</div>
					</form>
			</div>

			<!-- /.modal-content -->
		</div>
	<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-support">
		<div class="modal-dialog box box-info" style="width:350px;">
			<div class="modal-content">
					<form>
					<?php echo e(csrf_field()); ?>

						<div class="modal-header">	
							<b>FORM SUPPORTING DOCUMENT</b>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<input type="hidden" id="idComponent" class="form-control">
							<div class="form-group">
							<label>Document</label>
							<select id="iddocument" class="form-control selectpicker" data-live-search="true">
								<option value=""></option>
								<?php $__currentLoopData = $tb_training_document; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($dt2->id); ?>"><?php echo e($dt2->document_name); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

							</select>
						</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
							<button type="button" class="btn btn-info pull-right" id="simpandoc" data-dismiss="modal">Select</button>
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
					<h4 class="modal-title">Delete Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Delete : <b id="delname1"></b> ?
					<input type="hidden" id="delid1">
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
	<div class="modal fade" id="modal-deletesupport">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Delete Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Delete : <b id="supportname"></b> ?
					<input type="hidden" id="supportid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left deletesupport" data-dismiss="modal">Yes, Delete</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>

	<div class="modal fade" id="modal-test">
		<div class="modal-dialog box box-success" style="width:350px;">
			<div class="modal-content">
					<form>
					<?php echo e(csrf_field()); ?>

						<div class="modal-header">	
							<b>FORM TRAINING TEST</b>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<div class="form-group">
							<label>List Test</label>
							<select id="idtest" name="id_test" class="form-control selectpicker" data-live-search="true">
								<option value=""></option>
								<?php $__currentLoopData = $tb_training_test; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($dt2->id); ?>"><?php echo e($dt2->test_name); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

							</select>
						</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
							<button type="button" class="btn btn-success pull-right" id="simpantest" data-dismiss="modal">Select</button>
						</div>
					</form>
			</div>

			<!-- /.modal-content -->
		</div>
	<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-deletetest">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Delete Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Delete : <b id="delnametest"></b> ?
					<input type="hidden" id="deltest">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left deletetest" data-dismiss="modal">Yes, Delete</button>
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
	<script>
		$(function () {
			$('#table2').DataTable({
			'paging'      : true,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : false,
			})
		})
		$(function () {
			$('#table3').DataTable({
			'paging'      : false,
			'lengthChange': true,
			'searching'   : false,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : false,
			})
		})
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
				"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]]
        //"iDisplayLength": 50
				//dom: 'Bfrtip',buttons: ['print']
			});
		
			new $.fn.dataTable.Buttons( table, {
				buttons: ['copy', 'excel', 'print']
			} );
		
			table.buttons( 0, null ).container().prependTo(
				table.table().container()
			);
		} );


	</script>
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<script type="text/javascript">
		// Form
			$(document).on('click', '.form', function() {
				$('#modal-form').modal('show');
			});
			$(document).on('click', '.support', function() {
				$('#modal-support').modal('show');
			});

		// Form End
		// Delete Participan
			$(document).on('click', '.delete-modal', function() {
				$('#delid1').val($(this).data('delid'));
				$('#delname1').text($(this).data('delname'));
				$('#modal-delete').modal('show');
			});
			$('.modal-footer').on('click', '.delete', function() {
				var x=$('#delid1').val();
				var idtraining="<?php echo e($id_training); ?>";

				$.ajaxSetup({
					type:"POST",
					url: "/Training/Delete/Plan/Participant",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					data:{idtraining:idtraining,id:x},
					success: function(respond){
						$("#konten").html(respond);
					}
				})
			});
		// Delete Participant End
		// Delete Support
		$(document).on('click', '.deletesupport-modal', function() {
				$('#supportid').val($(this).data('supportid'));
				$('#supportname').text($(this).data('supportname'));
				$('#modal-deletesupport').modal('show');
			});
			$('.modal-footer').on('click', '.deletesupport', function() {
				var x=$('#supportid').val();
				var idtraining="<?php echo e($id_training); ?>";

				$.ajaxSetup({
					type:"POST",
					url: "/Training/Delete/Supporting",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					data:{idtraining:idtraining,id:x},
					success: function(respond){
						$("#supporting").html(respond);
					}
				})
			});
		// Delete Support End
	</script>
	<script>
		$(document).on('click', '#simpan', function() {
			var idtraining="<?php echo e($id_training); ?>";
			var idemployee=$('#idemployee').val();

			$.ajaxSetup({
				type:"POST",
				url: "/Training/Simpan/Plan/Participant",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:{idtraining:idtraining,idemployee:idemployee},
				success: function(respond){
					$("#konten").html(respond);
				}
			})
		});
		$(document).on('click', '.test', function() {
			$('#modal-test').modal('show');
		});
		$(document).on('click', '#simpantest', function() {
			var idtraining="<?php echo e($id_training); ?>";
			var idtest=$('#idtest').val();

			$.ajaxSetup({
				type:"POST",
				url: "/Training/Simpan/Supporting/Test",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:{idtraining:idtraining,idtest:idtest},
				success: function(respond){
					if(respond=='Sukses'){
						location.reload();
					}else{
						// alert(respond);
						location.reload();
					}
				}
			})
		});
		$(document).on('click', '.deletetest-modal', function() {
			$('#deltest').val($(this).data('deltest'));
			$('#delnametest').text($(this).data('delnametest'));
			$('#modal-deletetest').modal('show');
		});
		$('.modal-footer').on('click', '.deletetest', function() {
			var x=$('#deltest').val();
			window.location.href='/Training/Delete/Supporting/Test/'+x;
		});

	</script>
	<script>
		$(document).on('click', '#simpandoc', function() {
			var idtraining="<?php echo e($id_training); ?>";
			var iddocument=$('#iddocument').val();

			$.ajaxSetup({
				type:"POST",
				url: "/Training/Simpan/Supporting",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:{idtraining:idtraining,iddocument:iddocument},
				success: function(respond){
					//alert(respond);
					$("#supporting").html(respond);
				}
			})
		});

	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/training/training_plan_participant.blade.php ENDPATH**/ ?>