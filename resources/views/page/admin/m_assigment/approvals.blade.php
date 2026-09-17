@extends('layouts/admin')
@section('Contents')
   <!-- Contents -->
   <meta name="csrf-token" content="{{ csrf_token() }}">
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Form Assigment
				<small>for working on the day off</small>
			</h1>
			<ol class="breadcrumb">
				<li>
				<a href="#">
					<i class="fa fa-calendar"></i> 
					<?php 
						date_default_timezone_set('Asia/Jakarta');
						echo date('l, d M Y H:i');
					?>
				</a>
				</li>
			</ol>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-lg-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-calendar"></i>
						<h3 class="box-title">Approval</h3>
						<div class="pull-right">
							<a class="btn btn-app table2">
								<?php if (isset($qty_new) && $qty_new > 0) {
                                        echo "<span class='badge bg-red'>" . $qty_new . '</span>';
                                    } ?>
								<i class="fa fa-file-o"></i> New Form
							</a>
							<a class="btn btn-app table3">
								<i class="fa fa-check-square-o"></i> Complete
							</a>
						</div>
					</div>
					<div class="box-body">
					<div id="tabel2">	
						<table id="table2" class="table table-hover" style="min-width:100%;">
							<thead>
								<tr>
									<th style="width:50px;">No</th>
									<th style="width:90px;">NIK</th>
									<th style="width:150px;">Name</th>
									<th style="width:120px;">Plan OT</th>
									<th>Reason/Target</th>
									<th style="width:120px;">Status</th>
									<th style="width:150px;">Action</th>
								</tr>
							</thead>
							<tbody>
							<?php $no = 0; ?>
							@foreach ($tb_assigment_new as $dt)
								<?php
									$batas='+'.$limit_day.' days';
									if($limit_day==0){
										$tgl=date('Y-m-d',strtotime($dt->start_plan)).' 23:59:59';
										$date_limit=date('Y-m-d H:i:s',strtotime($tgl));
									}else{
										$date_limit= date('Y-m-d H:i:s',strtotime($batas,strtotime($dt->start_plan)));
									} 
									$now=date('Y-m-d H:i:s');
									if($date_limit<$now&&$limit_approval>0)$status_limit=1;
									else $status_limit=0;
									if($dt->exception==1)$status_limit=0;
									if($dt->conducted_status==1){
										if($dt->approved1>0&&$dt->approved1_status==1||$dt->approved1==''){
											if(($dt->approved2>0&&$dt->approved2_status==1)||$dt->approved2==''){
												$status_limit=0;
											}
										}
									}
								?>
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td title="{{$date_limit}}">{{$dt->NIK}}</td>
									<td>{{$dt->employee_name}}</td>
									<td><?php echo date('d-M-Y H:i',strtotime($dt->start_plan));?></td>
									<td>{{$dt->jobs}}</td>
									<td>
										<?php 
											if($dt->assigned_status=='0'){
												$status='assigned';
												echo "<span class='badge bg-yellow'>1</span>";
												if($dt->approved1!='')echo "<span class='badge bg-yellow'>2</span>";
												else echo "<span class='badge bg-white'>2</span>";
												if($dt->approved2!='')echo "<span class='badge bg-yellow'>3</span>";
												else echo "<span class='badge bg-white'>3</span>";
												echo "<span class='badge bg-yellow'>4</span>";
												echo "<span class='badge bg-yellow'>5</span>";
											}
											elseif($dt->approved1_status=='0'&&$dt->approved1!=''){
												$status='approved1';
												echo "<span class='badge bg-green'>1</span>";
												if($dt->approved1!='')echo "<span class='badge bg-yellow'>2</span>";
												else echo "<span class='badge bg-white'>2</span>";
												if($dt->approved2!='')echo "<span class='badge bg-yellow'>3</span>";
												else echo "<span class='badge bg-white'>3</span>";
												echo "<span class='badge bg-yellow'>4</span>";
												echo "<span class='badge bg-yellow'>5</span>";
											}
											elseif($dt->approved2_status=='0'&&$dt->approved2!=''){
												$status='approved2';
												echo "<span class='badge bg-green'>1</span>";
												echo "<span class='badge bg-green'>2</span>";
												if($dt->approved2!='')echo "<span class='badge bg-yellow'>3</span>";
												else echo "<span class='badge bg-white'>3</span>";
												echo "<span class='badge bg-yellow'>4</span>";
												echo "<span class='badge bg-yellow'>5</span>";
											}
											elseif($dt->conducted_status=='0'){
												$status='conducted';
												echo "<span class='badge bg-green'>1</span>";
												echo "<span class='badge bg-green'>2</span>";
												echo "<span class='badge bg-green'>3</span>";
												echo "<span class='badge bg-yellow'>4</span>";
												echo "<span class='badge bg-yellow'>5</span>";
											}
											elseif($dt->legalized_status=='0'){
												$status='legalized';
												echo "<span class='badge bg-green'>1</span>";
												echo "<span class='badge bg-green'>2</span>";
												echo "<span class='badge bg-green'>3</span>";
												echo "<span class='badge bg-green'>4</span>";
												echo "<span class='badge bg-yellow'>5</span>";											}
											else {
												$status='Completed';
												echo "<span class='badge bg-green'>1</span>";
												echo "<span class='badge bg-green'>2</span>";
												echo "<span class='badge bg-green'>3</span>";
												echo "<span class='badge bg-green'>4</span>";
												echo "<span class='badge bg-green'>5</span>";
											}
											
										?>
									</td>
									<td>
										<a title="Show" href='/Assigment/Show/{{$dt->id}}' target="_blank"><button type="button" class="btn btn-primary btn-xs">Preview</button></a>
										@if($status_limit==0)
											<?php if($id_employee==$dt->assigned&&$dt->assigned_status==0){?><a  title="<?php if($status!='Completed')echo 'Waiting '.$status;else echo $status;?>" href='/Assigment/Approvals/Sign/{{$dt->id}}/assigned_status'><button type="button" class="btn btn-info btn-xs">( 1 ) Assign</button></a><?php }?>
											@if($dt->assigned_status==1)
												<?php if($id_employee==$dt->approved1&&$dt->approved1_status==0){?><a  title="<?php if($status!='Completed')echo 'Waiting '.$status;else echo $status;?>" href='/Assigment/Approvals/Sign/{{$dt->id}}/approved1_status'><button type="button" class="btn btn-info btn-xs" <?php if($dt->assigned_status=='0')echo "disabled";?>>( 2 ) Approve</button></a><?php }?>
											@endif
											@if($dt->approved1_status==1)
												<?php if($id_employee==$dt->approved2&&$dt->approved2_status==0){?><a  title="<?php if($status!='Completed')echo 'Waiting '.$status;else echo $status;?>" href='/Assigment/Approvals/Sign/{{$dt->id}}/approved2_status'><button type="button" class="btn btn-info btn-xs" <?php if(($dt->approved1_status=='0'&&$dt->approved1!='')||($dt->assigned_status=='0'))echo "disabled";?>>( 3 ) Approve</button></a><?php }?>
											@endif
											@if($dt->approved2_status||($dt->approved2==''&&$dt->approved1_status==1)||($dt->approved1==''&&$dt->assigned_status==1))
												<?php if($id_employee==$dt->conducted&&$dt->conducted_status==0){?><a  title="<?php if($status!='Completed')echo 'Waiting '.$status;else echo $status;?>" href='/Assigment/Approvals/Sign/{{$dt->id}}/conducted_status'><button type="button" class="btn btn-info btn-xs" <?php if(($dt->approved2_status=='0'&&$dt->approved2!='')||($dt->approved1_status=='0'&&$dt->approved1!='')||($dt->assigned_status=='0'))echo "disabled";?>>( 4 ) Conduct</button></a><?php }?>
											@endif
											@if($dt->conducted_status==1)
												<?php if($id_employee==$dt->legalized&&$dt->legalized_status==0){?><a  title="<?php if($status!='Completed')echo 'Waiting '.$status;else echo $status;?>" href='/Assigment/Approvals/Sign/{{$dt->id}}/legalized_status'><button type="button" class="btn btn-info btn-xs" <?php if($dt->assigned_status=='0'||$dt->conducted_status=='0'||($dt->approved1!=''&&$dt->approved1_status=='0')||($dt->approved2!=''&&$dt->approved2_status=='0'))echo "disabled";?>>( 5 ) Legalize</button></a><?php }?>
											@endif
										@else	
										<i class="btn btn-danger btn-xs" title="Please Inform HR to open">OverDue</i>
										@endif
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
					<div id="tabel3">
						<div class="row" style="padding-bottom:20px;">
							<div class="col-lg-2 col-md-3 col-xs-12">
								<label>Periode</label>
								<input type="month" class="form-control" id="periode" name="periode" value="{{$periode}}">
							</div>
						</div>
						<table id="table3" class="table table-hover" style="min-width:100%;">
							<thead>
								<tr>
									<th style="width:50px;">No</th>
									<th style="width:90px;">NIK</th>
									<th style="width:150px;">Name</th>
									<th style="width:120px;">Plan OT</th>
									<th>Reason/Target</th>
									<th style="width:120px;">Status</th>
									<th style="width:150px;">Action</th>
								</tr>
							</thead>
							<tbody>
							<?php $no=0;?>
							@foreach($tb_assigment as $dt)
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td>{{$dt->NIK}}</td>
									<td>{{$dt->employee_name}}</td>
									<td><?php echo date('d-M-Y H:i',strtotime($dt->start_plan));?></td>
									<td>{{$dt->jobs}}</td>
									<td>
										<?php 
											if($dt->assigned_status=='0')$status='assigned';
											elseif($dt->conducted_status=='0')$status='conducted';
											elseif($dt->approved1_status=='0'&&$dt->approved1!='')$status='approved1';
											elseif($dt->approved2_status=='0'&&$dt->approved2!='')$status='approved2';
											elseif($dt->legalized_status=='0')$status='legalized';
											else $status='Completed';
											if($status!='Completed')echo 'Need '.$status;
											else echo $status;
										?>
									</td>
									<td>
										<a title="Show" href='/Assigment/Show/{{$dt->id}}' target="_blank"><button type="button" class="btn btn-primary btn-xs">Preview</button></a>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
					</div>
					<!-- /.box-body -->
					<div class="box-footer">

					</div>
				</div>

			</div>
		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->

    @if ($message = Session::get('success'))
		<div class="alert alert-info alert-dismissible"
            style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-info"></i> Success Alert</h4>
			{{$message}}
		</div>
    @endif
	@if ($errors->any())
		<div class="alert alert-danger alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-warning"></i> Saving Failed Alert!</h4>
				@if($errors->has('date_off'))
					- Date harus diisi<br>
				@endif
		</div>
	@endif

