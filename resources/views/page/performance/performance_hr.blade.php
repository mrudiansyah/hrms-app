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
				Performance Rank
				<div class="pull-right">
					<div class="col-xs-3" style="padding:3px;">
						<input type="number" id="periode" class="form-control kelompok" value="{{$periode}}">
						<input type="hidden" id="status" value="{{$status}}">
					</div>
					<div class="col-xs-4" style="padding:3px;">
						<select class="form-control kelompok" id="dept">
							<option value="{{$dept}}">{{$dept}}</option>
							@foreach($tb_department as $dt)
								@if($dt->dept_code!=$dept)
									<option value="{{$dt->dept_code}}">{{$dt->dept_code}}</option>
								@endif
							@endforeach
							<option value="0">All Dept</option>
						</select>
					</div>
					<div class="col-xs-5" style="padding:3px;">
						<select class="form-control kelompok" id="level">
							<option value="{{$nm_level}}">{{$nm_level}}</option>
							@foreach($tb_position as $dt)
								<option value="{{$dt->nama_level}}">{{$dt->nama_level}}</option>
							@endforeach
							<option value="0">All Level</option>
						</select>
					</div>
				</div>
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-xs-12">
			<div class="box box-primary" style="background:#FFF;">
				<div class="header" style="padding:10px;">
					<div class="pull-right">
						@if($status==1)
							<a href="/PerformancesRemining/{{$periode}}/{{$dept}}/{{$nm_level}}" class="btn btn-md btn-default"><i class="fa fa-user"></i> Remining</a>
							<a href="/Performance/Confirm/{{$periode}}" class="btn btn-md btn-default"><i class="fa fa-edit"></i> Confirm</a>
							<button id="cutoffUpdate" class="btn btn-md btn-warning"><i class="fa fa-refresh"></i> Update Cut off</button>
							<button id="leaderUpdate" class="btn btn-md btn-info"><i class="fa fa-refresh"></i> Update Penilai</button>
							<a href="/Performances/Progress/{{$periode}}/0/0" class="btn btn-md btn-primary"><i class="fa fa-pie-chart"></i> Progress</a>
							<a href="/Performances/Distribution/{{$periode}}/0/0" class="btn btn-md btn-success"><i class="fa fa-bar-chart"></i> Distribution</a>
						@else
							<a href="/Performances/{{$periode}}/{{$dept}}/{{$nm_level}}" class="btn btn-md btn-default"><i class="fa fa-home"></i> Back</a>
						@endif
					</div>
				</div>
				<div class="box-body">
				<table id="tables" class="table table-bordered">
					<thead>
						<tr>
							<th>No</th>
							<th>Rank</th>
							<th>Rank (BOD)</th>
							<th>NIK</th>
							<th>Nama Karyawan</th>
							<th>Dept</th>
							<th>Jabatan</th>
							<th>Atasan 1</th>
							<th>Atasan 2</th>
							<th style="width:100px;">Masa_Kerja</th>
							<th>Nilai</th>
							<th>Grade</th>
							<th>Detail</th>
						</tr>
					</thead>
					<tbody>
						<?php $no=0;$beda=0;$r1=0;$r2=0;$r3=0;$r4=0;$r5=0;?>
						<?php $beda_bod=0;$r1_bod=0;$r2_bod=0;$r3_bod=0;$r4_bod=0;$r5_bod=0;?>
						@foreach($tb_employee as $dt)
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
										if($dt->ranked=='1'){$warna="bg-green";$r1++;$equivalen='A+';}
										else if($dt->ranked=='2'){$warna="bg-primary";$r2++;$equivalen='A';}
										else if($dt->ranked=='3'){$warna="bg-gray";$r3++;$equivalen='B+';}
										else if($dt->ranked=='4'){$warna="bg-yellow";$r4++;$equivalen='B';}
										else {$warna="bg-red";$r5++;$equivalen='C+';}
										?>
									<div class="pull-left">
										<i class='label {{$warna}}'>
											<b ><?php if($dt->ranked=='')echo "0";else echo $equivalen;?></b>
										</i>
									</div>
									@endif
									<div class="pull-right">
									<?php
										// echo "Sort: ";
										// if($no<=$level[1]){echo "1";}
										// else if($no<=$level[2]){echo "2";}
										// else if($no<=$level[3]){echo "3";}
										// else if($no<=$level[4]){echo "4";}
										// else {$warna="bg-red"; echo "5";}
									?>
									</div>
								</td>
								<td>
									@if($dt->average>0)
									<?php 
										$warna_bod='';
										$equivalen_bod='';
										if($dt->ranked_bod=='1'){$warna_bod="bg-green";$r1_bod++;$equivalen_bod='A+';}
										else if($dt->ranked_bod=='2'){$warna_bod="bg-primary";$r2_bod++;$equivalen_bod='A';}
										else if($dt->ranked_bod=='3'){$warna_bod="bg-gray";$r3_bod++;$equivalen_bod='B+';}
										else if($dt->ranked_bod=='4'){$warna_bod="bg-yellow";$r4_bod++;$equivalen_bod='B';}
										else {$warna_bod="bg-red";$r5_bod++;$equivalen_bod='C+';}
										?>
									<div class="pull-left">
										<i class='label {{$warna_bod}}'>
											<b ><?php if($dt->ranked_bod=='')echo "0";else echo $equivalen_bod;?></b>
										</i>
									</div>
									@endif
								</td>
								<td>{{$dt->NIK}}</td>
								<td>{{$dt->employee_name}}</td>
								<td>{{$dt->dept_code}}</td>
								<td>{{$dt->position_name}}.</td>
								<td>
									<?php if($dt->atasan_langsung!=$dt->leader_name){echo "<i class='fa fa-question pull-right' title='".$dt->atasan_langsung."'></i> ";$beda++;}?>
									{{$dt->leader_name}}
								</td>
								<td>
									{{$dt->atasan2}}
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
								<td>
									<div class="pull-left">
										@if($dt->grade>0)
											<a href="/PerformancePreview/{{$dt->idperformance}}" target="_blank"><button class="btn btn-xs btn-primary"><i class="fa fa-print"></i></button></a>
										@endif
									</div>
									<div class="pull-right">
										<?php 
											if($dt->status_penilai_1==1)echo "<i class='fa fa-check-square-o'></i>";
											else echo "<i class='fa fa-square-o'></i>";
											echo "&nbsp;";
											if($dt->status_penilai_2==1)echo "<i class='fa fa-check-square-o'></i>";
											else echo "<i class='fa fa-square-o'></i>";
										?>
									</div>
							</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<input type="hidden" id="beda" value="{{$beda}}">
				@if($beda>0)
					<b>Note:</b> Terdapat {{$beda}} member yang mengalami perubahan direct leader, untuk update direct leader di Form penlianai, silahkan tekan tombol Update penilai di bagian atas.<br>
				@endif
				@if($status==1&&$nm_level!=0&&$no>0)
					<b>Summary:</b><br>
					<?php
						$p1=number_format($r1/$no*100,0);
						$p2=number_format($r2/$no*100,0);
						$p3=number_format($r3/$no*100,0);
						$p4=number_format($r4/$no*100,0);
						$p5=number_format($r5/$no*100,0);
						$s1=number_format($no*0.1,2);
						$s2=number_format($no*0.2,2);
						$s3=number_format($no*0.4,2);
						$s4=number_format($no*0.2,2);
						$s5=number_format($no*0.1,2);
					?>
					<ul>
						<li>Rank 1 : {{$p1}}% {{$r1}} (Std: {{$s1}})</li>
						<li>Rank 2 : {{$p2}}% {{$r2}} (Std: {{$s2}})</li>
						<li>Rank 3 : {{$p3}}% {{$r3}} (Std: {{$s3}})</li>
						<li>Rank 4 : {{$p4}}% {{$r4}} (Std: {{$s4}})</li>
						<li>Rank 5 : {{$p5}}% {{$r5}} (Std: {{$s5}})</li>
					</ul>
				@endif
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
    <div class="modal fade" id="modal-cutoff">
		<div class="modal-dialog box box-primary" style="width:400px;">
			<div class="modal-content">
				<form>
					<input type="hidden" id="id_performance">
					{{ csrf_field() }}
					<div class="modal-body">
						<div class="box box-info box-solid" style="border:0px;">
							<div class="box-header" style="padding:5px 10px;font-size:20px;">
								<h3 class="box-title"><label id="detail-info">CUT OFF DATE</label></h3>
								<div class="pull-right">
									<button type="button" class="btn btn-warning pull-right" id="simpan" data-dismiss="modal">Refresh</button>&nbsp;
									<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>&nbsp;
								</div>

							</div>
							<div class="box-body">
								<div class="form-group">
									<input type="date" class="form-control" id="cutoff" value="{{$today}}">
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
	<!-- page script Tabel-->
	<script>
		$(document).ready(function() {
			var beda=document.getElementById('beda').value;
			const myButton = document.getElementById("leaderUpdate");
			if(beda>0){
				myButton.disabled = false;
			}else{
				myButton.disabled = true;
			}
		});
		$('body').on("change",".kelompok",function(){
			var periode=document.getElementById('periode').value;
			var dept=document.getElementById('dept').value;
			var level=document.getElementById('level').value;
			var status=document.getElementById('status').value;
			if(status==1){
				window.location.href="/Performances/"+periode+"/"+dept+"/"+level;
			}else{
				window.location.href="/PerformancesRemining/"+periode+"/"+dept+"/"+level;
			}
		});
		$(document).on('click','#cutoffUpdate',function(){
			$('#modal-cutoff').modal('show');
		})
		$('.box-header').on('click', '#simpan', function() {
			var periode=$('#periode').val();
			var cutoff=$('#cutoff').val();
			$.ajaxSetup({
				type:"POST",
				url: "/Performance/CutOffUpdate",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			
			$.ajax({
				data:{periode:periode,cutoff:cutoff},
				success: function(respond){
					if(respond=="Sukses"){
						location.reload();
					}else{
						alert(respond);
					}
				}
			})
		});
		$(document).on('click', '#leaderUpdate', function() {
			var periode=document.getElementById('periode').value;
			var dept=document.getElementById('dept').value;
			var level=document.getElementById('level').value;
			$.ajaxSetup({
                type: "POST",
                url: "/Performance/RankLeaderUpdate",
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                data: {
                    periode: periode,
                },
                success: function(respond) {
					if(respond=='Sukses'){
						window.location.href="/Performances/"+periode+"/"+dept+"/"+level;
					}else{
						alert(respond);
					}
				}
            })
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
			"pageLength"  : 50,
			'autoWidth'   : true,
			"lengthMenu"  : [[5,10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
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
  @endsection
