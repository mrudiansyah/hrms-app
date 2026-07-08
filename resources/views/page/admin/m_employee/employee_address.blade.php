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
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Employee
				<small>list</small>
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
						<i class="fa fa-map-marker"></i>
						<h3 class="box-title">Address</h3>
						<div class="box-tools pull-right">
							<a href='/Admin/Employee/Domiciles'><button type="button" class="btn btn-primary btn-md"><i class="fa fa-map-marker"></i> &nbsp;Domiciles</button></a>
							<a href='/Admin/Employee'><button type="button" class="btn btn-default btn-md"><i class="fa fa-user"></i> &nbsp;Employee List</button></a>
						</div>
					</div>
					<div class="box-body">
						<div style="padding:20px;overflow-x: scroll;">
						<table id="tables" class="table table-hover">
							<thead>
								<tr>
									<th>No</th>
									<th>EMPLOYEE_NIK</th>
									<th>EMPLOYEE_NAME</th>
									<th>JABATAN</th>
									<th>DEPARTMENT</th>
									<th>DETAIL</th>
									<th>KELURAHAN</th>
									<th>KECAMATAN</th>
									<th>KABUPATEN</th>
									<th>PROVINSI</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								@foreach($tb_employee as $dt)
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td>{{$dt->NIK}}</td>
									<td>{{$dt->employee_name}}</td>
									<td>{{$dt->position_name}}</td>
									<td>{{$dt->dept_code}}</td>
									<td>{{$dt->detail}}</td>
									<td>{{$dt->kelurahan}}</td>
									<td>{{$dt->kecamatan}}</td>
									<td>{{$dt->kabupaten}}</td>
									<td>{{$dt->provinsi}}</td>
								</tr>
								@endforeach
							</tbody>
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
		$(document).ready(function() {
			var table = $('#tables').DataTable({
				'paging'      : true,
				'lengthChange': false,
				'searching'   : true,
				'ordering'    : true,
				'info'        : true,
				"pageLength"  : 10,
				'autoWidth'   : false,
				"pagingType": "full",
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
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
@endsection
