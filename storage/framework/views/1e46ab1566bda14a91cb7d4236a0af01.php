<?php 
    $awal=date('Y-m-d',strtotime('-31 days',strtotime($periode.'-01')));
    $text_awal=' 25 '.date('F',strtotime($awal));
    $text_akhir=' - 24 '.date('F Y',strtotime($periode.'-01'));
    $text_periode='PERIODE'.$text_awal.$text_akhir;
    $periode_text=strtoupper(date('F Y',strtotime($periode.'-01')));

?>

<style>
    div{font-family: Arial, Helvetica, sans-serif;}
    th,td{
        border:1px solid #000;
        font-size:18px;
    }
    td{
        text-align:right;
    }
</style>
<div style="text-align:left;padding:0px 0px 20px 0px;font-size:18px;"><b>
PT SUMMIT ADYAWINSA INDONESIA<br>
RECAPITULATION PKL SALARY PAYMENT<br>
PERIOD <?php echo strtoupper(date('F Y',strtotime($periode.'-01')));?>
</b>
</div>
<div>
    <?php
        $categories = ['PKL_Siswa', 'PKL_Mahasiswa'];
        $grand_total_all = 0;
    ?>

    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $filtered_data = $tb_salary_summary->where('kategori', $category);
            $sub_total = 0;
            $no = 0;
        ?>
        
        <?php if($filtered_data->count() > 0): ?>
            <h4><?php echo e(str_replace('_', ' ', $category)); ?></h4>
            <table class="table table-bordered table-striped" style="margin-bottom: 30px;" width="100%" border="1" cellspacing="0" cellpadding="5">
                <thead>
                    <tr>
                        <th width="40">NO</th>
                        <th>EMPLOYEE_NAME</th>
                        <th>DAYS</th>
                        <th>SALARY/DAY</th>
                        <th>RAPEL</th>
                        <th>TOTAL_AMOUNT</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $filtered_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e(++$no); ?></td>
                            <td><?php echo e($dt->nama ?? ''); ?></td>
                            <td><?php echo e($dt->days ?? '0'); ?></td>
                            <td style="text-align:right;"><?php echo e(isset($dt->salary_perday) ? number_format($dt->salary_perday, 0) : '0'); ?></td>
                            <td style="text-align:right;"><?php echo e(isset($dt->rapel) ? number_format($dt->rapel, 0) : '0'); ?></td>
                            <td style="text-align:right;"><?php echo e(isset($dt->total_amount) ? number_format($dt->total_amount, 0) : '0'); ?></td>
                        </tr>
                        <?php $sub_total += ($dt->total_amount ?? 0); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr style="background: #f9f9f9; font-weight: bold;">
                        <th colspan="5" style="text-align:right;">SUB TOTAL <?php echo e(str_replace('_', ' ', $category)); ?></th>
                        <th style="text-align:right;"><?php echo e(number_format($sub_total, 0)); ?></th>
                    </tr>
                </tfoot>
            </table>
            <?php $grand_total_all += $sub_total; ?>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="well" style="text-align:right; font-weight:bold; font-size:1.2em; background: #eee;">
                GRAND TOTAL: <?php echo e(number_format($grand_total_all, 0)); ?>

            </div>
        </div>
    </div>
</div>
<div style="height:50px;">&nbsp;</div>
<div style="font-size:18px;">
    <?php $__currentLoopData = $tb_approval; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <table border="1" cellspacing="0" cellpadding="5" width="100%" align="center">
            <tr>
                <td style="text-align:center;font-size:14px;">Prepared by</td>
                <td style="text-align:center;font-size:14px;">Checked by</td>
                <td colspan="4" style="text-align:center;font-size:14px;">Approved by</td>
                <td style="text-align:center;font-size:14px;">Verified by</td>
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
                        <img src='<?php echo e($rejected); ?>' style='width:80px;height:40px;'>
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
                        <img src='<?php echo e($rejected); ?>' style='width:80px;height:40px;'>
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
                        <img src='<?php echo e($rejected); ?>' style='width:80px;height:40px;'>
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
                        <img src='<?php echo e($rejected); ?>' style='width:80px;height:40px;'>
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
                        <img src='<?php echo e($rejected); ?>' style='width:80px;height:40px;'>
                        <br><?php echo e($dt2->date_5); ?>

                    <?php else: ?>
                        &nbsp;
                    <?php endif; ?>
                </td>
                <td style="text-align:center;vertical-align:middle;">
                    <?php if($dt2->status_6==1): ?>
                        <img src='<?php echo e($confirm); ?>' style='width:80px;height:40px;'>
                        <br><?php echo e($dt2->date_6); ?>

                    <?php elseif($dt2->status_6==2): ?>
                        <img src='<?php echo e($rejected); ?>' style='width:80px;height:40px;'>
                        <br><?php echo e($dt2->date_6); ?>

                    <?php else: ?>
                        &nbsp;
                    <?php endif; ?>
                </td>
                <td style="text-align:center;vertical-align:middle;">
                    <?php if($dt2->status_7==1): ?>
                        <img src='<?php echo e($confirm); ?>' style='width:80px;height:40px;'>
                        <br><?php echo e($dt2->date_7); ?>

                    <?php elseif($dt2->status_7==2): ?>
                        <img src='<?php echo e($rejected); ?>' style='width:80px;height:40px;'>
                        <br><?php echo e($dt2->date_7); ?>

                    <?php else: ?>
                        &nbsp;
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th style="width:16.6%;text-align:center;vertical-align:middle;font-size:14px;"><?php echo e($dt2->name_1); ?></th>
                <th style="width:16.6%;text-align:center;vertical-align:middle;font-size:14px;"><?php echo e($dt2->name_2); ?></th>
                <th style="width:16.6%;text-align:center;vertical-align:middle;font-size:14px;"><?php echo e($dt2->name_3); ?></th>
                <th style="width:16.6%;text-align:center;vertical-align:middle;font-size:14px;"><?php echo e($dt2->name_4); ?></th>
                <th style="width:16.6%;text-align:center;vertical-align:middle;font-size:14px;"><?php echo e($dt2->name_5); ?></th>
                <th style="width:16.6%;text-align:center;vertical-align:middle;font-size:14px;"><?php echo e($dt2->name_6); ?></th>
                <th style="width:16.6%;text-align:center;vertical-align:middle;font-size:14px;"><?php echo e($dt2->name_7); ?></th>
            </tr>
            <tr>
                <td style="text-align:center;font-size:14px;"><?php echo e($dt2->pos_1); ?></td>
                <td style="text-align:center;font-size:14px;"><?php echo e($dt2->pos_2); ?></td>
                <td style="text-align:center;font-size:14px;"><?php echo e($dt2->pos_3); ?></td>
                <td style="text-align:center;font-size:14px;"><?php echo e($dt2->pos_4); ?></td>
                <td style="text-align:center;font-size:14px;"><?php echo e($dt2->pos_5); ?></td>
                <td style="text-align:center;font-size:14px;"><?php echo e($dt2->pos_6); ?></td>
                <td style="text-align:center;font-size:14px;"><?php echo e($dt2->pos_7); ?></td>
            </tr>
        </table>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\payroll\salary_rekap_pkl_pdf.blade.php ENDPATH**/ ?>