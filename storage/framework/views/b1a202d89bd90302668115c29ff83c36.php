
<?php $__env->startSection('Contents'); ?>
   <!-- Contents -->
   <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
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
				Group/Shift
				<small>working time</small>
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
			<div class="col-lg-6 col-sm-12 col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-envelope-o"></i>
						<h3 class="box-title">User List</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div>
					</div>
					<div class="box-body" style="overflow-x:scroll;">
						<table id="table2" class="table table-hover">
							<thead>
								<tr>
									<th>Name</th>
									<th>Enail</th>
								</tr>
							</thead>
							<tbody>
								<?php $__currentLoopData = $tb_user; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr class="userlist" data-uid="<?php echo e($dt->id_employee); ?>" data-uname="<?php echo e($dt->name); ?>" data-uemail="<?php echo e($dt->email); ?>">
									<td><?php echo e($dt->name); ?></td>
									<td><?php echo e($dt->email); ?></td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
			</div>
			<div class="col-lg-3 col-sm-6 col-xs-12">
				<div class="box box-warning" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-envelope"></i>
						<h3 class="box-title" id="juduluser">Admin Department</h3>
						<div class="box-tools pull-right">
						<input type="hidden" id="userid">
						<button type="button" class="btn btn-warning btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div>
					</div>
					<div class="box-body">
						<table id="table3" class="table table-hover">
							<thead>
								<tr>
									<th style="width:50px;">ID</th>
									<th><i class="fa fa-key">&nbsp;Role</i></th>
								</tr>
							</thead>
							<tbody id="tbodyroleuser">

							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
			</div>
			<div class="col-lg-3 col-sm-6 col-xs-12">

				<div class="box box-success" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-key"></i>
						<h3 class="box-title">Department List</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-success btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div>
					</div>
					<div class="box-body">
						<table id="table4" class="table table-hover">
							<thead>
								<tr>
								<th style="width:50px;">&nbsp;</th>
									<th style="width:50px;">ID</th>
									<th>Department</th>
								</tr>
							</thead>
							<tbody id="tbodyrole">
								<?php $__currentLoopData = $tb_dept; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td>
									<button title="Add Role" type="button" class="addrole" data-roleid="<?php echo e($dt->id); ?>"><i class="fa fa-angle-double-left"></i></button>
									</td>
									<td><?php echo e($dt->id); ?></td>
									<td><?php echo e($dt->dept_code); ?></td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
				
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->

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
			'autoWidth'   : true
			})
		})
		$(function () {
			$('#table3').DataTable({
			'paging'      : false,
			'lengthChange': false,
			'searching'   : false,
			'ordering'    : false,
			'info'        : false,
			"pageLength"  : 10,
			'autoWidth'   : false
			})
		})
		$(function () {
			$('#table4').DataTable({
			'paging'      : false,
			'lengthChange': false,
			'searching'   : false,
			'ordering'    : false,
			'info'        : false,
			"pageLength"  : 10,
			'autoWidth'   : false
			})
		})
	</script>
	<!-- Durasi Alert -->
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<!-- Submit Data -->
	<script>
		$(document).on('click', '.userlist', function() {
			$.ajaxSetup({
				type:"POST",
				url: "/Staff/SelectUser",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			var uid=$(this).data('uid');
			var uname=$(this).data('uname');
			var uemail=$(this).data('uemail');
			$.ajax({
				data:{user_id:uid},
				success: function(respond){
					$("#tbodyroleuser").html(respond);
					$('#juduluser').text(uname);
					$('#userid').val(uid);
					//alert(goup);
				}
			})
		});
		$(document).on('click', '.addrole', function() {
			$.ajaxSetup({
				type:"POST",
				url: "/Staff/AddDept",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			var userid=$('#userid').val();
			var roleid=$(this).data('roleid');
			$.ajax({
				data:{user_id:userid,role_id:roleid},
				success: function(respond){
					$("#tbodyroleuser").html(respond);
				}
			})
		});
		$(document).on('click', '.removerole', function() {
			$.ajaxSetup({
				type:"POST",
				url: "/Staff/RemoveDept",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			var userid=$('#userid').val();
			var roleid=$(this).data('roleid');
			$.ajax({
				data:{user_id:userid,role_id:roleid},
				success: function(respond){
					$("#tbodyroleuser").html(respond);
				}
			})
		});

	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/staffs.blade.php ENDPATH**/ ?>