@extends('layouts/admin')
@section('Contents')
    <div class="content-wrapper">
        <section class="content-header">
            <?php $background_select='#cccccc';?>
            <h1 onclick="">
                {{$juduls}}
                <div class="pull-right">
                    <a href="/Performances/{{$periode}}/0/{{$nm_level}}" class="btn btn-md btn-default"><i class="fa fa-home"></i> Back</a>
                </div>
            </h1>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-xs-12 col-lg-6 col-md-6">
                    <div class="box box-primary">
                        <div class="box-header">
                            <i class="fa fa-user"></i>
                            <h3 class="box-title">Employee {{$periode}}</h3>
                            <div class="box-tools pull-right">
                                <input type="hidden" id="periode" value="{{$periode}}">
                                &nbsp;
                            </div>
                        </div>
                        <div class="box-body" style="padding:0px 20px 0px 20px;">
                            <div style="width: 100%">
                            <canvas id="canvas1" style="height: 350px; width: 100%;"></canvas>
                            </div><br><br><br>
                        </div>
                        <!-- /.box-body -->

                    </div>
                </div>
                <div class="col-xs-12 col-lg-6 col-md-6">
                    <div class="box box-primary">
                        <div class="box-header">
                            <i class="fa fa-bar-chart"></i>
                            <h3 class="box-title">Performance @if($nm_level!=0){{$nm_level}}@endif</h3>
                            <div class="box-tools pull-right">
                                &nbsp;
                            </div>
                        </div>
                        <div class="box-body" style="padding:0px 20px 0px 20px;">
                            <div style="width: 100%">
                            <canvas id="canvas3" style="height: 350px; width: 100%;"></canvas>
                            </div><br><br><br>
                        </div>
                        <!-- /.box-body -->

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                <div class="box box-primary" style="background:#FFF;">
                    <div class="header" style="padding:10px;">
                        <div class="pull-right">
                            <button class="btn btn-md btn-danger" id="resetRank"><i class="fa fa-refresh"></i> Reset Rank to Normal</button>			
                        </div>
                    </div>
                    <div class="box-body">
                    <table id="tables" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Grade (DH)</th>
                                <th>Grade <small>(BOD)</small></th>
                                <th>NIK</th>
                                <th>Nama Karyawan</th>
                                <th>Dept</th>
                                <th>Jabatan</th>
                                <th>Atasan 1</th>
                                <th style="width:100px;">Masa_Kerja</th>
                                <th>Nilai</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=0;?>
                            @foreach($table3 as $dt)
                                <?php
                                    $T1 = !empty($dt->triwulan1) ? $dt->triwulan1 : '0';
                                    $T2 = !empty($dt->triwulan2) ? $dt->triwulan2 : '0';
                                    $T3 = !empty($dt->triwulan3) ? $dt->triwulan3 : '0';
                                    $T4 = !empty($dt->triwulan4) ? $dt->triwulan4 : '0';
                                ?>
                                <tr>
                                    <td>
                                        <?php $no++;echo $no;?>
                                    </td>
                                    <td>
                                        @if($dt->average>0)
                                        <?php 
                                            $warna='';
                                            $equivalen='';
                                            if($dt->ranked=='1'){$warna="bg-green";$equivalen='A+';}
                                            else if($dt->ranked=='2'){$warna="bg-primary";$equivalen='A';}
                                            else if($dt->ranked=='3'){$warna="bg-gray";$equivalen='B+';}
                                            else if($dt->ranked=='4'){$warna="bg-yellow";$equivalen='B';}
                                            else {$warna="bg-red";$equivalen='C+';}
                                        ?>
                                        <div class="pull-left">
                                            <i class='label {{$warna}}'>
                                                <b ><?php if($dt->ranked=='')echo "0";else echo $equivalen;?></b>
                                            </i>
                                        </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dt->average>0)
                                        <?php 
                                            $warna='';
                                            $equivalen_bod='';
                                            if($dt->ranked_bod=='1'){$warna_bod="bg-green";$equivalen_bod='A+';}
                                            else if($dt->ranked_bod=='2'){$warna_bod="bg-primary";$equivalen_bod='A';}
                                            else if($dt->ranked_bod=='3'){$warna_bod="bg-gray";$equivalen_bod='B+';}
                                            else if($dt->ranked_bod=='4'){$warna_bod="bg-yellow";$equivalen_bod='B';}
                                            else {$warna_bod="bg-red";$equivalen_bod='C+';}
                                        ?>
                                        <div class="pull-left">
                                            <i class='label {{$warna_bod}}'>
                                                <b class="edit-modal" data-idperformance="{{$dt->idperformance}}"  data-ranked="{{$dt->ranked_bod}}" data-judul="{{$dt->employee_name}}"><?php if($dt->ranked_bod=='')echo "0";else echo $equivalen_bod;?></b>
                                            </i>
                                        </div>
                                        @endif
                                    </td>
                                    <td>{{$dt->NIK}}</td>
                                    <td>{{$dt->employee_name}}</td>
                                    <td>{{$dt->dept_code}}</td>
                                    <td>{{$dt->position_name}}</td>
                                    <td>
                                        
                                        {{$dt->leader_name}}
                                    </td>
                                    <td style="text-align:right;" title="{{$dt->tanggal_masuk}} to {{$dt->tgl_distribusi}}">
                                        <?php $thn=floor($dt->masa_kerja_member/12);$bln=$dt->masa_kerja_member%12;?>
                                        <?php if($thn>0)echo $thn.' Tahun';if($thn>0&&$bln>0)echo " ";if($bln>0)echo $bln.' Bulan';?>
                                    </td>
                                    <td style="text-align:center;color:blue;">
                                        {{$dt->average}}
                                    </td>
                                    <td>
                                        <?php
                                            if($dt->grade=='D')$warna='bg-red';
                                            else if($dt->grade=='C')$warna='bg-yellow';
                                            else if($dt->grade=='B+')$warna='bg-aqua';
                                            else if($dt->grade=='A')$warna='bg-primary';
                                            else if($dt->grade=='A+')$warna='bg-green';
                                            else $warna='bg-gray';
                                            echo "<label class='label ".$warna."'>".$dt->grade."</label>";
                                        ?>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

                </div>
                <!-- /.col -->
            </div>

        </section>
    </div>
