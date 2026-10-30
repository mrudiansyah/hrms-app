<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=deduction_template.xls");
?>
<table border="1">
    <tr>
        <th>No</th>
        <th>PERIODE</th>
        <th>TIPE</th>
        <th>EMS_ID</th>
        <th>NIK</th>
        <th>EMPLOYEE</th>
        <th>DEPARTMENT</th>
        <th>COMPONENT</th>
        <th>AMOUNT</th>
        <th>DESCRIPTION</th>
    </tr>
    <tr>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>koperasi,seragam,pant,lain_lain</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
    </tr>
    <?php $no=0;?>
    <?php $__currentLoopData = $tb_employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
        <td><?php $no++;echo $no;?></td>
        <td><?php echo e($periode); ?></td>
        <td><?php echo e($dt->tipe_kontrak); ?></td>
        <td><?php echo e($dt->id); ?></td>
        <td><?php echo e($dt->NIK); ?></td>
        <td><?php echo e($dt->nama_karyawan); ?></td>
        <td><?php echo e($dt->dept_code); ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\payroll\salary_deduction_template.blade.php ENDPATH**/ ?>