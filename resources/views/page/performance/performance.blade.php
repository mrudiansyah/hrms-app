@extends('layouts/admin')
@section('Contents')
	<meta name="csrf-token" content="{{ csrf_token() }}">
   <!-- Contents -->
   <style>
        tr:hover {
          background-color: #DCDCDC;
		  cursor:pointer;
        }
   </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Employees
				<div class="pull-right">
					<?php $tahun_ini=date('Y');?>
				<input type="number" id="periode" class="form-control" value="{{$periode}}" max="{{$tahun_ini}}">	
				</div>
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-xs-12">
			<div class="box box-primary" style="background:#FFF;">
				<div class="box-body">
				<table id="tables" class="table table-bordered">
					<thead>
						<tr>
							<th>No</th>
							<th>NIK</th>
							<th>Employee Name</th>
							<th>Dept</th>
							<th>Position</th>
							<th>Direct leader</th>
							<th>Masa Kerja</th>
							<th>Jan-Jun</th>
							<!-- <th>Apr-Jun</th> -->
							<th>Jul-Dec</th>
							<!-- <th>Oct-Dec</th> -->
							<th>Average</th>
							<th>Grade</th>
							<th style="width:60px;">Detail</th>
							<th>Info
							</th>
						</tr>
					</thead>
					<tbody>
						<?php $no=0;?>
						@foreach($tb_employee as $dt)
							@if($dt->leader_name==strtoupper($saya)||$id_user==$dt->id_leader2)
								<?php
									$T1 = !empty($dt->triwulan1) ? $dt->triwulan1 : '0';
									$T2 = !empty($dt->triwulan2) ? $dt->triwulan2 : '0';
									$T3 = !empty($dt->triwulan3) ? $dt->triwulan3 : '0';
									$T4 = !empty($dt->triwulan4) ? $dt->triwulan4 : '0';
								?>
								<tr>
									<td>
										<?php $no++;echo $no;
										// echo $dt->leader_name."=".strtoupper($saya)."||".$id_user."==".$dt->id_leader2;
										?>
									</td>
									<td>{{$dt->NIK}}</td>
									<td>{{$dt->employee_name}}</td>
									<td>{{$dt->dept_code}}</td>
									<td>{{$dt->position_name}}</td>
									<td>
										{{$dt->leader_name}}
									</td>
									<td>
										<?php $thn=floor($dt->masa_kerja_member/12);$bln=$dt->masa_kerja_member%12;?>
										<?php if($thn>0)echo $thn.' Tahun';if($thn>0&&$bln>0)echo " ";if($bln>0)echo $bln.' Bulan';?>
									</td>
									<?php
											$created_by=0;
											if($dt->leader_name==strtoupper($saya)){
												$created_by='1';
											}elseif($id_user==$dt->id_leader2){
												$created_by='2';
											}


									?>
									<!-- <td style="text-align:center;color:blue;">
										<?php $pos=$periode."-03";?>
										@if($bulan_skr>$pos)
											<a href="/PerformanceDetail/{{$dt->idperformance}}/{{$periode}}/1/{{$dt->employee_name}}">{{$T1}}</a>
										@endif
									</td> -->
									<td style="text-align:center;color:blue;">
										<?php $pos=$periode."-06";?>
										@if($bulan_skr>$pos)
											<a href="/PerformanceDetail/{{$dt->idperformance}}/{{$periode}}/2/{{$dt->employee_name}}/{{$created_by}}">{{$T2}}</a>
										@endif
										<!-- <a href="/PerformanceDetail/{{$dt->idperformance}}/{{$periode}}/2/{{$dt->employee_name}}/{{$created_by}}">{{$T2}}</a> -->
									</td>
									<!-- <td style="text-align:center;color:blue;">
										<?php $pos=$periode."-09";?>
										@if($bulan_skr>$pos)
											<a href="/PerformanceDetail/{{$dt->idperformance}}/{{$periode}}/3/{{$dt->employee_name}}">{{$T3}}</a>
										@endif
									</td> -->
									<td style="text-align:center;color:blue;">
										<?php $pos=$periode."-12";?>
										@if($bulan_skr>=$pos)
											<a href="/PerformanceDetail/{{$dt->idperformance}}/{{$periode}}/4/{{$dt->employee_name}}/{{$created_by}}">{{$T4}}</a>
										@endif
										<!-- <a href="/PerformanceDetail/{{$dt->idperformance}}/{{$periode}}/4/{{$dt->employee_name}}/{{$created_by}}">{{$T4}}</a> -->
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
									<td>
										<div class="pull-left">
											@if($dt->grade>0&&$periode>=2025)
												<a href="/PerformancePreview/{{$dt->idperformance}}" target="_blank"><button class="btn btn-xs btn-primary"><i class="fa fa-print"></i></button></a>
												<button class="btn btn-xs btn-info info-modal" data-nama="{{$dt->employee_name}}" data-idperformance="{{$dt->idperformance}}" data-target="{{$dt->target}}"><i class="fa fa-folder-o"></i></button>
											@endif
										</div>
										<div class="pull-right">
											@if($dt->grade>0)
												<!-- <form action="/PerformanceRecap" method="post">
													<input type="hidden" name="idperformance" value="{{$dt->idperformance}}">
													<input type="hidden" name="periode" value="{{$periode}}">
													<input type="hidden" name="employee_name" value="{{$dt->employee_name}}">
													{{ csrf_field() }}
													<button type="submit" class="btn btn-xs btn-success"><i class="fa fa-edit"></i></button>
												</form> -->
												<a href="/PerformanceRecap2/{{$dt->idperformance}}/{{$periode}}/{{$dt->employee_name}}"><button class="btn btn-xs btn-success"><i class="fa fa-edit"></i></button></a>
											@endif
										</div>
									</td>
									<td>
										<div class="pull-left">
											<?php 
												if($dt->status_penilai_1==1)echo "<i class='fa fa-check-square-o'></i>";
												else echo "<i class='fa fa-square-o'></i>";
												echo "&nbsp;";
												if($dt->status_penilai_2==1)echo "<i class='fa fa-check-square-o'></i>";
												else echo "<i class='fa fa-square-o'></i>";
											?>
										</div>
										<div class="pull-right">
											@if($dt->leader_name==strtoupper($saya))
												<i class="fa fa-user" style="color:blue"></i><i style="color:white";>..</i>
											@elseif($id_user==$dt->id_leader2)
												<i class="fa fa-user"></i><i style="color:white";>.</i>
											@endif
										</div>

									</td>
								</tr>
							@endif
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
		<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->


