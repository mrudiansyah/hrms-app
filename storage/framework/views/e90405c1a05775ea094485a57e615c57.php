
<?php $__env->startSection('Contents'); ?>
   <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
	<div class="content-wrapper">
		<section class="content-header">
			<h1>Working Area <small>Manage Employee Areas</small></h1>
		</section>

		<section class="content">
		<div class="row">
			<div class="col-xs-12 col-md-12 col-lg-8">
            
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
						<i class="fa fa-street-view"></i>
						<h3 class="box-title">Employee Area List</h3>
						<div class="box-tools pull-right">
                            <a href="<?php echo e(url('/working_area/create')); ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add New Data</a>
						</div>
					</div>
					<div class="box-body">
						<table id="tables" class="table table-hover table-bordered">
							<thead>
								<tr style="background:#d3d8d8ff">
									<th style="width:50px;">ID</th>
									<th>Employee</th>
									<th>Area</th>
                                    <!-- <th>Updated At</th>
                                    <th>Updated By</th> -->
                                    <th style="width:100px; text-align:center;">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php echo e($dt->id); ?></td>
									<td><?php echo e($dt->id_employee); ?> - <?php echo e($dt->employee_name); ?></td>
									<td><?php echo e($dt->id_area); ?> - <?php echo e($dt->area_name); ?></td>
                                    <!-- <td><?php echo e($dt->updated_at); ?></td>
                                    <td><?php echo e($dt->updated_by); ?></td> -->
                                    <td align="center">
                                        <a href="<?php echo e(url('/working_area/'.$dt->id.'/edit')); ?>" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>
                                        <form action="<?php echo e(url('/working_area/'.$dt->id)); ?>" method="POST" style="display:inline;">
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
				'lengthChange': false,
				'searching'   : true,
				'ordering'    : true,
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
		}, 3000);
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\working_area\index.blade.php ENDPATH**/ ?>