@endsection
@section('Scripts')
	<script>
		$('body').on("change", "#periode", function() {
			var periode = document.getElementById('periode').value;
			window.location.href = "/Assigment/Approvals/" + periode;
		});
	</script>
	<!-- page script Tabel-->
	<script>
		$(document).ready(function() {
			$("#tabel2").show();
			$("#tabel3").hide();
			$("#judul").text('New Assigment Form');
			$(document).on('click', '.table2', function() {
				$("#tabel2").show();
				$("#tabel3").hide();
				$("#judul").text('New Assigment Form');
			});
			$(document).on('click', '.table3', function() {
				$("#tabel3").show();
				$("#tabel2").hide();
				$("#judul").text('Completed Assigment Form');
			});
		});
	</script>
	<script>
		$(function() {
			$('#table2').DataTable({
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': true,
			'info': true,
			"pageLength": 15,
			'autoWidth': true,
			"scrollX": true
			})
		})
		$(function() {
			$('#table3').DataTable({
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': true,
			'info': true,
			"pageLength": 10,
			'autoWidth': false,
			})
		})
	</script>
	<script type="text/javascript">
		window.onload = function() {
			var hoursplan = $("#hoursplan").val();
			var idemployee = $("#idemployee").val();
			var jobs = $("#jobs").val();
			var assigned = $("#assigned").val();
			if (hoursplan <= 4 || idemployee == '' || jobs == '' || assigned == '') {
				document.getElementById("addmp").disabled = true;
			} else {
				document.getElementById("addmp").disabled = false;
			}
		}
		$(function() {
			$("#idemployee").change(function() {
				$.ajaxSetup({
					type: "POST",
					url: "/Assigment/Select",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				var idemployee = $("#idemployee").val();
				$.ajax({
					data: {
                        id_employee: idemployee
                    },
					success: function(respond) {
						$("#assigned").html(respond);

						$('#approved1')
							.find('option')
							.remove()
							.end()
							.append('<option value=""></option>')
							.val();
						$('#approved2')
							.find('option')
							.remove()
							.end()
							.append('<option value=""></option>')
							.val();
					//alert(valdep);
					}
				})

				var hoursplan = $("#hoursplan").val();
				var idemployee = $("#idemployee").val();
				var jobs = $("#jobs").val();
				var assigned = $("#assigned").val();
				if (hoursplan <= 4 || idemployee == '' || jobs == '' || assigned == '') {
					document.getElementById("addmp").disabled = true;
				} else {
					document.getElementById("addmp").disabled = false;
				}

			});
			$("#assigned").change(function() {
				$.ajaxSetup({
					type: "POST",
					url: "/Assigment/Select",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				var idemployee = $("#assigned").val();
				$.ajax({
					data: {
                        id_employee: idemployee
                    },
					success: function(respond) {
						$("#approved1").html(respond);
						$('#approved2')
							.find('option')
							.remove()
							.end()
							.append('<option value=""></option>')
							.val();
					}
				})

				var hoursplan = $("#hoursplan").val();
				var idemployee = $("#idemployee").val();
				var jobs = $("#jobs").val();
				var assigned = $("#assigned").val();
				if (hoursplan <= 4 || idemployee == '' || jobs == '' || assigned == '') {
					document.getElementById("addmp").disabled = true;
				} else {
					document.getElementById("addmp").disabled = false;
				}

			});
			$("#approved1").change(function() {
				$.ajaxSetup({
					type: "POST",
					url: "/Assigment/Select",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				var idemployee = $("#approved1").val();
				$.ajax({
					data: {
                        id_employee: idemployee
                    },
					success: function(respond) {
						$("#approved2").html(respond);
					}
				})

				var hoursplan = $("#hoursplan").val();
				var idemployee = $("#idemployee").val();
				var jobs = $("#jobs").val();
				var assigned = $("#assigned").val();
				if (hoursplan <= 4 || idemployee == '' || jobs == '' || assigned == '') {
					document.getElementById("addmp").disabled = true;
				} else {
					document.getElementById("addmp").disabled = false;
				}

			});
		})
	</script>
	<script>
		$("#startplan").change(function() {
			var Awal = new Date($('#startplan').val());
			var Akhir = new Date($('#finishplan').val());

			var n = ((Akhir - Awal) / 60000 / 60);
			$('#hoursplan').val(n);

			var hoursplan = $("#hoursplan").val();
			var idemployee = $("#idemployee").val();
			var jobs = $("#jobs").val();
			var assigned = $("#assigned").val();
			if (hoursplan <= 4 || idemployee == '' || jobs == '' || assigned == '') {
				document.getElementById("addmp").disabled = true;
			} else {
				document.getElementById("addmp").disabled = false;
			}

		});
		$("#finishplan").change(function() {
			var Awal = new Date($('#startplan').val());
			var Akhir = new Date($('#finishplan').val());

			var n = ((Akhir - Awal) / 60000 / 60);
			$('#hoursplan').val(n);

			var hoursplan = $("#hoursplan").val();
			var idemployee = $("#idemployee").val();
			var jobs = $("#jobs").val();
			var assigned = $("#assigned").val();
			if (hoursplan <= 4 || idemployee == '' || jobs == '' || assigned == '') {
				document.getElementById("addmp").disabled = true;
			} else {
				document.getElementById("addmp").disabled = false;
			}

		});
	</script>
	<!-- Durasi Alert -->
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function() {
			$(this).remove(); 
			});
		}, 5000);
	</script>
@endsection
