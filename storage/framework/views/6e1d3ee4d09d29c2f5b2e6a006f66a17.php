
<?php $__env->startSection('Contents'); ?>

<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Overtime Request
				<small>&nbsp;</small>
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-xs-12 col-md-6 col-lg-4">
                    <form action="<?php echo e(route('memos.update', $memo->id)); ?>" method="POST">
                        <div class="box box-primary" style="background:#FFF;">
                            <div class="box-header">
                                <i class="fa fa-edit"></i>
                                <h3 class="box-title">Update Reason</h3>
                                <div class="box-tools pull-right">
                                    &nbsp;
                                </div>
                            </div>
                            <div class="box-body">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <div class="row">
                                    <div class="col-xs-12 col-md-12 col-lg-12">
                                        <input type="hidden" class="form-control" id="id_memo" name="id_memo" value="<?php echo e($id_memo); ?>" required>
                                        <input type="hidden" class="form-control" id="statua" name="status" value="1" required>
                                        <input type="hidden" class="form-control" id="part_no" name="part_no" maxlength="30" value="<?php echo e($memo->part_no); ?>">
                                        <input type="hidden" class="form-control" id="part_name" name="part_name" maxlength="50" value="<?php echo e($memo->part_name); ?>">
                                        <input type="hidden" class="form-control" id="lines" name="lines" maxlength="50" value="<?php echo e($memo->lines); ?>">
                                        <input type="hidden" class="form-control" id="jph_gsph" name="jph_gsph" value="<?php echo e($memo->jph_gsph); ?>">
                                        <input type="hidden" class="form-control" id="process" name="process" maxlength="50" value="<?php echo e($memo->process); ?>">
                                        <input type="hidden" class="form-control" id="date_ot" name="date_ot" value="<?php echo e($memo->date_ot); ?>">
                                        <input type="hidden" class="form-control" id="start_ot" name="start_ot" value="<?php echo e($memo->start_ot); ?>">
                                        <input type="hidden" class="form-control" id="finish_ot" name="finish_ot" value="<?php echo e($memo->finish_ot); ?>">
                                        <input type="hidden" class="form-control" id="plan_qty" name="plan_qty" value="<?php echo e($memo->plan_qty); ?>">

                                        <div class="form-group">
                                            <label>Reason Category</label>
                                            <select name="reason_ot" class="form-control selectpicker" data-live-search="true" id="reason_ot">
                                                <option value="<?php echo e($memo->reason_ot); ?>"><?php echo e($memo->reason_ot); ?></option>
                                                <?php $__currentLoopData = $tb_reason; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($dt->reason_ot); ?>"><?php echo e($dt->reason_ot); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="remark">Remark</label>
                                            <textarea class="form-control" id="remark" name="remark" maxlength="100" rows="3"><?php echo e($memo->remark); ?></textarea>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <div class=" pull-right">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <a class="btn btn-default" href="/EMS/GeneralMemo/Detail/<?php echo e($id_memo); ?>" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>

				</div>
			</div>
			<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\memos\edit_reason.blade.php ENDPATH**/ ?>