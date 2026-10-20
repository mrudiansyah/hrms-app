@extends('layouts/home')
@section('Contents')
	<meta name="csrf-token" content="{{ csrf_token() }}">

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-lg-6 col-md-12 col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-list"></i>
						<h3 class="box-title">My Training</h3>
						<div class="box-tools pull-right">
							&nbsp;
						</div>
					</div>
					<div class="box-body">
						<table id="tables" class="table">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th>Training</th>
									<th>Date</th>
									<th>Time</th>
									<th>Status Training</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								@foreach($tb_training_invitation as $dt)
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td>{{$dt->training_name}}</td>
									<td>{{$dt->tanggal}}</td>
									<td>
										<?php 
											if($dt->start!=''&&$dt->finish!=''){
												echo date('H:i',strtotime($dt->start));
												echo "~";
												echo date('H:i',strtotime($dt->finish));
											}
										?>
									</td>
									<td style="width:220px;">
										@if(!empty($dt->idparticipant))
											<span class="label label-success">Hadir</span>
											@if($dt->grade_status == 1)
												<span class="label label-success">Lulus</span>
												<a href="/Training/Actual/Participant/Certificate/{{ $dt->idparticipant }}"
													class="btn btn-info btn-xs" target="_blank" rel="noopener">
													<i class="fa fa-certificate"></i> Certified
												</a>
											@elseif($dt->post_test !== null && $dt->post_test !== '')
												<span class="label label-danger">Tidak Lulus</span>
											@else
												<span class="label label-default">Belum dinilai</span>
											@endif
										@else
											<span class="label label-default">Tidak Hadir</span>
										@endif
										@if($dt->id_test>0)
											<div class="pull-right">
												<a href="/Training/Schedule/{{$dt->id}}" title="Participant" type="button" class="participant btn btn-primary btn-xs"><i class="fa fa-folder-o"></i> Detail</a>
											</div>
										@endif
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->


@endsection
@section('Scripts')
	<!-- page script Tabel-->
	<script>
		$(function () {
			$('#table2').DataTable({
			'paging'      : true,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : false,
			})
		})
		$(function () {
			$('#table3').DataTable({
			'paging'      : true,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : false,
			})
		})
	</script>
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<script>
		document.addEventListener("contextmenu", function(e){
			e.preventDefault();
		}, false);
	</script>
@endsection
