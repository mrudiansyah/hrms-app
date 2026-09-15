
<?php $__env->startSection('Contents'); ?>
	<div class="content-wrapper">
		<section class="content-header">
			<h1>
				Add Role
				<small>Create new system role</small>
			</h1>
		</section>

		<section class="content">
		<div class="row">
			<div class="col-xs-4">
				<div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Role Form</h3>
                    </div>
                    
                    <form action="<?php echo e(url('/role-management')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="box-body">
                            
                            <?php if($errors->any()): ?>
                                <div class="alert alert-danger">
                                    <ul style="margin-bottom:0;">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label>Role Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo e(old('name')); ?>" placeholder="Enter Role Name" required>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
                            <a href="<?php echo e(url('/role-management')); ?>" class="btn btn-default">Cancel</a>
                        </div>
                    </form>
				</div>
			</div>
		</div>
		</section>
  	</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\role_management\create.blade.php ENDPATH**/ ?>