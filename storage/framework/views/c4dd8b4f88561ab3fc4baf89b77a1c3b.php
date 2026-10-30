
<?php $__env->startSection('Contents'); ?>
<div style="font-size:12px;">
	<div class="text-left" style="margin-bottom: 10px;">
		<h4 style="margin: 0; font-weight: bold;">PT SUMMIT ADYAWINSA INDONESIA</h4>
		<h4 style="margin: 2px 0; font-weight: bold;">RECAPITULATION EMPLOYEE COMPENSATION PAYMENT</h4>
		<h4 style="margin: 0; font-weight: bold;">PERIODE <?php echo e(date('Y, F', strtotime($periode.'-01'))); ?></h4>
	</div>
	<table class="table" style="border: 1px solid #000;">
		<thead>
            <tr>
                <th style="border: 1px solid #000;">DEPARTEMEN</th>
                <th style="border: 1px solid #000; width: 60px;">COST CENTER</th>
                <th style="border: 1px solid #000; width: 60px;">Total Employee</th>
                <th style="border: 1px solid #000; width: 80px;">SALARY</th>
                <th style="border: 1px solid #000; width: 100px;">COMPENSATION<br>(Gross)</th>
                <th style="border: 1px solid #000; width: 80px;">TAX</th>
                <th style="border: 1px solid #000; width: 100px;">COMPENSATION<br>(take home pay)</th>
            </tr>
		</thead>
		<tbody>
            <?php 
                $prev_dept = '';
                $total_emp = 0; $total_sal = 0; $total_gross = 0; $total_tax = 0; $total_nett = 0;
            ?>
            <?php $__currentLoopData = $tb_rekap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php 
                    $total_emp += $dt->jml_employee;
                    $total_sal += $dt->salary;
                    $total_gross += $dt->compensation_gross;
                    $total_tax += $dt->tax;
                    $total_nett += $dt->compensation_nett;
                ?>
                <tr>
                    <?php if($prev_dept != $dt->bagian): ?>
                        <?php 
                            $rowspan = $tb_rekap->where('bagian', $dt->bagian)->count();
                            $prev_dept = $dt->bagian;
                        ?>
                        <td rowspan="<?php echo e($rowspan); ?>" style="vertical-align: middle; font-weight: bold; border: 1px solid #000;"><?php echo e($dt->bagian); ?></td>
                    <?php endif; ?>
                    <td style="text-align: center; border: 1px solid #000;"><?php echo e($dt->cc_code); ?></td>
                    <td style="text-align: center; border: 1px solid #000;"><?php echo e($dt->jml_employee); ?></td>
                    <td style="text-align: right; border: 1px solid #000;"><?php echo e(number_format($dt->salary, 0)); ?></td>
                    <td style="text-align: right; border: 1px solid #000;"><?php echo e(number_format($dt->compensation_gross, 0)); ?></td>
                    <td style="text-align: right; border: 1px solid #000;"><?php echo e(number_format($dt->tax, 0)); ?></td>
                    <td style="text-align: right; border: 1px solid #000;"><?php echo e(number_format($dt->compensation_nett, 0)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		</tbody>
        <tfoot style="font-weight: bold; background: #eee;">
            <tr>
                <td colspan="2" style="text-align: right; border: 1px solid #000;">GRAND TOTAL</td>
                <td style="text-align: center; border: 1px solid #000;"><?php echo e(number_format($total_emp, 0)); ?></td>
                <td style="text-align: right; border: 1px solid #000;"><?php echo e(number_format($total_sal, 0)); ?></td>
                <td style="text-align: right; border: 1px solid #000;"><?php echo e(number_format($total_gross, 0)); ?></td>
                <td style="text-align: right; border: 1px solid #000;"><?php echo e(number_format($total_tax, 0)); ?></td>
                <td style="text-align: right; border: 1px solid #000;"><?php echo e(number_format($total_nett, 0)); ?></td>
            </tr>
        </tfoot>
	</table>
    <div style="height:50px;">&nbsp;</div>
    <div style="width:100%;">
        <?php $__currentLoopData = $tb_approval; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <table border="1" cellspacing="0" cellpadding="5" width="100%" align="center">
                <tr>
                    <td colspan="4" style="text-align:center;">Approved by</td>
                    <td style="text-align:center;">Verified by</td>
                </tr>
                <tr>
                    <td style="text-align:center;vertical-align:middle;height:100px;" class="relative">
                        <?php
                        $confirm=base_path().'/public/approval/confirm.png';
                        $rejected=base_path().'/public/approval/rejected.png';
                        ?>
                        <?php if($dt2->status_1==1): ?>
                            <img src='<?php echo e($confirm); ?>' style='width:80px;height:40px;'>
                            <br><?php echo e($dt2->date_1); ?>

                        <?php elseif($dt2->status_1==2): ?>
                            <img src='<?php echo e($rejected); ?>' style='width:80px;height:60px;'>
                            <br><?php echo e($dt2->date_1); ?>

                        <?php else: ?>
                            &nbsp;
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;vertical-align:middle;">
                        <?php if($dt2->status_2==1): ?>
                            <img src='<?php echo e($confirm); ?>' style='width:80px;height:40px;'>
                            <br><?php echo e($dt2->date_2); ?>

                        <?php elseif($dt2->status_2==2): ?>
                            <img src='<?php echo e($rejected); ?>' style='width:80px;height:60px;'>
                            <br><?php echo e($dt2->date_2); ?>

                        <?php else: ?>
                            &nbsp;
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;vertical-align:middle;">
                        <?php if($dt2->status_3==1): ?>
                            <img src='<?php echo e($confirm); ?>' style='width:80px;height:40px;'>
                            <br><?php echo e($dt2->date_3); ?>

                        <?php elseif($dt2->status_3==2): ?>
                            <img src='<?php echo e($rejected); ?>' style='width:80px;height:60px;'>
                            <br><?php echo e($dt2->date_3); ?>

                        <?php else: ?>
                            &nbsp;
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;vertical-align:middle;">
                        <?php if($dt2->status_4==1): ?>
                            <img src='<?php echo e($confirm); ?>' style='width:80px;height:40px;'>
                            <br><?php echo e($dt2->date_4); ?>

                        <?php elseif($dt2->status_4==2): ?>
                            <img src='<?php echo e($rejected); ?>' style='width:80px;height:60px;'>
                            <br><?php echo e($dt2->date_4); ?>

                        <?php else: ?>
                            &nbsp;
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;vertical-align:middle;">
                        <?php if($dt2->status_5==1): ?>
                            <img src='<?php echo e($confirm); ?>' style='width:80px;height:40px;'>
                            <br><?php echo e($dt2->date_5); ?>

                        <?php elseif($dt2->status_5==2): ?>
                            <img src='<?php echo e($rejected); ?>' style='width:80px;height:60px;'>
                            <br><?php echo e($dt2->date_5); ?>

                        <?php else: ?>
                            &nbsp;
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th style="width:120px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_1); ?></th>
                    <th style="width:120px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_2); ?></th>
                    <th style="width:120px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_3); ?></th>
                    <th style="width:120px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_4); ?></th>
                    <th style="width:120px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_5); ?></th>
                </tr>
                <tr>
                    <td style="text-align:center;"><?php echo e($dt2->pos_1); ?></td>
                    <td style="text-align:center;"><?php echo e($dt2->pos_2); ?></td>
                    <td style="text-align:center;"><?php echo e($dt2->pos_3); ?></td>
                    <td style="text-align:center;"><?php echo e($dt2->pos_4); ?></td>
                    <td style="text-align:center;"><?php echo e($dt2->pos_5); ?></td>
                </tr>
            </table>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/pdf', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\payroll\salary_rekap_compensation_pdf.blade.php ENDPATH**/ ?>