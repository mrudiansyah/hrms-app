@extends('layouts/admin')
@section('Contents')
   <!-- Contents -->
   <meta name="csrf-token" content="{{ csrf_token() }}">

   <style>
		#tables th {
		border-top: 1px solid #999;
		border-bottom: 1px solid #999;
		background-color: #2F4F4F;
		color: white;
		}	
        .table1 tr:hover {
          background-color: #DCDCDC;
		  cursor:pointer;
        }
   </style>
	<?php

	$filename="fokar/".$id_employee.".jpg";
	if(!file_exists($filename))$filename="fokar/user.png";

	?>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Employee
				<small>setup</small>
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-lg-6 col-sm-6 col-xs-12">
				<form role="form" action="/LeaderUpdate" method="post" enctype="multipart/form-data">
				{{ csrf_field() }}
				<div class="row">
					<div class="col-xs-12">
						@foreach($tb_employee as $row)
						<?php 
							$PIN=$row->PIN;
							$gender=$row->gender;
							$id_employee=$row->id;
							echo "<input type='hidden' value='".$row->id."' name='id'>";
						?>
						<div class="box box-primary box-solid" style="background:#FFF;">
							<div class="box-header">
								<i class="fa fa-user"></i>
								<h3 class="box-title">{{$row->employee_name}}</h3>
								<div class="box-tools pull-right">
									<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
									<button type="button" class="btn btn-default btn-xs" onclick="window.location.href='/Admin/Employee'"><i class="fa fa-times"></i></button>
								</div>
							</div>
							<div class="box-body">
								<div class="row">
									<div class="col-lg-6 col-sm-6 col-xs-12">

										<div class="">
											<div class="box-body">
												<div class="form-group">
													<label>NIK</label>
													<input type="text" value="{{$row->NIK}}" name="NIK_show" class="form-control" disabled>
													<input type="hidden" value="{{$row->NIK}}" name="NIK" class="form-control" placeholder="Entry ...">
													@if($errors->has('NIK'))
														<div class="box-body text-danger">NIK harus diisi</div>
													@endif
													
												</div>
												<div class="form-group">
													<label>Employee Name</label>
													<input type="text" value="{{$row->employee_name}}" name="employee_name" class="form-control" disabled>
													@if($errors->has('employee_name'))
														<div class="box-body text-danger">Nama Employee harus diisi</div>
													@endif
													
												</div>
												<div class="form-group">
													<label>Join Date</label>
													<input type="date" value="{{$row->join_date}}" name="join_date" class="form-control" disabled>
													@if($errors->has('join_date'))
														<div class="box-body text-danger">Join Date belum dipilih</div>
													@endif
													
												</div>
												<div class="form-group">
													<label>Gender</label>
													<input type="text" value="{{$row->gender}}" name="gender" class="form-control" disabled>
													@if($errors->has('gender'))
														<div class="box-body text-danger">Nama Employee harus diisi</div>
													@endif
													
												</div>
											</div>
										</div>

									</div>
									<div class="col-lg-6 col-sm-6 col-xs-12">
									
										<div class="">
											<div class="box-body">
												<div class="form-group">
													<label>Department</label>
													<select class="form-control" name="dept_id" id="department" disabled>
														<option value="{{$row->dept_id}}">{{$row->dept_name}}</option>
														@foreach($tb_department as $dt)
															<option value="{{$dt->id}}">{{$dt->dept_name}}</option>
														@endforeach
													</select>
													
													@if($errors->has('dept_id'))
														<div class="box-body text-danger">Department belum dipilih</div>
													@endif
													
												</div>
												<div class="form-group">
													<label>Level</label>
													<select class="form-control" name="position_id" id="position" disabled>
														<option value="{{$row->position_id}}">{{$row->position_name}}</option>
														@foreach($tb_position as $dt)
															<option value="{{$dt->id}}">{{$dt->position_name}}</option>
														@endforeach
													</select>
													@if($errors->has('position_id'))
														<div class="box-body text-danger">Posisi belum dipilih</div>
													@endif
												</div>
												<div class="form-group">
													<label>Direct Leader</label>
													<select class="form-control" name="leader_id" id="leader">
														<option value="{{$row->leader_id}}">{{$leader_name}}</option>
													</select>
													
													@if($errors->has('leader_id'))
														<div class="box-body text-danger">Direct Leader belum dipilih</div>
													@endif
												</div>
												<div class="form-group">
													<label>Cost Center</label>
													<select class="form-control" name="cc_code" id="cc_code">
														<option value="{{$row->cc_code}}">{{$row->segment_name}}</option>
														@foreach($tb_cost_center as $dt)
															<option value="{{$dt->cc_code}}">{{$dt->segment_name}}</option>
														@endforeach
													</select>
													
													@if($errors->has('leader_id'))
														<div class="box-body text-danger">Direct Leader belum dipilih</div>
													@endif
												</div>

											</div>
										</div>
									
									</div>
								</div>
							</div>
							<div class="box-footer">
								<button  class="btn btn-default"><a href="/Leader">Back</a></button>
								<div class="pull-right">
									<button type="submit" class="btn btn-primary">Update</button>
								</div>
							</div>
							<!-- /.box-body -->
						</div>
						@endforeach
					</div>
				</div>
				</form>
			</div>
			<!-- /.col -->
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

    @if ($message = Session::get('success'))
		<div class="alert alert-success alert-dismissible" style="position:absolute;width:350px;right:10px;top:65px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-info"></i> Success Alert</h4>
			{{$message}}
		</div>
    @endif
@endsection
@section('Scripts')

	<!-- page script Tabel-->
	<script>
		$(function () {
			$('#table1').DataTable({
			'paging'      : true,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : false
			})
		})
	</script>
	<script type="text/javascript">
		window.onload=function(){
			var valdep=$("#department").val();
			var valpos=$("#position").val();
			var vallea=$("#leader").val();
			$.ajaxSetup({
				type:"POST",
				url: "/Admin/Employee/Select",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			if(valdep>0&&valpos>0){
				$.ajax({
					data:{deptid:valdep,posid:valpos,leader:vallea},
					success: function(respond){
						$("#leader").html(respond);
					}
				})
			}
		}
		$(function(){
			$.ajaxSetup({
				type:"POST",
				url: "/Admin/Employee/Select",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$("#department").change(function(){
				var valdep=$(this).val();
				var valpos=$("#position").val();
				if(valdep>0){
					$.ajax({
						data:{deptid:valdep,posid:valpos},
						success: function(respond){
						$("#leader").html(respond);
						//alert(valdep);
						}
					})
				}
			});

			$("#position").change(function(){
				var valdep=$("#department").val();
				var valpos=$(this).val();
				if(valpos>0){
				$.ajax({
					data:{deptid:valdep,posid:valpos},
					success: function(respond){
					$("#leader").html(respond);
					//alert(valpos);

					}
				})
				}
			});
		})
	</script>
	<!-- page script alert-->
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.delete-modal', function() {
			$('#delid').val($(this).data('delid'));
			$('#delid1').val($(this).data('delid1'));
			$('#delname').text($(this).data('delname'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#delid').val();
			var y=$('#delid1').val();
			window.location.href='/Admin/Employee/Create/Delete/'+x+'/'+y;
		});
	</script>
@endsection
