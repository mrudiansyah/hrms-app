
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
			<div class="col-lg-5 col-md-12 col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-list"></i>
						<h3 class="box-title">Training Actual</h3>
						<div class="box-tools pull-right">
							<a href="/Training/Actuals/0/0" title="Schedule" type="button" class="btn btn-default btn-xs"><i class="fa fa-angle-double-left"></i> &nbsp; Back</a>
						</div>
					</div>
					<div class="box-body" style="overflow-x: scroll;">
						<table id="table2" class="table table-hover">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th>Training Name</th>
									<th>Category</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								<?php $__currentLoopData = $tb_training_actual; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td><?php echo e($dt->training_name); ?></td>
									<td><?php echo e($dt->skill_type); ?>

										<?php 
											echo date('H:i',strtotime($dt->start_aktual));
											echo "~";
											echo date('H:i',strtotime($dt->finish_aktual));
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
							&nbsp;
						</div>
					</div>
					<div class="box-body" style="overflow-x: scroll;">
						<table id="table4" class="table table-hover">
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
											<a href="/ESS/Document/Download/<?php echo e($dt->id_doc); ?>" title="Download" type="button" class="btn btn-info btn-xs"><i class="fa fa-download"></i></a>
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
							<!-- <button type="button" class="btn btn-warning btn-xs test"><i class="fa fa-plus"></i> &nbsp;Add New</button> -->
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
								<?php $no=0;?>
								<?php $__currentLoopData = $tb_related_test; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td><?php echo e($dt->test_name); ?></td>
									<td>
										<?php echo e($dt->passing_grade); ?>

									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
			</div>
			<div class="col-lg-7 col-md-12 col-xs-12">
				<div class="box box-success" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-user"></i>
						<h3 class="box-title">Participants</h3>
						<div class="box-tools pull-right">
							<a href="/Training/Actual/<?php echo e($id_training); ?>" id="savechange"><button type="button" class="btn btn-info btn-xs"><i class="fa fa-floppy-o"></i> &nbsp;Save Change</button></a>
							<?php if($in_class==1): ?>
								<button type="button" class="btn btn-success btn-xs form"><i class="fa fa-plus"></i> &nbsp;Add New</button>
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
								<?php $__currentLoopData = $tb_training_participant; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td><?php echo e($dt->NIK); ?></td>
									<td><?php echo e($dt->nama_karyawan); ?></td>
									<td><?php echo e($dt->department); ?></td>
									<td>
										<?php echo e($dt->jabatan); ?>

										<div class="pull-right">
											<?php if($dt->free_test==''&&$in_class==1): ?>
												<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="<?php echo e($dt->id); ?>" data-delname="<?php echo e($dt->nama_karyawan); ?>"><i class="fa fa-trash"></i></button>
											<?php endif; ?>
										</div>
									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-graduation-cap"></i>
						<h3 class="box-title">Evaluation</h3>
						<div class="box-tools pull-right">
							&nbsp;
						</div>
					</div>
					<div class="box-body">
						<div class="pull-right">
							&nbsp;
						</div>
					</div>
					<div class="box-body" style="overflow-x: scroll;">
						<table id="table3" class="table table-hover">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th>NIK</th>
									<th>Name</th>
									<th>
										Free Test
										<div class="box-tools pull-right">
											<a href="/Training/Monitor/Free/<?php echo e($id_training); ?>" title="Monitoring" type="button" class="btn btn-primary btn-xs"><i class="fa fa-tv"></i></a>
										</div>
									</th>
									<th>
										Post Test
										<div class="box-tools pull-right">
											<a href="/Training/Monitor/Post/<?php echo e($id_training); ?>" title="Monitoring" type="button" class="btn btn-primary btn-xs"><i class="fa fa-tv"></i></a>
										</div>
									</th>
								</tr>
							</thead>
							<tbody id="konten">
								<?php $no=0;?>
								<?php $__currentLoopData = $tb_training_participant; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td><?php echo e($dt->NIK); ?></td>
									<td><?php echo e($dt->nama_karyawan); ?></td>
									<td><?php echo e($dt->free_test); ?></td>
									<td>
										<?php echo e($dt->post_test); ?>

										<div class="pull-right">
											&nbsp;
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
							<select id="idemployee" class="form-control"></select>
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

	<div class="modal fade" id="modal-delete">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Delete Participant</h4>
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

<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
	<!-- page script Tabel-->
	<script>
		$(function () {
			$('#table2').DataTable({
			'paging'      : false,
			'lengthChange': true,
			'searching'   : false,
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
		$(function () {
			$('#table4').DataTable({
			'paging'      : false,
			'lengthChange': true,
			'searching'   : false,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : false,
			})
		})
		$(function () {
			$('#table5').DataTable({
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
			document.getElementById('savechange').style.display = "none"; 
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
				var idtraining="<?php echo e($id_training); ?>";

				$.ajaxSetup({
					type:"POST",
					url: "/Training/Update/Participant",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					data:{idtraining:idtraining},
					success: function(respond){
						//alert(respond);
						$("#idemployee").html(respond);
					}
				})
				$('#modal-form').modal('show');
			});
		// Form End
		// Delete Data
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
					url: "/Training/Delete/Actual/Participant",
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
		// Delete End
	</script>
	<script>
		$(document).on('click', '#simpan', function() {
			var idtraining="<?php echo e($id_training); ?>";
			var idemployee=$('#idemployee').val();

			$.ajaxSetup({
				type:"POST",
				url: "/Training/Simpan/Actual/Participant",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:{idtraining:idtraining,idemployee:idemployee},
				success: function(respond){
					if(respond=='Failed, Employee already Exixts'||respond=='No Action'||respond=='Masuk'){
						alert(respond)
					}else{
						$("#konten").html(respond);
						document.getElementById('savechange').style.display = "inline"; 
					}
				}
			})
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
						alert(respond);
					}
				}
			})
		});

	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/training/training_actual_participant.blade.php ENDPATH**/ ?>