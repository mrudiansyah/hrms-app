

<?php $__env->startSection('content'); ?>
<div class="container">
    <h1>Memo List</h1>
    <a href="<?php echo e(route('memos.create')); ?>" class="btn btn-primary mb-3">Create New Memo</a>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Memo ID</th>
                <th>Part No</th>
                <th>Part Name</th>
                <th>Date OT</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $memos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $memo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($memo->id); ?></td>
                <td><?php echo e($memo->id_memo); ?></td>
                <td><?php echo e($memo->part_no); ?></td>
                <td><?php echo e($memo->part_name); ?></td>
                <td><?php echo e($memo->date_ot); ?></td>
                <td><?php echo e($memo->status); ?></td>
                <td>
                    <a href="<?php echo e(route('memos.show', $memo->id)); ?>" class="btn btn-info btn-sm">View</a>
                    <a href="<?php echo e(route('memos.edit', $memo->id)); ?>" class="btn btn-primary btn-sm">Edit</a>
                    <form action="<?php echo e(route('memos.destroy', $memo->id)); ?>" method="POST" style="display:inline;">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\memos\index.blade.php ENDPATH**/ ?>