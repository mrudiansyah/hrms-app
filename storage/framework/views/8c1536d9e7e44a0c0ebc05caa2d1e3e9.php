
<?php $__env->startSection('Contents'); ?>
   <!-- Contents -->
   <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

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
			<?php echo e(csrf_field()); ?>

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
												<?php $__currentLoopData = $tb_contract; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($dt->id); ?>"><?php echo e($dt->nomor_perjanjian); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
											
											<?php if($errors->has('dept_id')): ?>
												<div class="box-body text-danger">Department belum dipilih</div>
											<?php endif; ?>
											
										</div>
										<div class="form-group">
											<label>PIN</label>
											
											<select name="PIN" class="selectpicker form-control" data-live-search="true">
												<option value="<?php echo e(old('PIN')); ?>"><?php echo e(old('PIN')); ?></option>
                                                <?php $__currentLoopData = $tb_iclock; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<option value='<?php echo e($dt1->badgenumber); ?>'><?php echo e($dt1->userid); ?>: <?php echo e($dt1->name); ?> (<?php echo e($dt1->badgenumber); ?>)</option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
											
											<?php if($errors->has('PIN')): ?>
												<div class="box-body text-danger">PIN harus dipilih</div>
											<?php endif; ?>
										</div>
										<div class="form-group">
											<label>NIK</label>
											<input type="text" value="<?php echo e(old('NIK')); ?>" name="NIK" id="nik" class="form-control" placeholder="Entry ...">
											<?php if($errors->has('NIK')): ?>
												<div class="box-body text-danger">NIK harus diisi</div>
											<?php endif; ?>
											
										</div>
										<div class="form-group">
											<label>Employee Name</label>
											<input type="text" value="<?php echo e(old('employee_name')); ?>" name="employee_name" id="employeename" class="form-control" placeholder="Entry ...">
											<?php if($errors->has('employee_name')): ?>
												<div class="box-body text-danger">Nama Employee harus diisi</div>
											<?php endif; ?>
											
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
											<?php if($errors->has('gender')): ?>
												<div class="box-body text-danger">Gender belum dipilih</div>
											<?php endif; ?>
											
										</div>
										<div class="form-group">
											<label>Join Date</label>
											<input type="date" value="<?php echo e(old('join_date')); ?>" name="join_date" id="joindate" class="form-control" placeholder="Enter ...">
											<?php if($errors->has('join_date')): ?>
												<div class="box-body text-danger">Join Date belum dipilih</div>
											<?php endif; ?>
											
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
												<option value="<?php echo e(old('position_id')); ?>"><?php echo e($position_name); ?></option>
												<?php $__currentLoopData = $tb_position; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($dt->id); ?>"><?php echo e($dt->position_name); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
											<?php if($errors->has('position_id')): ?>
												<div class="box-body text-danger">Posisi belum dipilih</div>
											<?php endif; ?>
										</div>
										<div class="form-group">
											<label>Department</label>
											<select class="form-control" name="dept_id" id="department">
												<option value="<?php echo e(old('dept_id')); ?>"><?php echo e($dept_name); ?></option>
												<?php $__currentLoopData = $tb_department; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($dt->id); ?>"><?php echo e($dt->dept_name); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
											
											<?php if($errors->has('dept_id')): ?>
												<div class="box-body text-danger">Department belum dipilih</div>
											<?php endif; ?>
										</div>
										<div class="form-group">
											<label>Direct Leader</label>
											<select class="form-control" name="leader_id" id="leader">
												<option value="<?php echo e(old('leader_id')); ?>"><?php echo e($leader_name); ?></option>
											</select>
											
											<?php if($errors->has('leader_id')): ?>
												<div class="box-body text-danger">Direct Leader belum dipilih</div>
											<?php endif; ?>
										</div>
										<div class="form-group">
											<label>Cost Center</label>
											<select class="form-control" name="cc_code" id="cc_code">
												<option value="<?php echo e(old('cc_code')); ?>"><?php echo e(old('cc_code')); ?></option>
												<option value=""></option>
											</select>
											
											<?php if($errors->has('cc_code')): ?>
												<div class="box-body text-danger">Cost Center Bellum dipilih</div>
											<?php endif; ?>
										</div>
										<div class="form-group">
											<label for="exampleInputFile">Employee Photo</label>
											<input type="file" id="exampleInputFile">
										</div>
										<div class="form-group">
											<label>Email</label>
											<input type="email" value="<?php echo e(old('email_address')); ?>" name="email_address" class="form-control" placeholder="Entry ...">
											<?php if($errors->has('email_address')): ?>
												<div class="box-body text-danger">Email harus diisi</div>
											<?php endif; ?>
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

    <?php if($message = Session::get('success')): ?>
		<div class="alert alert-info alert-dismissible" style="position:absolute;width:350px;right:10px;top:105px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-info"></i> Success Alert</h4>
			<?php echo e($message); ?>

		</div>
    <?php endif; ?>
	<?php if($errors->any()): ?>
		<div class="alert alert-danger alert-dismissible" style="position:absolute;width:350px;right:10px;top:105px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-warning"></i> Saving Failed Alert!</h4>
				<?php if($errors->has('PIN')): ?>
					- PIN belum dipilih<br>
				<?php elseif($errors->has('NIK')): ?>
					- Kolom NIK masih belum sesuai atau NIK sudah pernah digunakan<br>
				<?php elseif($errors->has('employee_name')): ?>
					- Nama Employee tidak boleh kosong<br>
				<?php elseif($errors->has('gender')): ?>
					- Pilih jenis kelamin Laki-laki atau Perempuan<br>
				<?php elseif($errors->has('join_date')): ?>
					- Join Date harus diisi<br>
				<?php elseif($errors->has('dept_id')): ?>
					- Department belum dipilih<br>
				<?php elseif($errors->has('position_id')): ?>
					- Position belum dipilih<br>
				<?php elseif($errors->has('leader_id')): ?>
					- Direct leader belum dipilih<br>
				<?php elseif($errors->has('email_address')): ?>
					- Kolom Email belum sesuai atau sudah pernah digunakan di NIK yang lain<br>
				<?php else: ?>
					<?php echo e($errors); ?>

				<?php endif; ?>
		</div>
	<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>

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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/m_employee/employee_create.blade.php ENDPATH**/ ?>