@endsection
@section('Modals')
    <div class="modal fade" id="modal-info">
		<div class="modal-dialog box box-primary" style="width:600px;">
			<div class="modal-content">
				<form>
					<input type="hidden" id="id_performance">
					{{ csrf_field() }}
					<div class="modal-body">
						<div class="box box-info box-solid" style="border:0px;">
							<div class="box-header" style="padding:5px 10px;font-size:20px;">
								<h3 class="box-title"><label id="detail-info">Penilaian Kinerja</label></h3>
								<div class="pull-right">
									<button type="button" class="btn btn-warning pull-right" id="simpan">Save</button>&nbsp;
									<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>&nbsp;
								</div>

							</div>
							<div class="box-body">
								<div class="form-group">
									<label>Target Tahunan</label>
									<textarea class="form-control" id="target" rows="2" placeholder="Enter ..."></textarea>
								</div>
								<div class="form-group">
									<label>Kendalan Bulanan</label>
									<select class="form-control" id="bulan">
										<option value=""></option>
										@for($i=1;$i<=12;$i++)
											<?php
												if(strlen($i)==1)$j='0'.$i;
												else $j=$i;
												$bln=date('F',strtotime('2024-'.$j.'-01'));
											?>
											<option value="{{$i}}">{{$bln}}</option>
										@endfor
									</select>
								</div>
								<div class="form-group">
									<label>Detail Kendala</label>
									<textarea class="form-control" id="kendala" rows="2" placeholder="Enter ..."></textarea>
								</div>
								<div class="form-group">
									<label>Upaya/Antisipasi</label>
									<textarea class="form-control" id="upaya" rows="2" placeholder="Enter ..."></textarea>
								</div>
								<div id="listkendala"></div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

@endsection
@section('Scripts')
	<!-- page script Tabel-->
	<script>
		$('body').on("change","#periode",function(){
			var periode=document.getElementById('periode').value;
			window.location.href="/Performance/"+periode;
		});

	</script>
	<script>
	$(function () {
		$('#table1').DataTable({
		'paging'      : true,
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		//"pageLength"  : 25,
		'autoWidth'   : false
		})
		$('#table2').DataTable({
		'paging'      : false,
		'lengthChange': false,
		'searching'   : false,
		'ordering'    : false,
		'info'        : false,
		'autoWidth'   : false
		})
	})
	</script>
	<!-- page script alert-->
	<script>
		$(document).ready(function() {
		  var table = $('#tables').DataTable({
			'paging'      : true,
			'lengthChange': false,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : true,
			"lengthMenu"  : [[5,10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
			'scrollX': true,
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
	<script>
		$(document).on('click', '.info-modal', function() {
			var judul=$(this).data('nama');
			var idperformance=$(this).data('idperformance');
			document.getElementById("target").value =$(this).data('target');
			$('#id_performance').val(idperformance);
			document.getElementById("detail-info").textContent = judul;

			$.ajaxSetup({
                type: "POST",
                url: "/PerformanceInfo",
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                data: {
                    idperformance: idperformance,
                },
                success: function(respond) {
                    $('body,html').animate({
                        scrollTop: 0
                    }, 800);
                    $("#listkendala").html(respond);
                }
            })
			$('#modal-info').modal('show');
		});
		$('.box-header').on('click', '#simpan', function() {
			var idperformance=$('#id_performance').val();
			var target=$('#target').val();
			var bulan=$('#bulan').val();
			var kendala=$('#kendala').val();
			var upaya=$('#upaya').val();
			$.ajaxSetup({
				type:"POST",
				url: "/PerformanceInfo/Save",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			
			$.ajax({
				data:{idperformance:idperformance,target:target,bulan:bulan,kendala:kendala,upaya:upaya},
				success: function(respond){
					if(respond=="Sukses"){
						location.reload();
					}else{
						alert(respond);
					}
				}
			})
		});
		$(document).on('click', '.delete', function() {
			var idkendala=$(this).data('idkendala');
			$.ajaxSetup({
				type:"POST",
				url: "/PerformanceInfo/Delete",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			
			$.ajax({
				data:{idkendala:idkendala},
				success: function(respond){
					if(respond=="Sukses"){
						location.reload();
					}else{
						alert(respond);
					}
				}
			})
		});
	</script>
  @endsection
