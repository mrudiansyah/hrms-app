
<?php $__env->startSection('Contents'); ?>
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF; padding: 20px;">
					<div class="box-header">
						<div class="pull-left">
							<input type="month" id="periode" class="form-control" value="<?php echo e($periode); ?>">	
						</div>
						<div class="pull-right">
                            <a href="<?php echo e($site); ?>/RecapPDF/<?php echo e($periode); ?>/<?php echo e($tipe); ?>" class="btn btn-primary btn-md" target="_blank"><i class="fa fa-print"></i> &nbsp;Preview</a>
						</div>
					</div>
					<div class="text-center" style="margin-bottom: 20px;">
						<h3 style="margin: 0; font-weight: bold;">PT SUMMIT ADYAWINSA INDONESIA</h3>
						<h4 style="margin: 5px 0; font-weight: bold;">RECAPITULATION EMPLOYEE COMPENSATION PAYMENT</h4>
						<h4 style="margin: 0; font-weight: bold;">PERIODE <?php echo e(date('Y, F', strtotime($periode.'-01'))); ?></h4>
					</div>
					<div class="box-body">
						<table class="table table-bordered" style="border: 2px solid #000;">
							<thead style="border-bottom: 3px double #000;">
                                <tr>
                                    <th style="text-align: center; vertical-align: middle; border-right: 2px solid #000;">DEPARTEMEN</th>
                                    <th style="text-align: center; vertical-align: middle; border-right: 2px solid #000; width: 100px;">COST CENTER</th>
                                    <th style="text-align: center; vertical-align: middle; border-right: 2px solid #000; width: 100px;">Total Employee</th>
                                    <th style="text-align: center; vertical-align: middle; border-right: 2px solid #000; width: 120px;">SALARY</th>
                                    <th style="text-align: center; vertical-align: middle; border-right: 2px solid #000; width: 150px;">COMPENSATION<br>(Gross)</th>
                                    <th style="text-align: center; vertical-align: middle; border-right: 2px solid #000; width: 120px;">TAX</th>
                                    <th style="text-align: center; vertical-align: middle; width: 150px;">COMPENSATION<br>(take home pay)</th>
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
                                            <td rowspan="<?php echo e($rowspan); ?>" style="vertical-align: middle; font-weight: bold; border-right: 2px solid #000;"><?php echo e($dt->bagian); ?></td>
                                        <?php endif; ?>
                                        <td style="text-align: center; border-right: 2px solid #000;"><?php echo e($dt->cc_code); ?></td>
                                        <td style="text-align: center; border-right: 2px solid #000;"><?php echo e($dt->jml_employee); ?></td>
                                        <td style="text-align: right; border-right: 2px solid #000;"><?php echo e(number_format($dt->salary, 0)); ?></td>
                                        <td style="text-align: right; border-right: 2px solid #000;"><?php echo e(number_format($dt->compensation_gross, 0)); ?></td>
                                        <td style="text-align: right; border-right: 2px solid #000;"><?php echo e(number_format($dt->tax, 0)); ?></td>
                                        <td style="text-align: right;"><?php echo e(number_format($dt->compensation_nett, 0)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
                            <tfoot style="border-top: 3px double #000; font-weight: bold; background: #eee;">
                                <tr>
                                    <td colspan="2" style="text-align: right; border-right: 2px solid #000;">GRAND TOTAL</td>
                                    <td style="text-align: center; border-right: 2px solid #000;"><?php echo e(number_format($total_emp, 0)); ?></td>
                                    <td style="text-align: right; border-right: 2px solid #000;"><?php echo e(number_format($total_sal, 0)); ?></td>
                                    <td style="text-align: right; border-right: 2px solid #000;"><?php echo e(number_format($total_gross, 0)); ?></td>
                                    <td style="text-align: right; border-right: 2px solid #000;"><?php echo e(number_format($total_tax, 0)); ?></td>
                                    <td style="text-align: right;"><?php echo e(number_format($total_nett, 0)); ?></td>
                                </tr>
                            </tfoot>
						</table>
                        <br><br>
                        <?php $__currentLoopData = $tb_approval; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <table border="1" cellspacing="0" cellpadding="5" width="900" align="center">
                                <tr>
                                    <td colspan="4" style="text-align:center;">Approved by</td>
                                    <td style="text-align:center;">Verified by</td>
                                </tr>
                                <tr>
                                    <td style="text-align:center;vertical-align:middle;height:100px;" class="relative">
                                        <?php if($dt2->id_approver_1==$id_employee&&$dt2->status_1==0): ?>
                                            <button class="btn btn-success btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="1" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="1" data-status="2"><i class="fa fa-times"></i></button>
                                        <?php elseif($dt2->status_1==1): ?>
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br><?php echo e($dt2->date_1); ?>

                                        <?php elseif($dt2->status_1==2): ?>
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br><?php echo e($dt2->date_1); ?>

                                        <?php else: ?>
                                            &nbsp;
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:center;vertical-align:middle;">
                                        <?php if($dt2->id_approver_2==$id_employee&&$dt2->status_2==0&&$dt2->status_1==1): ?>
                                            <button class="btn btn-success btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="2" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="2" data-status="2"><i class="fa fa-times"></i></button>
                                        <?php elseif($dt2->status_2==1): ?>
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br><?php echo e($dt2->date_2); ?>

                                        <?php elseif($dt2->status_2==2): ?>
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br><?php echo e($dt2->date_2); ?>

                                        <?php else: ?>
                                            &nbsp;
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:center;vertical-align:middle;">
                                        <?php if($dt2->id_approver_3==$id_employee&&$dt2->status_3==0&&$dt2->status_2==1): ?>
                                            <button class="btn btn-success btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="3" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="3" data-status="2"><i class="fa fa-times"></i></button>
                                        <?php elseif($dt2->status_3==1): ?>
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br><?php echo e($dt2->date_3); ?>

                                        <?php elseif($dt2->status_3==2): ?>
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br><?php echo e($dt2->date_3); ?>

                                        <?php else: ?>
                                            &nbsp;
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:center;vertical-align:middle;">
                                        <?php if($dt2->id_approver_4==$id_employee&&$dt2->status_4==0&&$dt2->status_3==1): ?>
                                            <button class="btn btn-success btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="4" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="4" data-status="2"><i class="fa fa-times"></i></button>
                                        <?php elseif($dt2->status_4==1): ?>
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br><?php echo e($dt2->date_4); ?>

                                        <?php elseif($dt2->status_4==2): ?>
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br><?php echo e($dt2->date_4); ?>

                                        <?php else: ?>
                                            &nbsp;
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:center;vertical-align:middle;">
                                        <?php if($dt2->id_approver_5==$id_employee&&$dt2->status_5==0&&$dt2->status_4==1): ?>
                                            <button class="btn btn-success btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="4" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>" data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="4" data-status="2"><i class="fa fa-times"></i></button>
                                        <?php elseif($dt2->status_5==1): ?>
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br><?php echo e($dt2->date_5); ?>

                                        <?php elseif($dt2->status_5==2): ?>
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br><?php echo e($dt2->date_5); ?>

                                        <?php else: ?>
                                            &nbsp;
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_1); ?></th>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_2); ?></th>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_3); ?></th>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_4); ?></th>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_5); ?></th>
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
			</div>
		</div>
	</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
<script>
    $('body').on("change","#periode",function(){
        var periode=document.getElementById('periode').value;
        if(periode=='') var periode=0;
        var tipe="<?php echo $tipe;?>";
        window.location.href="<?php echo e($site); ?>/Recap/"+periode+"/"+tipe;
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/payroll_recap', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\payroll\salary_rekap_compensation.blade.php ENDPATH**/ ?>