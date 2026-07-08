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
		$dept_name='';
		if(old('dept_id')!=''){
			$dept_id=old('dept_id');
			$host1 = mysqli_connect("localhost","ems","123456","db_ems");
			$qry=mysqli_query($host1,"select * from tb_departments where id='$dept_id'")or die(mysqli_error($host1));
			while($dt=mysqli_fetch_array($qry)){
				$dept_name=$dt['dept_name'];
			}
		}
		$position_name='';
		if(old('position_id')!=''){
			$pos_id=old('position_id');
			$host1 = mysqli_connect("localhost","ems","123456","db_ems");
			$qry=mysqli_query($host1,"select * from tb_positions where id='$pos_id'")or die(mysqli_error($host1));
			while($dt=mysqli_fetch_array($qry)){
				$position_name=$dt['position_name'];
			}
		}
		$leader_name='';
		if(old('leader_id')!=''){
			$leader_id=old('leader_id');
			$host1 = mysqli_connect("localhost","ems","123456","db_ems");
			$qry=mysqli_query($host1,"select * from tb_employees where id='$leader_id'")or die(mysqli_error($host1));
			while($dt=mysqli_fetch_array($qry)){
				$leader_name=$dt['employee_name'];
			}
		}
   ?>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Employee
				<small>add new</small>
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<form role="form" action="/Admin/Employee/Create/Submit" method="post">
			{{ csrf_field() }}
			<div class="col-xs-8">
				<div class="box box-success box-solid" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-user"></i>
						<h3 class="box-title">Entry New Data</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button type="button" class="btn btn-default btn-xs" onclick="window.location.href='/Admin/Employee'"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-md-6">

								<div class="">
									<div class="box-body">
										<div class="form-group">
											<label>Agreement</label>
											<select class="form-control" name="id_agreement" id="idagreement">
												<option value=""></option>
												@foreach($tb_contract as $dt)
													<option value="{{$dt->id}}">{{$dt->nomor_perjanjian}}</option>
												@endforeach
											</select>
											
											@if($errors->has('dept_id'))
												<div class="box-body text-danger">Department belum dipilih</div>
											@endif
											
										</div>
										<div class="form-group">
											<label>PIN</label>
											
											<select name="PIN" class="selectpicker form-control" data-live-search="true">
												<option value="{{old('PIN')}}">{{old('PIN')}}</option>
                                                @foreach($tb_iclock as $dt1)
														<option value='{{ $dt1->badgenumber }}'>{{ $dt1->userid }}: {{ $dt1->name }} ({{ $dt1->badgenumber }})</option>
                                                @endforeach
											</select>
											
											@if($errors->has('PIN'))
												<div class="box-body text-danger">PIN harus dipilih</div>
											@endif
										</div>
										<div class="form-group">
											<label>NIK</label>
											<input type="text" value="{{old('NIK')}}" name="NIK" id="nik" class="form-control" placeholder="Entry ...">
											@if($errors->has('NIK'))
												<div class="box-body text-danger">NIK harus diisi</div>
											@endif
											
										</div>
										<div class="form-group">
											<label>Employee Name</label>
											<input type="text" value="{{old('employee_name')}}" name="employee_name" id="employeename" class="form-control" placeholder="Entry ...">
											@if($errors->has('employee_name'))
												<div class="box-body text-danger">Nama Employee harus diisi</div>
											@endif
											
										</div>
										<div class="form-group">
											<div class="radio">
												<label>
												<input type="radio" name="gender" id="optionsRadios1" value="Laki-laki" <?php if(old('gender')=='Laki-laki')echo "checked";?>>
												Laki-laki
												</label>
											</div>
											<div class="radio">
												<label>
												<input type="radio" name="gender" id="optionsRadios2" value="Perempuan" <?php if(old('gender')=='Perempuan')echo "checked";?>>
												Perempuan
												</label>
											</div>
											@if($errors->has('gender'))
												<div class="box-body text-danger">Gender belum dipilih</div>
											@endif
											
										</div>
										<div class="form-group">
											<label>Join Date</label>
											<input type="date" value="{{old('join_date')}}" name="join_date" id="joindate" class="form-control" placeholder="Enter ...">
											@if($errors->has('join_date'))
												<div class="box-body text-danger">Join Date belum dipilih</div>
											@endif
											
										</div>

									</div>
								</div>

							</div>
							<div class="col-md-6" style="border-left:1px solid #DDD;border-right:1px solid #DDD;">
							
								<div class="">
									<div class="box-body">
										<div class="form-group">
											<label>Position</label>
											<select class="form-control" name="position_id" id="position">
												<option value="{{old('position_id')}}">{{$position_name}}</option>
												@foreach($tb_position as $dt)
													<option value="{{$dt->id}}">{{$dt->position_name}}</option>
												@endforeach
											</select>
											@if($errors->has('position_id'))
												<div class="box-body text-danger">Posisi belum dipilih</div>
											@endif
										</div>
										<div class="form-group">
											<label>Department</label>
											<select class="form-control" name="dept_id" id="department">
												<option value="{{old('dept_id')}}">{{$dept_name}}</option>
												@foreach($tb_department as $dt)
													<option value="{{$dt->id}}">{{$dt->dept_name}}</option>
												@endforeach
											</select>
											
											@if($errors->has('dept_id'))
												<div class="box-body text-danger">Department belum dipilih</div>
											@endif
										</div>
										<div class="form-group">
											<label>Direct Leader</label>
											<select class="form-control" name="leader_id" id="leader">
												<option value="{{old('leader_id')}}">{{$leader_name}}</option>
											</select>
											
											@if($errors->has('leader_id'))
												<div class="box-body text-danger">Direct Leader belum dipilih</div>
											@endif
										</div>
										<div class="form-group">
											<label>Cost Center</label>
											<select class="form-control" name="cc_code" id="cc_code">
												<option value="{{old('cc_code')}}">{{old('cc_code')}}</option>
												<option value=""></option>
											</select>
											
											@if($errors->has('cc_code'))
												<div class="box-body text-danger">Cost Center Bellum dipilih</div>
											@endif
										</div>
										<div class="form-group">
											<label for="exampleInputFile">Employee Photo</label>
											<input type="file" id="exampleInputFile">
										</div>
										<div class="form-group">
											<label>Email</label>
											<input type="email" value="{{old('email_address')}}" name="email_address" class="form-control" placeholder="Entry ...">
											@if($errors->has('email_address'))
												<div class="box-body text-danger">Email harus diisi</div>
											@endif
										</div>


									</div>
								</div>
							
							</div>
						</div>
					</div>
					<div class="box-footer">
						<button type="reset" class="btn btn-default">Reset</button>
						<div class="pull-right">
							<button type="submit" class="btn btn-success">Simpan</button>
						</div>
					</div>
					<!-- /.box-body -->
				</div>
			<!-- /.box -->

			</div>
			</form>
			<!-- /.col -->

		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->

    @if ($message = Session::get('success'))
		<div class="alert alert-info alert-dismissible" style="position:absolute;width:350px;right:10px;top:105px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-info"></i> Success Alert</h4>
			{{$message}}
		</div>
    @endif
	@if ($errors->any())
		<div class="alert alert-danger alert-dismissible" style="position:absolute;width:350px;right:10px;top:105px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-warning"></i> Saving Failed Alert!</h4>
				@if($errors->has('PIN'))
					- PIN belum dipilih<br>
				@elseif($errors->has('NIK'))
					- Kolom NIK masih belum sesuai atau NIK sudah pernah digunakan<br>
				@elseif($errors->has('employee_name'))
					- Nama Employee tidak boleh kosong<br>
				@elseif($errors->has('gender'))
					- Pilih jenis kelamin Laki-laki atau Perempuan<br>
				@elseif($errors->has('join_date'))
					- Join Date harus diisi<br>
				@elseif($errors->has('dept_id'))
					- Department belum dipilih<br>
				@elseif($errors->has('position_id'))
					- Position belum dipilih<br>
				@elseif($errors->has('leader_id'))
					- Direct leader belum dipilih<br>
				@elseif($errors->has('email_address'))
					- Kolom Email belum sesuai atau sudah pernah digunakan di NIK yang lain<br>
				@else
					{{$errors}}
				@endif
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
		$(function(){
			$("#position").change(function(){
				var valdep=$(this).val();
				$.ajaxSetup({
					type:"POST",
					url: "/Admin/Employee/SelectDept",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					data:{deptid:valdep},
					success: function(respond){
						$("#department").html(respond);
						document.getElementById("leader").innerHTML = "";
						document.getElementById("cc_code").innerHTML = "";
					}
				})
			});
			$("#department").change(function(){
				var valdep=$(this).val();
				var valpos=$("#position").val();
				if(valdep>0){
				$.ajaxSetup({
					type:"POST",
					url: "/Admin/Employee/Select",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					data:{deptid:valdep,posid:valpos},
					success: function(respond){
						$("#leader").html(respond);
						document.getElementById("cc_code").innerHTML = "";
					}
				})
				}
			});

		})
		$("#leader").change(function(){
			var valdep=$("#department").val();
			$.ajaxSetup({
				type:"POST",
				url: "/Admin/Employee/SelectCC",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:{deptid:valdep},
				success: function(respond){
					$("#cc_code").html(respond);
					//alert(respond);
				}
			});
		});
	</script>
	<script>
		$('body').on("change","#idagreement",function(){
			var id = $(this).val();
			var data = "id="+id;
			$.ajax({
				type: 'POST',
				url: "/AgreementCheck2",
				data: data,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: function(hasil) {
					str=hasil.split('#');
					$('#employeename').val(str[1]);
					$('#joindate').val(str[8]);
					$('#department').val(str[10]);
					$('#position').val(str[11]);
					$('#nik').val(str[12]);
				}
			});
		});

	</script>
@endsection
