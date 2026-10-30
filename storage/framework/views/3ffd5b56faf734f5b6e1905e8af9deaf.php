
<?php $__env->startSection('Contents'); ?>
	<div class="content-wrapper">
		<section class="content-header">
			<h1>Master Area <small>Edit Data</small></h1>
		</section>

		<section class="content">
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Update</h3>
                        </div>
                        <form action="<?php echo e(url('/master_area/'.$data->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="plant">Plant</label>
                                    <input type="text" class="form-control" name="plant" id="plant" value="<?php echo e($data->plant); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="area">Area</label>
                                    <input type="text" class="form-control" name="area" id="area" value="<?php echo e($data->area); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="ap">Access Point (AP ID)</label>
                                    <input type="number" class="form-control" name="ap" id="ap" value="<?php echo e($data->ap); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="is_active">Status</label>
                                    <select name="is_active" id="is_active" class="form-control">
                                        <option value="1" <?php echo e($data->is_active == 1 ? 'selected' : ''); ?>>Active</option>
                                        <option value="0" <?php echo e($data->is_active == 0 ? 'selected' : ''); ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="box-footer">
                                <a href="<?php echo e(url('/master_area')); ?>" class="btn btn-default">Cancel</a>
                                <button type="submit" class="btn btn-warning pull-right">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
		</section>
  	</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\master_area\edit.blade.php ENDPATH**/ ?>