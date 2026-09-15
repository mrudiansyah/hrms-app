
<?php $__env->startSection('Contents'); ?>
   <!-- Contents -->
   <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <style>
		#tables th {
            border-top: 1px solid #999;
            border-bottom: 1px solid #999;
            background-color: #d3d8d8ff;
            color: black;
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
			<h1>
				Users
				<small>Manage System Users</small>
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
            
                <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Success!</h4>
                    <?php echo e(session('success')); ?>

                </div>
                <?php endif; ?>
            
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-users"></i>
						<h3 class="box-title">User List</h3>
						<div class="box-tools pull-right">
                            <a href="<?php echo e(url('/user-management/create')); ?>" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Add User</a>
							<!-- <button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button> -->
						</div>
					</div>
					<div class="box-body">
						<table id="tables" class="table table-hover">
							<thead>
								<tr>
									<th style="width:50px;">ID</th>
									<th>NIK</th>
									<th>Name</th>
									<th>Email</th>
									<th>Registered</th>
									<th>Expired Date</th>
									<th>Status</th>
									<?php if(request()->user()->hasRole('role')): ?>
										<th style="width:100px; text-align:center;">Action</th>
									<?php endif; ?>
								</tr>
							</thead>
							<tbody>
								<?php $__currentLoopData = $tb_user; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php echo e($dt->id); ?></td>
									<td><?php echo e($dt->nik); ?></td>
									<td><?php echo e($dt->name); ?></td>
									<td><?php echo e($dt->email); ?></td>
									<td><?php echo e($dt->created_at); ?></td>
									<td><?php echo e($dt->expired_date); ?></td>
									<td>
										<?php if($dt->email_verified_at == null): ?>
											<span class="label label-danger">Unverified</span>
										<?php else: ?>
											<span class="label label-success">Verified</span>
										<?php endif; ?>
									</td>
									<?php if(request()->user()->hasRole('role')): ?>
                                    <td align="center">
                                        <a href="<?php echo e(url('/user-management/'.$dt->id.'/edit')); ?>" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>
                                        <form action="<?php echo e(url('/user-management/'.$dt->id)); ?>" method="POST" style="display:inline;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <?php if($dt->email_verified_at): ?>
                                                <button type="submit" class="btn btn-danger btn-xs" title="Set Unverified / Deactivate" onclick="return confirm('Are you sure you want to set email_verified_at to NULL for this user?');"><i class="fa fa-ban"></i></button>
                                            <?php else: ?>
                                                <button type="submit" class="btn btn-success btn-xs" title="Verify User" onclick="return confirm('Are you sure you want to set this user as verified?');"><i class="fa fa-check"></i></button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
									<?php endif; ?>
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

<?php $__env->stopSection(); ?>

<?php $__env->startSection('Scripts'); ?>
	<!-- Durasi Alert -->
	<script>
		$(document).ready(function() {
			var table = $('#tables').DataTable({
				'paging'      : true,
				'lengthChange': false,
				'searching'   : true,
				'ordering'    : true,
				'order'       : [[0, 'desc']], 
				'info'        : true,
				"pageLength"  : 10,
				'autoWidth'   : false,
				"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]]
			});
		
			new $.fn.dataTable.Buttons( table, {
				buttons: ['copy', 'excel', 'print']
			} );
		
			table.buttons( 0, null ).container().prependTo(
				table.table().container()
			);
		} );
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\user_management\index.blade.php ENDPATH**/ ?>