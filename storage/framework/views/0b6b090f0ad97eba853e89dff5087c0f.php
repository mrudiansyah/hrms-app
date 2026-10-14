
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
                                <?php if($is_psab==1): ?>
                                    <a href="/payroll/tax_overtime_psab_pdf/<?php echo e($start); ?>/<?php echo e($end); ?>" class="btn btn-danger btn-md" target="_blank"><i class="fa fa-file-pdf-o"></i> &nbsp;Download PDF</a>
                                <?php else: ?>
                                    <a href="/payroll/tax_overtime_pdf/<?php echo e($start); ?>/<?php echo e($end); ?>" class="btn btn-danger btn-md" target="_blank"><i class="fa fa-file-pdf-o"></i> &nbsp;Download PDF</a>
                                <?php endif; ?>
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
                                                return $item->comb_category . '-' .$item->dept_code . '-' . $item->cc_code . '-' . $item->employee_name;
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
                                <div style="font-size:18px;">
                                    <table style="width:50%;" border="1" cellspacing="0" cellpadding="5" align="center">
                                        <tr>
                                            <td colspan="4" style="text-align:center;">Approved by</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:center;vertical-align:middle;height:100px;" class="relative">
                                                &nbsp;
                                            </td>
                                            <td style="text-align:center;vertical-align:middle;">
                                                &nbsp;
                                            </td>
                                            <td style="text-align:center;vertical-align:middle;">
                                                &nbsp;
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width:180px;text-align:center;vertical-align:bottom;">ANGELLA PUSPA DEWI P</th>
                                            <th style="width:180px;text-align:center;vertical-align:bottom;">NOVA TRIANA NOFIANTI</th>
                                            <th style="width:180px;text-align:center;vertical-align:bottom;">RISTIYONO</th>
                                            <th style="width:180px;text-align:center;vertical-align:bottom;">ARIF BASUKI</th>
                                        </tr>
                                        <tr>
                                            <td style="text-align:center;">Specialist</td>
                                            <td style="text-align:center;">Section Head</td>
                                            <td style="text-align:center;">Dept.Head HRGA</td>
                                            <td style="text-align:center;">Asst.General Manager</td>
                                        </tr>
                                    </table>
                                </div>
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