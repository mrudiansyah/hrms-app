@extends('layouts/admin')
@section('Contents')
   <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
		#tables th {
            border-top: 1px solid #999;
            border-bottom: 1px solid #999;
            background-color: #d3d8d8ff;
            color: black;
		}	
        .table1 tr:hover { cursor:pointer; }
		#table2 th { border-top: 2px solid #999; border-bottom: 2px solid #999; }	
		#table2 tbody tr:hover{ cursor:pointer; }
    </style>
	<div class="content-wrapper">
		<section class="content-header">
			<h1>
				Roles
				<small>Manage System Roles</small>
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
		<div class="row">
			<div class="col-xs-12 col-md-6 col-lg-6">
            
                @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Success!</h4>
                    {{ session('success') }}
                </div>
                @endif
            
				<div class="box box-success" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-key"></i>
						<h3 class="box-title">Role List</h3>
						<div class="box-tools pull-right">
                            <a href="{{ url('/role-management/create') }}" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> Add Role</a>
							<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div>
					</div>
					<div class="box-body">
						<table id="tables" class="table table-hover table-striped">
							<thead>
								<tr>
									<th style="width:50px;">ID</th>
									<th>Role Name</th>
									<th>Users</th>
                                    <th style="width:100px; text-align:center;">Action</th>
								</tr>
							</thead>
							<tbody>
								@foreach($tb_role as $dt)
								<tr>
									<td>{{$dt->id}}</td>
									<td>{{$dt->name}}</td>
									<td><span class="badge bg-light-blue">{{$dt->users_count}} users</span></td>
                                    <td align="center">
                                        <a href="{{ url('/role-management/'.$dt->id.'/edit') }}" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>
                                        <form action="{{ url('/role-management/'.$dt->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this role?');"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		</section>
  	</div>
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
				"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]]
			});
		
			new $.fn.dataTable.Buttons( table, {
				buttons: ['copy', 'excel', 'print']
			} );
		
			table.buttons( 0, null ).container().prependTo(
				table.table().container()
			);
		} );
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
@endsection
