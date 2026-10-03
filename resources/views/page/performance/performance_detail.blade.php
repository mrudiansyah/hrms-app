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
				{{$employee_name}}
				<div class="pull-right">
					<button type="button" class="btn btn-default" onclick="window.location.href='/Performance/{{$periode}}'">Back</button>
					@if($status_leader==1)
						<?php if($status_ttd==1){?>
							<button type="button" class="btn btn-danger delete-modal" data-idperformance="{{$id_performance}}">Roll Back Approval</button>
						<?php }else{?>
							@if($status_leader==1)
								<button type="button" class="btn btn-warning reset-modal" data-idperformance="{{$id_performance}}" data-triwulan="{{$triwulan}}">Reset Performance</button>
							@endif
							@if($created_by==1||$created_by==2)
								<button type="button" class="btn btn-info" id="copyData" data-idperformance="{{$id_performance}}" data-triwulan="{{$triwulan}}">Copy from Previous</button>
							@endif
							<button type="button" class="btn btn-primary" id="simpanData" data-idperformance="{{$id_performance}}" data-triwulan="{{$triwulan}}" data-periode="{{$periode}}">Save Performance</button>
						<?php }?>
					@endif
				</div>
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-xs-12">
			<div class="box box-primary box-solid">
				<div class="box-header">
					<h3 class="box-title">Penilaian Kinerja {{$triwulan_text}}</h3>

					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body">
				<table id="tables" class="table table-bordered">
					<thead>
						<tr>
							<th style="width:5px;">No</th>
							<th style="width:150px;">Aspek</th>
							<th style="width:50px;">Nilai</th>
							<th style="width:50px;">Grade</th>
							<th>Keterangan</th>
							<th style="width:50px;">
								<label id="copyData1" data-idperformance="{{$id_performance}}" data-triwulan="{{$triwulan}}">Nilai2</label>
							</th>
							<th style="width:50px;">Grade2</th>
						</tr>
					</thead>
					<tbody>
						<?php $no=0;$item='';$sum=0;$sum2=0;?>
						@foreach($tb_performance_detail as $dt)
							@if($item!=$dt->id_aspek)
							<tr>
								<td><?php $no++;echo $no;?></td>
								<td>{{$dt->nama_aspek}}</td>
								<td><!-- deleted #created_by=1 -->
									@if($status_ttd==0&&$status_leader==1)
										<b style="color:blue;" class="edit-modal" data-iddetail="{{$dt->id}}" data-idvalue="{{$dt->id_value}}" data-idaspek="{{$dt->id_aspek}}" data-aspek="{{$dt->nama_aspek}}">{{number_format((float) $dt->value, 2)}}</b>
									@else
										{{number_format((float) $dt->value, 2)}}
									@endif
									<?php $sum=$sum+$dt->value;?>
								</td>
								<td>{{$dt->grade}}</td>
								<td>
									<?php
										$teks = $dt->kriteria;
										$jumlah = substr_count($teks, '#');
										$array = explode("#", $teks);
										if($jumlah>0){
											for($i=0;$i<=$jumlah;$i++){
												echo str_replace("#", "", $array[$i])."<br>";
											}
										}else{
											echo $teks;
										}

									?>
								<td>
									@if($status_ttd==0&&$status_leader==1&&$created_by==2)
										<b style="color:blue;" class="edit-modal" data-iddetail="{{$dt->id2}}" data-idvalue="{{$dt->id_value2}}" data-idaspek="{{$dt->id_aspek}}" data-aspek="{{$dt->nama_aspek}}">{{number_format((float) $dt->value2, 2)}}</b>
									@else
										{{number_format((float) $dt->value2, 2)}}
									@endif
									<?php $sum2=$sum2+$dt->value2;?>
								</td>
								<td>{{$dt->grade2}}</td>
								</td>
							</tr>
							@endif
							<?php $item=$dt->id_aspek;?>
						@endforeach
					</tbody>
					<tfoot>
						<tr>
							<td colspan="2">
								<?php
									//$pesan="* Info : Average Kehadiran ".$data['pr_ave']."% , Ketepatan ".$data['hr_ave']."%;";
									//echo $pesan;
									//echo " Detail silahkan check <a href='/AbsensiRate/0/0' target='_blank'>Absensi Rate</a>";
								?>
							</td>
							<td>
								{{number_format((float) $sum, 2)}}
							</td>
							<td colspan="2">&nbsp;</td>
							<td>
								{{number_format((float) $sum2, 2)}}
							</td>
							<td>&nbsp;</td>
						</tr>
					</tfoot>
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
    <div class="modal fade" id="modal-edit">
		<div class="modal-dialog box box-danger" style="width:800px;">
			<div class="modal-content">
				<form>
					<input type="hidden" id="id_perform_dtl">
					<input type="hidden" id="id_value">
					<input type="hidden" id="created_by" value="{{$created_by}}">
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
									<?php $kriteria='';?>
									@foreach($tb_perform_value as $dt)
										<?php $idaspek='aspek'.$dt->id_aspek;?> 
										<div class="myClass {{$idaspek}}">
											<?php 
												if($kriteria!=$dt->kriteria){
													if($dt->grade=='D')$warna='bg-red';
													if($dt->grade=='C')$warna='bg-yellow';
													if($dt->grade=='B')$warna='bg-primary';
													if($dt->grade=='A')$warna='bg-green';
													echo "<small class='label ".$warna."'>".$dt->grade."</small><br>";
													$teks = $dt->kriteria;
													$jumlah = substr_count($teks, '#');
													$array = explode("#", $teks);
													if($jumlah>0){
														for($i=0;$i<=$jumlah;$i++){
															echo str_replace("#", "", $array[$i])."<br>";
														}
													}else{
														echo $teks;
													}
													//echo $dt->kriteria;
												}
												$kriteria=$dt->kriteria;
											?>
											<?php $status='';?>
											 @if($dt->id_aspek==11||$dt->id_aspek==19||$dt->id_aspek==31)
											 	<?php
													if($data['pr_ave']>=$dt->pr_0&&$data['pr_ave']<=$dt->pr_1&&$data['hr_ave']>=$dt->hr_0&&$data['hr_ave']<=$dt->hr_1){
														$status=' checked';
													}
												?>
											 @endif
											<div class="radio">
												<label>
												<input class="nilai" type="radio" id="aspek{{$dt->id_aspek}}" name="nilai" value="{{$dt->id}}" data-nilai="{{$dt->id}}"{{$status}}>
												{{number_format((float) $dt->nilai, 2)}}
												
												</label>
											</div>

										</div>
									@endforeach
								</div>
							</div>
						</div>
					</div>
				</form>		
			</div>
		</div>
    </div>
	<div class="modal fade" id="modal-delete">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Delete Confirmation</h4>
				</div>
				<div class="modal-body">
					Jika dilanjut, maka atasan Anda harus TTD ulang. Yakin akan mereset approval ?
					<input type="hidden" id="delid1">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left delete" data-dismiss="modal">Yakin</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-reset">
		<div class="modal-dialog box box-warning" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Reset Confirmation</h4>
				</div>
				<div class="modal-body">
					Yakin akan mereset penilaian periode ini ?
					<input type="hidden" id="resetid1">
					<input type="hidden" id="resetid2">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-warning pull-left" id="resetPerformance" data-dismiss="modal">Yakin</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>

