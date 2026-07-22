@extends('layouts/admin')
@section('Contents')
   <!-- Contents -->
   	<style>
		#tablesx th {
		border-top: 1px solid #999;
		border-bottom: 1px solid #999;
		background-color: #2F4F4F;
		color: white;
		}	
        .table1 tr:hover {
		  cursor:pointer;
        }
		#tables th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#tables tbody tr:hover{
			cursor:pointer;
		}
		#table2 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table2 tbody tr:hover{
			cursor:pointer;
		}
		#table3 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table3 tbody tr:hover{
			cursor:pointer;
		}
		#table4 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table4 tbody tr:hover{
			cursor:default;
		}
    </style>
	<?php
		date_default_timezone_set("Asia/Bangkok");
		$Today=date('Y-m-d');
		$AWeek=date('Y-m-d',strtotime('+ 14 days',strtotime($Today)));
		$AMonth=date('Y-m-d',strtotime('+ 1 Months',strtotime($Today)));
	?>

	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				KSK List
				<small>konfirmasi status karyawan</small>
			</h1>
			<ol class="breadcrumb">
				<li>
				<a href="#">
					<i class="fa fa-calendar"></i> 
					<?php 
						date_default_timezone_set("Asia/Jakarta");
						echo date('l, d M Y H:i');
						$now=date('Y-m-d');
						
						$periode_sekarang=date('Y-m',strtotime('30 days',strtotime($now)));
					?>
				</a>
				</li>
			</ol>
		</section>
		<?php //$qty_total=0;?>
		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-user"></i>
						<h3 class="box-title" style="padding-bottom:25px;">{{$Judul}}</h3>
						<div class="box-tools pull-right">
							<a href='/Status/KSK/{{$periode}}'><button type="button" class="btn btn-default btn-xs"><i class="fa fa-backward"></i> &nbsp;Back</button></a>
							<?php if($periode>=$periode_sekarang){?>
								<a href='/Status/KSK/Refresh/{{$periode}}' id="bahaya"><button type="button" class="btn btn-info btn-xs"><i class="fa fa-refresh"></i> &nbsp;Generate/Reset</button></a>
							<?php }?>
							<a href='/Status/KSK/Distribute/{{$periode}}'><button type="button" class="btn btn-primary btn-xs" id="distribute"><i class="fa fa-upload"></i> &nbsp;Distribute</button></a>
							<a href='/Status/KSK/Detail/0/{{$periode}}'><button type="button" class="btn btn-default btn-xs" id="detail"><i class="fa fa-folder-o"></i> &nbsp;Detail</button></a>
						</div>
					</div>
					<div class="box-body" style="overflow-x:scroll;">
						<div class="box-header" style="padding-top:0px;padding-left:0px;">
							<div class="box-tools pull-left">
								<input type="month" class="form-control" id="periode" name="periode" value="{{$periode}}">
							</div>
							<div class="box-tools pull-right">
								<?php if($status_lock==1)echo "Status Locked"; else echo "Status UnLocked";?>&nbsp;
								<a href='/Status/KSK/Lock/{{$periode}}/{{$status_lock}}'><button type="button" class="btn btn-default btn-md"><?php if($status_lock==1)echo "<i class='fa fa-unlock'>"; else echo "<i class='fa fa-lock'>";?></i> </button></a>
							</div>
						</div>
						<table id="table2" class="table table-hover tabel2">
							<thead>
								<tr>
									<th>NO</th>
									<th>KSK NO</th>
									<th>DEPARTMENT</th>
									<th>APPROVAL_01</th>
									<th>APPROVAL_02</th>
									<th>APPROVAL_03</th>
									<th>APPROVAL_04</th>
									<th>APPROVAL_05</th>
									<th>QTY_EMPLOYEE</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;$lock_status=0;$performance_status=1;$quota_status=1;$qty_approval=0;?>
								@foreach($data as $dt)
								<tr>
									<td>{{ $dt['no'] }}</td>
									<td>{{ $dt['no_ksk'] }}</td>
									<td>{{ $dt['dept_code'] }}</td>
									
									<td>
										@if($dt['approval1'] > 0 && $dt['approval1_status'] == 0)
											<i class='fa fa-square-o'></i>
										@elseif($dt['approval1'] > 0 && $dt['approval1_status'] == 1)
											<i class='fa fa-check-square-o'></i>
										@endif
										{{ $dt['approvalname1'] }}
									</td>
									
									<td>
										@if($dt['approval2'] > 0 && $dt['approval2_status'] == 0)
											<i class='fa fa-square-o'></i>
										@elseif($dt['approval2'] > 0 && $dt['approval2_status'] == 1)
											<i class='fa fa-check-square-o'></i>
										@endif
										{{ $dt['approvalname2'] }}
									</td>
									
									<td>
										@if($dt['approval3'] > 0 && $dt['approval3_status'] == 0)
											<i class='fa fa-square-o'></i>
										@elseif($dt['approval3'] > 0 && $dt['approval3_status'] == 1)
											<i class='fa fa-check-square-o'></i>
										@endif
										{{ $dt['approvalname3'] }}
									</td>
									
									<td>
										@if($dt['approval4'] > 0 && $dt['approval4_status'] == 0)
											<i class='fa fa-square-o'></i>
										@elseif($dt['approval4'] > 0 && $dt['approval4_status'] == 1)
											<i class='fa fa-check-square-o'></i>
										@endif
										{{ $dt['approvalname4'] }}
									</td>
									
									<td>
										@if($dt['approval5'] > 0 && $dt['approval5_status'] == 0)
											<i class='fa fa-square-o'></i>
										@elseif($dt['approval5'] > 0 && $dt['approval5_status'] == 1)
											<i class='fa fa-check-square-o'></i>
										@endif
										{{ $dt['approvalname5'] }}
									</td>
									
									<td>
										@if($dt['approval6'] > 0 && $dt['approval6_status'] == 0)
											<i class='fa fa-square-o'></i>
										@elseif($dt['approval6'] > 0 && $dt['approval6_status'] == 1)
											<i class='fa fa-check-square-o'></i>
										@endif
										
										<div class="pull-right">
											@if($dt['quota_status'] == 1)
												<a href="/Status/KSK/Detail/{{ $dt['id'] }}/{{ $periode }}" 
												title="{{ $dt['qty_performance'] }}" 
												type="button" 
												class="btn btn-xs{{ $dt['warna'] }}">
													<i class="fa fa-folder-o"></i>
												</a>
											@endif
											
											<a href="/Employee/KSK/Print/{{ $dt['id'] }}" 
											type="button" 
											class="btn btn-info btn-xs" 
											target="_blank">
												<i class="fa fa-print"></i>
											</a>
											
											<button type="button" 
													class="btn btn-primary btn-xs update-modal" 
													data-deptid="{{ $dt['dept_id'] }}" 
													data-permanentp="{{ $dt['permanent_target'] }}" 
													data-contractp="{{ $dt['contract_target'] }}" 
													data-magangp="{{ $dt['magang_target'] }}" 
													data-permanenta="{{ $dt['permanent_actual'] }}" 
													data-contracta="{{ $dt['contract_actual'] }}" 
													data-maganga="{{ $dt['magang_actual'] }}">
												<i class="fa fa-edit"></i>
											</button>
										</div>
										{{ $dt['qty_total'] }}
									</td>
								</tr>
								@endforeach
							</tbody>
							<tfoot>

							</tfoot>
						</table>
						<input type="hidden" id="qty_approval" value="{{$qty_approval}}">
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
	</div>
	<div class="modal fade" id="modal-update">
		<div class="modal-dialog box box-primary" style="width:400px;">
			<div class="modal-content">
			<form action="/Status/KSK/Target" method="post">
			<input type="hidden" id="deptid" name="deptid">
			<input type="hidden" id="periodetarget" name="periode_target" value="{{$periode}}">
			{{ csrf_field() }}
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Form Update Quota</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-lg-6 col-xs-12">
							<div class="form-group">
								<label>Permanent Target</label>
								<input type="number" class="form-control" id="permanentp" name="permanent_plan">
							</div>
							<div class="form-group">
								<label>Contract Target</label>
								<input type="number" class="form-control" id="contractp" name="contract_plan">
							</div>
							<div class="form-group">
								<label>Magang Target</label>
								<input type="number" class="form-control" id="magangp" name="magang_plan">
							</div>
						</div>
						<div class="col-lg-6 col-xs-12">
							<div class="form-group">
								<label>Permanent Actual</label>
								<input type="number" class="form-control" id="permanenta" name="permanent_actual">
							</div>
							<div class="form-group">
								<label>Contract Actual</label>
								<input type="number" class="form-control" id="contracta" name="contract_actual">
							</div>
							<div class="form-group">
								<label>Magang Actual</label>
								<input type="number" class="form-control" id="maganga" name="magang_actual">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" style="text-align:left;">
					<input type="submit" class="btn btn-primary" value="Update">
					<button type="button" class="btn btn-default pull-right cancelafter" data-dismiss="modal">Cancel</button>
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
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<!--  on Load  -->
	<script>
		$(document).ready(function() {
			var lock="{{$lock_status}}";
			var quota="{{$quota_status}}";
			var performance="{{$performance_status}}";
			if(lock==1){
				$('#refresh').hide();
				$('#distribute').hide();
			//}else if(quota==0||performance==0){
			//}else if(performance==0){
				//$('#refresh').show();
				//$('#distribute').hide();
			}else{
				$('#refresh').show();
				$('#distribute').show();
			}
			//alert(performance);
			var qty_approval=document.getElementById('qty_approval').value;
			if(qty_approval>0){
				document.getElementById("bahaya").style.display = "none";
			}
		});
	</script>
	<script>
		$('body').on("change","#periode",function(){
			var periode=document.getElementById('periode').value;
			window.location.href="/Status/KSK/Create/"+periode;
		});
	</script>
	<script type="text/javascript">
		$(document).on('click', '.update-modal', function() {
			$('#deptid').val($(this).data('deptid'));
			$('#permanentp').val($(this).data('permanentp'));
			$('#contractp').val($(this).data('contractp'));
			$('#magangp').val($(this).data('magangp'));
			$('#permanenta').val($(this).data('permanenta'));
			$('#contracta').val($(this).data('contracta'));
			$('#maganga').val($(this).data('maganga'));
			$('#modal-update').modal('show');
		});
	</script>
@endsection
