
<?php $__env->startSection('Contents'); ?>
   <!-- Contents -->
   <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <style>
		#tables th {
		border-top: 1px solid #999;
		border-bottom: 1px solid #999;
		}	
        .table1 tr:hover {
		  cursor:pointer;
        }
		#table2 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table3 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		vertical-align:middle;
		text-align:left;
		}	
		#table4 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table5 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
    </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Employee Leave
				<small>cuti karyawan</small>
			</h1>
			<ol class="breadcrumb">
				<li>
				<a href="#">
					<i class="fa fa-calendar"></i> 
					<?php 
						date_default_timezone_set("Asia/Jakarta");
						echo date('l, d M Y H:i');
					?>
				</a>
				</li>
			</ol>
		</section>

		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-xs-12">
					<div class="box box-primary" style="background:#FFF;">
						<div class="box-header">
							<i class="fa fa-list"></i>
							<h3 class="box-title" id="judul" style="padding-bottom:25px;">New Applied Leave</h3>
							<div class="pull-right">
								<a class="btn btn-app" href="/SKD" target="_blank">
									<i class="fa fa-upload"></i> Upload
								</a>
								<a class="btn btn-app table2">
									<?php if(isset($qty_sick_new)&&$qty_sick_new>0)echo "<span class='badge bg-yellow'>".$qty_sick_new."</span>";?>
									<i class="fa fa-file-o"></i> New SKD
								</a>
								<a class="btn btn-app table3">
									<i class="fa fa-check-square-o"></i> Completed
								</a>
							</div>
						</div>
						<div class="box-body" style="min-height:200px;overflow-x:scroll;">
							<div id="tabel2">
								<table id="table2" class="table table-striped" border="0">
									<thead>
										<tr>
											<th style="width:30px;">NO</th>
											<th style="width:100px;">UPLOAD TIME</th>
											<th style="width:90px;">DEPT</th>
											<th style="width:90px;">NIK</th>
											<th style="width:180px;">EMPLOYEE NAME</th>
											<th style="width:80px;">START</th>
											<th style="width:80px;">FINISH</th>
											<th style="width:40px;">DAYS</th>
											<th>DIAGNOZE</th>
											<th style="width:70px;">SKD</th>
										</tr>
									</thead>
									<tbody>
									<?php $no=0;?>
										<?php $__currentLoopData = $tb_sick_new; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td><?php echo e($dt->upload_time); ?></td>
											<td><?php echo e($dt->dept_code); ?></td>
											<td><?php echo e($dt->NIK); ?></td>
											<td><?php echo e($dt->employee_name); ?></td>
											<td><?php if($dt->start_leave!='')echo date('d-M-Y',strtotime($dt->start_leave));?></td>
											<td><?php if($dt->start_leave!='')echo date('d-M-Y',strtotime($dt->finish_leave));?></td>
											<td><?php echo e($dt->leave_count); ?></td>
											<td><?php echo e($dt->reason); ?></td>
											<td>
												<a href="/SKD/Image/<?php echo e($dt->id); ?>" class="btn btn-info btn-xs" target="_blank"><i class="fa fa-image"></i></a>
												<div class="pull-right">
													<?php $hide=1;if (request()->user()->hasRole('hr_access')&&$hide==0){?>
														<button title="Approve" type="button" class="update-modal btn btn-primary btn-xs" data-idskd="<?php echo e($dt->id); ?>" data-idemployee="<?php echo e($dt->id_employee); ?>" data-idleader="<?php echo e($dt->leader_id); ?>"><i class="fa fa-check-square-o"></i></button>
														<!-- <button title="Approve" type="button" class="approve-modal btn btn-primary btn-xs" data-approveid="<?php echo e($dt->id); ?>" data-approvename="<?php echo e($dt->employee_name); ?>"><i class="fa fa-check"></i></button> -->
														<button title="Refuse" type="button" class="refuse-modal btn btn-danger btn-xs" data-refuseid="<?php echo e($dt->id); ?>" data-refusename="<?php echo e($dt->employee_name); ?>"><i class="fa fa-close"></i></button>
													<?php }?>
												</div>
											</td>
										</tr>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<tbody>
								</table>
							</div>
							<div id="tabel3">
								<div class="row" style="padding-bottom:10px;">
									<div class="col-lg-2 col-xs-6">
										From: 
										<input type="date" class="form-control" id="start" name="start" value="<?php echo e($start); ?>"> 
									</div>
									<div class="col-lg-2 col-xs-6">
										To:
										<input type="date" class="form-control" id="finish" name="finish" value="<?php echo e($finish); ?>">
									</div>
								</div>
								<table id="tables" class="table table-striped" border="0">
									<thead>
										<tr>
											<th style="width:30px;">NO</th>
											<th style="width:100px;">UPLOAD TIME</th>
											<th style="width:90px;">NIK</th>
											<th>EMPLOYEE NAME</th>
											<th style="width:70px;">SKD</th>
										</tr>
									</thead>
									<tbody>
									<?php $no=0;?>
										<?php $__currentLoopData = $tb_sick_proccess; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td><?php echo e($dt->upload_time); ?></td>
											<td><?php echo e($dt->NIK); ?></td>
											<td><?php echo e($dt->employee_name); ?></td>
											<td>
												<a href="/SKD/Image/<?php echo e($dt->id); ?>" target="_blank"><i class="fa fa-image"></i></a>
												<div class="pull-right">
													<?php if (request()->user()->hasRole('allowance')&&$dt->status=='2') {?>
														<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-deleteid="<?php echo e($dt->id); ?>" data-deletename="<?php echo e($dt->employee_name); ?>"><i class="fa fa-trash"></i></button>
													<?php }?>
												</div>
											</td>
										</tr>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<tbody>
								</table>
							</div>
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

	<div class="modal fade" id="modal-refuse">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Refuse Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Refuse : <b id="refusename"></b> ?
					<input type="hidden" id="refuseid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left refuseskd" data-dismiss="modal">Yes, Refuse</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>

	<div class="modal fade" id="modal-approve">
		<div class="modal-dialog box box-primary" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Approve Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Approve : <b id="approvename"></b> ?
					<input type="hidden" id="approveid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary pull-left approve" data-dismiss="modal">Approve</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
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
					Click Yes to Delete : <b id="deletename"></b> ?
					<input type="hidden" id="deleteid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left delete" data-dismiss="modal">Delete</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-update">
		<div class="modal-dialog box box-primary" style="width:350px;">
			<div class="modal-content">
			<form action="/Leave/Add" method="post">
			<input type="hidden" name="id_employee" id="idemployee">
			<input type="hidden" name="id_leave" id="0">
			<input type="hidden" name="id_doc" id="idskd">
			<input type="hidden" name="leader_id" id="idleader">

			<?php echo e(csrf_field()); ?>

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">SKD Form</h4>
				</div>
				<div class="modal-body">
					<div class="col-xs-8" style="padding:0px;padding-right:3px;">
						<div class="form-group">
							<label>Category</label>
							<select name="category" class="form-control" id="category">
								<option value="SKD">SKD</option>
							</select>
						</div>
					</div>
					<div class="col-xs-4" style="padding:0px;padding-left:3px;">
						<div class="form-group">
							<label>Jumlah Hari</label>
							<input type="number" name="leave_count" id="leavecount" class="form-control">
						</div>
					</div>
					<div class="col-xs-6" style="padding:0px;padding-right:3px;">
						<div class="form-group">
							<label>Mulai Sakit</label>
							<input type="date" name="start_leave" class="form-control">
						</div>
					</div>
					<div class="col-xs-6" style="padding:0px;padding-left:3px;">
						<div class="form-group">
							<label>Akhir Sakit</label>
							<input type="date" name="finish_leave" class="form-control">								
						</div>
					</div>

					<div class="form-group">
						<label>Mulai Bekerja</label>
						<input type="date" name="start_working" class="form-control">								
					</div>
					<div class="form-group">
						<label>Diagnosa</label>
						<input type="text" name="reason" class="form-control">								
					</div>
					<div class="form-group">
						<label>Telpon/Alamat Saat Sakit</label>
						<input type="text" name="remark" class="form-control">								
					</div>


				</div>
				<div class="modal-footer" style="text-align:left;">
					<input type="submit" class="btn btn-primary" value="Confirm">
					<button type="button" class="btn btn-default pull-right cancelafter" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
    <?php if($message = Session::get('success')): ?>
		<div class="alert alert-info alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-info"></i> Success Alert</h4>
			<?php echo e($message); ?>

		</div>
    <?php endif; ?>

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
			$('#table4').DataTable({
			'paging'      : true,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : false,
			})
		})	
	</script>
	<!-- Durasi Alert --->
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<script type="text/javascript">
		// Refuse Data
		$(document).on('click', '.approve-modal', function() {
			$('#approveid').val($(this).data('approveid'));
			$('#approvename').text($(this).data('approvename'));
			$('#modal-approve').modal('show');
		});
		$('.modal-footer').on('click', '.approve', function() {
			var x=$('#approveid').val();
			window.location.href='/Leave/SKDApprove/'+x;
		});
	</script>
	<script type="text/javascript">
		// Refuse Data
		$(document).on('click', '.refuse-modal', function() {
			$('#refuseid').val($(this).data('refuseid'));
			$('#refusename').text($(this).data('refusename'));
			$('#modal-refuse').modal('show');
		});
		$('.modal-footer').on('click', '.refuseskd', function() {
			var x=$('#refuseid').val();
			//alert(x);
			window.location.href='/Leave/SKDRefuse/'+x;
		});
	</script>
	<script type="text/javascript">
		// Refuse Data
		$(document).on('click', '.delete-modal', function() {
			$('#deleteid').val($(this).data('deleteid'));
			$('#deletename').text($(this).data('deletename'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#deleteid').val();
			window.location.href='/Leave/SKDDelete/'+x;
		});
	</script>
	<script type="text/javascript">
		// Approve Data
		$(document).on('click', '.update-modal', function() {
			var x=$(this).data('approveid');
			$('#idskd').val($(this).data('idskd'));
			$('#idemployee').val($(this).data('idemployee'));
			$('#idleader').val($(this).data('idleader'));
			$('#modal-update').modal('show');
		});
	</script>
	<script>
		$( document ).ready(function() {
			$("#tabel2").show();
			$("#tabel3").hide();
			$("#judul").text('New Letter');
			$(document).on('click', '.table2', function() {
				$("#tabel2").show(1000);
				$("#tabel3").hide(50);
				$("#judul").text('New Letter');
			});
			$(document).on('click', '.table3', function() {
				$("#tabel3").show(1000);
				$("#tabel2").hide(50);
				$("#judul").text('Proccessed Letter');
			});
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
		$('body').on("change","#start",function(){
			var start=document.getElementById('start').value;
			var finish=document.getElementById('finish').value;
			window.location.href="/Leave/SKD/"+start+"/"+finish;
		});
		$('body').on("change","#finish",function(){
			var start=document.getElementById('start').value;
			var finish=document.getElementById('finish').value;
			window.location.href="/Leave/SKD/"+start+"/"+finish;
		});
	</script>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/m_leave/skd.blade.php ENDPATH**/ ?>