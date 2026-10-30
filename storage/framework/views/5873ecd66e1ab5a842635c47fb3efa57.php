
<?php $__env->startSection('Contents'); ?>
	<div class="content-wrapper">
		<section class="content-header">
			<h1>Master Access Point <small>Create New Data</small></h1>
		</section>

		<section class="content">
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Input</h3>
                        </div>
                        <form action="<?php echo e(url('/master_ap')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="access_point">Assembly Point (Number)</label>
                                    <input type="number" class="form-control" name="access_point" id="access_point" required>
                                </div>
                                <div class="form-group">
                                    <label for="area">Area Description</label>
                                    <input type="text" class="form-control" name="area" id="area" required>
                                </div>
                            </div>
                            <div class="box-footer">
                                <a href="<?php echo e(url('/master_ap')); ?>" class="btn btn-default">Cancel</a>
                                <button type="submit" class="btn btn-primary pull-right">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
		</section>
  	</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\master_ap\create.blade.php ENDPATH**/ ?>