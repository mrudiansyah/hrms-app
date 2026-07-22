
<?php $__env->startSection('Contents'); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Payroll
                <small>Overtime Summary Detail</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li>Overtime Summary</li>
                <li class="active">Summary Detail</li>
            </ol>
        </section>

        <section class="content">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Success!</h4>
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-12">
                    <!-- Filter Box -->
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-filter"></i> Filter Periode</h3>
                        </div>
                        <div class="box-body">
                            <div class="pull-right">
                                <a href="<?php echo e(url('/payroll/tax_overtime/' . $start . '/' . $end)); ?>"
                                    class="btn btn-default btn-md"><i class="fa fa-angle-double-left"></i> &nbsp;Back</a>
                                <a href="/payroll/tax_overtime_approval/Overtime/<?php echo e($periode); ?>"
                                    class="btn btn-info btn-md"><i class="fa fa-edit"></i> &nbsp;Add Approval</a>
                                <button class="btn btn-success btn-md"
                                    onclick="exportTableToExcel('payroll-table', 'Overtime_Summary_<?php echo e($periode); ?>')"><i
                                        class="fa fa-file-excel-o"></i>
                                    &nbsp;Save as Excel</button>
                                <a href="/payroll/tax_overtime_pdf/<?php echo e($start); ?>/<?php echo e($end); ?>" class="btn btn-danger btn-md"
                                    target="_blank"><i class="fa fa-file-pdf-o"></i> &nbsp;Download PDF</a>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Import -->
                    <div class="modal fade" id="modal-import" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-aqua">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Import Rapel</h4>
                                </div>
                                <form action="<?php echo e(url('/payroll/import_rapel')); ?>" method="POST"
                                    enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="start" value="<?php echo e($start); ?>">
                                    <input type="hidden" name="end" value="<?php echo e($end); ?>">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="file">Excel File</label>
                                            <input type="file" name="file" id="file" class="form-control" required>
                                            <p class="help-block">
                                                Format: Kolom A (NIK), Kolom B (Periode YYYY-MM), Kolom C (Amount).
                                                <br>
                                                <a href="<?php echo e(url('/payroll/download_format_rapel')); ?>" class="text-primary">
                                                    <i class="fa fa-download"></i> Download Format Excel
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Import Now</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Data Box -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-list"></i> Overtime Summary (<?php echo e($start); ?> - <?php echo e($end); ?>)
                            </h3>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="payroll-table" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr style="background-color: #f4b084;">
                                            <th>No</th>
                                            <th>Employee Name</th>
                                            <th>NIK</th>
                                            <th>Position</th>
                                            <th>Dept</th>
                                            <th>CC_Code</th>
                                            <th>SLPJ</th>
                                            <th>Hours</th>
                                            <th>Convetion</th>
                                            <th>Amount</th>
                                            <th>Meal</th>
                                            <th>Rapel</th>
                                            <th>Final_Amount</th>
                                            <th>PPh21</th>
                                            <th>Amount</th>
                                            <th>Norek</th>
                                            <th>Total_Paid</th>
                                            <th>Nama</th>
                                            <th>Slip</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $grand_total = [
                                                'slpj' => 0,
                                                'hours_act' => 0,
                                                'hour_convertion' => 0,
                                                'ot_amount' => 0,
                                                'meal_amount' => 0,
                                                'rapel_amount' => 0,
                                                'gross_amount' => 0,
                                                'pph21_amount' => 0,
                                                'net_amount' => 0
                                            ];
                                            $no = 1;
                                            $sortedTb1 = collect($tb1)->sortBy(function ($item) {
                                                return $item->dept_code . '-' . $item->cc_code . '-' . $item->employee_name;
                                            });
                                            $groupedData = $sortedTb1->groupBy(['dept_code', 'cc_code']);
                                        ?>

                                        <?php $__currentLoopData = $groupedData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept_code => $cc_codes): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $sub_dept = [
                                                    'slpj' => 0,
                                                    'hours_act' => 0,
                                                    'hour_convertion' => 0,
                                                    'ot_amount' => 0,
                                                    'meal_amount' => 0,
                                                    'rapel_amount' => 0,
                                                    'gross_amount' => 0,
                                                    'pph21_amount' => 0,
                                                    'net_amount' => 0
                                                ];
                                            ?>
                                            <?php $__currentLoopData = $cc_codes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cc_code => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $sub_cc = [
                                                        'slpj' => 0,
                                                        'hours_act' => 0,
                                                        'hour_convertion' => 0,
                                                        'ot_amount' => 0,
                                                        'meal_amount' => 0,
                                                        'rapel_amount' => 0,
                                                        'gross_amount' => 0,
                                                        'pph21_amount' => 0,
                                                        'net_amount' => 0
                                                    ];
                                                ?>
                                                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        $sub_cc['slpj'] += $item->slpj;
                                                        $sub_cc['hours_act'] += $item->hours_act;
                                                        $sub_cc['hour_convertion'] += $item->hour_convertion;
                                                        $sub_cc['ot_amount'] += $item->ot_amount;
                                                        $sub_cc['meal_amount'] += $item->meal_amount;
                                                        $sub_cc['rapel_amount'] += $item->rapel_amount;
                                                        $sub_cc['gross_amount'] += $item->gross_amount;
                                                        $sub_cc['pph21_amount'] += $item->pph21_amount;
                                                        $sub_cc['net_amount'] += $item->net_amount;

                                                        $sub_dept['slpj'] += $item->slpj;
                                                        $sub_dept['hours_act'] += $item->hours_act;
                                                        $sub_dept['hour_convertion'] += $item->hour_convertion;
                                                        $sub_dept['ot_amount'] += $item->ot_amount;
                                                        $sub_dept['meal_amount'] += $item->meal_amount;
                                                        $sub_dept['rapel_amount'] += $item->rapel_amount;
                                                        $sub_dept['gross_amount'] += $item->gross_amount;
                                                        $sub_dept['pph21_amount'] += $item->pph21_amount;
                                                        $sub_dept['net_amount'] += $item->net_amount;

                                                        $grand_total['slpj'] += $item->slpj;
                                                        $grand_total['hours_act'] += $item->hours_act;
                                                        $grand_total['hour_convertion'] += $item->hour_convertion;
                                                        $grand_total['ot_amount'] += $item->ot_amount;
                                                        $grand_total['meal_amount'] += $item->meal_amount;
                                                        $grand_total['rapel_amount'] += $item->rapel_amount;
                                                        $grand_total['gross_amount'] += $item->gross_amount;
                                                        $grand_total['pph21_amount'] += $item->pph21_amount;
                                                        $grand_total['net_amount'] += $item->net_amount;
                                                    ?>
                                                    <tr>
                                                        <td><?php echo e($no++); ?></td>
                                                        <td><?php echo e($item->employee_name); ?></td>
                                                        <td><?php echo e($item->nik); ?></td>
                                                        <td><?php echo e($item->position_name); ?></td>
                                                        <td><?php echo e($item->dept_code); ?></td>
                                                        <td><?php echo e($item->cc_code); ?></td>
                                                        <td><?php echo e(number_format($item->slpj, 0)); ?></td>
                                                        <td><?php echo e(number_format($item->hours_act, 2)); ?></td>
                                                        <td><?php echo e(number_format($item->hour_convertion, 2)); ?></td>
                                                        <td><?php echo e(number_format($item->ot_amount, 0)); ?></td>
                                                        <td><?php echo e(number_format($item->meal_amount, 0)); ?></td>
                                                        <td><?php echo e(number_format($item->rapel_amount, 0)); ?></td>
                                                        <td><?php echo e(number_format($item->gross_amount, 0)); ?></td>
                                                        <td><?php echo e(number_format($item->pph21_amount, 0)); ?></td>
                                                        <td><?php echo e(number_format($item->net_amount, 0)); ?></td>
                                                        <td><?php echo e($item->nomor_rekening); ?></td>
                                                        <td><?php echo e(number_format($item->net_amount, 0)); ?></td>
                                                        <td><?php echo e($item->employee_name); ?></td>
                                                        <td>
                                                            <a href="<?php echo e(url('/payroll/slip/overtime/' . $start . '/' . $end . '/' . $item->id_employee)); ?>"
                                                                class="btn btn-xs btn-primary"><i class="fa fa-print"></i></a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <tr style="background-color: #eee; font-weight: bold;">
                                                    <td colspan="6" class="text-right">Sub Total CC <?php echo e($cc_code); ?></td>
                                                    <td><?php echo e(number_format($sub_cc['slpj'], 0)); ?></td>
                                                    <td><?php echo e(number_format($sub_cc['hours_act'], 2)); ?></td>
                                                    <td><?php echo e(number_format($sub_cc['hour_convertion'], 2)); ?></td>
                                                    <td><?php echo e(number_format($sub_cc['ot_amount'], 0)); ?></td>
                                                    <td><?php echo e(number_format($sub_cc['meal_amount'], 0)); ?></td>
                                                    <td><?php echo e(number_format($sub_cc['rapel_amount'], 0)); ?></td>
                                                    <td><?php echo e(number_format($sub_cc['gross_amount'], 0)); ?></td>
                                                    <td><?php echo e(number_format($sub_cc['pph21_amount'], 0)); ?></td>
                                                    <td><?php echo e(number_format($sub_cc['net_amount'], 0)); ?></td>
                                                    <td></td>
                                                    <td><?php echo e(number_format($sub_cc['net_amount'], 0)); ?></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <tr style="background-color: #d2d6de; font-weight: bold;">
                                                <td colspan="6" class="text-right">Sub Total Dept <?php echo e($dept_code); ?></td>
                                                <td><?php echo e(number_format($sub_dept['slpj'], 0)); ?></td>
                                                <td><?php echo e(number_format($sub_dept['hours_act'], 2)); ?></td>
                                                <td><?php echo e(number_format($sub_dept['hour_convertion'], 2)); ?></td>
                                                <td><?php echo e(number_format($sub_dept['ot_amount'], 0)); ?></td>
                                                <td><?php echo e(number_format($sub_dept['meal_amount'], 0)); ?></td>
                                                <td><?php echo e(number_format($sub_dept['rapel_amount'], 0)); ?></td>
                                                <td><?php echo e(number_format($sub_dept['gross_amount'], 0)); ?></td>
                                                <td><?php echo e(number_format($sub_dept['pph21_amount'], 0)); ?></td>
                                                <td><?php echo e(number_format($sub_dept['net_amount'], 0)); ?></td>
                                                <td></td>
                                                <td><?php echo e(number_format($sub_dept['net_amount'], 0)); ?></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                    <tfoot>
                                        <tr style="font-weight: bold; background-color: #d9d9d9;">
                                            <td colspan="6" class="text-right">Grand Total All</td>
                                            <td><?php echo e(number_format($grand_total['slpj'], 0)); ?></td>
                                            <td><?php echo e(number_format($grand_total['hours_act'], 2)); ?></td>
                                            <td><?php echo e(number_format($grand_total['hour_convertion'], 2)); ?></td>
                                            <td><?php echo e(number_format($grand_total['ot_amount'], 0)); ?></td>
                                            <td><?php echo e(number_format($grand_total['meal_amount'], 0)); ?></td>
                                            <td><?php echo e(number_format($grand_total['rapel_amount'], 0)); ?></td>
                                            <td><?php echo e(number_format($grand_total['gross_amount'], 0)); ?></td>
                                            <td><?php echo e(number_format($grand_total['pph21_amount'], 0)); ?></td>
                                            <td><?php echo e(number_format($grand_total['net_amount'], 0)); ?></td>
                                            <td></td>
                                            <td><?php echo e(number_format($grand_total['net_amount'], 0)); ?></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <?php $__currentLoopData = $tb_approval; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <table border="1" cellspacing="0" cellpadding="5" width="900" align="center">
                                        <tr>
                                            <td colspan="3" style="text-align:center;">Approved by</td>
                                            <td style="text-align:center;">Verified by</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:center;vertical-align:middle;height:100px;" class="relative">
                                                <?php if($dt2->id_approver_1 == $id_employee && $dt2->status_1 == 0): ?>
                                                    <button class="btn btn-success btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>"
                                                        data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="1"
                                                        data-status="1"><i class="fa fa-check"></i></button>
                                                    <button class="btn btn-danger btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>"
                                                        data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="1"
                                                        data-status="2"><i class="fa fa-times"></i></button>
                                                <?php elseif($dt2->status_1 == 1): ?>
                                                    <img src='/approval/confirm.png' style='width:100px;height:40px;'>
                                                    <br><?php echo e($dt2->date_1); ?>

                                                <?php elseif($dt2->status_1 == 2): ?>
                                                    <img src='/approval/rejected.png' style='width:100px;height:50px;'>
                                                    <br><?php echo e($dt2->date_1); ?>

                                                <?php else: ?>
                                                    &nbsp;
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align:center;vertical-align:middle;">
                                                <?php if($dt2->id_approver_2 == $id_employee && $dt2->status_2 == 0 && $dt2->status_1 == 1): ?>
                                                    <button class="btn btn-success btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>"
                                                        data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="2"
                                                        data-status="1"><i class="fa fa-check"></i></button>
                                                    <button class="btn btn-danger btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>"
                                                        data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="2"
                                                        data-status="2"><i class="fa fa-times"></i></button>
                                                <?php elseif($dt2->status_2 == 1): ?>
                                                    <img src='/approval/confirm.png' style='width:100px;height:40px;'>
                                                    <br><?php echo e($dt2->date_2); ?>

                                                <?php elseif($dt2->status_2 == 2): ?>
                                                    <img src='/approval/rejected.png' style='width:100px;height:50px;'>
                                                    <br><?php echo e($dt2->date_2); ?>

                                                <?php else: ?>
                                                    &nbsp;
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align:center;vertical-align:middle;">
                                                <?php if($dt2->id_approver_3 == $id_employee && $dt2->status_3 == 0 && $dt2->status_2 == 1): ?>
                                                    <button class="btn btn-success btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>"
                                                        data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="3"
                                                        data-status="1"><i class="fa fa-check"></i></button>
                                                    <button class="btn btn-danger btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>"
                                                        data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="3"
                                                        data-status="2"><i class="fa fa-times"></i></button>
                                                <?php elseif($dt2->status_3 == 1): ?>
                                                    <img src='/approval/confirm.png' style='width:100px;height:40px;'>
                                                    <br><?php echo e($dt2->date_3); ?>

                                                <?php elseif($dt2->status_3 == 2): ?>
                                                    <img src='/approval/rejected.png' style='width:100px;height:50px;'>
                                                    <br><?php echo e($dt2->date_3); ?>

                                                <?php else: ?>
                                                    &nbsp;
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align:center;vertical-align:middle;">
                                                <?php if($dt2->id_approver_4 == $id_employee && $dt2->status_4 == 0 && $dt2->status_3 == 1): ?>
                                                    <button class="btn btn-success btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>"
                                                        data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="4"
                                                        data-status="1"><i class="fa fa-check"></i></button>
                                                    <button class="btn btn-danger btn-md simpan" data-periode="<?php echo e($dt2->periode); ?>"
                                                        data-category="<?php echo e($dt2->category); ?>" data-tipe="<?php echo e($dt2->tipe); ?>" data-kolom="4"
                                                        data-status="2"><i class="fa fa-times"></i></button>
                                                <?php elseif($dt2->status_4 == 1): ?>
                                                    <img src='/approval/confirm.png' style='width:100px;height:40px;'>
                                                    <br><?php echo e($dt2->date_4); ?>

                                                <?php elseif($dt2->status_4 == 2): ?>
                                                    <img src='/approval/rejected.png' style='width:100px;height:50px;'>
                                                    <br><?php echo e($dt2->date_4); ?>

                                                <?php else: ?>
                                                    &nbsp;
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width:180px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_1); ?>

                                            </th>
                                            <th style="width:180px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_2); ?>

                                            </th>
                                            <th style="width:180px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_3); ?>

                                            </th>
                                            <th style="width:180px;text-align:center;vertical-align:bottom;"><?php echo e($dt2->name_4); ?>

                                            </th>
                                        </tr>
                                        <tr>
                                            <td style="text-align:center;"><?php echo e($dt2->pos_1); ?></td>
                                            <td style="text-align:center;"><?php echo e($dt2->pos_2); ?></td>
                                            <td style="text-align:center;"><?php echo e($dt2->pos_3); ?></td>
                                            <td style="text-align:center;"><?php echo e($dt2->pos_4); ?></td>
                                        </tr>
                                    </table>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('Scripts'); ?>
    <script>
        $(document).ready(function () {
            $('#payroll-table').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': false,
                'info': true,
                'autoWidth': false,
                "pageLength": -1,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'print'
                ]
            });
        });
        function exportTableToExcel(tableID, filename = '') {
            var tableSelect = document.getElementById(tableID);
            var tableHTML = tableSelect.outerHTML;
            var blob = new Blob([tableHTML], {
                type: "application/vnd.ms-excel"
            });
            var url = URL.createObjectURL(blob);
            var downloadLink = document.createElement("a");
            filename = filename ? filename + '.xls' : 'excel_data.xls';
            downloadLink.href = url;
            downloadLink.download = filename;
            downloadLink.click();
            URL.revokeObjectURL(url);
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/payroll/tax_overtime_excel.blade.php ENDPATH**/ ?>