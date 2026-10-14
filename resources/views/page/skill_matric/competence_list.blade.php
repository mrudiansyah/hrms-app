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
			<div class="col-xs-12 col-md-12 col-lg-9">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-file-text-o"></i>Competence List
						<div class="box-tools pull-right">
							<button class="btn btn-primary btn-md create-modal" data-id="" data-id_type="" data-competence_name="" data-group_competence="">
								<i class="fa fa-plus"></i> &nbsp;Create
							</button>
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
									<th style="width:50px;">No</th>
									<th style="width:70px;">Type</th>
									<th style="width:70px;">Competence</th>
									<th style="width:70px;">Category</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								@foreach($data['tb_competence_list'] as $dt)
								<tr>
									<td>
										<?php $no++;echo $no;?>
									</td>
									<td>{{$dt->type_skill}}</td>
									<td>{{$dt->competence_name}}</td>
									<td>{{$dt->category}}</td>
									<td>
										<div class="pull-left">
											<button class="btn btn-info btn-xs" onclick="location.href='/EMS/SkillMatric/Level/{{$dt->id}}'">
												<i class="fa fa-list-ol"></i>
											</button>
											<button class="btn btn-primary btn-xs" onclick="location.href='/EMS/SkillMatric/Machine/{{$dt->id}}'">
												<i class="fa fa-industry"></i>
											</button>
											<button class="btn btn-success btn-xs" onclick="location.href='/EMS/SkillMatric/CompetenceEmployee/{{$dt->id}}'">
												<i class="fa fa-user"></i>
											</button>
											<button class="btn btn-warning btn-xs create-modal" data-id="{{$dt->id}}" data-id_type="{{$dt->id_type}}" data-competence_name="{{$dt->competence_name}}" data-category="{{$dt->category}}">
												<i class="fa fa-edit"></i>
											</button>
											<button class="btn btn-danger btn-xs delete-modal" data-delid="{{$dt->id}}" data-delid2="tb_competence_list" data-delname="{{$dt->competence_name}}">
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
					<h4 class="modal-title">Form Competence</h4>
					<input type="hidden" name="id_tabel" id="id_tabel" class="form-control">
				</div>
				<div class="modal-body">
					<form role="form" action="" method="post">
						<meta name="csrf-token" content="{{ csrf_token() }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="form-group">
								<label>Type</label>
								<select name="id_type" class="form-control" id="id_type">
									<option value=""></option>
									@foreach($data['tb_skill_type'] as $dt2)
										<option value="{{$dt2->id}}">{{$dt2->type_skill}}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group">
								<label>Competence Name</label>
								<input type="text" name="competence_name" id="competence_name" class="form-control">
							</div>
							<div class="form-group">
								<label>Category</label>
								<select name="category" class="form-control" id="category">
									<option value=""></option>
									@foreach($data['tb_category'] as $dt2)
										<option value="{{$dt2->category}}">{{$dt2->category}}</option>
									@endforeach
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
            $("#id_type").val($(this).data('id_type'));
            $("#competence_name").val($(this).data('competence_name'));
            $("#category").val($(this).data('category'));
			$('#modal-create').modal('show');
		});

		$('.modal-footer').on('click', '.simpan', function() {
            var a=$("#id_tabel").val();
			var b=$("#id_type").val();
			var c=$("#competence_name").val();
			var d=$("#category").val();
            var datas = {
                id:a,
				id_type:b,
				competence_name:c,
				category:d
            }
			$.ajaxSetup({
				type:"POST",
				url: "{{$site}}/SkillMatric/Save",
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
	
@endsection

