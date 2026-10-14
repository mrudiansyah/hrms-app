
<?php $__env->startSection('Contents'); ?>
<section class="content">
    <div class="row">
        <div style="text-align:center;padding:0px 0px 20px 0px;font-size:14px;">
            <?php if(isset($is_pdf)): ?>
            <b>
                PT SUMMIT ADYAWINSA INDONESIA<br>
                RECAPITULATION EMPLOYEE COMPENSATION PAYMENT<br>
                PERIOD <?php echo strtoupper(date('F Y',strtotime($periode.'-01')));?>
            </b>
            <?php endif; ?>
        </div>
        <div class="col-xs-12">
            <div class="box box-primary" style="background:#FFF;">
                <div class="box-header">
                    <h3 class="box-title">Compensation (Contract)</h3>
                    <div class="pull-right" style="margin-left: 10px; display: flex; align-items: center;">
                        <input type="month" id="periode" class="form-control" value="<?php echo e($periode); ?>">    
                        &nbsp;&nbsp;
                        <a href="<?php echo e($site); ?>/Compensation/<?php echo e($periode); ?>" class="btn btn-default btn-md"><i class="fa fa-angle-double-left"></i> &nbsp;Back</a>
                        &nbsp;&nbsp;
                        <button class="btn btn-success btn-md" onclick="exportTableToExcel('table_summary', 'Salary_Summary_<?php echo e($tipe); ?>_<?php echo e($periode); ?>')"><i class="fa fa-file-excel-o"></i> &nbsp;Save as Excel</button>
                        &nbsp;&nbsp;
                        <a href="<?php echo e($site); ?>/CompensationDownloadPDF/<?php echo e($periode); ?>/<?php echo e($department); ?>/<?php echo e($tipe); ?>" class="btn btn-danger btn-md" target="_blank"><i class="fa fa-file-pdf-o"></i> &nbsp;Download PDF</a>
                    </div>
                </div>
                <div class="box-body" style="overflow: auto;">

                    <table id="table_summary" class="table table-bordered table-hover">
                        <thead>
                            <tr style="background: #3c8dbc; color: white;">
                                <th style="width:30px;">No</th>
                                <th>NIK</th>
                                <th>EMPLOYEE_NAME</th>
                                <th>DEPARTMENT</th>
                                <th>CC_CODE</th>
                                <th>STATUS</th>
                                <th>REKENING</th>
                                <th>PERIODE</th>
                                <th>MONTH</th>
                                <th style="text-align: right;">SALARY</th>
                                <th style="text-align: right;">AMOUNT</th>
                                <th style="text-align: right;">PPH21</th>
                                <th style="text-align: right;">RECEIVED</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $no = 0; 
                                $current_dept = ''; 
                                $current_cc = '';
                                
                                // Total per department
                                $dept_total = [
                                    'salary' => 0, 
                                    'compensation' => 0, 
                                    'pph21' => 0, 
                                    'received' => 0
                                ];
                                
                                // Total per cc_code
                                $cc_total = [
                                    'salary' => 0, 
                                    'compensation' => 0, 
                                    'pph21' => 0, 
                                    'received' => 0
                                ];
                                
                                // Grand total
                                $grand_total = [
                                    'salary' => 0, 
                                    'compensation' => 0, 
                                    'pph21' => 0, 
                                    'received' => 0
                                ];
                            ?>
                            
                            <?php $__currentLoopData = $tb_kompensasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    // CETAK SUB TOTAL CC_CODE jika ganti cc_code dalam department yang sama
                                    if($current_dept == $dt->Bagian && $current_cc != $dt->cc_code && $current_cc != '') {
                                        echo '<tr style="background: #d9edf7; font-weight: bold;">';
                                        echo '<td colspan="5" style="text-align: right;">Sub Total CC: ' . $current_cc . '</td>';
                                        echo '<td></td>';
                                        echo '<td></td>';
                                        echo '<td></td>';
                                        echo '<td></td>';
                                        echo '<td style="text-align: right; border-top: 1px solid #ccc;">' . number_format($cc_total['salary'], 0) . '</td>';
                                        echo '<td style="text-align: right; border-top: 1px solid #ccc;">' . number_format($cc_total['compensation'], 0) . '</td>';
                                        echo '<td style="text-align: right; border-top: 1px solid #ccc;">' . number_format($cc_total['pph21'], 0) . '</td>';
                                        echo '<td style="text-align: right; border-top: 1px solid #ccc;">' . number_format($cc_total['received'], 0) . '</td>';
                                        echo '</tr>';
                                        
                                        // Reset CC total
                                        $cc_total = [
                                            'salary' => 0, 
                                            'compensation' => 0, 
                                            'pph21' => 0, 
                                            'received' => 0
                                        ];
                                    }
                                    
                                    // CETAK SUB TOTAL DEPARTMENT jika ganti department
                                    if($current_dept != $dt->Bagian && $current_dept != '') {
                                        // Cetak sub total CC terakhir untuk department sebelumnya
                                        if($current_cc != '') {
                                            echo '<tr style="background: #d9edf7; font-weight: bold;">';
                                            echo '<td colspan="5" style="text-align: right;">Sub Total CC: ' . $current_cc . '</td>';
                                            echo '<td></td>';
                                            echo '<td></td>';
                                            echo '<td></td>';
                                            echo '<td></td>';
                                            echo '<td style="text-align: right; border-top: 1px solid #ccc;">' . number_format($cc_total['salary'], 0) . '</td>';
                                            echo '<td style="text-align: right; border-top: 1px solid #ccc;">' . number_format($cc_total['compensation'], 0) . '</td>';
                                            echo '<td style="text-align: right; border-top: 1px solid #ccc;">' . number_format($cc_total['pph21'], 0) . '</td>';
                                            echo '<td style="text-align: right; border-top: 1px solid #ccc;">' . number_format($cc_total['received'], 0) . '</td>';
                                            echo '</tr>';
                                        }
                                        
                                        // Cetak sub total department
                                        echo '<tr style="background: #fcf8e3; font-weight: bold;">';
                                        echo '<td colspan="5" style="text-align: right;">TOTAL DEPARTMENT: ' . $current_dept . '</td>';
                                        echo '<td></td>';
                                        echo '<td></td>';
                                        echo '<td></td>';
                                        echo '<td></td>';
                                        echo '<td style="text-align: right; border-top: 2px solid #333;">' . number_format($dept_total['salary'], 0) . '</td>';
                                        echo '<td style="text-align: right; border-top: 2px solid #333;">' . number_format($dept_total['compensation'], 0) . '</td>';
                                        echo '<td style="text-align: right; border-top: 2px solid #333;">' . number_format($dept_total['pph21'], 0) . '</td>';
                                        echo '<td style="text-align: right; border-top: 2px solid #333;">' . number_format($dept_total['received'], 0) . '</td>';
                                        echo '</tr>';
                                        
                                        // Reset department total
                                        $dept_total = [
                                            'salary' => 0, 
                                            'compensation' => 0, 
                                            'pph21' => 0, 
                                            'received' => 0
                                        ];
                                        
                                        // Reset CC total (sudah di-reset di atas)
                                        $cc_total = [
                                            'salary' => 0, 
                                            'compensation' => 0, 
                                            'pph21' => 0, 
                                            'received' => 0
                                        ];
                                    }
                                    
                                    $current_dept = $dt->Bagian;
                                    $current_cc = $dt->cc_code;
                                    
                                    // Accumulate CC totals
                                    $cc_total['salary'] += $dt->Total_Pendapatan;
                                    $cc_total['compensation'] += $dt->Kompensasi;
                                    $cc_total['pph21'] += $dt->PPH21;
                                    $cc_total['received'] += $dt->Total_Kompensasi;
                                    
                                    // Accumulate department totals
                                    $dept_total['salary'] += $dt->Total_Pendapatan;
                                    $dept_total['compensation'] += $dt->Kompensasi;
                                    $dept_total['pph21'] += $dt->PPH21;
                                    $dept_total['received'] += $dt->Total_Kompensasi;
                                    
                                    // Accumulate grand totals
                                    $grand_total['salary'] += $dt->Total_Pendapatan;
                                    $grand_total['compensation'] += $dt->Kompensasi;
                                    $grand_total['pph21'] += $dt->PPH21;
                                    $grand_total['received'] += $dt->Total_Kompensasi;
                                    
                                    $no++;
                                ?>
                                <tr>
                                    <td><?php echo e($no); ?></td>
                                    <td><?php echo e($dt->NIK); ?></td>
                                    <td><?php echo e($dt->Nama); ?></td>
                                    <td><?php echo e($dt->Bagian); ?></td>
                                    <td><?php echo e($dt->cc_code); ?></td>
                                    <td><?php echo e($dt->contract_name); ?></td>
                                    <td><?php echo e($dt->nomor_rekening); ?></td>
                                    <td><?php echo date('d-M-y',strtotime($dt->Start_Kontrak)).' ~ '.date('d-M-y',strtotime($dt->Finish_Kontrak)); ?></td>
                                    <td><?php echo e($dt->Jumlah_Bulan); ?></td>
                                    <td style="text-align: right;"><?php echo number_format($dt->Total_Pendapatan,0); ?></td>
                                    <td style="text-align: right;"><?php echo number_format($dt->Kompensasi,0); ?></td>
                                    <td style="text-align: right;"><?php echo number_format($dt->PPH21,0); ?></td>
                                    <td style="text-align: right;"><?php echo number_format($dt->Total_Kompensasi,0); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            
                            <?php
                                // CETAK SUB TOTAL CC TERAKHIR
                                if($current_cc != '') {
                                    echo '<tr style="background: #d9edf7; font-weight: bold;">';
                                    echo '<td colspan="5" style="text-align: right;">Sub Total CC: ' . $current_cc . '</td>';
                                    echo '<td></td>';
                                    echo '<td></td>';
                                    echo '<td></td>';
                                    echo '<td></td>';
                                    echo '<td style="text-align: right; border-top: 1px solid #ccc;">' . number_format($cc_total['salary'], 0) . '</td>';
                                    echo '<td style="text-align: right; border-top: 1px solid #ccc;">' . number_format($cc_total['compensation'], 0) . '</td>';
                                    echo '<td style="text-align: right; border-top: 1px solid #ccc;">' . number_format($cc_total['pph21'], 0) . '</td>';
                                    echo '<td style="text-align: right; border-top: 1px solid #ccc;">' . number_format($cc_total['received'], 0) . '</td>';
                                    echo '</tr>';
                                }
                                
                                // CETAK SUB TOTAL DEPARTMENT TERAKHIR
                                if($current_dept != '') {
                                    echo '<tr style="background: #fcf8e3; font-weight: bold;">';
                                    echo '<td colspan="5" style="text-align: right;">TOTAL DEPARTMENT: ' . $current_dept . '</td>';
                                    echo '<td></td>';
                                    echo '<td></td>';
                                    echo '<td></td>';
                                    echo '<td></td>';
                                    echo '<td style="text-align: right; border-top: 2px solid #333;">' . number_format($dept_total['salary'], 0) . '</td>';
                                    echo '<td style="text-align: right; border-top: 2px solid #333;">' . number_format($dept_total['compensation'], 0) . '</td>';
                                    echo '<td style="text-align: right; border-top: 2px solid #333;">' . number_format($dept_total['pph21'], 0) . '</td>';
                                    echo '<td style="text-align: right; border-top: 2px solid #333;">' . number_format($dept_total['received'], 0) . '</td>';
                                    echo '</tr>';
                                }
                            ?>
                        </tbody>
                        <tfoot style="background: #3c8dbc; color: white; font-weight: bold;">
                            <tr>
                                <td colspan="9" style="text-align: right;">GRAND TOTAL :</td>
                                <td style="text-align: right;"><?php echo e(number_format($grand_total['salary'], 0)); ?></td>
                                <td style="text-align: right;"><?php echo e(number_format($grand_total['compensation'], 0)); ?></td>
                                <td style="text-align: right;"><?php echo e(number_format($grand_total['pph21'], 0)); ?></td>
                                <td style="text-align: right;"><?php echo e(number_format($grand_total['received'], 0)); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                    <div style="font-size:14px;">
							<?php if($tipe=='MANAGEMENT'): ?>
							<table style="width:35%;" border="1" cellspacing="0" cellpadding="5" align="center">
								<tr>
									<td colspan="2" style="text-align:center;">Approved by</td>
								</tr>
								<tr>
									<td style="text-align:center;vertical-align:middle;height:60px;" class="relative">
										&nbsp;
									</td>
									<td style="text-align:center;vertical-align:middle;">
										&nbsp;
									</td>
								</tr>
								<tr>
									<th style="width:180px;text-align:center;vertical-align:bottom;">NOVA TRIANA NOFIANTI</th>
									<th style="width:180px;text-align:center;vertical-align:bottom;">RISTIYONO</th>
								</tr>
								<tr>
									<td style="text-align:center;">Section Head</td>
									<td style="text-align:center;">Dept.Head HRGA</td>
								</tr>
							</table>
							<?php else: ?>
								<table style="width:50%;" border="1" cellspacing="0" cellpadding="5" align="center">
									<tr>
										<td colspan="3" style="text-align:center;">Approved by</td>
									</tr>
									<tr>
										<td style="text-align:center;vertical-align:middle;height:60px;" class="relative">
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
									</tr>
									<tr>
										<td style="text-align:center;">Specialist</td>
										<td style="text-align:center;">Section Head</td>
										<td style="text-align:center;">Dept.Head HRGA</td>
									</tr>
								</table>
							<?php endif; ?>
						</div>

                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('Scripts'); ?>
    <script>
        $('body').on("change","#periode",function(){
            var periode = document.getElementById('periode').value;
            if(periode == '') var periode = 0;
            window.location.href = "<?php echo e($site); ?>/CompensationExcel/"+periode;
        });

        function exportTableToExcel(tableId, filename) {
            // Get the table HTML
            var table = document.getElementById(tableId);
            var tableHtml = table.outerHTML;
            
            // Get periode value
            var periodeValue = document.getElementById('periode').value;
            
            // Add style for better formatting
            var style = `
                <style>
                    th {
                        background-color: #3c8dbc;
                        color: white;
                        padding: 8px;
                        text-align: center;
                    }
                    td {
                        padding: 5px;
                    }
                    td:first-child, td:nth-child(2), td:nth-child(3), td:nth-child(4), td:nth-child(5), td:nth-child(6), td:nth-child(7), td:nth-child(8) {
                        text-align: left;
                    }
                    tr[style*="background: #d9edf7"] {
                        background-color: #d9edf7 !important;
                    }
                    tr[style*="background: #fcf8e3"] {
                        background-color: #fcf8e3 !important;
                    }
                </style>
            `;
            
            // Create full HTML document for export
            var htmlContent = `
                <html>
                    <head>
                        <title>${filename}</title>
                        ${style}
                    </head>
                    <body>
                        <h3>Compensation (Contract) - Periode ${periodeValue}</h3>
                        ${tableHtml}
                    </body>
                </html>
            `;
            
            // Create blob and download
            var blob = new Blob([htmlContent], { type: 'application/vnd.ms-excel' });
            var link = document.createElement('a');
            var url = URL.createObjectURL(blob);
            
            link.href = url;
            link.download = filename + '.xls';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make(isset($is_pdf) ? 'layouts/pdf' : 'layouts/payroll_payments', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/payroll/salary_compensation_excel.blade.php ENDPATH**/ ?>