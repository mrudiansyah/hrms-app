@extends('layouts/admin')
@section('Contents')
<meta name="csrf-token" content="{{ csrf_token() }}">
	<style>
		ol li{
			padding-bottom:5px;
		}
		.tabelot th{
			text-align:center;
			font-weight:normal;
			border:1px solid #000;
		}
		.tabelot td{
			text-align:center;
			border:1px solid #000;
			height:30px;
		}
		.tabelbingkai{
			border:1px solid #000;
		}
		.tabeljudul td{
			border:0px;
		}
		.absolute {
			position: absolute;
			height:80px;
			left:20px;
			max-width:130px;
			top:-10px;
		}		
		.relative {
			left:0px;
			position: relative;
			height:60px;
		}	
	</style>



    <div class="content-wrapper">
		<section class="content-header">
				<h1 onclick="">
					Approval SPL
					<small>Surat Perintah Lembur</small>
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
		<section class="content">
			<?php 
				?>
			<?php 
				?>
				<div class="box box-success" style="border:0px;">
					<div class="box-header">
						<i class="fa fa-bar-chart"></i>
					<h3 class="box-title" id="ovtitle">Overtime vs Sales</h3>
						<div class="box-tools pull-right" id="btn-canvas">
							<button title="Edit Sales" type="button" id="salesammount" class="sales-modal btn btn-info btn-xs" data-periodesales="{{$periode}}" data-salesammount="{{$salesammount}}"><i class="fa fa-wrench"></i> Sales: <?php echo number_format($salesammount,0);?></button>
							<a href="javascript:void(0)" id="paretto" type="button" class="btn btn-primary btn-xs" onclick=""><i class="fa fa-refresh"></i>&nbsp; Pareto {{$periode}}</a>
						</div>
						<div class="box-tools pull-right" id="btn-canvases">
						</div>

					</div>
					<div class="box-body" style="padding:0px 100px 30px 100px;" class="col-md-12 col-sm-12 px-3 py-5" >
						<div class="overlay" id="overlay" style="">
							<i class="fa fa-refresh fa-spin"></i> Calculating data...
						  </div>
						<div id="this_chart" class="d-flex justify-content-center">
							
						</div>
					</div>
				</div>
			<?php 
		?>
			<?php 
		?>
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-list"></i>
						<h3 class="box-title" id="judul">SPL List</h3>
						<div class="pull-right">
							<a  href="javascript:void(0)" onclick="refreshTable()" class="btn btn-app table2">
								<?php 
									if(isset($jmlot_plan)&&$jmlot_plan>0)echo "<span class='badge bg-yellow'>".$jmlot_plan."</span>";
									?>
								<i class="fa fa-file-o"></i> New SPL
							</a>
							@if(request()->user()->hasRole('hr_access'))
                            <a  class="btn btn-app" href="{{ route('ApproveSPLAll') }}">
                                <i class="fa fa-check-square"></i> Approve All
                            </a>
							@endif
							
							<a class="btn btn-app table4" id="complete">
								<i class="fa fa-check-square-o"></i> Complete
							</a>
						</div>
					</div>
					
					<div class="box-body" style="min-height:200px;overflow-x: scroll;">
						<div class="row" style="padding-bottom:20px;">
							<div class="col-lg-2 col-md-3 col-xs-12" id="selectPeriode" hidden>
								<label>Periode</label>
								<input type="month" class="form-control" id="periode" name="periode" value="{{$periode}}">
								<input type="hidden" class="form-control" id="cabang" value="{{$cabang}}">
							</div>
						</div>
						<div class="row col-md-12 table-responsive" id="planData">
						<table id="table2" class="table table-striped" border="0">
							<thead>
								<tr>
									<th style="width:60px;">Action</th>
									<th style="width:60px;">NO</th>
									<th style="width:90px;">NO.SPL</th>
									<th style="width:90px;">OT.DATE</th>
									<th>DEPARTMENT</th>
									<th style="width:70px;background:#DDD;">ORDER</th>
									<th style="width:70px;background:#DDD;">APPROVED</th>
									<th style="width:70px;background:#DDD;">SEEN</th>
									<th style="width:70px;background:#DDD;">RECORDED</th>
									<th style="width:70px;background:#0F0;">APPROVE</th>
									<th style="width:70px;background:#0F0;">PAID</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
						</div>
						<div class="row col-md-12 table-responsive" id="completeData" hidden>
							<table id="completeTable" class="table table-striped" border="0">
								<thead>
									<tr>
										<th style="width:60px;">Actionc</th>
										<th style="width:60px;">NO</th>
										<th style="width:90px;">NO.SPL</th>
										<th style="width:90px;">OT.DATE</th>
										<th>DEPARTMENT</th>
										<th style="width:70px;background:#DDD;">ORDER</th>
										<th style="width:70px;background:#DDD;">APPROVED</th>
										<th style="width:70px;background:#DDD;">SEEN</th>
										<th style="width:70px;background:#DDD;">RECORDED</th>
										<th style="width:70px;background:#0F0;">APPROVE</th>
										<th style="width:70px;background:#0F0;">PAID</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		</section>
    </div>
	<div class="modal fade" id="modal-sales">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				{{-- <form action="/Admin/Overtime/Sales" method="post"> --}}
				<form >

				<input type="hidden" id="periodesales" name="periodesales">

				{{ csrf_field() }}
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Update Sales Ammount</h4>
					</div>
					<div class="modal-body">
						<div class="form-group come">
							<label>Sales Ammount</label>
							<input type="number" name="salesammount" id="salesammounts" class="form-control">
						</div>

					</div>
					<div class="modal-footer" style="text-align:left;padding:20px;">
						<button type="button" class="btn btn-primary confirmafter" id="btnUpdate" onclick="simpan();">Update</button>
						{{-- <input type="submit" class="btn btn-primary confirmafter" value="Update"> --}}
						<button type="button" class="btn btn-default pull-right cancelafter" data-dismiss="modal">Cancel</button>
					</div>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	
	<script src="{{ asset('/public/assets/bower_components/jquery/dist/jquery.min.js') }}"></script>
