
<?php $__env->startSection('Contents'); ?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary" style="background:#FFF;">
                <div class="box-header">
                    <h3 class="box-title"><?php echo e($status == 0 ? 'New Severance' : 'Paid Severance'); ?></h3>
                    <div class="pull-right">
                        <a href="<?php echo e($site); ?>/Pesangon/0" class="btn btn-info btn-md">New Severance</a>
                        <a href="<?php echo e($site); ?>/Pesangon/1" class="btn btn-primary btn-md">Paid Severance</a>
                    </div>
                </div>
                <div class="box-body" style="overflow: auto;">
                    <table id="tables" class="table table-hover table-bordered">
                        <thead>
                            <tr style="background-color: #f4f4f4;">
                                <th style="width:30px; text-align: center;">No</th>
                                <th style="text-align: center;">NIK</th>
                                <th>Employee Name</th>
                                <th style="text-align: center;">Dept</th>
                                <th style="text-align: center;">Date Permanent</th>
                                <th style="text-align: center;">Cutoff Date</th>
                                <th style="text-align: center;">Tenure</th>
                                <th style="text-align: right;">Base Salary</th>
                                <th style="text-align: right;">Amount</th>
                                <th style="text-align: right;">PPh21</th>
                                <th style="text-align: right;">Paid Amount</th>
                                <?php if($status == 0): ?>
                                <th style="text-align: center;">Action</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=0; $t_amount=0; $t_pph21=0; $t_paid=0;?>
                            <?php $__currentLoopData = $tb_pesangon; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $t_amount += $dt->amount;
                                    $t_pph21 += $dt->pph21;
                                    $t_paid += $dt->amount_paid;
                                ?>
                                <tr>
                                    <td align="center"><?php $no++; echo $no;?></td>
                                    <td align="center"><?php echo e($dt->nik); ?></td>
                                    <td><?php echo e($dt->employee_name); ?></td>
                                    <td align="center"><?php echo e($dt->department); ?></td>
                                    <td align="center"><?php echo e($dt->date_permanent); ?></td>
                                    <td align="center"><?php echo e($dt->date_cutoff); ?></td>
                                    <td align="center"><?php echo e($dt->tahun); ?> th <?php echo e($dt->bulan); ?> bln</td>
                                    <td align="right"><?php echo e(number_format($dt->total_salary,0)); ?></td>
                                    <td align="right"><?php echo e(number_format($dt->amount,0)); ?></td>
                                    <td align="right"><?php echo e(number_format($dt->pph21,0)); ?></td>
                                    <td align="right"><?php echo e(number_format($dt->amount_paid,0)); ?></td>
                                    <?php if($status == 0): ?>
                                    <td align="center">
                                        <a href="<?php echo e($site); ?>/Pesangon/Distribute/<?php echo e($dt->id); ?>" class="btn btn-xs btn-success" onclick="return confirm('Distribute this item?')"><i class="fa fa-send"></i> Distribute</a>
                                        <a href="<?php echo e($site); ?>/Pesangon/Delete/<?php echo e($dt->id); ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this item?')"><i class="fa fa-trash"></i></a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #f4f4f4; font-weight: bold;">
                                <td colspan="8" align="right">TOTAL</td>
                                <td align="right"><?php echo e(number_format($t_amount,0)); ?></td>
                                <td align="right"><?php echo e(number_format($t_pph21,0)); ?></td>
                                <td align="right"><?php echo e(number_format($t_paid,0)); ?></td>
                                <?php if($status == 0): ?>
                                <td></td>
                                <?php endif; ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
<script>
    $(document).ready(function() {
        // Initializing DataTable is handled by the layout, but we can override or add options if needed
    });

    $('body').on("change","#periode",function(){
        var periode = document.getElementById('periode').value;
        if(periode == '') periode = 0;
        window.location.href = "<?php echo e($site); ?>/Pesangon/" + periode;
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/payroll_payments', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/payroll/pesangon_list.blade.php ENDPATH**/ ?>