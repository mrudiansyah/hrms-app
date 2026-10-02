@extends('layouts/admin')
@section('Contents')
   <!-- Contents -->
<div class="content-wrapper">
	<section class="content-header">
		<h1 onclick="">
			Finger Print
			<small>Record</small>
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
		<div class="box box-primary">
			<div class="box-header with-border">
				<b style="font-size:21px;"><?php echo ucwords(strtolower($employee_name));?></b>
			<div class="box-tools pull-right">
				<a href="/Absency/Finger/{{$badgenumber}}/{{$awal}}/{{$akhir}}" class="btn btn-info btn-md"><i class="fa fa-clock-o"></i> &nbsp;Database iClock</a>
			</div>
			<!-- /.box-tools -->
			</div>
			<!-- /.box-header -->
			<div class="box-body" style="padding:20px;overflow-x:scroll;">

				<table id="tables" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>No</th>
							<th>Date</th>
							<th>Day</th>
							<th>Group</th>
							<th>Schedule</th>
							<th>
								<i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
								Checkin
							</th>
							<th>
								<i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
								Checkout
							</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						@foreach($checktime_records as $record)
								<tr>
									<td>{{$record['day']}}</td>
									<td>{{$record['date']}}</td>
									<td>{{date('l',strtotime($record['date']))}}</td>
									<td>{{$record['shift_code']}}</td>
									<td>
										@if($record['check_in']!=0&&$record['check_out']!=0)
											{{date('H:i',strtotime($record['check_in']))}} - {{date('H:i',strtotime($record['check_out']))}}
										@endif
									</td>
									<td>@if($record['checkin_act']!=0){{date('H:i:s',strtotime($record['checkin_act']))}}@endif</td>
									<td>@if($record['checkout_act']!=0){{date('H:i:s',strtotime($record['checkout_act']))}}@endif</td>
									<td>
										@if($record['status']=='Present')<span class="badge bg-green">{{$record['status']}}</span>
										@elseif($record['status']=='Absent')<span class="badge bg-light">{{$record['status']}}</span>
										@elseif($record['status']=='Holiday')<span class="badge bg-red">{{$record['status']}}</span>
										@elseif($record['status']=='Leave')<span class="badge bg-blue">{{$record['status']}}</span>
										@elseif($record['status']=='Change')<span class="badge bg-yellow">{{$record['status']}}</span>@endif
									</td>
								</tr>
						@endforeach
					</tbody>
				</table>

			</div>
		
		</div>
	</section>
</div>
    <!-- /.Content -->

	@endsection
@section('Scripts')
	<!-- page script Tabel-->
	<script>
		$(document).ready(function() {
			var table = $('#tables').DataTable({
				'paging'      : true,
				'lengthChange': false,
				'searching'   : true,
				'ordering'    : true,
				'info'        : true,
				"pageLength"  : 50,
				'autoWidth'   : false,
				"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]]
        //"iDisplayLength": 50
				//dom: 'Bfrtip',buttons: ['print']
			});
		
			new $.fn.dataTable.Buttons( table, {
				buttons: ['copy', 'excel', 'print']
			} );
		
			table.buttons( 0, null ).container().prependTo(
				table.table().container()
			);
		} );


	</script>
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.update-modal', function() {
			$('#idemployee').val($(this).data('idemployee'));
			$('#implementasi').val($(this).data('implement'));
			$('#modal-update').modal('show');
		});
	</script>
@endsection
