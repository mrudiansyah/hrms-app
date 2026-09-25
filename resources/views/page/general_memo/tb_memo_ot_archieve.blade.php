@extends('layouts/admin')
@section('Contents')
<?php
	//$serverName = "192.168.1.4"; //serverName\instanceName
	//$connectionInfo = array( "Database"=>"db_ems_memo", "UID"=>"EMS", "PWD"=>"1nd()n3514572");
	//$conn = sqlsrv_connect( $serverName, $connectionInfo);
?>
   <!-- Contents -->
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <?php $user=Auth::user()->name;?>
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
		#table2 tbody tr:hover{
			cursor:pointer;
		}
    </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Overtime Request
				<small>&nbsp;</small>
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-xs-12 col-md-12 col-lg-12">
					<div class="box box-primary" style="background:#FFF;">
						<div class="box-header">
							<i class="fa fa-file-text-o"></i>
							<h3 class="box-title">Request Production ( {{$data['periode']}} )</h3>
							<div class="box-tools pull-right">
								<button type="button" class="btn btn-default btn-md" onclick="window.location.href='/ArchieveMemo/{{$data['kategori']}}/{{$data['periode']}}';"><i class="fa fa-angle-double-left"></i> &nbsp;Back</button>
							</div>
						</div>
						<div class="box-body">
							<div class="row">
								<div class="col-xs-6 col-md-3 col-lg-2 pull-right">
									<input type="month" id="periode" class="form-control" value="{{$data['periode']}}" min="2025-04">	
								</div>
							</div>
							<table id="tables" class="table table-hover">
								<thead>
									<tr style="background:#CCCCCC;">
										<th style="width:50px;">No</th>
										<th>Kategori</th>
										<th>Department</th>
										<th>Part No.</th>
										<th>Part Name</th>
										<th>Line</th>
										<th>Date</th>
										<th>Qty</th>
										<th>Reason OT</th>
										<th>Remark</th>
										<th>Status</th>
										<th>Memo No.</th>
										<th>Info</th>
									</tr>
								</thead>
								<tbody>
									<?php $no=0;$akses2=0;?>
									@foreach($data['table2'] as $dt)
									<tr <?php if($dt->status=='0')echo "style='background:#ebeeee;'";?>>
										<td>
											<?php $no++;echo $no;?>
										</td>
										<td>{{$dt->code_memo}}</td>
										<td>{{$dt->from_dept}}-{{$dt->to_dept}}</td>
										<td>{{$dt->part_no}}</td>
										<td>{{$dt->part_name}}</td>
										<td>{{$dt->lines}}</td>
										<td>{{date('d-M-y',strtotime($dt->date_ot))}} {{date('H:i',strtotime($dt->start_ot))}}-{{date('H:i',strtotime($dt->finish_ot))}}</td>
										<td>{{$dt->plan_qty}}</td>
										<td>{{$dt->reason_ot}}</td>
										<td>{{$dt->remark}}</td>
										<td>
											<div class="pull-left">
												@if($dt->status==1)
													<i class="update_status fa fa-check-square-o" data-id="{{$dt->id}}" data-status="0"></i>
												@else
													<i class="update_status fa fa-square-o" data-id="{{$dt->id}}" data-status="1"></i>
												@endif
											</div>
										</td>
										<td>{{$dt->memo_number}}</td>
										<td>
											@if($dt->status=='0')
												Canceled by {{$dt->canceled_by}}
											@endif
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						<!-- /.box-body -->
						<div class="box-footer">
							&nbsp;
						</div>
					</div>

				</div>
			</div>
			<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->
	@if ($errors->any())
		<div class="alert alert-danger alert-dismissible" style="position:absolute;width:350px;right:10px;top:65px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-warning"></i> Saving Failed Alert!</h4>
				{{$errors}}
		</div>
	@endif

@endsection
@section('Scripts')

	<script>
		$(document).ready(function() {
			var table = $('#tables').DataTable({
			'paging'      : true,
			'lengthChange': false,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : false,
			"lengthMenu"  : [[5,10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
			"scrollX"     : true
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
		$('body').on("change","#periode",function(){
			var periode=document.getElementById('periode').value;
			var kategori="<?php echo $data['kategori'];?>";
			window.location.href="{{$site}}/ArchieveMemoDetail/"+kategori+"/"+periode;
		});
	</script>
@endsection

