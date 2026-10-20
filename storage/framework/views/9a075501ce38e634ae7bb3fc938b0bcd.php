<style>
	canvas{

max-width:100% !important;
height:600px !important;

}
</style>
<div class="chart-container col-md-12 col-xs-12 ">
    <canvas id="canvas" class="d-flex justify-content-center"></canvas>
</div>
<script src="<?php echo e(asset('/public/assets/js/Chart.min.js')); ?>"></script>
	<script src="<?php echo e(asset('/public/assets/js/utils.js')); ?>"></script>
<script>
        var chartData = {
			labels: [
                <?php 
					foreach($tb_sumot as $dt){
						$kolom= ($dt->periode != '' ?date('M-Y',strtotime($dt->periode)) : '' ) ;
					 	echo "'".$kolom."',";
				}
                ?>
            			],
			datasets: [{
				type: 'line',
				label: '1% from Sales',
				borderColor: '#000',
				borderWidth: 2,
				fill: false,
				data: [
                    <?php 
						foreach($tb_sumot as $dt){
								echo "'".$dt->target_persentase."',";
						}
					?>
				],
				yAxisID: 'y-axis-1',
				
			}, {
				type: 'bar',
				label: 'Overtime Amount',
				backgroundColor: '#f39c12',
				data: [
                    <?php 
						foreach($tb_sumot as $dt){
							echo "'".$dt->aktual_persentase."',";
					}
                    ?>
            	],
				yAxisID: 'y-axis-1',
				//borderColor: 'white',
				//borderWidth: 2
			}]

		};
			var ctx = document.getElementById('canvas').getContext('2d');
            window.myMixedChart = new Chart(ctx, {
				type: 'bar',
				data: chartData,
				options: {
					responsive: true,
					maintainAspectRatio: false,
					title: {
						display: true,
						text: ''
					},
					tooltips: {
						mode: 'index',
						intersect: true
					},
					responsive: true,
					scales: {
						xAxes: [{
							stacked: true,
							display: true,
							scaleLabel: {
								display: true,
								labelString: 'Department'
							}
						}],
						yAxes: [{
							type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
							stacked: true,
							display: true,
							
							scaleLabel: {
								display: true,
								labelString: 'percent (%)'
							},
							position: 'left',
							id: 'y-axis-1',
								ticks: {
									min: 0,
									callback: function (value) {
									return value.toLocaleString('de-DE', {style:'decimal'});}
								}
						}],
					}
					
				}
            });
			
</script><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/m_overtime/approvalspl_chartFixed.blade.php ENDPATH**/ ?>