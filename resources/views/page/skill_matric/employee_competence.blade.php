@extends('layouts/memo')
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
				Employee Competence
				<small>&nbsp;</small>
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-xs-12 col-md-12 col-lg-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12 col-md-3 col-lg-2">
								<div class="form-group">
									<label>Employee</label>
									<select name="select_employee" class="form-control update_page" id="select_employee">
										<option value="{{$data['id_employee']}}">{{$data['employee_name']}}</option>
										<option value="0">&nbsp;</option>
										@foreach($data['tb_employee_mc'] as $dt3)
											<option value="{{$dt3->id_employee}}">{{$dt3->NIK}} {{$dt3->employee_name}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-xs-12 col-md-3 col-lg-2">
								<div class="form-group">
									<label>Periode</label>
									<select name="select_periode" class="form-control update_page" id="select_periode">
										<option value="{{$data['periode']}}">{{$data['periode']}}</option>
										<option value="0">&nbsp;</option>
										@foreach($data['periode_list'] as $dt3)
											<option value="{{$dt3->periode}}">{{$dt3->periode}}</option>
										@endforeach
									</select>
								</div>
								
							</div>
							<div class="col-xs-12 col-md-6 col-lg-8">
                                <div class="box-tools pull-right form-group">
									@if($data['id_employee']!=0)
									<a class="btn btn-primary btn-md" href="/EMS/SkillMatric/Print/{{$data['id_employee']}}" target="_blank">
    									<i class="fa fa-print"></i> &nbsp;Preview
									</a>
									@endif
									@if($data['periode_copy']!=0)
									<button class="btn btn-primary btn-md" id="copy">
										<i class="fa fa-copy"></i> &nbsp;Copy from {{$data['periode_copy']}}
									</button>
									@endif
									<!-- @if($data['id_employee']!=0)
									<button class="btn btn-primary btn-md create-modal" data-id="" data-id_employee="{{$data['id_employee']}}" data-actual_level="" data-periode="{{$data['periode']}}" data-id_competence="" data-minimum_level="3">
										<i class="fa fa-plus"></i> &nbsp;Create
									</button>
									@endif -->
                               </div>
							</div>
						</div><br>
						<table id="tables" class="table table-hover">
							<thead>
								<tr>
									<th style="width:50px;">No</th>
									<th>Type</th>
									<th>Competence Name</th>
									<th>Minimum</th>
									<th>level</th>
									<th>Periode</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								@foreach($data['tb_employee_competence'] as $dt)
								<tr>
									<td>
										<?php $no++;echo $no;?>
									</td>
									<td>{{$dt->type_skill}}</td>
									<td>{{$dt->competence_name}}</td>
									<td>{{$dt->minimum_level}}</td>
									<td>{{$dt->actual_level}}</td>
									<td>
										{{$dt->periode}}
										<div class="pull-right">
											<button class="btn btn-warning btn-xs create-modal" data-id="{{$dt->id}}" data-id_employee="{{$dt->id_employee}}" data-actual_level="{{$dt->actual_level}}" data-periode="{{$dt->periode}}" data-id_competence="{{$dt->id_competence}}" data-minimum_level="{{$dt->minimum_level}}">
												<i class="fa fa-edit"></i>
											</button>
											<button class="btn btn-danger btn-xs delete-modal" data-delid="{{$dt->id}}" data-delid2="tb_employee_competence" data-delname="{{$dt->competence_name}}">
												<i class="fa fa-trash"></i>
											</button>
										</div>
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
	<div class="modal fade" id="modal-delete">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Delete Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Delete : <b id="delname"></b> ?
					<input type="hidden" id="delid">
					<input type="hidden" id="delid2">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left delete" data-dismiss="modal">Yes, Delete</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-create">
		<div class="modal-dialog box box-primary" style="width:300px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Form Employee Competence</h4>
					<input type="hidden" name="id_tabel" id="id_tabel" class="form-control">
					<input type="hidden" name="id_employee" id="id_employee" class="form-control">
				</div>
				<div class="modal-body">
					<form role="form" action="" method="post">
						<meta name="csrf-token" content="{{ csrf_token() }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="form-group">
								<label>Periode</label>
								<input type="month" name="periode" id="periode" class="form-control">
							</div>
							<div class="form-group">
								<label>Competence</label>
								<select name="id_competence" class="form-control" id="id_competence">
									<option value=""></option>
									@foreach($data['tb_competence_list'] as $dt2)
										<option value="{{$dt2->id}}">{{$dt2->competence_name}}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group">
								<label>Minimum Level</label>
								<input type="number" name="minimum_level" id="minimum_level" class="form-control" min="1" max="4">
							</div>
							<div class="form-group">
								<label>Level</label>
								<select name="actual_level" class="form-control" id="actual_level">
									<option value=""></option>
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
								</select>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary pull-left simpan" data-dismiss="modal">Save</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>

@endsection
@section('Scripts')
	<script type="text/javascript">
		$(document).on('click', '.create-modal', function() {
            $("#id_tabel").val($(this).data('id'));
            $("#id_employee").val($(this).data('id_employee'));
            $("#id_competence").val($(this).data('id_competence'));
            $("#minimum_level").val($(this).data('minimum_level'));
            $("#actual_level").val($(this).data('actual_level'));
            $("#periode").val($(this).data('periode'));
            var x=$(this).data('id_employee');
            if(x!=''){
                document.getElementById('id_employee').disabled = true;
            }else{
                document.getElementById('id_employee').disabled = false;
            }
			$('#modal-create').modal('show');
		});

		$('.modal-footer').on('click', '.simpan', function() {
            var a=$("#id_tabel").val();
			var b=$("#id_employee").val();
			var c=$("#id_competence").val();
			var d=$("#minimum_level").val();
			var e=$("#actual_level").val();
			var f=$("#periode").val();
            var datas = {
                id:a,
				id_employee:b,
				id_competence:c,
				minimum_level:d,
                actual_level:e,
                periode:f
            }
			$.ajaxSetup({
				type:"POST",
				url: "{{$site}}/SkillMatric/EmployeeCompetence/Save",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:datas,
				success: function(respond){
					//alert(respond);
					location.reload();
				}
			})
		});
		// Delete Data
		$(document).on('click', '.delete-modal', function() {
			$('#delid').val($(this).data('delid'));
			$('#delname').text($(this).data('delname'));
			$('#delid2').val($(this).data('delid2'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#delid').val();
			var y=$('#delid2').val();
            var datas = {
                id:x,
				tabel:y
            }
			$.ajaxSetup({
				type:"POST",
				url: "{{$site}}/SkillMatric/Delete/",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:datas,
				success: function(respond){
					//alert(respond);
					location.reload();
				}
			})
		});
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
		$('body').on("change",".update_page",function(){
			var select_employee=document.getElementById('select_employee').value;
			var periode=document.getElementById('select_periode').value;
			window.location.href="{{$site}}/SkillMatric/EmployeeCompetence/"+select_employee+"/"+periode;
		});
	</script>
	<script>
		$(document).on('click', '#copy', function() {
            var a="{{$data['id_employee']}}";
			var b="{{$data['periode_copy']}}";
			var c="{{$data['periode']}}";
            var datas = {
                id_employee:a,
				periode_copy:b,
				periode:c
            }
			$.ajaxSetup({
				type:"POST",
				url: "{{$site}}/SkillMatric/EmployeeCompetence/Copy",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:datas,
				success: function(respond){
					//alert(respond);
					location.reload();
				}
			})
		});

	</script>
	
@endsection

