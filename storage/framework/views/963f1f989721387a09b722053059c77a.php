
<?php $__env->startSection('Contents'); ?>
    <style>
        @media print {
            /* Sembunyikan elemen yang tidak perlu di print */
            .no-print, nav, sidebar, .btn {
                display: none !important;
            }
            /* Atur ukuran kertas */
            @page {
                size: A4;
                margin: 1cm;
            }
        }
    </style>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Payroll
                <small>Recap Assignment</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Recap Assignment</li>
            </ol>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box" style="background:#FFF; padding: 20px;border:0px;">
                        
                        <div class="text-left" style="margin-bottom: 20px;">
                            <!-- <img src="<?php echo e(asset('dist/img/SummitLogo.png')); ?>" alt="Logo" style="height: 50px; margin-bottom: 10px;"> -->
                            <h4 style="margin: 0; font-weight: bold;">RECAPITULATION INCENTIVE PAYMENT PT. SUMMIT ADYAWINSA INDONESIA</h4>
                            <h5 style="margin: 5px 0; font-weight: bold;">LEVEL SECTION HEAD & DEPARTMENT HEAD</h5>
                            <h5 style="margin: 0; font-weight: bold;">PERIOD <?php echo e($periode_name); ?></h5>
                            <br>
                            <!-- <a href="<?php echo e(url('/payroll/recap_assignment_pdf/'.$start.'/'.$end)); ?>" class="btn btn-danger btn-sm" target="_blank">
                                <i class="fa fa-file-pdf-o"></i> &nbsp;Preview PDF
                            </a> -->
                            <button onclick="window.print()" class="btn btn-primary no-print">
                                Print
                            </button>
                        </div>

                        <div class="box-body">
                            <div class="row">
                                <!-- Tabel Departemen -->
                                <div class="col-md-5">
                                    <table class="table table-bordered table-striped" style="border: 2px solid #000;">
                                        <thead style="border-bottom: 3px double #000;">
                                            <tr>
                                                <th style="text-align: center; vertical-align: middle; border-right: 2px solid #000;">DEPARTMENT</th>
                                                <th style="text-align: center; vertical-align: middle; width: 150px;">NOMINAL INCENTIVE</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $total_incentive = 0;
                                            ?>
                                            <?php $__currentLoopData = $tb_rekap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php 
                                                    $total_incentive += $dt->nominal_incentive;
                                                ?>
                                                <tr>
                                                    <td style="border-right: 2px solid #000;"><?php echo e($dt->department != '' ? $dt->department : ($dt->dept_code != '' ? $dt->dept_code : 'PRODUCTION STAMPING')); ?></td>
                                                    <td style="text-align: right;"><?php echo e(number_format($dt->nominal_incentive, 0)); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                        <tfoot style="border-top: 3px double #000; font-weight: bold; background: #eee;">
                                            <tr>
                                                <td style="border-right: 2px solid #000;"><b>SUB TOTAL</b></td>
                                                <td style="text-align: right;"><b><?php echo e(number_format($total_incentive, 0)); ?></b></td>
                                            </tr>
                                            <tr style="background:#000; color:#fff;">
                                                <td style="border-right: 2px solid #000; text-align:center;"><b>TOTAL</b></td>
                                                <td style="text-align: right;"><b><?php echo e(number_format($total_incentive, 0)); ?></b></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                
                                <!-- Grafik ChartJS -->
                                <div class="col-md-7">
                                    <div id="chart-wrapper" style="border: 2px solid #ccc; padding: 10px;">
                                        <h4 id="chart-title" style="text-align:center; font-weight:bold;">PT SUMMIT ADYAWINSA INDONESIA TREND OF INCENTIVE EXPENSES MONTH BY MONTH</h4>
                                        <div id="canvas-container" style="position: relative;">
                                            <canvas id="incentiveChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <br><br>
                                <div style="font-size:14px;">
                                    <table border="1" cellspacing="0" cellpadding="5" align="center" style="width: 1200px;">
                                        <tr>
                                            <td colspan="4" style="text-align:center;">Approved by</td>
                                            <td style="text-align:center;">Verified by</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:center;vertical-align:middle;height:90px;" class="relative">&nbsp;</td>
                                            <td style="text-align:center;vertical-align:middle;height:90px;" class="relative">&nbsp;</td>
                                            <td style="text-align:center;vertical-align:middle;height:90px;" class="relative">&nbsp;</td>
                                            <td style="text-align:center;vertical-align:middle;height:90px;" class="relative">&nbsp;</td>
                                            <td style="text-align:center;vertical-align:middle;height:90px;" class="relative">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td style="width:180px;text-align:center;vertical-align:bottom;">RISTIYONO</td>
                                            <td style="width:180px;text-align:center;vertical-align:bottom;">ARIF BASUKI</td>
                                            <td style="width:180px;text-align:center;vertical-align:bottom;">EKA NOVIANTO NUGROHO</td>
                                            <td style="width:180px;text-align:center;vertical-align:bottom;">PORNTEP KLINSRISUK</td>
                                            <td style="width:180px;text-align:center;vertical-align:bottom;">YUPAWAN CHARUARAYANANT</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:center;">HRGA DEPT. HEAD</td>
                                            <td style="text-align:center;">ASST GENERAL MANAGER</td>
                                            <td style="text-align:center;">VICE PRESIDENT DIRECTOR</td>
                                            <td style="text-align:center;">PRESIDENT DIRECTOR</td>
                                            <td style="text-align:center;">FA DIRECTOR</td>
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
<!-- Load Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Match canvas-container height to left table column height
        var tableCol   = document.querySelector('.col-md-5');
        var tableH     = tableCol.offsetHeight;
        var titleH     = document.getElementById('chart-title').offsetHeight;
        var containerH = Math.max(tableH - titleH - 30, 150);
        document.getElementById('canvas-container').style.height = containerH + 'px';

        var ctx = document.getElementById('incentiveChart').getContext('2d');
        
        // Data derived from controller
        var chartLabels = [
            <?php $__currentLoopData = $monthly_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                "<?php echo e($m['month']); ?>",
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        ];
        
        var chartData = [
            <?php $__currentLoopData = $monthly_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo e($m['amount']); ?>,
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        ];

        var maxAmount = Math.max.apply(null, chartData);
        var suggestedMax = Math.ceil((maxAmount + 1000000) / 10000000) * 10000000;

        var incentiveChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Nominal Incentive',
                    data: chartData,
                    borderColor: 'rgba(54, 115, 169, 1)',
                    backgroundColor: 'rgba(54, 115, 169, 1)',
                    pointBackgroundColor: 'rgba(54, 115, 169, 1)',
                    borderWidth: 3,
                    pointRadius: 5,
                    fill: false,
                    tension: 0.1 // straight connecting lines
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var value = context.parsed.y;
                                return value.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: suggestedMax > 60000000 ? suggestedMax : 60000000,
                        ticks: {
                            callback: function(value, index, values) {
                                return value.toLocaleString('id-ID');
                            },
                            stepSize: 10000000
                        }
                    }
                }
            },
            plugins: [{
                // Plugin to draw data labels above points
                afterDatasetsDraw: function(chart) {
                    var ctx = chart.ctx;
                    chart.data.datasets.forEach(function(dataset, i) {
                        var meta = chart.getDatasetMeta(i);
                        if (!meta.hidden) {
                            meta.data.forEach(function(element, index) {
                                // Draw only if value > 0 to match screenshot visually
                                var dataString = dataset.data[index];
                                if(dataString > 0) {
                                    ctx.fillStyle = 'black';
                                    var fontSize = 10;
                                    var fontStyle = 'normal';
                                    var fontFamily = 'Verdana';
                                    ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'middle';

                                    var position = element.tooltipPosition();
                                    ctx.fillText(dataString.toLocaleString('id-ID'), position.x, position.y - 12);
                                }
                            });
                        }
                    });
                }
            }]
        });
    });
    window.addEventListener('load', function() {
        //window.print();
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/payroll/recap_assignment.blade.php ENDPATH**/ ?>