@section('Scripts')
	<script>
		
		const urlParams = new URLSearchParams(window.location.search);
		var ref_doc = urlParams.get('ref_doc');
		var dt;
		if (ref_doc == null) {
			$("#data_table").addClass('active');
			$.fn.dataTable.ext.errMode = 'none';
			
		var tablePlan = $("#table2").DataTable({
			processing: true,
			serverSide: true,
			rowReorder: {
				selector: 'td:nth-child(2)'
			},
			responsive: true,
			ajax: {
				type: 'POST',
				url: "{{ route('ApprovalSPL.GetDataPlanApproval') }}",
				data: function(d) {
					d._token = document.querySelector('meta[name="csrf-token"]')
						.getAttribute('content');
				},
				cache: false,
				dataType: 'json'
			},
			columns: [
						{
							data: 'action'
						},
						{
							data: 'no',
							className: 'text-center'
						},
						
						{
							data: 'id_overtime'
						},
						{
							data: 'ot_date'
						},
						{
							data: 'dept_name',
						},
						{
							data: 'status_diperintah',
						},
						{
							data: 'disetujui',
						},
						{
							data: 'diketahui',
						},
						{
							data: 'status_dicatat',
						},
						{
							data: 'status_approve',
						},
						{
							data: 'status_paid',
						}]
				});
		} else {
		}
	GetChart(0);
	
	$("#paretto").click(function(e){
		$("#overlay").show();
		GetParetto(1,0);
	})
	function GetParetto(paretto,periode){
		var token = document.querySelector('meta[name="csrf-token"]')
					.getAttribute('content')		
		var paretto = paretto;
		var periode = periode;
		if(periode == 0){
			periode = "<?php echo date('Y-m'); ?>";
		}
		var t = {_token : token, paretto : paretto, periode:periode }
		x = x = $.ajax({
		type: "POST",
		url: "{{ route('ApprovalSPL.ChartApproval') }}",
		data: t,
		success: function (data) {
			
			$("#overlay").hide();
			$('#ovtitle').html('Pareto Overtime Periode '+ moment(periode).format('MMMM YYYY') );
			$('#btn-canvases').html('<a href="javascript:void(0)" id="ovtbutton" class="btn btn-warning btn-xs" onclick="GetChart(0);"><i class="fa fa-refresh"></i>&nbsp; OT vs Sales</a>').show();
			$('#btn-canvas').hide();
			$('#this_chart').html(data);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			}		
		});
	}
	function GetChart(paretto){0
		var token = document.querySelector('meta[name="csrf-token"]')
					.getAttribute('content')		
		var paretto = paretto;
		var t = {_token : token, paretto : paretto }
		$.ajax({
		type: "POST",
		url: "{{ route('ApprovalSPL.ChartApproval') }}",
		data: t,
		success: function (data) {
			$("#overlay").hide();
			$('#btn-canvases').hide();
			$('#btn-canvas').show();
			$('#ovtitle').html('Overtime vs Sales');
		setTimeout(() => {
			$('#this_chart').html(data);
			$("#overlay").hide();
			
		}, 100);

		},
		error: function(jqXHR, textStatus, errorThrown) {
				
			}		
		});
	}
	
	$("#complete").click(function (e) { 
		e.preventDefault();
		$("#selectPeriode").show();
		$("#completeData").show();
		$("#planData").hide();
		$("#judul").html("Completed SPL")
		GetCompleteApproval()
	});
	var x = null ; 
	$("#periode").on('change',function (e){
		// e.preventDefault();
		var a = $(this).val();
		$("#completeData").show();
		GetCompleteApproval();
	if (x !=null ){
		$("#overlay").hide();
		x.abort();
		$("#overlay").show();

		GetParetto(1,a)
	}else{
		$("#overlay").show();

		GetParetto(1,a)
	}
	});
