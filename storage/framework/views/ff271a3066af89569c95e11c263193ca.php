
<?php $__env->startSection('Contents'); ?>
	<div class="content-wrapper">
		<section class="content-header">
			<h1>
				Add User
				<small>Create new system user</small>
			</h1>
		</section>

		<section class="content">
		<div class="row">
			<div class="col-xs-12 col-md-6 col-lg-4">
				<div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">User Form</h3>
                    </div>
                    
                    <form action="<?php echo e(url('/user-management')); ?>" method="POST">
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
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo e(old('name')); ?>" placeholder="Enter Name" required>
                            </div>
                            <div class="form-group">
                                <label>Email address</label>
                                <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>" placeholder="Enter email" required>
                            </div>

                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                            <a href="<?php echo e(url('/user-management')); ?>" class="btn btn-default">Cancel</a>
                        </div>
                    </form>
				</div>
			</div>
		</div>
		</section>
  	</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\user_management\create.blade.php ENDPATH**/ ?>