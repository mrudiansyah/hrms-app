
<?php $__env->startSection('Contents'); ?>
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF; padding: 20px;">
					
					<div class="text-left" style="margin-bottom: 20px;">
						<h4 style="margin: 0; font-weight: bold;">RECAPITULATION INCENTIVE PAYMENT PT. SUMMIT ADYAWINSA INDONESIA</h4>
						<h5 style="margin: 5px 0; font-weight: bold;">LEVEL SECTION HEAD & DEPARTMENT HEAD</h5>
						<h5 style="margin: 0; font-weight: bold;">PERIOD <?php echo e($periode_name); ?></h5>
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
                                            <td style="border-right: 2px solid #000;">SUB TOTAL</td>
                                            <td style="text-align: right;"><?php echo e(number_format($total_incentive, 0)); ?></td>
                                        </tr>
                                        <tr style="background:#000; color:#fff;">
                                            <td style="border-right: 2px solid #000; text-align:center;">TOTAL</td>
                                            <td style="text-align: right;"><?php echo e(number_format($total_incentive, 0)); ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            <!-- Grafik ChartJS -->
                            <div class="col-md-7">
                                <div style="border: 2px solid #ccc; padding: 10px;">
                                    <h4 style="text-align:center; font-weight:bold;">PT SUMMIT ADYAWINSA INDONESIA TREND OF INCENTIVE EXPENSES MONTH BY MONTH</h4>
                                    <canvas id="incentiveChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>

					</div>
				</div>
			</div>
		</div>
	</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
<!-- Load Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
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
                    tension: 0.1
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
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/payroll_recap', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\payroll\salary_summary_assignment.blade.php ENDPATH**/ ?>