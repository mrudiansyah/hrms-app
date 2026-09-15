@extends('layouts/admin')
@section('Contents')
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

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-xs-12 col-md-12 col-lg-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-file-pdf-o"></i>
						<h3 class="box-title">Legalization Arsif</h3>
						<div class="box-tools pull-right">
							<?php $today=date('Y-m-d');?>
							@if(request()->user()->hasRole('legal'))
								<button class="btn btn-default btn-md" onclick="window.location.href='/PolicyControl'">
									<i class="fa fa-home"></i> &nbsp;Back
								</button>
							@endif
						</div>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12">
								&nbsp;
							</div>
						</div>
						<table id="tables" class="table table-hover">
							<thead>
								<tr>
									<th>No</th>
									<th>Category</th>
									<th>Document Name</th>
									<th>Expired Date</th>
									<th>Status</th>
									<th>Remark</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								@foreach($data['tb_legal_permit'] as $dt)
								<tr>
									<td>
										<?php $no++;echo $no;?>
									</td>
									<td>{{$dt->category}}</td>
									<td>{{$dt->permit_name}}</td>
									<td>{{date('d F Y',strtotime($dt->expiry_date))}}</td>
									<td>{{$dt->status}}</td>
									<td>{{$dt->description}}</td>
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

@endsection
@section('Scripts')
	<script>
		$(document).ready(function() {
			var table = $('#tables').DataTable({
			'paging'      : false,
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

@endsection

