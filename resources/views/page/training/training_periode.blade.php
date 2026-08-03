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
			<div class="col-lg-12 col-md-12 col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-list"></i>
						<h3 class="box-title">Training Schedule</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-primary btn-xs form"><i class="fa fa-plus"></i> &nbsp;Add New</button>
							<a href="/EMS/Training/Periode/Refresh/{{$periode}}" type="button" class="btn btn-info btn-xs"><i class="fa fa-refresh"></i> &nbsp;Refresh</a>
							<!-- <a href="/EMS/Training/Plan/Print/{{$tahun}}" type="button" class="btn btn-success btn-xs"><i class="fa fa-download"></i> &nbsp;Download All</a> -->
						</div>
					</div>
					<div class="box-body">
						<div class="pull-right">
							<input type="month" id="periode" class="form-control" value="{{$periode}}">	
						</div>
					</div>
					<div class="box-body" style="overflow-x: scroll;">
						<table id="tables" class="table table-hover">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th>Training Name</th>
									<th>Category</th>
									<th>Trainer</th>
									<th>Location</th>
									<th>Department</th>
									<th>Periode</th>
									<th>Week</th>
									<th>Date</th>
									<th>Plan</th>
									<th>Invite</th>
									<th>Actual</th>
									<th>&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								@foreach($tb_training_schedule as $dt)
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td>{{$dt->training_name}}</td>
									<td>{{$dt->skill_type}}</td>
									<td>{{$dt->nara_sumber}}</td>
									<td>{{$dt->home_line}}</td>
									<td>{{$dt->department}}</td>
									<td>{{$dt->periode}}</td>
									<td>{{$dt->week_number}}</td>
									<td>{{$dt->tanggal}}
										<?php 
											if(($dt->start!=''&&$dt->finish!='')){
												//echo date('H:i',strtotime($dt->start));
											}
										?>
									</td>
									<td>{{$dt->draft_qty}}</td>
									<td>{{$dt->plan_qty}}</td>
									<td>{{$dt->actual_qty}}</td>
									<td>
										<div class="pull-right">
											<a href="/Training/Plan/{{$dt->id}}" title="Participant" type="button" class="participant btn btn-primary btn-xs"><i class="fa fa-folder-o"></i></a>
											<button title="Edit" type="button" class="form btn btn-success btn-xs" data-idtrainingplan="{{$dt->id}}" data-idtraining="{{$dt->id_training}}" data-narasumber="{{$dt->nara_sumber}}" data-tanggal="{{$dt->tanggal}}" data-start="{{$dt->start}}" data-finish="{{$dt->finish}}" data-draftqty="{{$dt->draft_qty}}" data-weeknumber="{{$dt->week_number}}" data-homeline="{{$dt->home_line}}" data-department="{{$dt->department}}"><i class="fa fa-edit"></i></button>
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
							<b>FORM TRAINING SCHEDULE</b>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<input type="hidden" id="idComponent" class="form-control">
							<div class="form-group">
								<label>Training</label>
								<select id="idtraining" class="form-control selectpicker" data-live-search="true">
								<!-- <select id="idtraining" class="form-control"> -->
									<option value=""></option>
									@foreach($tb_training_list as $dt)
										<option value="{{$dt->id}}">{{$dt->training_name}}</option>
									@endforeach

								</select>
							</div>
							<div class="form-group opsiedit">
								<label>Trainer Name</label>
								<input type="text" id="narasumber" class="form-control">
							</div>
							<div class="form-group opsiedit">
								<label>Department</label>
								<select id="department" class="form-control">
									<option value=""></option>
									@foreach($tb_department as $dt)
										<option value="{{$dt->dept_name}}">{{$dt->dept_name}}</option>
									@endforeach
								</select>
							</div>
							<div class="row">
								<div class="col-lg-6 col-md-6 col-xs-12">
									<div class="form-group opsiedit">
										<label>Date</label>
										<input type="date" id="tanggal" class="form-control">
									</div>
								</div>
								<div class="col-lg-6 col-md-6 col-xs-12">
									<div class="form-group">
										<label>Week Number</label>
										<input type="number" min="1" max="4" id="weeknumber" class="form-control">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-6 col-md-6 col-xs-12">
									<div class="form-group opsiedit">
										<label>Start</label>
										<input type="time" id="start" class="form-control">
									</div>
								</div>
								<div class="col-lg-6 col-md-6 col-xs-12">
									<div class="form-group opsiedit">
										<label>Finish</label>
										<input type="time" id="finish" class="form-control">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-6 col-md-6 col-xs-12">
									<div class="form-group opsiedit">
										<label>Location</label>
										<select id="homeline" class="form-control">
											<option value=""></option>
											<option value="Internal">Internal</option>
											<option value="External">External</option>
										</select>
									</div>
								</div>
								<div class="col-lg-6 col-md-6 col-xs-12">
									<div class="form-group opsiedit">
										<label>Qty Plan Participant</label>
										<input type="number" id="draftqty" class="form-control">
									</div>
								</div>
							</div>
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
			$('body').on("change","#periode",function(){
				var periode=document.getElementById('periode').value;
				if(periode=='') var periode=0;
				window.location.href="/Training/Periode/"+periode;
			});
		// Periode End
		// Form
			$(document).on('click', '.form', function() {
				$('#idComponent').val($(this).data('idtrainingplan'));
				$('#idtraining').val($(this).data('idtraining'));
				$('#narasumber').val($(this).data('narasumber'));
				$('#tanggal').val($(this).data('tanggal'));
				$('#start').val($(this).data('start'));
				$('#finish').val($(this).data('finish'));
				$('#draftqty').val($(this).data('draftqty'));
				$('#weeknumber').val($(this).data('weeknumber'));
				$('#homeline').val($(this).data('homeline'));
				$('#department').val($(this).data('department'));
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
				window.location.href='/Training/Delete/Plan/'+x;
			});
		// Delete End
	</script>
	<script>
		$(document).on('click', '#simpan', function() {
			var idcomponent=$('#idComponent').val();
			var idtraining=$('#idtraining').val();
			var narasumber=$('#narasumber').val();
			var tanggal=$('#tanggal').val();
			var start=$('#start').val();
			var finish=$('#finish').val();
			var draftqty=$('#draftqty').val();
			var weeknumber=$('#weeknumber').val();
			var homeline=$('#homeline').val();
			var department=$('#department').val();

			$.ajaxSetup({
				type:"POST",
				url: "/Training/Simpan/Plan",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:{idcomponent:idcomponent,idtraining:idtraining,narasumber:narasumber,tanggal:tanggal,start:start,finish:finish,draftqty:draftqty,weeknumber:weeknumber,department:department,homeline:homeline},
				success: function(respond){
					if(respond=='Sukses'){
						location.reload();
					}else{
						alert(respond);
						location.reload();
					}
				}
			})
		});

	</script>
@endsection
