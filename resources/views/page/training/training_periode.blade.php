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
							<button type="button" class="btn btn-primary btn-xs form"><i class="fa fa-plus"></i> &nbsp;Add Schedule</button>
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
									<th>Training</th>
									<th>Schedule Type</th>
									<th>Trainer</th>
									<th>Level</th>
									<th>Location</th>
									<th>Department</th>
									<th>Date</th>
									<th>Plan</th>
									<th>Invite</th>
									<th>Actual</th>
									<th>Status</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								@foreach($tb_training_schedule as $dt)
								<?php
									if($dt->skill_code=='BS')$warna='primary';
									elseif($dt->skill_code=='SS')$warna='success';
									elseif($dt->skill_code=='MS')$warna='warning';
									else $warna='default';
								?>
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td>{{$dt->training_name}} <label class="label label-{{$warna}}">{{$dt->skill_code}}</label></td>
									<td>{{$dt->schedule_type}}</td>
									<td title="Backup Trainer: {{$dt->nara_sumber_backup}}">{{$dt->nara_sumber}}</td>
									@php
										$levelNames = $training_level_names_by_schedule->get($dt->id, []);
									@endphp
									<td>
										@if(count($levelNames) > 1)
											<details>
												<summary style="cursor:pointer;color:blue;"><u>Multi_Levels</u></summary>
												{{implode(', ', $levelNames)}}
											</details>
										@else
											{{implode(', ', $levelNames)}}
										@endif
									</td>
									<td>{{$dt->home_line}}</td>
									<td>{{$dt->department}}</td>
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
										@if(!empty($dt->tanggal) && \Carbon\Carbon::parse($dt->tanggal)->lt(today())&&$dt->actual_qty==0)
											<label class="label label-danger">Overdue</label>
										@elseif($dt->plan_qty==0)
											<label class="label label-warning">Draft</label>
										@elseif($dt->actual_qty==0)
											<label class="label label-primary">OnPlan</label>
										@elseif($dt->actual_qty>0)
											<label class="label label-success">Conducted</label>
										@endif
									</td>
									<td style="width:100px;">
										<div class="pull-right">
											@if(\Carbon\Carbon::parse($dt->tanggal)->gt(today()))
												<a href="/Training/Plan/{{$dt->id}}" title="Participant" type="button" class="participant btn btn-primary btn-xs"><i class="fa fa-folder-o"></i></a>
												@if($dt->plan_qty == 0)
													<button title="Edit" type="button" class="form btn btn-success btn-xs" data-idtrainingplan="{{$dt->id}}" data-idtraining="{{$dt->id_training}}" data-scheduletype="{{$dt->schedule_type}}" data-levels="{{implode(',', $training_levels_by_schedule->get($dt->id, []))}}" data-narasumber="{{$dt->nara_sumber}}" data-narasumberbackup="{{$dt->nara_sumber_backup}}" data-tanggal="{{$dt->tanggal}}" data-start="{{$dt->start}}" data-finish="{{$dt->finish}}" data-draftqty="{{$dt->draft_qty}}" data-weeknumber="{{$dt->week_number}}" data-homeline="{{$dt->home_line}}" data-department="{{$dt->department}}"><i class="fa fa-edit"></i></button>
													<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="{{$dt->id}}" data-delname="{{$dt->training_name}}"><i class="fa fa-trash"></i></button>
												@else
													<button title="Edit" type="button" class="form btn btn-success btn-xs" disabled><i class="fa fa-edit"></i></button>
													<button title="Delete" type="button" class="btn btn-danger btn-xs" disabled><i class="fa fa-trash"></i></button>
												@endif
											@endif
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
		<div class="modal-dialog modal-lg" style="width:700px;max-width:95%;">
			<div class="modal-content">
					<form>
					{{ csrf_field() }}
						<div class="modal-header">	
							<b>FORM TRAINING SCHEDULE</b>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<div class="row">
							<input type="hidden" id="idComponent" class="form-control">
							<div class="col-md-12" id="reschedule-option-group" style="display:none;">
								<div class="form-group">
									<label>
										<input type="checkbox" id="is_reschedule" value="1">
										Reschedule this schedule
									</label>
									<p class="help-block">A new schedule will be created with the existing details and invitations. Only the date and time can be changed.</p>
								</div>
							</div>
							<div class="col-md-6 col-sm-6">
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
							</div>
							<div class="col-md-6 col-sm-6">
							<div class="form-group">
								<label>Level</label>
								<select id="levels" class="form-control selectpicker" multiple data-live-search="true" title="Select level">
									@foreach($tb_level as $level)
										<option value="{{$level->id}}">{{$level->nama_level}}</option>
									@endforeach
								</select>
							</div>
							</div>
							<div class="col-md-6 col-sm-6">
								<div class="form-group">
									<label>Schedule Type</label>
									<select id="schedule_type" class="form-control" required>
										<option value="Annual Plan">Annual Plan</option>
										<option value="UnPlan">UnPlan</option>
										<option value="Reschedule">Reschedule</option>
									</select>
								</div>
							</div>
							<div class="col-md-6 col-sm-6">
							<div class="form-group opsiedit">
								<label>Trainer Name</label>
								<input type="text" id="narasumber" class="form-control">
							</div>
							</div>
							<div class="col-md-6 col-sm-6">
							<div class="form-group opsiedit">
								<label>Trainer Name (Backup)</label>
								<input type="text" id="narasumber_backup" class="form-control">
							</div>
							</div>
							<div class="col-md-6 col-sm-6">
							<div class="form-group opsiedit">
								<label>Department</label>
								<select id="department" class="form-control">
									<option value=""></option>
									@foreach($tb_department as $dt)
										<option value="{{$dt->dept_name}}">{{$dt->dept_name}}</option>
									@endforeach
								</select>
							</div>
							</div>
							<div class="col-md-6 col-sm-6">
								<div class="form-group opsiedit">
									<label>Date</label>
									<input type="date" id="tanggal" class="form-control">
								</div>
							</div>
							<div class="col-md-6 col-sm-6">
								<div class="form-group">
									<label>Week Number</label>
									<input type="number" min="1" max="53" id="weeknumber" class="form-control">
								</div>
							</div>
							<div class="col-md-6 col-sm-6">
								<div class="form-group opsiedit">
									<label>Start</label>
									<input type="time" id="start" class="form-control">
								</div>
							</div>
							<div class="col-md-6 col-sm-6">
								<div class="form-group opsiedit">
									<label>Finish</label>
									<input type="time" id="finish" class="form-control">
								</div>
							</div>
							<div class="col-md-6 col-sm-6">
								<div class="form-group opsiedit">
									<label>Location</label>
									<select id="homeline" class="form-control">
										<option value=""></option>
										<option value="Internal">Internal</option>
										<option value="Inhouse">Inhouse</option>
										<option value="External">External</option>
									</select>
								</div>
							</div>
							<div class="col-md-6 col-sm-6">
								<div class="form-group opsiedit">
									<label>Qty Plan Participant</label>
									<input type="number" id="draftqty" class="form-control">
								</div>
							</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
							<button type="button" class="btn btn-success pull-right" id="simpan">Save</button>
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
			$(document).on('change', '#tanggal', function() {
				var tanggal = $(this).val();
				if (tanggal) {
					var dateParts = tanggal.split('-');
					var date = new Date(Date.UTC(dateParts[0], dateParts[1] - 1, dateParts[2]));
					date.setUTCDate(date.getUTCDate() + 4 - (date.getUTCDay() || 7));
					var yearStart = new Date(Date.UTC(date.getUTCFullYear(), 0, 1));
					var weekNumber = Math.ceil((((date - yearStart) / 86400000) + 1) / 7);
					$('#weeknumber').val(weekNumber);
				} else {
					$('#weeknumber').val('');
				}
			});

			$(document).on('click', '.form', function() {
				var button = $(this);
				var isEditing = Boolean(button.data('idtrainingplan'));
				$('#idComponent').val(button.data('idtrainingplan') || '');
				$('#idtraining').val(button.data('idtraining') || '').trigger('change');
				var originalScheduleType = button.data('scheduletype') || 'Annual Plan';
				$('#schedule_type').data('original-value', originalScheduleType).val(originalScheduleType);
				jQuery_3_2_1('#levels').selectpicker('val', (button.data('levels') || '').toString().split(',').filter(Boolean));
				$('#narasumber').val(button.data('narasumber') || '');
				$('#narasumber_backup').val(button.data('narasumberbackup') || '');
				$('#tanggal').val(button.data('tanggal') || '');
				$('#start').val(button.data('start') || '');
				$('#finish').val(button.data('finish') || '');
				$('#draftqty').val(button.data('draftqty') || '');
				$('#weeknumber').val(button.data('weeknumber') || '');
				$('#homeline').val(button.data('homeline') || '');
				$('#department').val(button.data('department') || '');
				$('#is_reschedule').prop('checked', false);
				$('#reschedule-option-group').toggle(isEditing);
				$('#schedule_type option[value="Reschedule"]').prop('disabled', true);
				$('#is_reschedule').trigger('change');
				$('#modal-form').modal('show');
			});

			$(document).on('change', '#is_reschedule', function() {
				var isReschedule = $(this).is(':checked');
				var rescheduleLockedFields = '#idtraining,#levels,#narasumber,#narasumber_backup,#department,#weeknumber,#homeline,#draftqty';
				$(rescheduleLockedFields).prop('disabled', isReschedule);
				$('#schedule_type option[value="Reschedule"]').prop('disabled', !isReschedule);
				$('#schedule_type')
					.val(isReschedule ? 'Reschedule' : ($('#schedule_type').data('original-value') || 'Annual Plan'))
					.prop('disabled', isReschedule);
				$('#tanggal,#start,#finish').prop('disabled', false);
				jQuery_3_2_1('#idtraining,#levels').selectpicker('refresh');
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
			var is_reschedule=$('#is_reschedule').is(':checked') ? 1 : 0;
			var idtraining=$('#idtraining').val();
			var schedule_type=$('#schedule_type').val();
			var levels=$('#levels').val() || [];
			var narasumber=$('#narasumber').val();
			var narasumber_backup=$('#narasumber_backup').val();
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
				data:{idcomponent:idcomponent,is_reschedule:is_reschedule,idtraining:idtraining,schedule_type:schedule_type,levels:levels,narasumber:narasumber,narasumber_backup:narasumber_backup,tanggal:tanggal,start:start,finish:finish,draftqty:draftqty,weeknumber:weeknumber,department:department,homeline:homeline},
				success: function(respond){
					if(respond=='Sukses'){
						location.reload();
					}else{
						alert(respond);
						location.reload();
					}
				},
				error: function(xhr){
					alert(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to save training schedule.');
				}
			})
		});

	</script>
@endsection
