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
					<form role="form" action="" method="post">
					<div class="box-header">
						<i class="fa fa-file-text-o"></i>Competence List
						<div class="box-tools pull-right">
							@if($data['id_group']!=0)
								<button type="button" class="btn btn-primary pull-left simpan">Save Change</button>
							@endif
						</div>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12 col-md-6 col-lg-4">
								<select class="form-control" id="select_group">
									<option value="{{$data['id_group']}}">{{$data['group_name']}}</option>
									@foreach($data['tb_group'] as $dt)
										@if($data['id_group']!=$dt->id)
											<option value="{{$dt->id}}">{{$dt->group_name}}</option>
										@endif
									@endforeach
								</select>
							</div>
							<div class="col-xs-12 col-md-8 col-lg-9">&nbsp;</div>
						</div><br>
						@if($data['id_group']!=0)
							<div class="col-lg-6 col-md-12 col-xs-12">
								<table id="tables" class="table table-hover">
									<thead>
										<tr>
											<th style="width:50px;">No</th>
											<th style="width:70px;">Type</th>
											<!-- <th style="width:70px;">Category</th> -->
											<th style="width:70px;">Competence</th>
											<th style="width:50px;">Standard</th>
										</tr>
									</thead>
									<tbody>
										<?php $no=0;?>
										@foreach($data['tb_competence_group_list'] as $dt)
										@if($dt->id_group==$data['id_group'])
										<tr>
											<td>
												<?php $no++;echo $no;?>
											</td>
											<td>{{$dt->type_skill}}</td>
											<!-- <td>{{$dt->category}}</td> -->
											<td>{{$dt->competence_name}}</td>
											<td>
												<i class="create-modal" data-id="{{$dt->id_matric}}" data-min_level="{{$dt->min_level}}" onmouseover="this.style.cursor='pointer'">{{$dt->min_level}}</i>
												<div class="form-group pull-right">
													<label>
														<input type="checkbox" id="c_{{$dt->id}}" class="flat-red" <?php if($dt->id_group==$data['id_group'])echo "checked";?>>
													</label>
												</div>
											</td>
										</tr>
										@endif
										@endforeach
									</tbody>
								</table>
							</div>
							<div class="col-lg-6 col-md-12 col-xs-12">
								<table id="tables2" class="table table-hover">
									<thead>
										<tr>
											<th style="width:50px;">No</th>
											<th style="width:50px;">ID</th>
											<th style="width:70px;">Type</th>
											<!-- <th style="width:70px;">Category</th> -->
											<th style="width:70px;">Competence</th>
										</tr>
									</thead>
									<tbody>
										<?php $no=0;?>
										@foreach($data['tb_competence_group_list'] as $dt)
										@if($dt->id_group!=$data['id_group'])
										<tr>
											<td>
												<?php $no++;echo $no;?>
											</td>
											<td>{{$dt->id}}</td>
											<td>{{$dt->type_skill}}</td>
											<!-- <td>{{$dt->category}}</td> -->
											<td>
												{{$dt->competence_name}}
												<div class="form-group pull-right">
													<label>
														<input type="checkbox" id="c_{{$dt->id}}" class="flat-red" <?php if($dt->id_group==$data['id_group'])echo "checked";?>>
													</label>
												</div>
											</td>
										</tr>
										@endif
										@endforeach
									</tbody>
								</table>
							</div>
						@endif
					</div>
					<!-- /.box-body -->
					<div class="box-footer">
						&nbsp;
					</div>
					</form>
				</div>

			</div>
		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->
	<div class="modal fade" id="modal-create">
		<div class="modal-dialog box box-primary" style="width:300px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Minimum Level</h4>
					<input type="hidden" name="id_tabel" id="id_tabel" class="form-control">
				</div>
				<div class="modal-body">
					<form role="form" action="" method="post">
						<meta name="csrf-token" content="{{ csrf_token() }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="form-group">
								<label>Type Skill</label>
								<input type="number" name="min_level" id="min_level" class="form-control">
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary pull-left simpan_level" data-dismiss="modal">Save</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
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
			"pageLength"  : 500,
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
		$(document).ready(function() {
			var table = $('#tables2').DataTable({
			'paging'      : true,
			'lengthChange': false,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 500,
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
		$('body').on("change","#select_group",function(){
			var select_group=document.getElementById('select_group').value;
			window.location.href="{{$site}}/SkillMatric/Group/"+select_group;
		});
	</script>
	<script type="text/javascript">
		$(document).on('click', '.simpan', function() {
			var id_group = "{{$data['id_group']}}";
			var group_skill = {};
			
			// Kumpulkan semua nilai kompetensi
			<?php foreach($data['tb_competence_group_list'] as $dt){ ?>
				group_skill["{{$dt->id}}"] = $("#c_{{$dt->id}}").is(':checked') ? 1 : 0;
			<?php } ?>
			
			
			$.ajax({
				type: "POST",
				url: "{{$site}}/SkillMatric/CompetenceGroup/Save",
				data: {
					id: id_group,
					group_skill: group_skill,
				},
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: function(respond) {
					//alert(respond); // Tampilkan response dari server
					location.reload();
				},
				error: function(xhr) {
					alert("Error: " + xhr.responseText);
				}
			});
		});
	</script>
	<script>
		$(document).on('click', '.create-modal', function() {
            $("#id_tabel").val($(this).data('id'));
            $("#min_level").val($(this).data('min_level'));
			$('#modal-create').modal('show');
		});

		$('.modal-footer').on('click', '.simpan_level', function() {
            var a=$("#id_tabel").val();
			var b=$("#min_level").val();
            var datas = {
                id:a,
				min_level:b,
            }
			$.ajaxSetup({
				type:"POST",
				url: "{{$site}}/SkillMatric/CompetenceGroup/SaveMin",
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

