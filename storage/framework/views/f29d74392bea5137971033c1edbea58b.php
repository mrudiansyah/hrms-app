
<?php $__env->startSection('Contents'); ?>
	<div class="content-wrapper">
		<section class="content-header">
			<h1>Master Access Point <small>Edit Data</small></h1>
		</section>

		<section class="content">
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Update</h3>
                        </div>
                        <form action="<?php echo e(url('/master_ap/'.$data->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="access_point">Assembly Point (Number)</label>
                                    <input type="number" class="form-control" name="access_point" id="access_point" value="<?php echo e($data->access_point); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="area">Area Description</label>
                                    <input type="text" class="form-control" name="area" id="area" value="<?php echo e($data->area); ?>" required>
                                </div>
                            </div>
                            <div class="box-footer">
                                <a href="<?php echo e(url('/master_ap')); ?>" class="btn btn-default">Cancel</a>
                                <button type="submit" class="btn btn-warning pull-right">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
		</section>
  	</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\master_ap\edit.blade.php ENDPATH**/ ?>