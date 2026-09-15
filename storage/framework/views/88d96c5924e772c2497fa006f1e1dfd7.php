<!DOCTYPE html>
<html>
<head>
    <title>Data Manifest <?php echo e($tgl); ?></title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #d3d8d8; }
        .dept-header { background-color: #f4f4f4; font-weight: bold; font-size: 1.1em; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Data Manifest - <?php echo e($tgl); ?></h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Employee</th>
                <th>Masuk</th>
                <th>Ijin</th>
                <th>TL</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $manifests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept => $employees): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="dept-header">
                    <td></td>
                    <td colspan="5"><?php echo e($dept ?: 'NO DEPARTMENT'); ?></td>
                </tr>
                <?php $no=0;?>
                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $no++;?>
                <tr>
                    <td><?php echo e($no); ?></td>
                    <td><?php echo e($dt->employee_name); ?><br><?php echo e($dt->NIK); ?></td>
                    <td>
                        <?php if($dt->masuk!=null): ?>
                            <?php echo e(date('H:i',strtotime($dt->masuk))); ?>

                        <?php endif; ?>
                    </td>
                    <td><?php echo e($dt->ijin); ?></td>
                    <td class="text-center"><?php echo e($dt->tugas_luar == '1' ? '√' : ' '); ?></td>
                    <td class="text-center"><?php echo e($dt->status == '1' ? '√' : ' '); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center">No data found for <?php echo e($tgl); ?>.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\manifest\pdf.blade.php ENDPATH**/ ?>