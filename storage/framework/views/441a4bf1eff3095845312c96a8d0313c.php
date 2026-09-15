<!DOCTYPE html>
<html>
<head>
    <title>Slip Overtime</title>
    <style>
        @page { margin: 20px 50px; }
        body { font-family: sans-serif; font-size: 7.8px; line-height: 1.0; }
        .header { text-align: center; font-weight: bold; margin-bottom: 2px; font-size: 9px; }
        .info { width: 100%; margin-bottom: 1px; }
        .info td { padding: 0.1px; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 1.2px; }
        table.data th, table.data td { border: 1px solid black; padding: 1.2px; text-align: center; }
        .bg-red { background-color: red !important; color: white !important; font-weight: bold; }
        .summary-box { width: 100%; font-size: 7.8px; page-break-inside: avoid; }
        table.bottom-left { border-collapse: collapse; width: 180px; }
        table.bottom-left th, table.bottom-left td { border: 1px solid black; padding: 1.2px; text-align: center; }
        table.bottom-left td.val { text-align: right; }
        table.bottom-right { border-collapse: collapse; width: 250px;}
        table.bottom-right th, table.bottom-right td { border: 1px solid black; padding: 2px; }
        .sign-area { height: 40px; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="header">
        SLIP UPAH LEMBUR PT SAI<br>
        PERIODE <?php echo e(date('d F', strtotime($start))); ?> S/D <?php echo e(date('d F Y', strtotime($end))); ?>

    </div>

    <table class="info">
        <tr>
            <td width="15%">NAMA KARYAWAN</td>
            <td width="55%">: <?php echo e($employee->employee_name ?? '-'); ?></td>
            <td width="10%">DIVISI</td>
            <td width="20%">: <?php echo e($employee->dept_name ?? ($employee->dept_code ?? '-')); ?></td>
        </tr>
        <tr>
            <td>NIK</td>
            <td colspan="3">: <?php echo e($employee->NIK ?? '-'); ?></td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">HARI</th>
                <th rowspan="2">TANGGAL</th>
                <th rowspan="2">LEMBUR<br>PERJAM</th>
                <th colspan="2">OVER TIME</th>
                <th rowspan="2">TOTAL<br>JAM</th>
                <th rowspan="2">KONVERSI<br>TOTAL JAM</th>
                <th rowspan="2">UANG LEMBUR</th>
                <th colspan="2">UANG MAKAN</th>
                <th rowspan="2">TOTAL<br>BAYAR</th>
            </tr>
            <tr>
                <th>AWAL</th>
                <th>AKHIR</th>
                <th>OT</th>
                <th>TL</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $grand_total_jam = 0;
                $grand_konversi_jam = 0;
                $grand_uang_lembur = 0;
                $grand_uang_makan_ot = 0;
                $grand_uang_makan_tl = 0;
                $grand_total_bayar = 0;
                $no = 1;
            ?>
            <?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $hari_en = date('l', strtotime($date));
                    $hari_id = [
                        'Sunday' => 'MINGGU', 'Monday' => 'SENIN', 'Tuesday' => 'SELASA', 'Wednesday' => 'RABU', 
                        'Thursday' => 'KAMIS', 'Friday' => 'JUMAT', 'Saturday' => 'SABTU'
                    ];
                    $hari = $hari_id[$hari_en] ?? strtoupper($hari_en);
                    
                    $isEndOfWeek = in_array($hari, ['SABTU', 'MINGGU']);
                    $ot = $tabel->get($date);
                    $meal = $meals->get($date);
                    
                    $slpj = $employee->slpj ?? 0;
                    
                    $jam_awal = '';
                    $jam_akhir = '';
                    $total_jam = '';
                    $konversi = '';
                    $uang_lembur = 0;
                    $uang_makan_ot = 0;
                    $uang_makan_tl = 0;

                    if ($ot) {
                        $jam_awal = $ot->start_act ? date('H:i', strtotime($ot->start_act)) : '';
                        $jam_akhir = $ot->finish_act ? date('H:i', strtotime($ot->finish_act)) : '';
                        $total_jam = $ot->hours_act;
                        $konversi = $ot->hours_convertion;
                        
                        if(($employee->position_id ?? 0) == 18) {
                            $uang_lembur = $amt_driver;
                        } else {
                            $uang_lembur = $slpj * $konversi;
                        }
                    }

                    if ($meal) {
                        $uang_makan_ot = $meal->meal;
                    }

                    $total_bayar = $uang_lembur + $uang_makan_ot + $uang_makan_tl;

                    $grand_total_jam += (float)$total_jam;
                    $grand_konversi_jam += (float)$konversi;
                    $grand_uang_lembur += $uang_lembur;
                    $grand_uang_makan_ot += $uang_makan_ot;
                    $grand_uang_makan_tl += $uang_makan_tl;
                    $grand_total_bayar += $total_bayar;
                ?>
                <tr>
                    <td class="<?php echo e($isEndOfWeek ? 'bg-red' : ''); ?>"><?php echo e(str_pad($no++, 2, '0', STR_PAD_LEFT)); ?></td>
                    <td class="<?php echo e($isEndOfWeek ? 'bg-red' : ''); ?>"><?php echo e($hari); ?></td>
                    <td><?php echo e($date != '' ? date('d-m-Y', strtotime($date)) : ''); ?></td>
                    <td><?php echo e($slpj > 0 ? number_format($slpj, 0, '.', ',') : ''); ?></td>
                    <td><?php echo e($jam_awal); ?></td>
                    <td><?php echo e($jam_akhir); ?></td>
                    <td><?php echo e($total_jam ?: ''); ?></td>
                    <td><?php echo e($konversi ?: ''); ?></td>
                    <td><?php echo e($uang_lembur > 0 ? number_format($uang_lembur, 0, '.', ',') : ''); ?></td>
                    <td><?php echo e($uang_lembur || $uang_makan_ot ? number_format($uang_makan_ot, 0, '.', ',') : ($ot ? '0' : '')); ?></td>
                    <td><?php echo e($uang_lembur || $uang_makan_tl ? number_format($uang_makan_tl, 0, '.', ',') : ($ot ? '0' : '')); ?></td>
                    <td><?php echo e($total_bayar > 0 ? number_format($total_bayar, 0, '.', ',') : ''); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <tr>
                <td colspan="6" style="border:none;"></td>
                <td><?php echo e((float)$grand_total_jam); ?></td>
                <td><?php echo e((float)$grand_konversi_jam); ?></td>
                <td><?php echo e(number_format($grand_uang_lembur, 0, '.', ',')); ?></td>
                <td><?php echo e(number_format($grand_uang_makan_ot, 0, '.', ',')); ?></td>
                <td><?php echo e(number_format($grand_uang_makan_tl, 0, '.', ',')); ?></td>
                <td><?php echo e(number_format($grand_total_bayar, 0, '.', ',')); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="summary-box">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 50%; vertical-align: top; border: none; padding: 0;">
                    <table class="bottom-left" align="left" style="width:220px;">
                        <tr>
                            <td width="50%">DIBAYAR OLEH</td>
                            <td width="50%">DITERIMA OLEH</td>
                        </tr>
                        <tr>
                            <td class="sign-area"></td>
                            <td class="sign-area"></td>
                        </tr>
                        <tr>
                            <td>PAYROLL</td>
                            <td><?php echo e($employee->employee_name ?? '-'); ?></td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top; border: none; padding: 0;">
                    <table class="bottom-right" style="width:200px;" align="right">
                        <tr>
                            <td>RAPEL OTHER</td>
                            <td class="val"><?php echo e(number_format($summary->rapel_amount ?? 0, 0, '.', ',')); ?></td>
                        </tr>
                        <tr>
                            <td>PPH 21</td>
                            <td class="val"><?php echo e(number_format($summary->pph21_amount ?? 0, 0, '.', ',')); ?></td>
                        </tr>
                        <tr>
                            <td>Others</td>
                            <td class="val">0</td>
                        </tr>
                        <tr>
                            <td><strong>TOTAL PAID</strong></td>
                            <td class="val"><strong><?php echo e(number_format(($grand_total_bayar + ($summary->rapel_amount ?? 0)) - ($summary->pph21_amount ?? 0), 0, '.', ',')); ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\payroll\slip_overtime.blade.php ENDPATH**/ ?>