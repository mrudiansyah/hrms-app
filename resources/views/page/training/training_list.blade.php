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
    </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Training
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-lg-6 col-md-8 col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-list"></i>
						<h3 class="box-title">Registered Training</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-success btn-xs form"><i class="fa fa-plus"></i> &nbsp;Add New</button>
							<button type="button" class="btn btn-primary btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button type="button" class="btn btn-danger btn-xs" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">
						<div class="pull-right">
							<div class="col-lg-6">
							<select id="category" class="form-control pilihan">
								<?php 
								if($type!=0){
									echo "<option value=".$type.">".$skill_type."</option>";
								}else echo "<option value='0'>All Type</option>";
								?>
								@foreach($tb_skill_type as $dt)
									<?php if($dt->id!=$type)echo "<option value=".$dt->id.">".$dt->skill_type."</option>";?>
								@endforeach
								<?php if($type!=0){echo "<option value='0'>All Level</option>";}?>
							</select>
							</div>
							<div class="col-lg-6">
							<select id="category2" class="form-control pilihan">
								<option value="{{$category}}">{{$category}}</option>
								@foreach($tb_category as $dt)
									@if($dt->category!=$category)
										<option value="{{$dt->category}}">{{$dt->category}}</option>
									@endif
								@endforeach
								@if($category!='All Category')
									<option value="0">All Category</option>
								@endif
							</select>
							</div>
						</div>
					</div>
					<div class="box-body" style="overflow-x: scroll;">
						<table id="tables" class="table table-hover">
							<thead>
								<tr>
									<th>No</th>
									<th>Training Name</th>
									<th>Level</th>
									<!-- <th>ID_Competence</th> -->
									<th>Category</th>
									<!-- <th>Participant</th> -->
									<th>&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								@foreach($tb_training_list as $dt)
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td>{{$dt->training_name}}</td>
									<td>{{$dt->skill_type}}</td>
									<!-- <td>{{$dt->id_competence_list}}</td> -->
									<td>{{$dt->category}}</td>
									<!-- <td>{{$dt->level_participant}}</td> -->
									<td>
										<div class="pull-right">
											<button title="Edit" type="button" class="form btn btn-primary btn-xs" data-idtraininglist="{{$dt->id}}" data-trainingname="{{$dt->training_name}}" data-idtype="{{$dt->id_type}}" data-categorytraining="{{$dt->category}}" data-levelparticipant="{{$dt->level_participant}}"><i class="fa fa-edit"></i></button>
											<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="{{$dt->id}}" data-delname="{{$dt->training_name}}"><i class="fa fa-trash"></i></button>
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

	<div class="modal fade" id="modal-form">
		<div class="modal-dialog box box-success" style="width:350px;">
			<div class="modal-content">
					<form>
					{{ csrf_field() }}
						<div class="modal-header">	
							<b>FORM TRAINING</b>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<input type="hidden" id="idComponent" class="form-control">
							<div class="form-group">
								<label>Training Name</label>
								<input type="text" id="trainingname" class="form-control">
							</div>
							<div class="form-group opsiedit">
								<label>Skill Type</label>
								<select id="skilltype" class="form-control">
									<option value=""></option>
									@foreach($tb_skill_type as $dt)
										<option value="{{$dt->id}}">{{$dt->skill_type}}</option>
									@endforeach

								</select>
							</div>
							<div class="form-group">
								<label>Category</label>
								<select id="categorytraining" class="form-control">
									<option value=""></option>
									@foreach($tb_category as $dt)
										<option value="{{$dt->category}}">{{$dt->category}}</option>
									@endforeach
								</select>
								<input type="hidden" id="levelparticipant" class="form-control" value="Leader Up">
							</div>
							<!-- <div class="form-group">
								<label>Level Participant</label>
								<input type="text" id="levelparticipant" class="form-control">
							</div> -->
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
							<button type="button" class="btn btn-success pull-right" id="simpan" data-dismiss="modal">Save</button>
						</div>
					</form>
			</div>

			<!-- /.modal-content -->
		</div>
	<!-- /.modal-dialog -->
	</div>

	<div class="modal fade" id="modal-delete">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Delete Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Delete : <b id="delname1"></b> ?
					<input type="hidden" id="delid1">
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
				'pagingType'  : 'simple_numbers',
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
	<script type="text/javascript">
		// Periode
			$('body').on("change",".pilihan",function(){
				var category=document.getElementById('category').value;
				var category2=document.getElementById('category2').value;
				window.location.href="/Training/List/"+category+"/"+category2;
			});
		// Periode End
		// Form
			$(document).on('click', '.form', function() {
				$('#idComponent').val($(this).data('idtraininglist'));
				$('#trainingname').val($(this).data('trainingname'));
				$('#skilltype').val($(this).data('idtype'));
				$('#categorytraining').val($(this).data('categorytraining'));
				$('#levelparticipant').val($(this).data('levelparticipant'));
				$('#modal-form').modal('show');
			});

		// Form End
		// Delete Data
			$(document).on('click', '.delete-modal', function() {
				$('#delid1').val($(this).data('delid'));
				$('#delname1').text($(this).data('delname'));
				$('#modal-delete').modal('show');
			});
			$('.modal-footer').on('click', '.delete', function() {
				var x=$('#delid1').val();
				window.location.href='/Training/Delete/List/'+x;
			});
		// Delete End
	</script>
	<script>
		$(document).on('click', '#simpan', function() {
			var idcomponent=$('#idComponent').val();
			var trainingname=$('#trainingname').val();
			var skilltype=$('#skilltype').val();
			var categorytraining=$('#categorytraining').val();
			var levelparticipant=$('#levelparticipant').val();

			$.ajaxSetup({
				type:"POST",
				url: "/Training/Simpan/List",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:{idcomponent:idcomponent,trainingname:trainingname,skilltype:skilltype,categorytraining:categorytraining,levelparticipant:levelparticipant},
				success: function(respond){
					if(respond=='Sukses'){
						location.reload();
					}else{
						// alert(respond);
						location.reload();
					}
				}
			})
		});

	</script>
@endsection