//#endregion


//#region Completed SPL
	function GetCompleteApproval(){
		$.fn.dataTable.ext.errMode = 'none';
		var table = $("#completeTable").DataTable({
			processing: true,
			serverSide: true,
			rowReorder: {
				selector: 'td:nth-child(2)'
			},
			responsive: true,
			ajax: {
				type: 'POST',
				url: "{{ route('ApprovalSPL.GetDataCompleteApproval') }}",
				data: function(d) {
					d._token = document.querySelector('meta[name="csrf-token"]')
						.getAttribute('content');
					d.periode = $("#periode").val()
				},
				cache: false,
				dataType: 'json'
			},
			columns: [
						{
							data: 'action'
						},
						{
							data: 'no',
							className: 'text-center'
						},
						
						{
							data: 'id_overtime'
						},
						{
							data: 'ot_date'
						},
						{
							data: 'dept_name',
						},
						{
							data: 'status_diperintah',
						},
						{
							data: 'disetujui',
						},
						{
							data: 'diketahui',
						},
						{
							data: 'status_dicatat',
						},
						{
							data: 'status_approve',
						},
						{
							data: 'status_paid',
						}]
			});
			table.ajax.reload();
	}
	
	$(".sales-modal").click(function(e){
		GetSalesAmmount();
		// $("#salesammounts").val(0);
		// var periodesales = 0
		// var salesammount = 0
		//  periodesales = $(this).data('periodesales')
		//  salesammount = $(this).data('salesammount')

		
	});
	function GetSalesAmmount(){  
		var periode = $("#periode").val();
		var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
		var data = {
			_token : token,
			periode : periode
		};
		$.ajax({
			type: "POST",
			url: "{{ route('ApprovalSPL.GetSalesAmmount') }}",
			data: data,
			dataType: "json",
			success: function (data) {
				// console.log(data);
			$('#periodesales').val(data.periode);
			$('#salesammounts').val(data.ammount_sales);
			$('#modal-sales').modal('show');
			}
		});
	}
	function simpan() {  
			// alert();
			var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
			var periodesales = $("#periodesales").val();
			var salesammount = $("#salesammounts").val();
			var string = {
				_token : token,
				salesammount : salesammount,
				periodesales : periodesales
			}
			$.ajax({
				type: "POST",
				url: "{{ route('ApprovalSPL.updateSales') }}",
				data: string,
				dataType: "json",
				success: function (data) {
					if(data.success == 1){
						$('#salesammount').html('<i class="fa fa-wrench"></i> Sales: '+data.amount_sales);
						
						$('#modal-sales').modal('hide');

					}else{

					}
				}
			});
		}
		function refreshTable(){
			$("#completeData").hide()
			$("#selectPeriode").hide();
			$("#judul").html("New SPL")
			$("#planData").show()

			var tablePlan = $("#table2").DataTable({
			processing: true,
			serverSide: true,
			rowReorder: {
				selector: 'td:nth-child(2)'
			},
			responsive: true,
			ajax: {
				type: 'POST',
				url: "{{ route('ApprovalSPL.GetDataPlanApproval') }}",
				data: function(d) {
					d._token = document.querySelector('meta[name="csrf-token"]')
						.getAttribute('content');
				},
				cache: false,
				dataType: 'json',
				error: function(){
					$(".alert").show();
				}
			},
			columns: [
						{
							data: 'action'
						},
						{
							data: 'no',
							className: 'text-center'
						},
						
						{
							data: 'id_overtime'
						},
						{
							data: 'ot_date'
						},
						{
							data: 'dept_name',
						},
						{
							data: 'status_diperintah',
						},
						{
							data: 'disetujui',
						},
						{
							data: 'diketahui',
						},
						{
							data: 'status_dicatat',
						},
						{
							data: 'status_approve',
						},
						{
							data: 'status_paid',
						}]
				});
				tablePlan.ajax.reload()
		}
        //#endregion
		// $(document).on('click', '.sales-modal', function() {
			
		// });
	</script>

@endsection

    @endsection
