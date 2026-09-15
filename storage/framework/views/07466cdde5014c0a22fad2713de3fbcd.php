
<?php $__env->startSection('Contents'); ?>
   <!-- Contents -->
   <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>Master Access Point<small>Manage Access Points</small></h1>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-xs-12 col-md-8 col-lg-6">
            
                <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Success!</h4>
                    <?php echo e(session('success')); ?>

                </div>
                <?php endif; ?>
                <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-ban"></i> Error!</h4>
                    <?php echo e(session('error')); ?>

                </div>
                <?php endif; ?>

				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-users"></i>
						<h3 class="box-title">Access Point List</h3>
						<div class="box-tools pull-right">
                            <a href="<?php echo e(url('/master_ap/create')); ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add New Data</a>
						</div>
					</div>
					<div class="box-body">
						<table id="tables" class="table table-hover table-bordered">
							<thead>
								<tr style="background:#d3d8d8ff">
									<th style="width:50px;">ID</th>
									<th>Assembly Point</th>
									<th>Area</th>
                                    <th style="width:100px; text-align:center;">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php echo e($dt->id); ?></td>
									<td><?php echo e($dt->access_point); ?></td>
									<td><?php echo e($dt->area); ?></td>
                                    <td align="center">
                                        <a href="<?php echo e(url('/master_ap/'.$dt->id.'/edit')); ?>" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>
                                        <form action="<?php echo e(url('/master_ap/'.$dt->id)); ?>" method="POST" style="display:inline;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this data?');"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		</section>
  	</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('Scripts'); ?>
	<script>
		$(document).ready(function() {
			var table = $('#tables').DataTable({
				'paging'      : true,
				'lengthChange': true,
				'searching'   : true,
				'ordering'    : true,
				'info'        : true,
				"pageLength"  : 10,
				'autoWidth'   : false
			});
		} );
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			    $(this).remove(); 
			});
		}, 3000);
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\master_ap\index.blade.php ENDPATH**/ ?>