@endsection
@section('Modals')
    <div class="modal fade" id="modal-edit">
		<div class="modal-dialog box box-primary" style="width:400px;">
			<div class="modal-content">
				<form>
					<input type="hidden" id="idperformance">
					{{ csrf_field() }}
					<div class="modal-body">
						<div class="box box-info box-solid" style="border:0px;">
							<div class="box-header" style="padding:5px 10px;font-size:20px;">
								<label id="judul"></label>
								<div class="pull-right">
									<button type="button" class="btn btn-warning pull-right" id="simpan">Save</button>&nbsp;
									<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>&nbsp;
								</div>
							</div>
							<div class="box-body" style="padding:10px 0px;">
								<div class="form-group">
									<label>Ranked</label>
									<input type="number" min="1" max="5" class="form-control" id="ranked">
								</div>
							</div>
						</div>
					</div>
				</form>		
			</div>
		</div>
    </div>

@endsection
@section('Scripts')
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
		$(document).on('click', '#resetRank', function() {
			var periode=document.getElementById('periode').value;
			//alert(periode);
			$.ajaxSetup({
				type:"POST",
				url: "/Performance/RankResetBOD",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			
			$.ajax({
				data:{periode:periode},
				success: function(respond){
					if(respond=="Sukses"){
						location.reload();
					}else{
						alert(respond);
						//alert('Gagal Update');
					}
				}
			})
		});
		$(document).on('click', '.edit-modal', function() {
			var judul=$(this).data('judul');
			$('#idperformance').val($(this).data('idperformance'));
			$('#ranked').val($(this).data('ranked'));
			document.getElementById("judul").textContent = judul;
			$('#modal-edit').modal('show');
		});
		$('.box-header').on('click', '#simpan', function() {
			var idperformance=$('#idperformance').val();
			var ranked=$('#ranked').val();
			$.ajaxSetup({
				type:"POST",
				url: "/Performance/RankUpdateBOD",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			
			$.ajax({
				data:{idperformance:idperformance,ranked:ranked},
				success: function(respond){
					location.reload();
					// if(respond=="Sukses"){
					// 	location.reload();
					// }else{
					// 	alert(respond);
					// 	//alert('Gagal Update');
					// }
				}
			})
		});

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
	<script src="{{ asset('/public/assets/js/Chart.min.js') }}"></script>
	<script src="{{ asset('/public/assets/js/utils.js') }}"></script>
	<!-- Grafik Bar/Line -->
	<script>
		window.onload = function() {
			// Data untuk grafik pertama
			var chartData1 = {
				labels: [
					<?php if(isset($table1)){
						foreach($table1 as $dt){
							$kolom = $dt->jabatan;
							echo "'".$kolom."',";
						}
					}?>
				],
				datasets: [{
					type: 'bar',
					label: 'Person',
					backgroundColor: '#0dc0ef',
					data: [
						<?php if(isset($table1)){
							foreach($table1 as $dt){
								echo "'".$dt->total."',";
							}
						}?>
					],
					yAxisID: 'y-axis-1',
				}]
			};
			// Grafik pertama
			var ctx1 = document.getElementById('canvas1').getContext('2d');
			window.myMixedChart1 = new Chart(ctx1, {
				type: 'bar',
				data: chartData1,
				options: {
					responsive: true,
					title: {
						display: false,
						text: 'Grafik Pertama'
					},
					tooltips: {
						mode: 'index',
						intersect: true
					},
					onClick: (event, elements) => {
						if (elements.length > 0) {
							const index = typeof elements[0].index === 'number'
								? elements[0].index
								: elements[0]._index;

							// Sesuaikan URL berdasarkan elemen yang diklik
							const urls =
								<?php
								$distributionUrls = [];
								if(isset($table1)){
									foreach($table1 as $dt){
										$distributionUrls[] =
											'/Performances/Distribution/' .
											rawurlencode((string) $periode) . '/' .
											rawurlencode((string) $dt->jabatan) . '/0';
									}
								}
								?>
								@json($distributionUrls);

							// Redirect ke URL yang sesuai
							//window.location.href = "/DashboardOT/2024/2024-01";
							if (typeof urls[index] === 'string') {
								window.location.href = urls[index];
							}
						}
					},

					scales: {
						xAxes: [{
							stacked: true,
							display: true,
							scaleLabel: {
								display: true,
								labelString: 'Position'
							}
						}],
						yAxes: [{
							type: 'linear',
							stacked: true,
							display: true,
							scaleLabel: {
								display: true,
								labelString: 'Person'
							},
							position: 'left',
							id: 'y-axis-1',
							ticks: {
								callback: function(value) {
									if (value >= 1000000000) {
										return value / 1000000000 + ' M'; // Jutaan
									} else if (value >= 1000000) {
										return value / 1000000 + ' Jt'; // Ribuan
									} else if (value >= 1000) {
										return value / 1000 + ' K'; // Ribuan
									}
									return value; // Default (satuan)
								}
							}
							
						}],
					}
				}
			});

			<?php //if($nm_level!=0){?>
			// Data untuk grafik ketiga
			var chartData3 = {
				labels: [
					'A+','A','B+','B','C+'
				],
				datasets: [{
					type: 'line',
					label: '% Standar',
					borderColor: '#000',
					borderDash: [5, 5], // Membuat garis menjadi putus-putus
					pointRadius: 0, // Menghilangkan bullet (titik data)
					borderWidth: 1,
					fill: false,
					data: [
						'10','20','40','20','10'
					],
					yAxisID: 'y-axis-2',
				}, {
					type: 'line',
					label: '% Distribution',
					borderColor: '#0000fe',
					//borderDash: [5, 5], // Membuat garis menjadi putus-putus
					pointRadius: 1, // Menghilangkan bullet (titik data)
					borderWidth: 1,
					fill: false,
					data: [
						<?php 
						foreach($table2 as $dt){
							echo "'".$dt->R1P."','".$dt->R2P."','".$dt->R3P."','".$dt->R4P."','".$dt->R5P."'";
						}
						?>
					],
					yAxisID: 'y-axis-2',
				}, {
					type: 'bar',
					label: 'Person',
					backgroundColor:[
						'#34b128', // Warna untuk bar pertama
						'#0262fe', // Warna untuk bar kedua
						'#CCCCCC', // Warna untuk bar ketiga
						'#f39c11', // Warna untuk bar keempat
						'#dd4b39', // Warna untuk bar kelima
					],
					data: [
						<?php 
						foreach($table2 as $dt){
							echo "'".$dt->R1R."','".$dt->R2R."','".$dt->R3R."','".$dt->R4R."','".$dt->R5R."'";
						}
						?>
					],
					yAxisID: 'y-axis-1',
				},{
					type: 'line',
					label: 'Ideal',
					borderColor: '#ffffff',
					//borderDash: [5, 5], // Membuat garis menjadi putus-putus
					pointRadius: 1, // Menghilangkan bullet (titik data)
					borderWidth: 0,
					fill: false,
					data: [
						<?php 
						foreach($table2 as $dt){
							$total=$dt->TOTAL;
							$std1=number_format($total*0.1,0);
							$std2=number_format($total*0.2,0);
							$std3=number_format($total*0.4,0);
							echo "'".$std1."','".$std2."','".$std3."','".$std2."','".$std1."'";
						}
						?>
					],
					yAxisID: 'y-axis-1',

				}]
			};

			// Grafik ketiga
			var ctx3 = document.getElementById('canvas3').getContext('2d');
			window.myMixedChart3 = new Chart(ctx3, {
				type: 'bar',
				data: chartData3,
				options: {
					responsive: true,
					title: {
						display: false,
						text: 'Grafik Kedua'
					},
					tooltips: {
						mode: 'index',
						intersect: true
					},
					onClick: (event, elements) => {
						if (elements.length > 0) {
							const index = typeof elements[0].index === 'number'
								? elements[0].index
								: elements[0]._index;

							// Sesuaikan URL berdasarkan elemen yang diklik
							const urls =
								<?php
								$distributionUrls = [];
								for ($distributionRank = 1; $distributionRank <= 5; $distributionRank++) {
									$distributionUrls[] =
										'/Performances/Distribution/' .
										rawurlencode((string) $periode) . '/' .
										rawurlencode((string) $nm_level) . '/' .
										$distributionRank;
								}
								?>
								@json($distributionUrls);

							// Redirect ke URL yang sesuai
							//window.location.href = "/DashboardOT/2024/2024-01";
							if (typeof urls[index] === 'string') {
								window.location.href = urls[index];
							}
						}
					},
					scales: {
						xAxes: [{
							stacked: false,
							display: true,
							scaleLabel: {
								display: true,
								labelString: 'Rank'
							}
						}],
						yAxes: [{
							type: 'linear',
							stacked: false,
							display: true,
							scaleLabel: {
								display: true,
								labelString: '%'
							},
							position: 'right',
							id: 'y-axis-2',
						},{
							type: 'linear',
							stacked: false,
							display: true,
							scaleLabel: {
								display: true,
								labelString: 'Person'
							},
							position: 'left',
							id: 'y-axis-1',
							ticks: {
								callback: function(value) {
									if (value >= 1000000) {
										return value / 1000000 + ' Jt'; // Jutaan
									} else if (value >= 1000) {
										return value / 1000 + ' K'; // Ribuan
									}
									return value; // Default (satuan)
								}
							}
						}],
					}
				}
			});
			<?php //}?>
		};
	</script>
	
@endsection
