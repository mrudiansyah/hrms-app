

<?php $__env->startSection('content'); ?>
<div class="container">
    <h1>Memo Details</h1>
    
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Memo #<?php echo e($memo->id); ?></h5>
            <p class="card-text">
                <strong>Memo ID:</strong> <?php echo e($memo->id_memo); ?><br>
                <strong>Part No:</strong> <?php echo e($memo->part_no); ?><br>
                <strong>Part Name:</strong> <?php echo e($memo->part_name); ?><br>
                <strong>Lines:</strong> <?php echo e($memo->lines); ?><br>
                <strong>JPH/GSPH:</strong> <?php echo e($memo->jph_gsph); ?><br>
                <strong>Process:</strong> <?php echo e($memo->process); ?><br>
                <strong>Date OT:</strong> <?php echo e($memo->date_ot); ?><br>
                <strong>Start OT:</strong> <?php echo e($memo->start_ot); ?><br>
                <strong>Finish OT:</strong> <?php echo e($memo->finish_ot); ?><br>
                <strong>Plan Quantity:</strong> <?php echo e($memo->plan_qty); ?><br>
                <strong>Reason OT:</strong> <?php echo e($memo->reason_ot); ?><br>
                <strong>Remark:</strong> <?php echo e($memo->remark); ?><br>
                <strong>Status:</strong> <?php echo e($memo->status == 1 ? 'Active' : 'Inactive'); ?>

            </p>
            
            <a href="<?php echo e(route('memos.edit', $memo->id)); ?>" class="btn btn-primary">Edit</a>
            <form action="<?php echo e(route('memos.destroy', $memo->id)); ?>" method="POST" style="display:inline;">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
            </form>
           <a href="<?php echo e(route('memos.index', ['id_memo' => $memo->id_memo])); ?>" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\memos\show.blade.php ENDPATH**/ ?>