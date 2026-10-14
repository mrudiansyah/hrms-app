@extends('layouts/payroll_recap')
@section('Contents')
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF; padding: 20px;">
					
					<div class="text-left" style="margin-bottom: 20px;">
						<h4 style="margin: 0; font-weight: bold;">RECAPITULATION INCENTIVE PAYMENT PT. SUMMIT ADYAWINSA INDONESIA</h4>
						<h5 style="margin: 5px 0; font-weight: bold;">LEVEL SECTION HEAD & DEPARTMENT HEAD</h5>
						<h5 style="margin: 0; font-weight: bold;">PERIOD {{$periode_name}}</h5>
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
                                        @php 
                                            $total_incentive = 0;
                                        @endphp
                                        @foreach($tb_rekap as $dt)
                                            @php 
                                                $total_incentive += $dt->nominal_incentive;
                                            @endphp
                                            <tr>
                                                <td style="border-right: 2px solid #000;">{{ $dt->department != '' ? $dt->department : ($dt->dept_code != '' ? $dt->dept_code : 'PRODUCTION STAMPING') }}</td>
                                                <td style="text-align: right;">{{ number_format($dt->nominal_incentive, 0) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot style="border-top: 3px double #000; font-weight: bold; background: #eee;">
                                        <tr>
                                            <td style="border-right: 2px solid #000;">SUB TOTAL</td>
                                            <td style="text-align: right;">{{ number_format($total_incentive, 0) }}</td>
                                        </tr>
                                        <tr style="background:#000; color:#fff;">
                                            <td style="border-right: 2px solid #000; text-align:center;">TOTAL</td>
                                            <td style="text-align: right;">{{ number_format($total_incentive, 0) }}</td>
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
@endsection
@section('Scripts')
<!-- Load Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById('incentiveChart').getContext('2d');
        
        // Data derived from controller
        var chartLabels = [
            @foreach($monthly_data as $m)
                "{{ $m['month'] }}",
            @endforeach
        ];
        
        var chartData = [
            @foreach($monthly_data as $m)
                {{ $m['amount'] }},
            @endforeach
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
@endsection
