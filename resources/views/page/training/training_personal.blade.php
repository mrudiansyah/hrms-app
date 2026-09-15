@extends('layouts/admin')
@section('Contents')
	<meta name="csrf-token" content="{{ csrf_token() }}">
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
        .box-header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 0;
            overflow: visible;
            padding-left: 0;
            margin-left: 0;
        }
        .box-header .box-title {
            margin: 0;
        }
        .box-tools {
            flex: 0 0 360px;
            max-width: 360px;
            display: flex;
            justify-content: flex-start;
            overflow: visible;
            margin-left: 0;
            padding-left: 5px;
        }
        .box-tools .form-group {
            width: 100%;
            max-width: 360px;
            margin-bottom: 0;
            margin-left: 0;
        }
        .selectpicker.form-control,
        .bootstrap-select,
        .bootstrap-select .dropdown-toggle {
            width: 100% !important;
        }
        .box.box-info {
            overflow: visible;
        }
    </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Training Record
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-lg-12 col-md-10 col-xs-12">
				<div class="box box-info" style="background:#FFF;">
					<div class="box-header">
						<div class="box-tools">
                            <div class="form-group">
                                <select id="id_employee" class="selectpicker form-control pilihan" data-live-search="true">
                                    <option value="0" {{ old('id_employee', $selected_employee ?? 0) == 0 ? 'selected' : '' }}>All Employee</option>
                                    @foreach($tb_list as $dt)
                                        <option value="{{$dt->id_employee}}" {{ old('id_employee', $selected_employee ?? 0) == $dt->id_employee ? 'selected' : '' }}>
                                            {{$dt->nama_karyawan}} ({{$dt->NIK}})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
 						</div>
					</div>
					<div class="box-body">
						<div class="pull-right">
							&nbsp;
						</div>
					</div>
					<div class="box-body" style="overflow-x: scroll;">
						<table id="table3" class="table table-hover">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th>Training Name</th>
									<th>Category</th>
									<th>NIK</th>
									<th>Employee</th>
									<th>Department</th>
									<th>Jabatan</th>
									<th>Date</th>
									<th>Time</th>
									<th>&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								@foreach($tb_training_personal as $dt)
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td>{{$dt->training_name}}</td>
									<td>{{$dt->skill_type}}</td>
									<td>{{$dt->NIK}}</td>
									<td>{{$dt->nama_karyawan}}</td>
									<td>{{$dt->department}}</td>
									<td>{{$dt->jabatan}}</td>
									<td>{{$dt->tanggal_aktual}}</td>
									<td>
										<?php 
											echo date('H:i',strtotime($dt->start_aktual));
											echo "~";
											echo date('H:i',strtotime($dt->finish_aktual));
										?>
									</td>
									<td>
										<div class="pull-right">
											<a href="{{$site}}/Training/Actual/{{$dt->id}}" title="Participant" type="button" class="participant btn btn-primary btn-xs"><i class="fa fa-folder-o"></i></a>
										</div>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
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
        $('body').on("change", ".pilihan", function () {
            var id_employee = document.getElementById('id_employee').value;
            window.location.href = "/Training/Personal/" + id_employee;
        });
	</script>
@endsection
