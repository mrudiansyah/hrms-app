@extends('layouts/admin')
@section('Contents')
   <meta name="csrf-token" content="{{ csrf_token() }}">
	<div class="content-wrapper">
		<section class="content-header">
			<h1>Master Area <small>Manage Data Areas</small></h1>
		</section>

		<section class="content">
		<div class="row">
			<div class="col-xs-12 col-md-12 col-lg-8">
            
                @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Success!</h4>
                    {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-ban"></i> Error!</h4>
                    {{ session('error') }}
                </div>
                @endif
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-map-marker"></i>
						<h3 class="box-title">Area List</h3>
						<div class="box-tools pull-right">
                            <a href="{{ url('/master_area/create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add New Data</a>
						</div>
					</div>
					<div class="box-body">
						<table id="tables" class="table table-hover table-bordered">
							<thead>
								<tr style="background:#d3d8d8ff">
									<th style="width:50px;">ID</th>
									<th>Plant</th>
									<th>Area</th>
                                    <th>AP ID</th>
                                    <th>Status</th>
                                    <th style="width:100px; text-align:center;">Action</th>
								</tr>
							</thead>
							<tbody>
								@foreach($data as $dt)
								<tr>
									<td>{{$dt->id}}</td>
									<td>{{$dt->plant}}</td>
									<td>{{$dt->area}}</td>
                                    <td>{{$dt->ap}}</td>
                                    <td>
                                        @if($dt->is_active == 1)
                                            <span class="label label-success">Active</span>
                                        @else
                                            <span class="label label-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td align="center">
                                        <a href="{{ url('/master_area/'.$dt->id.'/edit') }}" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>
                                        <form action="{{ url('/master_area/'.$dt->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this data?');"><i class="fa fa-trash"></i></button>
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
		}, 3000);
	</script>
@endsection
