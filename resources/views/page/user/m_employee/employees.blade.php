@extends('layouts/admin')
@section('Contents')
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
		</h1>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-xs-12">
			<div class="box box-primary" style="background:#FFF;">
				<div class="box-header">
					<i class="fa fa-user"></i>&nbsp;<label style="font-size:18px;font-weight:normal;">{{$status}}</label>
					<div class="pull-right">
						<a href="/Employees/0" class="btn btn-app">
							<i class="fa fa-users"></i> All
						</a>
						<a href="/Employees/Magang" class="btn btn-app">
							<i class="fa fa-user"></i> Magang
						</a>
						<a href="/Employees/Kontrak" class="btn btn-app">
							<i class="fa fa-user"></i> Kontrak
						</a>
						<a href="/Employees/Permanen" class="btn btn-app">
							<i class="fa fa-user"></i> Permanen
						</a>
					</div>
				</div>
				<div class="box-body">
				<table id="tables" class="table table-bordered">
					<thead>
						<tr>
							<th>No</th>
							<th>NIK</th>
							<th>Employee Name</th>
							<th>Gender</th>
							<th>Join Date</th>
							<th>Dept</th>
							<th>Position</th>
							@if($status!='Permanen')
							<th>End Contract</th>
							@endif
						</tr>
					</thead>
					<tbody>
						<?php $no=0;?>
						@foreach($tb_employee as $dt)
							<tr onclick=window.location.href='/Employee/<?php echo $dt->id;?>/<?php echo $dt->PIN;?>'>
								<td><?php $no++;$pin=$dt->PIN;$userid=$dt->id;echo $no;?></td>
								<td>{{$dt->NIK}}</td>
								<td>{{$dt->employee_name}}</td>
								<td>{{$dt->gender}}</td>
								<td>{{$dt->join_date}}</td>
								<td>{{$dt->dept_code}}</td>
								<td>{{$dt->position_name}}</td>
								@if($status!='Permanen')
								<td>{{$dt->finish_contract}}</td>
								@endif
							</tr>
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
			"pageLength"  : 10,
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
