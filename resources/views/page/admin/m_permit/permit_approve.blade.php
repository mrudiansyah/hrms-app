@extends('layouts/admin')
@section('Contents')
   <!-- Contents -->
   <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
		#tables th {
		border-top: 1px solid #999;
		border-bottom: 1px solid #999;
		background-color: #2F4F4F;
		color: white;
		}	
        .table1 tr:hover {
		  cursor:pointer;
        }
		#table2 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table3 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		vertical-align:middle;
		text-align:left;
		}	
		#table4 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table5 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
    </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Employee Permit
				<small>approval form</small>
			</h1>
			<ol class="breadcrumb">
				<li>
				<a href="#">
					<i class="fa fa-calendar"></i> 
					<?php 
						date_default_timezone_set("Asia/Jakarta");
						echo date('l, d M Y H:i');
					?>
				</a>
				</li>
			</ol>
		</section>

		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-xs-12">
					<div class="box box-primary" style="background:#FFF;">
						<div class="box-header">
							<i class="fa fa-list"></i>
							<h3 class="box-title" id="judul">Approval Permit</h3>
							<div class="pull-right">
								<a class="btn btn-app table2">
									<?php if(isset($qty_permit_new)&&$qty_permit_new>0)echo "<span class='badge bg-yellow'>".$qty_permit_new."</span>";?>
									<i class="fa fa-file-o"></i> New Permit
								</a>
								<!-- <a class="btn btn-app table3">
									<i class="fa fa-check-square-o"></i> Approved
								</a>
								<a class="btn btn-app table4">
									<i class="fa fa-trash"></i> Abort
								</a> -->
							</div>
						</div>
						<div class="box-body" style="min-height:200px;overflow-x:scroll;">
							<div id="tabel2">
								<table id="table2" class="table table-striped" border="0">
									<thead>
										<tr>
											<th style="width:30px;">NO</th>
											<th>DOC.DATE</th>
											<th>FORM</th>
											<th>NIK</th>
											<th>EMPLOYEE NAME</th>
											<th>DATE</th>
											<th>START</th>
											<th>FINISH</th>
											<th>KEPERLUAN</th>
											<th>APPROVED</th>
										</tr>
									</thead>
									<tbody>
									<?php $no=0;?>
										@foreach($tb_permit_new as $dt)
										<?php
											$batas='+'.$limit_day.' days';
											if($limit_day==0){
												$tgl=date('Y-m-d',strtotime($dt->apply_date)).' 23:59:59';
												$date_limit=date('Y-m-d H:i:s',strtotime($tgl));
											}else{
												$date_limit= date('Y-m-d H:i:s',strtotime($batas,strtotime($dt->apply_date)));
											} 
											$now=date('Y-m-d H;i:s');
											if($date_limit<$now&&$limit_approval>0)$status_limit=1;
											else $status_limit=0;
											if($dt->exception==1)$status_limit=0;
										?>
										<tr>
											<td title="{{$date_limit}}"><?php $no++;echo $no;?></td>
											<td>{{$dt->doc_date}}</td>
											<td><?php echo ucfirst($dt->category);?></td>
											<td>{{$dt->NIK}}</td>
											<td>{{$dt->employee_name}}</td>
											<td><?php echo date('d-M-Y',strtotime($dt->apply_date));?></td>
											<td><?php if($dt->category!='A')echo date('H:i',strtotime($dt->start_izin));;?></td>
											<td><?php if($dt->finish_izin!=$dt->start_izin&&$dt->category!='A'&&$dt->category!='B')echo date('H:i',strtotime($dt->finish_izin));?></td>
											<td>
												{{$dt->keperluan}} {{$dt->keluhan}} {{$dt->berobat_ke}}
											</td>
											<td>
												{{$dt->nama_atasan}}
												<div class="pull-right">
													<a href="/Permit/Preview/{{$dt->id}}" title="Cetak" type="button" class="btn btn-info btn-xs" target="_balnk"><i class="fa fa-print"></i></a>
													@if($status_limit==0)
														<button title="Approve" type="button" class="approve-modal btn btn-success btn-xs" data-approveid="{{$dt->id}}" data-approvename="{{$dt->employee_name}}"><i class="fa fa-check-square-o"></i></button>
														<button title="Refuse" type="button" class="refuse-modal btn btn-danger btn-xs" data-refuseid="{{$dt->id}}" data-refusename="{{$dt->employee_name}}"><i class="fa fa-close"></i></button>
													@else	
														<i class="btn btn-danger btn-xs" title="Please Inform HR to open">OverDue</i>
													@endif
												</div>
											</td>
										</tr>
										@endforeach
									<tbody>
								</table>
							</div>
							<div id="tabel3">
								<table id="table3" class="table table-striped" border="0">
									<thead>
										<tr>
											<th style="width:30px;">NO</th>
											<th>DOC.DATE</th>
											<th>FORM</th>
											<th>NIK</th>
											<th>EMPLOYEE NAME</th>
											<th>DATE</th>
											<th>START</th>
											<th>FINISH</th>
											<th>KEPERLUAN</th>
											<th>APPROVED</th>
										</tr>
									</thead>
									<tbody>
									<?php $no=0;?>
										@foreach($tb_permit_approve as $dt)
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td>{{$dt->doc_date}}</td>
											<td><?php echo ucfirst($dt->category);?></td>
											<td>{{$dt->NIK}}</td>
											<td>{{$dt->employee_name}}</td>
											<td><?php echo date('d-M-Y',strtotime($dt->apply_date));?></td>
											<td><?php if($dt->category!='A')echo date('H:i',strtotime($dt->start_izin));;?></td>
											<td><?php if($dt->finish_izin!=$dt->start_izin&&$dt->category!='A'&&$dt->category!='B')echo date('H:i',strtotime($dt->finish_izin));?></td>
											<td>
												{{$dt->keperluan}} {{$dt->keluhan}} {{$dt->berobat_ke}}
											</td>
											<td>
												{{$dt->nama_atasan}}
											</td>
										</tr>
										@endforeach
									<tbody>
								</table>
							</div>
							<div id="tabel4">
								<table id="table4" class="table table-striped" border="0">
									<thead>
										<tr>
											<th style="width:30px;">NO</th>
											<th>DOC.DATE</th>
											<th>FORM</th>
											<th>NIK</th>
											<th>EMPLOYEE NAME</th>
											<th>DATE</th>
											<th>START</th>
											<th>FINISH</th>
											<th>KEPERLUAN</th>
											<th>APPROVED</th>
										</tr>
									</thead>
									<tbody>
									<?php $no=0;?>
										@foreach($tb_permit_refuse as $dt)
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td>{{$dt->doc_date}}</td>
											<td><?php echo ucfirst($dt->category);?></td>
											<td>{{$dt->NIK}}</td>
											<td>{{$dt->employee_name}}</td>
											<td><?php echo date('d-M-Y',strtotime($dt->apply_date));?></td>
											<td><?php if($dt->category!='A')echo date('H:i',strtotime($dt->start_izin));;?></td>
											<td><?php if($dt->finish_izin!=$dt->start_izin&&$dt->category!='A'&&$dt->category!='B')echo date('H:i',strtotime($dt->finish_izin));?></td>
											<td>
												{{$dt->keperluan}} {{$dt->keluhan}} {{$dt->berobat_ke}}
											</td>
											<td>
												{{$dt->nama_atasan}}
											</td>
										</tr>
										@endforeach
									<tbody>
								</table>
							</div>
						</div>
						<!-- /.box-body -->
					</div>

				</div>
			</div>
			<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->
	<!-- Modal Approve -->
	<div class="modal fade" id="modal-approve">
		<div class="modal-dialog box box-success" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Approve Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Approve : <b id="approvename"></b> ?
					<input type="hidden" id="approveid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success pull-left approve" data-dismiss="modal">Yes, Approve</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- Modal Refuse -->
	<div class="modal fade" id="modal-refuse">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Refuse Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Approve : <b id="refusename"></b> ?
					<input type="hidden" id="refuseid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left refuse" data-dismiss="modal">Yes, Refuse</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>


    @if ($message = Session::get('success'))
		<div class="alert alert-info alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-info"></i> Success Alert</h4>
			{{$message}}
		</div>
    @endif

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
		$(function () {
			$('#table4').DataTable({
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
	<!-- Durasi Alert --->
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<!-- Approve -->
	<script type="text/javascript">
		// Approve Data
		$(document).on('click', '.approve-modal', function() {
			$('#approveid').val($(this).data('approveid'));
			$('#approvename').text($(this).data('approvename'));
			$('#modal-approve').modal('show');
		});
		$('.modal-footer').on('click', '.approve', function() {
			var x=$('#approveid').val();
			window.location.href='/Permit/Approve/'+x+'/1';
		});
	</script>
	<!-- Refuse -->
	<script type="text/javascript">
		// Refuse Data
		$(document).on('click', '.refuse-modal', function() {
			$('#refuseid').val($(this).data('refuseid'));
			$('#refusename').text($(this).data('refusename'));
			$('#modal-refuse').modal('show');
		});
		$('.modal-footer').on('click', '.refuse', function() {
			var x=$('#refuseid').val();
			window.location.href='/Permit/Approve/'+x+'/2';
		});
	</script>
	<!--- on Load -->
	<script>
		$( document ).ready(function() {
			$("#tabel2").show();
			$("#tabel3").hide();
			$("#tabel4").hide();
			$("#judul").text('New Applied Permit');
			$(document).on('click', '.table2', function() {
				$("#tabel2").show(1000);
				$("#tabel3").hide(50);
				$("#tabel4").hide(50);
				$("#judul").text('New Applied Permit');
			});
			$(document).on('click', '.table3', function() {
				$("#tabel3").show(1000);
				$("#tabel2").hide(50);
				$("#tabel4").hide(50);
				$("#judul").text('Approved Permit');
			});
			$(document).on('click', '.table4', function() {
				$("#tabel4").show(1000);
				$("#tabel3").hide(50);
				$("#tabel2").hide(50);
				$("#judul").text('Refused Permit');
			});
		});
	</script>
	<script>
		$('body').on("change","#start",function(){
			var start=document.getElementById('start').value;
			var finish=document.getElementById('finish').value;
			window.location.href="/Permit/Approves/"+start+"/"+finish;
		});
		$('body').on("change","#finish",function(){
			var start=document.getElementById('start').value;
			var finish=document.getElementById('finish').value;
			window.location.href="/Permit/Approves/"+start+"/"+finish;
		});
	</script>


@endsection
