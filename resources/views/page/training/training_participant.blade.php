@extends('layouts/home')
@section('Contents')
	<meta name="csrf-token" content="{{ csrf_token() }}">
	@foreach($tb_training_participant as $dt)
		<?php if ($dt->free_test != '')
				$doc = '1';
			else
				$doc = 0;?>
	@endforeach
	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-md-12" id="prev-doc"></div>
			<div class="col-lg-4 col-md-6 col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-list"></i>
						<h3 class="box-title">Training Actual</h3>
						<div class="box-tools pull-right">
							<a href="/Training/Invitation" title="Schedule" type="button" class="btn btn-default btn-xs"><i
									class="fa fa-angle-double-left"></i> &nbsp; Kembali</a>
						</div>
					</div>
					<div class="box-body">
						<table class="table table-hover">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th>Training Name</th>
									<th>Category</th>
									<th>Date</th>
								</tr>
							</thead>
							<tbody>
								<?php $no = 0;?>
								@foreach($tb_training_actual as $dt)
									<tr>
										<td><?php $no++; echo $no;?></td>
										<td>{{$dt->training_name}}</td>
										<td>{{$dt->skill_type}}</td>
										<td><?php echo date('d-M-Y H:i', strtotime($dt->tanggal_aktual . ' ' . $dt->start_aktual)) . ' ~ ' . date('H:i', strtotime($dt->tanggal_aktual . ' ' . $dt->finish_aktual));?>
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
						<i class="fa fa-folder-o"></i>
						<h3 class="box-title">Materi</h3>
						<div class="box-tools pull-right">
							<div class="box-tools">
								<a href="/FreeTest/{{$id_participant}}" <?php if ($data['pre_test'] != '') echo "disabled";?>
									title="Pre Test" type="button" class="btn btn-primary btn-xs">Start Pre Test
								</a>
							</div>
						</div>
					</div>
					<div class="box-body">
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
										<td><?php $no++; echo $no;?></td>
										<td>
											@if($dt->file_name == '')
												{{$dt->document_name}}
											@else
												{{$dt->file_name}}
											@endif
											<input type="hidden" name="doc_id" id="doc_id" value={{ $doc }}>
											@if($doc == 1)
												<div class="pull-right">
													@php
														$extensi = strtolower(pathinfo($dt->file_name, PATHINFO_EXTENSION));
													@endphp
													@if($dt->related_link)
														<a href="{{$dt->related_link}}" target="_blank" rel="noopener noreferrer" title="Open Link" class="btn btn-primary btn-xs">Lihat Materi</a>
													@elseif(in_array($extensi, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']))
														<button class="btn btn-primary btn-xs" id="preview" onclick="GetPreview({{ $dt->id_doc }})">Lihat Materi</button>
													@elseif(in_array($extensi, ['mp4']))
														<a href="{{ $dt->related_link }}"
														onclick="window.open(this.href, '_blank', 'width=800,height=700,scrollbars=yes,resizable=yes,noopener,noreferrer'); return false;"
														title="Preview"
														class="btn btn-primary btn-xs">
															Lihat Materi
														</a>
													@else
														<a href="" target="_blank" title="Preview" type="button" class="btn btn-primary btn-xs" disabled>Lihat Materi</a>
													@endif
													@if($dt->file_name && $extensi != 'mp4')
														<a href="/Document/Download/{{$dt->id_doc}}" title="Download" type="button" class="btn btn-info btn-xs"><i class="fa fa-download"></i></a>
													@else
														<a href="#" title="Download unavailable" type="button" class="btn btn-info btn-xs disabled" aria-disabled="true"><i class="fa fa-download"></i></a>
													@endif
												</div>
											@endif
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-graduation-cap"></i>
						<h3 class="box-title">Evaluation</h3>
						<div class="box-tools pull-right">
							<div class="box-tools">
								<a href="/PostTest/{{$id_participant}}" <?php if ($data['pre_test'] == '' || ($data['post_test'] != '' && $data['grade_status'] == 1)) echo "disabled";?>
									title="Post Test" type="button" class="btn btn-primary btn-xs">Start
									Post Test
								</a>
							</div>
						</div>
					</div>
					<div class="box-body" style="overflow-x: scroll;">
						<table id="table3" class="table table-hover">
							<thead>
								<tr>
									<th>Nilai Pre Test</th>
									<th>Nilai Post Test</th>
									<th>Nilai Minimum</th>
									<th>Hasil Training</th>
								</tr>
							</thead>
							<tbody id="konten">
								<?php $no = 0;?>
								@foreach($tb_training_participant as $dt)
									<tr>
										<td>
											{{$dt->free_test}}
										</td>
										<td>
											{{$dt->post_test}}
											<?php if ($dt->progress == '1') echo "<i class='fa fa-angle-double-up' style='color:green;'></i>";?>
											<?php if ($dt->progress == '-1') echo "<i class='fa fa-angle-double-down' style='color:red;'></i>";?>
											<?php if ($dt->progress == '0') echo "<i class='fa fa-frown-o' style='color:blue;'></i>";?>
										</td>
										<td>{{$dt->passing_grade}}</td>
										<td>
											<input type="hidden" name="grade" id="grade" value="{{ $dt->grade_status }}">
											<input type="hidden" name="post_test" id="post_test"
												value="{{ $dt->post_test }}">
											<?php 
												if ($dt->grade_status == 1) echo "<label class='label label-success'>Lulus</label>";
												elseif ($dt->grade_status == 0 && $dt->post_test != '') echo "<label class='label label-danger'>Gagal</label>";
											?>
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
				<div class="box box-info" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-info"></i>
						<h3 class="box-title">Assignment</h3>
						<div class="box-tools pull-right">
							<div class="box-tools">
								&nbsp;
							</div>
						</div>
					</div>
					<div class="box-body" style="overflow-x: scroll;">
						<table id="table3" class="table table-hover">
							<thead>
								<tr>
									<th>No</th>
									<th>Assignment Description</th>
									<th>Due Date</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody id="konten">
								<?php $no = 0;?>
								@foreach($tb_assignment as $dt)
									<tr>
										<td>
											{{$no++}}
										</td>
										<td>
											{{$dt->assignment}}
										</td>
										<td>{{$dt->duedate}}</td>
										<td>
											@if($dt->assignment_status == 1)
												<label class='label label-success'>Completed</label>
											@else
												<label class='label label-warning'>Not Submitted</label>
											@endif
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


@endsection
@section('Scripts')
	<!-- page script Tabel-->
	<script type="text/Javascript">
				$(document).ready(function (e) {
					var grade = $("#grade").val();
					var post_test = $("#post_test").val();
					if(grade == 1){
						swal(
							"Success",
							"Your test is passed",
							"success",
						)
					}else if(grade == 0 && post_test != ''){
						swal(
							'Information',
							"You're not passed, Better luck next time",
							"warning",
						)		
					}else{
					}
				/* Get the documentElement (<html>) to display the page in fullscreen */
				var doc_id = $("#doc_id").val();
				if(doc_id > 0){
					// openFullscreen();
					$("#fullscreen").click();
				}
				/* View in fullscreen */

				$(function () {
					$('#table2').DataTable({
					'paging'      : false,
					'lengthChange': true,
					'searching'   : true,
					'ordering'    : true,
					'info'        : true,
					"pageLength"  : 10,
					'autoWidth'   : false,
					})
				})

			});
				function GetPreview(id_doc){
					var token =  $('meta[name="csrf-token"]').attr('content')
					var data = { _token : token , id_doc : id_doc}
					$.ajax({

						type: "POST",
						url: "{{ url('Training.DocPreview') }}",
						data: data,
						success: function (data) {
							$("#prev-doc").html(data);
							var elem = document.getElementById("prev-doc");
							openFullscreen(elem);
						}
					});
				}
				function openFullscreen(elem) {
					if (elem.requestFullscreen) {
						elem.requestFullscreen();
					} else if (elem.webkitRequestFullscreen) { /* Safari */
						elem.webkitRequestFullscreen();
					} else if (elem.msRequestFullscreen) { /* IE11 */
						elem.msRequestFullscreen();
					}
					}

					/* Close fullscreen */
					function closeFullscreen() {
					if (document.exitFullscreen) {
						document.exitFullscreen();
					} else if (document.webkitExitFullscreen) { /* Safari */
						document.webkitExitFullscreen();
					} else if (document.msExitFullscreen) { /* IE11 */
						document.msExitFullscreen();
					}
					}

					document.addEventListener('fullscreenchange', exitHandler);
					document.addEventListener('webkitfullscreenchange', exitHandler);
					document.addEventListener('mozfullscreenchange', exitHandler);
					document.addEventListener('MSFullscreenChange', exitHandler);

					function exitHandler() {
						if (!document.fullscreenElement && !document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
							$("#prev-doc").html("");

							///fire your event
						}
					}  
				</script>
	<script>
		window.setTimeout(function () {
			$(".alert").fadeTo(500, 0).slideUp(500, function () {
				$(this).remove();
			});
		}, 5000);

	</script>
	<script>
		document.addEventListener("contextmenu", function (e) {
			e.preventDefault();
		}, false);
		function previewDocs(id) {

		}
	</script>
@endsection