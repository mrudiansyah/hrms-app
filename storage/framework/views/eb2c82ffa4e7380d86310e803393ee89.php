<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=work_entry_template.xls");
?>
<table border="1">
    <tr>
        <th>No</th>
        <th>EMS_ID</th>
        <th>Group</th>
        <th>NIK</th>
        <th>Nama</th>
        <th>Dept</th>
        <th>Periode</th>
        <th>Transport</th>
        <th>Shift</th>
        <th>Ketidakhadiran</th>
        <!--
        <th>Hari Kerja</th>
        <th>Cuti</th>
        <th>Ijin</th>
        <th>Sakit</th>
        <th>Mangkir</th>
        <th>OT Weekend</th>
        -->
    </tr>
    <?php $no=0;?>
    <?php $__currentLoopData = $tb_employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
        <td><?php $no++;echo $no;?></td>
        <td><?php echo e($dt->id); ?></td>
        <td><?php echo e($dt->tipe_kontrak); ?></td>
        <td><?php echo e($dt->NIK); ?></td>
        <td><?php echo e($dt->nama_karyawan); ?></td>
        <td><?php echo e($dt->dept_code); ?></td>
        <td><?php echo e($periode); ?></td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <!--
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        -->
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\payroll\work_entry_template.blade.php ENDPATH**/ ?>