@endsection
@section('Scripts')
	<!-- page script Tabel-->
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
			'paging'      : true,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			'autoWidth'   : true
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
			"pageLength"  : 15,
			'autoWidth'   : false,
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
		<script>
			$(document).on('click', '.edit-modal', function() {
				var iddetail=$(this).data('iddetail');
				var idvalue=$(this).data('idvalue');
				var idaspek=$(this).data('idaspek');
				var aspek=$(this).data('aspek');

				$('#id_perform_dtl').val(iddetail);

				document.querySelectorAll(".myClass").forEach(element => {
					element.style.display = "none";
				});
				document.querySelectorAll(".aspek"+idaspek).forEach(element => {
					element.style.display = "block";
				});
				var $radios = $('.myClass.aspek'+idaspek+' input.nilai');
				if (idvalue) {
					$('input.nilai').prop('checked', false);
					$radios.filter(function() {
						return String($(this).data('nilai')) === String(idvalue);
					}).prop('checked', true);
				}
				var $selectedRadio = $radios.filter(':checked').first();
				$('#id_value').val($selectedRadio.length ? $selectedRadio.data('nilai') : '');
				document.getElementById("judul").textContent = aspek;
				$('#modal-edit').modal('show');
			});
		</script>
	<script type="text/javascript">
		$(document).on('click', '.nilai', function() {
			var id_value=$(this).data('nilai');
			$('#id_value').val(id_value);
		});

		$('.box-header').on('click', '#simpan', function() {
			var id_perform_dtl=$('#id_perform_dtl').val();
			var id_value=$('#id_value').val();
			var nilai = document.querySelector('input[name="nilai"]').value;
			var created_by=$('#created_by').val();
			$.ajaxSetup({
				type:"POST",
				url: "/PerformanceSubmit",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			
			$.ajax({
				data:{id_perform_dtl:id_perform_dtl,id_value:id_value,created_by:created_by},
				success: function(respond){
					// if(respond=="Sukses"){
					// 	location.reload();
					// }else{
					// 	alert(respond);
					// }
					location.reload();
				}
			})
		});
		$(document).on('click', '#simpanData', function() {
			var id_performance=$(this).data('idperformance');
			var triwulan=$(this).data('triwulan');
			var periode=$(this).data('periode')
			$.ajaxSetup({
				type:"POST",
				url: "/PerformanceSave",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			
			$.ajax({
				data:{id_performance:id_performance,triwulan:triwulan},
				success: function(respond){
					if(respond=="No Action"){
						alert(respond);
					}else{
						window.location.href="/Performance/"+periode;
					}
				}
			})
		});
		$(document).on('click', '#copyData', function() {
			var id_performance=$(this).data('idperformance');
			var triwulan=$(this).data('triwulan');
			var created_by=$('#created_by').val();
			$.ajaxSetup({
				type:"POST",
				url: "/PerformanceCopy",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			
			$.ajax({
				data:{id_performance:id_performance,triwulan:triwulan,created_by:created_by},
				success: function(respond){
					// if(respond=="Sukses"){
					// 	location.reload();
					// }else{
					// 	alert(respond);
					// }
					location.reload();
				}
			})
		});
		$(document).on('click', '#copyData1', function() {
			var id_performance=$(this).data('idperformance');
			var triwulan=$(this).data('triwulan');
			var created_by=$('#created_by').val();
			$.ajaxSetup({
				type:"POST",
				url: "/PerformanceCopy1",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			
			$.ajax({
				data:{id_performance:id_performance,triwulan:triwulan,created_by:created_by},
				success: function(respond){
					if(respond=="Sukses"){
						location.reload();
					}else{
						alert(respond);
					}
					location.reload();
				}
			})
		});
	</script>
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.delete-modal', function() {
			$('#delid1').val($(this).data('idperformance'));
			$('#delname1').text($(this).data('idperformance'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var id_performance=$('#delid1').val();
			$.ajaxSetup({
				type:"POST",
				url: "/Performance/Rollback",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			
			$.ajax({
				data:{id_performance:id_performance},
				success: function(respond){
					// if(respond=="Sukses"){
					// 	location.reload();
					// }else{
					// 	alert(respond);
					// }
					location.reload();
				}
			})
		});
		$(document).on('click', '.reset-modal', function() {
			$('#resetid1').val($(this).data('idperformance'));
			$('#resetid2').val($(this).data('triwulan'));
			$('#resetname1').text($(this).data('idperformance'));
			$('#modal-reset').modal('show');
		});
		$(document).on('click', '#resetPerformance', function() {
			var id_performance=$('#resetid1').val();
			var triwulan=$('#resetid2').val();
			var created_by=$('#created_by').val();
			$.ajaxSetup({
				type:"POST",
				url: "/Performance/TriwulanReset",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			
			$.ajax({
				data:{id_performance:id_performance,triwulan:triwulan,created_by:created_by},
				success: function(respond){
					// if(respond=="Sukses"){
					// 	location.reload();
					// }else{
					// 	alert(respond);
					// }
					location.reload();
				}
			})
		});

	</script>
  @endsection
