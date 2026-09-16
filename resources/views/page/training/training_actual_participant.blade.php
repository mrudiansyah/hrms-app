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
			cursor: pointer;
		}

		#tables th {
			border-top: 2px solid #999;
			border-bottom: 2px solid #999;
		}

		#tables tbody tr:hover {
			cursor: pointer;
		}

		#table2 th {
			border-top: 2px solid #999;
			border-bottom: 2px solid #999;
		}

		#table2 tbody tr:hover {
			cursor: pointer;
		}

		#table3 th {
			border-top: 2px solid #999;
			border-bottom: 2px solid #999;
		}

		#table3 tbody tr:hover {
			cursor: pointer;
		}

		#table4 th {
			border-top: 2px solid #999;
			border-bottom: 2px solid #999;
		}

		#table4 tbody tr:hover {
			cursor: default;
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
				<div class="col-lg-5 col-md-12 col-xs-12">
					<div class="box box-primary" style="background:#FFF;">
						<div class="box-header">
							<i class="fa fa-list"></i>
							<h3 class="box-title">Training Actual</h3>
							<div class="box-tools pull-right">
								<a href="/Training/Actuals/0/0" title="Schedule" type="button"
									class="btn btn-default btn-xs"><i class="fa fa-angle-double-left"></i> &nbsp; Back</a>
							</div>
						</div>
						<div class="box-body" style="overflow-x: scroll;">
							<table id="table2" class="table table-hover">
								<thead>
									<tr>
										<th style="width:30px;">No</th>
										<th>Training Name</th>
										<th>Category</th>
									</tr>
								</thead>
								<tbody>
									<?php $no = 0;?>
									@foreach($tb_training_actual as $dt)
																	<tr>
																		<td><?php $no++;
										echo $no;?></td>
																		<td>{{$dt->training_name}}</td>
																		<td>{{$dt->skill_type}}
																			<?php 
																				echo date('H:i', strtotime($dt->start_aktual));
										echo "~";
										echo date('H:i', strtotime($dt->finish_aktual));
																			?>
																		</td>
																	</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						<!-- /.box-body -->
					</div>
					<div class="box box-info" style="background:#FFF;">
						<div class="box-header">
							<i class="fa fa-folder-o"></i>
							<h3 class="box-title">Supporting Document</h3>
							<div class="box-tools pull-right">
								&nbsp;
							</div>
						</div>
						<div class="box-body" style="overflow-x: scroll;">
							<table id="table4" class="table table-hover">
								<thead>
									<tr>
										<th style="width:30px;">No</th>
										<th>File Name</th>
									</tr>
								</thead>
								<tbody id="supporting">
									<?php $no = 0;?>
									@foreach($tb_related_document as $dt)
																	<tr>
																		<td><?php $no++;
										echo $no;?></td>
																		<td>
																			{{$dt->file_name}}
																			<div class="pull-right">
																				<a href="/ESS/Document/Download/{{$dt->id_doc}}" title="Download"
																					type="button" class="btn btn-info btn-xs"><i
																						class="fa fa-download"></i></a>
																			</div>
																		</td>
																	</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						<!-- /.box-body -->
					</div>
					<div class="box box-warning" style="background:#FFF;">
						<div class="box-header">
							<i class="fa fa-question-circle"></i>
							<h3 class="box-title">Supporting Test</h3>
							<div class="box-tools pull-right">
								<!-- <button type="button" class="btn btn-warning btn-xs test"><i class="fa fa-plus"></i> &nbsp;Add New</button> -->
							</div>
						</div>
						<div class="box-body" style="overflow-x: scroll;">
							<table id="table5" class="table table-hover">
								<thead>
									<tr>
										<th style="width:30px;">No</th>
										<th>Test Name</th>
										<th>Passing Grade</th>
									</tr>
								</thead>
								<tbody id="supporting">
									<?php $no = 0;?>
									@foreach($tb_related_test as $dt)
																	<tr>
																		<td><?php $no++;
										echo $no;?></td>
																		<td>{{$dt->test_name}}</td>
																		<td>
																			{{$dt->passing_grade}}
																		</td>
																	</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						<!-- /.box-body -->
					</div>
				</div>
				<div class="col-lg-7 col-md-12 col-xs-12">
					<div class="box box-success" style="background:#FFF;">
						<div class="box-header">
							<i class="fa fa-user"></i>
							<h3 class="box-title">Participants</h3>
							<div class="box-tools pull-right">
								<a href="/Training/Actual/{{$id_training}}" id="savechange"><button type="button"
										class="btn btn-info btn-xs"><i class="fa fa-floppy-o"></i> &nbsp;Save
										Change</button></a>
								@if($in_class == 1)
									<button type="button" class="btn btn-success btn-xs form"><i class="fa fa-plus"></i>
										&nbsp;Add New</button>
								@endif
							</div>
						</div>
						<div class="box-body">
							<div class="pull-right">
								&nbsp;
							</div>
						</div>
						<div class="box-body" style="overflow-x: scroll;">
							<table id="tables" class="table table-hover">
								<thead>
									<tr>
										<th style="width:30px;">No</th>
										<th>NIK</th>
										<th>Name</th>
										<th>Department</th>
										<th>Position</th>
									</tr>
								</thead>
								<tbody id="konten">
									<?php $no = 0;?>
									@foreach($tb_training_participant as $dt)
																	<tr>
																		<td><?php $no++;
										echo $no;?></td>
																		<td>{{$dt->NIK}}</td>
																		<td>{{$dt->nama_karyawan}}</td>
																		<td>{{$dt->department}}</td>
																		<td>
																			{{$dt->jabatan}}
																			<div class="pull-right">
																				@if($dt->free_test == '' && $in_class == 1)
																					<button title="Delete" type="button"
																						class="delete-modal btn btn-danger btn-xs" data-delid="{{$dt->id}}"
																						data-delname="{{$dt->nama_karyawan}}"><i
																							class="fa fa-trash"></i></button>
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
					<div class="box box-primary" style="background:#FFF;">
						<div class="box-header">
							<i class="fa fa-graduation-cap"></i>
							<h3 class="box-title">Evaluation</h3>
							<div class="box-tools pull-right">
								&nbsp;
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
										<th>NIK</th>
										<th>Name</th>
										<th>
											Free Test
											<div class="box-tools pull-right">
												<a href="/Training/Monitor/Free/{{$id_training}}" title="Monitoring"
													type="button" class="btn btn-primary btn-xs"><i
														class="fa fa-tv"></i></a>
											</div>
										</th>
										<th>
											Post Test
											<div class="box-tools pull-right">
												<a href="/Training/Monitor/Post/{{$id_training}}" title="Monitoring"
													type="button" class="btn btn-primary btn-xs"><i
														class="fa fa-tv"></i></a>
											</div>
										</th>
									</tr>
								</thead>
								<tbody id="konten">
									<?php $no = 0;?>
									@foreach($tb_training_participant as $dt)
																	<tr>
																		<td><?php $no++;
										echo $no;?></td>
																		<td>{{$dt->NIK}}</td>
																		<td>{{$dt->nama_karyawan}}</td>
																		<td>{{$dt->free_test}}</td>
																		<td>
																			{{$dt->post_test}}
																			<div class="pull-right">
																				&nbsp;
																			</div>
																		</td>
																	</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						<!-- /.box-body -->
					</div>
					<div class="box box-warning" style="background:#FFF;">
						<div class="box-header">
							<i class="fa fa-tasks"></i>
							<h3 class="box-title">Training Assignment</h3>
							<div class="box-tools pull-right">
								<button type="button" class="btn btn-warning btn-xs assignment"><i class="fa fa-plus"></i> &nbsp;Add New</button>
							</div>
						</div>
						<div class="box-body" style="overflow-x: scroll;">
							<table id="table6" class="table table-hover">
								<thead>
									<tr>
										<th style="width:30px;">No</th>
										<th>Assignment</th>
										<th>Due Date</th>
									</tr>
								</thead>
								<tbody id="supporting">
									<?php $assign=0;?>
									@foreach($tb_training_assignment as $dt)
									<tr>
										<td><?php $assign++;echo $assign;?></td>
										<td>{{$dt->assignment}}</td>
										<td>
											{{$dt->duedate}}
											<div class="pull-right">
												<button title="Edit" type="button" class="assignment-edit btn btn-primary btn-xs" data-id="{{$dt->id}}" data-assignment="{{$dt->assignment}}" data-duedate="{{$dt->duedate}}"><i class="fa fa-edit"></i></button>
												<button title="Delete" type="button" class="assignment-delete btn btn-danger btn-xs" data-id="{{$dt->id}}" data-assignment="{{$dt->assignment}}"><i class="fa fa-trash"></i></button>
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
						<b>FORM TRAINING PARTICIPANT</b>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						<input type="hidden" id="idComponent" class="form-control">
						<div class="form-group">
							<label>Employee</label>
							<select id="idemployee" class="form-control"></select>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
						<button type="button" class="btn btn-success pull-right" id="simpan"
							data-dismiss="modal">Select</button>
					</div>
				</form>
			</div>

			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>

	<div class="modal fade" id="modal-assignment">
		<div class="modal-dialog box box-warning" style="width:400px;">
			<div class="modal-content">
				<form id="assignment-form">
					{{ csrf_field() }}
					<div class="modal-header">
						<b id="assignment-modal-title">ADD TRAINING ASSIGNMENT</b>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<input type="hidden" id="assignment-id" name="id_assignment">
						<input type="hidden" name="id_training_actual" value="{{$id_training}}">
						<div class="form-group">
							<label>Assignment</label>
							<textarea class="form-control" id="assignment-name" name="assignment" rows="3" required></textarea>
						</div>
						<div class="form-group">
							<label>Due Date</label>
							<input type="date" class="form-control" id="assignment-due-date" name="duedate" required>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-warning pull-right">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modal-delete">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Delete Participant</h4>
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
				'paging': false,
				'lengthChange': true,
				'searching': false,
				'ordering': true,
				'info': true,
				"pageLength": 10,
				'autoWidth': false,
			})
		})
		$(function () {
			$('#table3').DataTable({
				'paging': false,
				'lengthChange': true,
				'searching': false,
				'ordering': true,
				'info': true,
				"pageLength": 10,
				'autoWidth': false,
			})
		})
		$(function () {
			$('#table4').DataTable({
				'paging': false,
				'lengthChange': true,
				'searching': false,
				'ordering': true,
				'info': true,
				"pageLength": 10,
				'autoWidth': false,
			})
		})
		$(function () {
			$('#table5').DataTable({
				'paging': false,
				'lengthChange': true,
				'searching': false,
				'ordering': true,
				'info': true,
				"pageLength": 10,
				'autoWidth': false,
			})
		})
		$(function () {
			$('#table6').DataTable({
				'paging': false,
				'lengthChange': true,
				'searching': false,
				'ordering': true,
				'info': true,
				"pageLength": 10,
				'autoWidth': false,
			})
		})
	</script>
	<script>
		$(document).ready(function () {
			document.getElementById('savechange').style.display = "none";
			var table = $('#tables').DataTable({
				'paging': true,
				'lengthChange': false,
				'searching': true,
				'ordering': true,
				'info': true,
				"pageLength": 10,
				'autoWidth': false,
				"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]
				//"iDisplayLength": 50
				//dom: 'Bfrtip',buttons: ['print']
			});

			new $.fn.dataTable.Buttons(table, {
				buttons: ['copy', 'excel', 'print']
			});

			table.buttons(0, null).container().prependTo(
				table.table().container()
			);
		});


	</script>
	<script>
		window.setTimeout(function () {
			$(".alert").fadeTo(500, 0).slideUp(500, function () {
				$(this).remove();
			});
		}, 5000);
	</script>
	<script type="text/javascript">
		// Form
		$(document).on('click', '.form', function () {
			var idtraining = "{{$id_training}}";

			$.ajaxSetup({
				type: "POST",
				url: "/Training/Update/Participant",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data: { idtraining: idtraining },
				success: function (respond) {
					//alert(respond);
					$("#idemployee").html(respond);
				}
			})
			$('#modal-form').modal('show');
		});
		// Form End
		// Delete Data
		$(document).on('click', '.delete-modal', function () {
			$('#delid1').val($(this).data('delid'));
			$('#delname1').text($(this).data('delname'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function () {
			var x = $('#delid1').val();
			var idtraining = "{{$id_training}}";

			$.ajaxSetup({
				type: "POST",
				url: "/Training/Delete/Actual/Participant",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data: { idtraining: idtraining, id: x },
				success: function (respond) {
					$("#konten").html(respond);
				}
			})
		});
		// Delete End
	</script>
	<script>
		$(document).on('click', '#simpan', function () {
			var idtraining = "{{$id_training}}";
			var idemployee = $('#idemployee').val();

			$.ajaxSetup({
				type: "POST",
				url: "/Training/Simpan/Actual/Participant",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data: { idtraining: idtraining, idemployee: idemployee },
				success: function (respond) {
					if (respond == 'Failed, Employee already Exixts' || respond == 'No Action' || respond == 'Masuk') {
						alert(respond)
					} else {
						$("#konten").html(respond);
						document.getElementById('savechange').style.display = "inline";
					}
				}
			})
		});
		$(document).on('click', '#simpantest', function () {
			var idtraining = "{{$id_training}}";
			var idtest = $('#idtest').val();

			$.ajaxSetup({
				type: "POST",
				url: "/Training/Simpan/Supporting/Test",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data: { idtraining: idtraining, idtest: idtest },
				success: function (respond) {
					if (respond == 'Sukses') {
						location.reload();
					} else {
						alert(respond);
					}
				}
			})
		});
		$(document).on('click', '.assignment', function () {
			$('#assignment-form')[0].reset();
			$('#assignment-id').val('');
			$('#assignment-modal-title').text('ADD TRAINING ASSIGNMENT');
			$('#modal-assignment').modal('show');
		});

		$(document).on('click', '.assignment-edit', function () {
			var button = $(this);
			$('#assignment-id').val(button.data('id'));
			$('#assignment-name').val(button.data('assignment'));
			$('#assignment-due-date').val(String(button.data('duedate')).substring(0, 10));
			$('#assignment-modal-title').text('EDIT TRAINING ASSIGNMENT');
			$('#modal-assignment').modal('show');
		});

		$('#assignment-form').on('submit', function (event) {
			event.preventDefault();
			$.ajax({
				url: '/Training/Simpan/Assignment',
				type: 'POST',
				data: $(this).serialize(),
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: function () {
					window.location.reload();
				},
				error: function (xhr) {
					var message = xhr.responseJSON && xhr.responseJSON.message
						? xhr.responseJSON.message
						: 'Assignment gagal disimpan.';
					alert(message);
				}
			});
		});

		$(document).on('click', '.assignment-delete', function () {
			var button = $(this);
			if (!confirm('Hapus assignment ' + button.data('assignment') + '?')) {
				return;
			}
			$.ajax({
				url: '/Training/Delete/Assignment',
				type: 'POST',
				data: {
					id_assignment: button.data('id'),
					id_training_actual: "{{$id_training}}"
				},
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: function () {
					window.location.reload();
				},
				error: function () {
					alert('Assignment gagal dihapus.');
				}
			});
		});

	</script>
@endsection