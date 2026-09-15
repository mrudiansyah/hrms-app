
<?php $__env->startSection('Contents'); ?>
	<div class="content-wrapper">
		<section class="content-header">
			<h1>Working Area <small>Edit Data</small></h1>
		</section>

		<section class="content">
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Update</h3>
                        </div>
                        <form action="<?php echo e(url('/working_area/'.$data->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="id_employee">Employee</label>
                                    <select class="form-control" name="id_employee" id="id_employee" required>
                                        <option value="">-- Select Employee --</option>
                                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($emp->id); ?>" <?php echo e($data->id_employee == $emp->id ? 'selected' : ''); ?>><?php echo e($emp->id); ?> - <?php echo e($emp->employee_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="id_area">Area</label>
                                    <select class="form-control" name="id_area" id="id_area" required>
                                        <option value="">-- Select Area --</option>
                                        <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($area->id); ?>" <?php echo e($data->id_area == $area->id ? 'selected' : ''); ?>><?php echo e($area->id); ?> - <?php echo e($area->area); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="box-footer">
                                <a href="<?php echo e(url('/working_area')); ?>" class="btn btn-default">Cancel</a>
                                <button type="submit" class="btn btn-warning pull-right">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
		</section>
  	</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\working_area\edit.blade.php ENDPATH**/ ?>