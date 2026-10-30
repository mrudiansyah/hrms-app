<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=work_entry_other_template.xls");
?>
<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>EMS_ID</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Dept</th>
            <th>Periode</th>
            <th>Days</th>
        </tr>
    </thead>
    <tbody>
        <?php $no=0;?>
        <?php $__currentLoopData = $tb_employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e(++$no); ?></td>
            <td><?php echo e($dt->id_employee); ?></td>
            <td><?php echo e($dt->NIK); ?></td>
            <td><?php echo e($dt->employee_name ?? $dt->nama_karyawan); ?></td>
            <td><?php echo e($dt->department); ?></td>
            <td><?php echo e($periode); ?></td>
            <td>0</td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\payroll\work_entry_other_template.blade.php ENDPATH**/ ?>