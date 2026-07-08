
<?php $__env->startSection('Contents'); ?>
    <section class="content">
        <div class="row">
            <div class="col-xs-12 col-md-6 col-lg-6">
                <div class="box box-primary" style="background:#FFF;">
                    <div class="box-header with-border">
                        <h3 class="box-title">Overtime Slip</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                    class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body" style="overflow: auto;">
                        <table id="polos" class="table table-bordered" style="min-width:100%;">
                            <thead>
                                <tr>
                                    <th>PERIODE</th>
                                    <th>OVERTIME</th>
                                    <th>RAPELS</th>
                                    <th>PPH21</th>
                                    <th>RECEIVED</th>
                                    <th style="width:30px;">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 0;?>
                                <?php $__currentLoopData = $tb1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php
                                    $pendapatan = $dt1->ot_amount + $dt1->meal_amount;
                                    $pph21_total = $dt1->pph21_amount;
                                    $rapel = $dt1->rapel_amount;
                                    $potongan = $pph21_total;
                                                                                                                                                                                                                                                ?>
                                                            <tr>
                                                                <td><?php echo e($dt1->periode); ?></td>
                                                                <td style="text-align:right;"><?php    echo number_format($pendapatan, 0);?></td>
                                                                <td style="text-align:right;"><?php    echo number_format($rapel, 0);?></td>
                                                                <td style="text-align:right;"><?php    echo number_format($potongan, 0);?></td>
                                                                <td style="text-align:right;"><b
                                                                        id="netto<?php echo e($dt1->id); ?>"><?php    echo number_format($dt1->net_amount, 0);?></b>
                                                                </td>
                                                                <td>
                                                                    <?php if($dt1->kategori == 'Overtime'): ?>
                                                                        <a href="/payroll/slip/overtime_personal/<?php echo e($dt1->start_date); ?>/<?php echo e($dt1->end_date); ?>/<?php echo e($dt1->id_employee); ?>"
                                                                            target="_blank"><button type="button" class="btn btn-primary btn-xs"><i
                                                                                    class="fa fa-print"></i></button></a>
                                                                    <?php else: ?>
                                                                        <a href="/payroll/slip/assignment/<?php echo e($dt1->start_date); ?>/<?php echo e($dt1->end_date); ?>/<?php echo e($dt1->id_employee); ?>"
                                                                            target="_blank"><button type="button" class="btn btn-primary btn-xs"><i
                                                                                    class="fa fa-print"></i></button></a>
                                                                    <?php endif; ?>

                                                                </td>
                                                            </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>

            </div>
            <div class="col-xs-12 col-md-6 col-lg-6">
                <div class="box box-primary" style="background:#FFF;">
                    <div class="box-header with-border">
                        <h3 class="box-title">Previous Slip</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                    class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body" style="overflow: auto;">
                        <table id="polos" class="table table-bordered" style="min-width:100%;">
                            <thead>
                                <tr>
                                    <th>PERIODE</th>
                                    <th>OVERTIME</th>
                                    <th>RAPELS</th>
                                    <th>PPH21</th>
                                    <th>RECEIVED</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 0;?>
                                <?php $__currentLoopData = $tb_summary_overtime; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php
                                    $pendapatan = $dt->total_bayars;
                                    $pph21_total = $dt->pph21 + $dt->pph21_rapel;
                                    $rapel = $dt->rapel;
                                    //$potongan=$dt->pph21;
                                    $potongan = $pph21_total;
                                                                                                                                                                                                                                                ?>
                                                            <?php    if (($dt->total_paid > 0 || $dt->rapel_total > 0) && $dt->show_status == '1') {?>
                                                            <tr>
                                                                <td><?php echo e($dt->periode); ?></td>
                                                                <td style="text-align:right;"><?php        echo number_format($pendapatan, 0);?></td>
                                                                <td style="text-align:right;"><?php        echo number_format($rapel, 0);?></td>
                                                                <td style="text-align:right;"><?php        echo number_format($potongan, 0);?></td>
                                                                <td style="text-align:right;"><b id="netto<?php echo e($dt->id); ?>"><?php        $netto = $pendapatan + $rapel - $potongan;
                                        echo number_format($netto, 0);?></b>
                                                                </td>
                                                            </tr>
                                                            <?php    }?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>

            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page_ess/slip_overtime.blade.php ENDPATH**/ ?>