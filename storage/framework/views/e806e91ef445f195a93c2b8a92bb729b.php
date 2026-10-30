<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=rapel_template.xls");
?>
<table border="1">
    <tr>
        <th rowspan="2">No</th>
        <th rowspan="2">PERIODE</th>
        <th rowspan="2">TIPE</th>
        <th rowspan="2">EMS_ID</th>
        <th rowspan="2">NIK</th>
        <th rowspan="2">EMPLOYEE</th>
        <th rowspan="2">DEPARTMENT</th>
        <th colspan="2">MASA_KERJA</th>
        <th rowspan="2">TOTAL</th>
        <th rowspan="2">PAY_DATE</th>
        <th rowspan="2">RAPEL_GAJI</th>
        <th rowspan="2">RAPEL_OT</th>
        <th rowspan="2">RAPEL_THR</th>
        <th rowspan="2">TOTAL_RAPEL</th>
    </tr>
    <tr>
        <th>YEAR</th>
        <th>MONTH</th>
    </tr>
    <?php $no=0;?>
    <?php $__currentLoopData = $tb_employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $joindate=$dt->tanggal_kontrak;
        $sekarang=date('Y-m-d');
        $datetime1 = date_create($sekarang);
        $datetime2 = date_create($joindate);
        $bulan = date_diff($datetime1, $datetime2);
        $lama_tahun=$bulan->format('%y');
        $lama_bulan=$bulan->format('%m');
        $masa_kerja=number_format(($lama_tahun*12)+$lama_bulan,2);
    ?>
    <tr>
        <td><?php $no++;echo $no;?></td>
        <td><?php echo e($periode); ?></td>
        <td><?php echo e($dt->tipe_kontrak); ?></td>
        <td><?php echo e($dt->id); ?></td>
        <td><?php echo e($dt->NIK); ?></td>
        <td><?php echo e($dt->nama_karyawan); ?></td>
        <td><?php echo e($dt->dept_code); ?></td>
        <td><?php echo e($lama_tahun); ?></td>
        <td><?php echo e($lama_bulan); ?></td>
        <td><?php echo e($masa_kerja); ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\payroll\salary_rapel_template.blade.php ENDPATH**/ ?>