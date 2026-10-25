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
	<?php $__env->startSection('Scripts'); ?>
	<script>
	$('#ovtitle').html('Pareto Overtime Periode <?php echo e($periode_teks); ?>');
	</script>
	<?php $__env->stopSection(); ?>

<script>
        var chartData = {
			labels: [
                <?php 
                if(isset($tb_sumot_detail)){
					$no=0;
					foreach($tb_sumot_detail as $dt){
						$no++;
						if($dt->total_act>0)echo "'".$dt->dept_code."',";
					}
					if($no<=10){
						for($i=$no;$i<=10;$i++){
							echo "'',";
						}
					}
				}?>
            			],
			datasets: [{
				type: 'line',
				label: 'Cumulative',
				borderColor: '#000',
				borderWidth: 2,
				fill: false,
				data: [
                    <?php 
                    $stepSize2='1';
							$cum=0;
							foreach($tb_sumot_detail as $dt){
								$cum=$cum+$dt->total_act;
								$stepSize=floor($total_sumot/5);
								$stepSize2=floor($stepSize/100)*100;
								if($total_sumot==0)$pre=0;
								else $pre=$cum/$total_sumot;
								if($dt->total_act>0)echo "'".$cum."',";
							}
					?>
				],
				yAxisID: 'y-axis-1',
				
			}, {
				type: 'bar',
				label: 'Realisation Overtime',
				backgroundColor: '#0000fe',
				data: [
                    <?php 
                    foreach($tb_sumot_detail as $dt){
							if($dt->total_act>0)echo "'".$dt->total_act2."',";
						}
                    ?>
            	],
				yAxisID: 'y-axis-1',
				//borderColor: 'white',
				//borderWidth: 2
			},{
				type: 'bar',
				label: 'New Overtime',
				backgroundColor: '#0297fe',
				data: [
					<?php if(isset($tb_sumot_detail)){
						foreach($tb_sumot_detail as $dt){
							if($dt->total_act>0)echo "'".$dt->total_act3."',";
						}
					}?>
				],
				yAxisID: 'y-axis-1',
			}]

		};
        console.log(chartData)
			var ctx = document.getElementById('canvas').getContext('2d');
            window.myMixedChart = new Chart(ctx, {
				type: 'bar',
				data: chartData,
				options: {
					responsive: true,
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
								labelString: 'Ammount (Rp)'
							},
							position: 'left',
							id: 'y-axis-1',
							<?php if(isset($tb_sumot_detail)){?>
								ticks: {
									min: 0,
									max: <?php echo e($cum); ?>,
									callback: function (value) {
									return value.toLocaleString('de-DE', {style:'decimal'});}
								}
							<?php }?>
						}, {
							type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
							display: true,
							position: 'right',
							id: 'y-axis-2',
							ticks: {
								callback: function (value) {
								return value.toLocaleString('de-DE', {style:'percent'});
								},
							},

							// grid line settings
							gridLines: {
								drawOnChartArea: false, // only want the grid lines for one axis to show up
							},
						}],
					}
				}
            });
			
</script><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/m_overtime/approvalspl_chartParetto.blade.php ENDPATH**/ ?>