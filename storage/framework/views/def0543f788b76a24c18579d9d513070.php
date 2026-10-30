<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=tax_conditional_template.xls");
?>
<table border="1" style="min-width:100%;">
    <thead>
        <tr>
            <th style="width:50px;">No<br>&nbsp;</th>
            <th>NIK<br>&nbsp;</th>
            <th >NAME<br>&nbsp;</th>
            <th>DEPT<br>&nbsp;</th>
            <th>PERIODE<br>&nbsp;</th>
            <th>ID_TAX<br>&nbsp;</th>
            <th>PPh21<br>Conditional</th>
            <th>Status<br>Implement</th>
        </tr>
    </thead>
    <tbody>
    <?php $no=0;?>
    <?php $__currentLoopData = $tb_salary_tax; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td>
                <?php
                    $no++;
                ?>
                <?php echo e($no); ?>

            </td>
            <td><?php echo e($dt->NIK); ?></td>
            <td><?php echo e($dt->nama_karyawan); ?></td>
            <td><?php echo e($dt->dept_code); ?></td>
            <td><?php echo e($dt->periode); ?></td>
            <td><?php echo e($dt->id); ?></td>
            <td><?php if($dt->tax_amount=='')echo "0";else echo $dt->tax_amount?></td>
            <td><?php if($dt->status_implement=='')echo "0";else echo $dt->status_implement;?></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\payroll\tax_conditional_template.blade.php ENDPATH**/ ?>