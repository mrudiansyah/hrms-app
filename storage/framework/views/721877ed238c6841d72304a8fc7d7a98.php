
<?php $__env->startSection('Contents'); ?>
	<div class="content-wrapper">
		<section class="content-header">
			<?php $background_select='#cccccc';?>
			<h1 onclick="">
				<?php echo e($juduls); ?>

				<small><?php if(isset($subjudul))echo $subjudul;else echo 'TMS';?></small>
				<div class="pull-right">
					<a href="/Performances/<?php echo e($periode); ?>/0/0" class="btn btn-md btn-info"><i class="fa fa-arrow-left"></i> Back</a>
				</div>
			</h1>
		</section>
		<section class="content">
			<div class="row">
				<div class="col-xs-12 col-lg-5 col-md-5">
					
					<div class="box box-primary">
						<div class="box-header">
							<i class="fa fa-bar-chart"></i>
							<h3 class="box-title">Summary</h3>
							<div class="box-tools pull-right">
							<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button type="button" class="btn btn-default btn-xs" data-widget="remove"><i class="fa fa-times"></i></button>
							</div>
						</div>
						<div class="box-body" style="padding:0px 20px 0px 20px;">
							<div class="col-xs-12 col-sm-12 col-lg-6" style="padding:3px;">
								<!-- small box -->
								<div class="small-box bg-yellow">
									<div class="inner">
									<h3><?php echo e($data['progress_create']); ?><sup style="font-size: 20px">%</sup></h3>

									<p><?php echo e($data['jumlah_create']); ?> Created | <?php echo e($data['sisa_create']); ?> Not Create</p>
									</div>
									<div class="icon">
									<i class="ion ion-person"></i>
									</div>
									<a href="#" class="small-box-footer">Peniali 1</a>
								</div>
							</div>
							<div class="col-xs-12 col-sm-12 col-lg-6" style="padding:3px;">
								<!-- small box -->
								<div class="small-box bg-green">
									<div class="inner">
									<h3><?php echo e($data['progress_confirm']); ?><sup style="font-size: 20px">%</sup></h3>

									<p><?php echo e($data['jumlah_confirm']); ?> Firm | <?php echo e($data['sisa_confirm']); ?> Not Firm</p>
									</div>
									<div class="icon">
									<i class="ion ion-person"></i>
									</div>
									<a href="#" class="small-box-footer">Penilai 2</a>
								</div>
							</div>
							<div style="width: 100%">
								<canvas id="pieCanvas"></canvas>
							</div><br><br>
						</div>
						<!-- /.box-body -->

					</div>
				</div>
				<div class="col-xs-12 col-lg-7 col-md-7">
					<div class="box box-primary">
						<div class="box-header">
							<i class="fa fa-bar-chart"></i>
							<h3 class="box-title">Progress</h3>
							<div class="box-tools pull-right">
							<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button type="button" class="btn btn-default btn-xs" data-widget="remove"><i class="fa fa-times"></i></button>
							</div>
						</div>
						<div class="box-body" style="padding:0px 20px 0px 20px;">
							<div style="width: 100%">
							<canvas id="canvas"></canvas>
							</div><br><br><br>
						</div>
						<!-- /.box-body -->

					</div>
				</div>
			</div>
		</section>
	</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<script>
		$(document).ready(function() {

			var table = $('#tables').DataTable({
				'paging'      : true,
				'lengthChange': false,
				'searching'   : true,
				'ordering'    : false,
				'info'        : true,
				"pageLength"  : 50,
				'autoWidth'   : false,
				"lengthMenu"  : [[5,10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
				//"scrollX"     : true
			});
			
			new $.fn.dataTable.Buttons( table, {
				//buttons: ['copy', 'excel', 'print']
				buttons: [
				{ extend: 'copyHtml5', footer: true },
				{ extend: 'excelHtml5', footer: true },
				{ extend: 'print', footer: true }
				]
			} );
			
			table.buttons( 0, null ).container().prependTo(
				table.table().container()
			);
		} );


	</script>
	<script src="<?php echo e(asset('/public/assets/js/Chart.min.js')); ?>"></script>
	<script src="<?php echo e(asset('/public/assets/js/utils.js')); ?>"></script>
	<!-- Grafik Bar/Line -->
	<script>
			var chartData = {
				labels: [
					<?php 
						foreach($tb_result as $dt){
							echo "'".$dt->department."',";
						}
					?>
				],
				datasets: [{
					type: 'bar',
					label: 'Not Create',
					backgroundColor: '#f08080',
					data: [
						<?php 
						foreach($tb_result as $dt){
							echo "'".$dt->sisa_create."',";
						}
					?>
					],
					yAxisID: 'y-axis-1',
				}, {
					type: 'bar',
					label: 'Created Not Firm',
					backgroundColor: '#f39c11',
					data: [
						<?php 
						foreach($tb_result as $dt){
							echo "'".$dt->sisa_confirm."',";
						}
					?>
					],
					yAxisID: 'y-axis-1',
				}, {
					type: 'bar',
					label: 'Created & Firm',
					backgroundColor: '#02a55a',
					data: [
						<?php 
						foreach($tb_result as $dt){
							echo "'".$dt->jumlah_confirm."',";
						}
					?>
					],
					yAxisID: 'y-axis-1',
				}]

			};
			// Data Pie Chart
			<?php
			$pieData = [
				'Not Firm' => $data['NotFirm'],
				'Firm' => $data['Firm'],
			];
			?>
			
			var pieChartData = {
				labels: ['Not Firm', 'Firm'],
				datasets: [{
					data: [
						<?php
						echo implode(',', array_values($pieData)); // Ambil nilai data untuk Pie Chart
						?>
					],
					backgroundColor: [
						'#f39c11',
						'#00a65a',
					],
					borderColor: 'rgba(255, 255, 255, 1)',
					borderWidth: 1
				}]
			};

			// Inisialisasi Pie Chart
			var ctxPie = document.getElementById('pieCanvas').getContext('2d');
			var myPieChart = new Chart(ctxPie, {
				type: 'doughnut',
				data: pieChartData,
				options: {
					responsive: true,
					plugins: {
						legend: {
							position: 'top',
						},
						title: {
							display: true,
							text: 'Distribusi Data Pie Chart'
						}
					}
				},
				plugins: [{
                id: 'centerText',
                beforeDraw: function(chart) {
                    const ctx = chart.ctx;
                    const width = chart.width;
                    const height = chart.height;

                    ctx.save();
                    ctx.font = 'bold 20px Arial';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillStyle = 'black';

                    // Menampilkan total data di tengah
                    const total = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                    ctx.fillText(total, width / 2, height / 2);
                    ctx.restore();
                }
            }]

			});

			window.onload = function() {
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
									labelString: 'Departments'
								}
							}],
							yAxes: [{
								type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
								stacked: true,
								display: true,
								
								scaleLabel: {
									display: true,
									labelString: 'Qty Employee'
								},
								position: 'left',
								id: 'y-axis-1',
									ticks: {
										stepSize:1,
									}
							}, {
								type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
								display: false,
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
				
			};
		</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/performance/performance_progress.blade.php ENDPATH**/ ?>