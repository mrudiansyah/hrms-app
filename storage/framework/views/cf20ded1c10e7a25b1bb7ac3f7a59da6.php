<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=ksk_performance_template.xls");
?>
<table border="1">
    <thead>
        <tr>
            <th>NO</th>
            <th>PERIODE</th>
            <th>ID_KSK_DETAIL</th>
            <th>NIK</th>
            <th>NAME</th>
            <th>DEPT</th>
            <th>WARNING_LETER</th>
            <th>SICK</th>
            <th>PERMIT</th>
            <th>ALPA</th>
            <th>LATE(Frequency)</th>
            <th>LATE(Minutes)</th>
            <th>PERFORMANCE</th>
        </tr>
    </thead>
    <tbody>
        <?php $no=0;?>
        <?php $__currentLoopData = $tb_ksk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $bulan=$dt->months%12;$tahun=($dt->months-$bulan)/12;?>
            <tr>
                <td><?php $no++;echo $no;?></td>
                <td><?php echo e($periode); ?></td>
                <td><?php echo e($dt->id); ?></td>
                <td><?php echo e($dt->NIK); ?></td>
                <td><?php echo e($dt->employee_name); ?></td>
                <td><?php echo e($dt->dept_code); ?></td>
                <td><?php echo e($dt->warning_letter); ?></td>
                <td><?php echo e($dt->sick); ?></td>
                <td><?php echo e($dt->permit); ?></td>
                <td><?php echo e($dt->alpa); ?></td>
                <td><?php echo e($dt->late); ?></td>
                <td><?php echo e($dt->minutes); ?></td>
                <td><?php echo e($dt->performance); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/m_employee/ksk_detail_template.blade.php ENDPATH**/ ?>