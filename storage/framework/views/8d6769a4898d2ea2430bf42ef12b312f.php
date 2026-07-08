
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
			<div class="col-lg-8 col-sm-12 col-xs-12">
				<form role="form" action="/Admin/Employee/Update/Submit" method="post" enctype="multipart/form-data">
				<?php echo e(csrf_field()); ?>

				<div class="row">
					<div class="col-xs-12">
						<?php $__currentLoopData = $tb_employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<?php 
							$PIN=$row->PIN;
							$gender=$row->gender;
							$id_employee=$row->id;
							echo "<input type='hidden' value='".$row->id."' name='id'>";
						?>
						<div class="box box-primary box-solid" style="background:#FFF;">
							<div class="box-header">
								<i class="fa fa-user"></i>
								<h3 class="box-title"><?php echo e($row->employee_name); ?></h3>
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
													<label>PIN</label>
													<input type="number" value="<?php echo e($row->badgenumber); ?>" name="PIN" class="form-control" placeholder="Entry ...">
													<?php if($errors->has('PIN')): ?>
														<div class="box-body text-danger">PIN harus dipilih</div>
													<?php endif; ?>
													
												</div>
												<div class="form-group">
													<label>NIK</label>
													<input type="text" value="<?php echo e($row->NIK); ?>" name="NIK_show" class="form-control" disabled>
													<input type="hidden" value="<?php echo e($row->cc_code); ?>" name="cc_code" class="form-control" placeholder="Entry ...">
													<input type="hidden" value="<?php echo e($row->NIK); ?>" name="NIK" class="form-control" placeholder="Entry ...">
													<?php if($errors->has('NIK')): ?>
														<div class="box-body text-danger">NIK harus diisi</div>
													<?php endif; ?>
													
												</div>
												<div class="form-group">
													<label>Employee Name</label>
													<input type="text" value="<?php echo e($row->employee_name); ?>" name="employee_name" class="form-control" placeholder="Entry ...">
													<?php if($errors->has('employee_name')): ?>
														<div class="box-body text-danger">Nama Employee harus diisi</div>
													<?php endif; ?>
													
												</div>
												<div class="form-group">
													<div class="radio">
														<label>
														<input type="radio" name="gender" id="optionsRadios1" value="Laki-laki" <?php if($gender=='Laki-laki')echo 'checked';?>>
														Laki-laki
														</label>
													</div>
													<div class="radio">
														<label>
														<input type="radio" name="gender" id="optionsRadios2" value="Perempuan" <?php if($gender=='Perempuan')echo 'checked';?>>
														Perempuan
														</label>
													</div>
													<?php if($errors->has('gender')): ?>
														<div class="box-body text-danger">Gender belum dipilih</div>
													<?php endif; ?>
													
												</div>
												<div class="form-group">
													<label>Join Date</label>
													<input type="date" value="<?php echo e($row->join_date); ?>" name="join_date" class="form-control" placeholder="Enter ...">
													<?php if($errors->has('join_date')): ?>
														<div class="box-body text-danger">Join Date belum dipilih</div>
													<?php endif; ?>
													
												</div>

											</div>
										</div>

									</div>
									<div class="col-lg-6 col-sm-6 col-xs-12">
										<div class="">
											<div class="box-body">
												<div class="form-group">
													<label>Department</label>
													<select class="form-control" name="dept_id" id="department">
														<option value="<?php echo e($row->dept_id); ?>"><?php echo e($row->dept_name); ?></option>
														<?php $__currentLoopData = $tb_department; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($dt->id); ?>"><?php echo e($dt->dept_name); ?></option>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													</select>
													
													<?php if($errors->has('dept_id')): ?>
														<div class="box-body text-danger">Department belum dipilih</div>
													<?php endif; ?>
												</div>
												<div class="form-group">
													<label>Level</label>
													<select class="form-control" name="position_id" id="position">
														<option value="<?php echo e($row->position_id); ?>"><?php echo e($row->position_name); ?></option>
														<?php $__currentLoopData = $tb_position; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($dt->id); ?>"><?php echo e($dt->position_name); ?></option>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													</select>
													<?php if($errors->has('position_id')): ?>
														<div class="box-body text-danger">Posisi belum dipilih</div>
													<?php endif; ?>
												</div>
												<div class="form-group">
													<label>Direct Leader</label>
													<select class="form-control" name="leader_id" id="leader">
														<option value="<?php echo e($row->leader_id); ?>"><?php echo e($leader_name); ?></option>
													</select>
													
													<?php if($errors->has('leader_id')): ?>
														<div class="box-body text-danger">Direct Leader belum dipilih</div>
													<?php endif; ?>
												</div>
												<div class="form-group">
													<input type="hidden" name="status" value="<?php echo e($row->status); ?>">
													<label for="exampleInputFile">Employee Photo</label>
													<input type="file" name="foto" id="exampleInputFile">
												</div>


											</div>
										</div>
									
									</div>
								</div>
							</div>
							<div class="box-footer">
								<button type="reset" class="btn btn-default">Cancel</button>
								<div class="pull-right">
									<button type="submit" class="btn btn-primary">Update</button>
								</div>
							</div>
							<!-- /.box-body -->
						</div>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</div>
				</div>
				</form>

				<div class="row">
					<div class="col-lg-6 col-sm-5 col-xs-12">
						<div class="box box-success box-solid" style="background:#FFF;">
							<div class="box-header">
								<i class="fa fa-map-marker"></i>
								<h3 class="box-title">Position</h3>
								<div class="box-tools pull-right">
									<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
									<button type="button" class="btn btn-default btn-xs" data-widget="remove"><i class="fa fa-times"></i></button>
								</div>
							</div>
							<div class="box-body">
								<ul>
									<?php $__currentLoopData = $tb_bagian; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<li>
										<?php echo e($dt->posisi); ?> <?php if($dt->line!='')echo ", Line ".$dt->line;?>
										<a class="delete-modal" data-delid="<?php echo e($dt->id); ?>" data-delid1="Posisi" data-delname="<?php echo e($dt->posisi); ?>">
											<span class="pull-right-container">
												<i class="fa fa-trash-o pull-right" style="color:#C00;cursor:pointer;"></i>
											</span>
										</a>
									</li>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</ul>
							</div>
							<div class="box-footer" style="border:0px;">
								<div class="box box-success collapsed-box" style="border:0px;">
									<div class="box-header with-border">
										<div class="box-tools pull-right">
											<label>New Position</label>&nbsp;&nbsp;
											<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-plus"></i></button>
										</div>
									</div>
									<div class="box-body" style="padding:10px;">
										<form action="/Admin/Employee/Create/Posisi" method="post">
										<input type="hidden" name="id_employee" value="<?php echo e($id_employee); ?>">
										<?php echo e(csrf_field()); ?>

										<div class="form-group">
											<label>Line</label>
											<input type="text" id="line" name="line" class="form-control">
										</div>
										<div class="form-group">
											<label>Position</label>
											<input type="text" id="posisi" name="posisi2" class="form-control">
										</div>
										<div class="form-group">
											<label>Start Implement</label>
											<input type="date" id="implement" name="implement" class="form-control">
										</div>

										<div class="form-group pull-right" style="padding-top:15px;">
											<button type="submit" class="btn btn-success">Simpan</button>
										</div>
										</form>
									</div>
									<!-- /.box-body -->
								</div>
							</div>
						</div>
						<?php if (request()->user()->hasRole('allowance')){?>
						<div class="box box-success box-solid" style="background:#FFF;">
							<div class="box-header">
								<i class="fa fa-info"></i>
								<h3 class="box-title">General Data</h3>
								<div class="box-tools pull-right">
									<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
									<button type="button" class="btn btn-default btn-xs" data-widget="remove"><i class="fa fa-times"></i></button>
								</div>
							</div>
							<div class="box-body">
								<form role="form" action="/Admin/Employee/Create/Detail" method="post">
									<?php echo e(csrf_field()); ?>

									<?php $i=0;?>
									<?php $__currentLoopData = $tb_detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php $i++;?>
									<input type="hidden" value="<?php echo e($id_employee); ?>" name="id_employee">
									<div class="box-body">
										<div class="form-group">
											<label>Birth Place</label>
											<input type="text" name="tempat_lahir" class="form-control" value="<?php echo e($dt->tempat_lahir); ?>">
										</div>
										<div class="form-group">
											<label>Birth Date</label>
											<input type="date" name="tanggal_lahir" class="form-control" value="<?php echo e($dt->tanggal_lahir); ?>">
										</div>
										<div class="form-group">
											<label>Agama</label>
											<select class="form-control" name="agama">
												<option value="<?php echo e($dt->agama); ?>"><?php echo e($dt->agama); ?></option>
												<option value="Islam">Islam</option>
												<option value="Protestan">Protestan</option>
												<option value="Katolik">Katolik</option>
												<option value="Hindu">Hindu</option>
												<option value="Budha">Budha</option>
												<option value="Konghucu">Konghucu</option>
											</select>
										</div>
										<div class="form-group">
											<div class="row"><div class="col-lg-12"><label>Golongan Darah</label></div></div>
											<div class="row">
												<div class="col-lg-3 col-sm-6 col-xs-6"><div class="radio"><label><input type="radio" name="golongan_darah" value="A" <?php if($dt->golongan_darah=='A')echo "checked";?>>A</label></div></div>
												<div class="col-lg-3 col-sm-6 col-xs-6"><div class="radio"><label><input type="radio" name="golongan_darah" value="B" <?php if($dt->golongan_darah=='B')echo "checked";?>>B</label></div></div>
												<div class="col-lg-3 col-sm-6 col-xs-6"><div class="radio"><label><input type="radio" name="golongan_darah" value="AB" <?php if($dt->golongan_darah=='AB')echo "checked";?>>AB</label></div></div>
												<div class="col-lg-3 col-sm-6 col-xs-6"><div class="radio"><label><input type="radio" name="golongan_darah" value="O" <?php if($dt->golongan_darah=='O')echo "checked";?>>O</label></div></div>
											</div>
										</div>
										<div class="form-group">
											<label>Mother Name</label>
											<input type="text" name="ibu_kandung" class="form-control" value="<?php echo e($dt->ibu_kandung); ?>">
										</div>
										<div class="form-group">
											<label>Nomor KTP</label>
											<input type="number" name="nomor_ktp" class="form-control" value="<?php echo e($dt->nomor_ktp); ?>">
										</div>
										<div class="form-group">
											<label>Nomor KK</label>
											<input type="number" name="nomor_kk" class="form-control" value="<?php echo e($dt->nomor_kk); ?>">
											<input type="hidden" name="nomor_npwp" class="form-control" value="<?php echo e($dt->nomor_npwp); ?>">
											<input type="hidden" name="nomor_rekening" class="form-control" value="<?php echo e($dt->nomor_rekening); ?>">
											<input type="hidden" name="nama_bank" class="form-control" value="<?php echo e($dt->nama_bank); ?>">
										</div>
										<div class="form-group">
											<label>Nomor BPJS Kesehatan</label>
											<input type="number" name="nomor_bpjs_kes" class="form-control" value="<?php echo e($dt->nomor_bpjs_kes); ?>">
										</div>
										<div class="form-group">
											<label>Nomor BPJS Ketenagakerjaan</label>
											<input type="number" name="nomor_bpjs_ket" class="form-control" value="<?php echo e($dt->nomor_bpjs_ket); ?>">
										</div>
										<div class="form-group">
											<label>Nomor Telpon</label>
											<input type="text" name="nomor_telepon" class="form-control" value="<?php echo e($dt->nomor_telepon); ?>">
										</div>
									</div>
									<div class="box-footer">
										<div class="pull-right" style="border:0px;">
											<button type="submit" class="btn btn-success">Save/Update</button>
										</div>
									</div>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<?php if($i==0){?>
										<input type="hidden" value="<?php echo e($id_employee); ?>" name="id_employee">
										<div class="box-body">
											<div class="form-group">
												<label>Birth Place</label>
												<input type="text" name="tempat_lahir" class="form-control" value="">
											</div>
											<div class="form-group">
												<label>Birth Date</label>
												<input type="date" name="tanggal_lahir" class="form-control" value="">
											</div>
											<div class="form-group">
												<label>Agama</label>
												<select class="form-control" name="agama">
													<option value=""></option>
													<option value="Islam">Islam</option>
													<option value="Protestan">Protestan</option>
													<option value="Katolik">Katolik</option>
													<option value="Hindu">Hindu</option>
													<option value="Budha">Budha</option>
													<option value="Konghucu">Konghucu</option>
												</select>
											</div>
											<div class="form-group">
												<div class="row"><div class="col-lg-12"><label>Golongan Darah</label></div></div>
												<div class="row">
													<div class="col-lg-3 col-sm-6 col-xs-6"><div class="radio"><label><input type="radio" name="golongan_darah" value="A">A</label></div></div>
													<div class="col-lg-3 col-sm-6 col-xs-6"><div class="radio"><label><input type="radio" name="golongan_darah" value="B">B</label></div></div>
													<div class="col-lg-3 col-sm-6 col-xs-6"><div class="radio"><label><input type="radio" name="golongan_darah" value="AB">AB</label></div></div>
													<div class="col-lg-3 col-sm-6 col-xs-6"><div class="radio"><label><input type="radio" name="golongan_darah" value="O">O</label></div></div>
												</div>
											</div>
											<div class="form-group">
												<label>Mother Name</label>
												<input type="text" name="ibu_kandung" class="form-control" value="">
											</div>
											<div class="form-group">
												<label>Nomor KTP</label>
												<input type="number" name="nomor_ktp" class="form-control" value="">
											</div>
											<div class="form-group">
												<label>Nomor KK</label>
												<input type="number" name="nomor_kk" class="form-control" value="">
											</div>
											<div class="form-group">
												<label>Nomor NPWP</label>
												<input type="number" name="nomor_npwp" class="form-control" value="">
											</div>
											<div class="form-group">
												<label>Nomor BPJS Kesehatan</label>
												<input type="number" name="nomor_bpjs_kes" class="form-control" value="">
											</div>
											<div class="form-group">
												<label>Nomor BPJS Ketenagakerjaan</label>
												<input type="number" name="nomor_bpjs_ket" class="form-control" value="">
											</div>
											<div class="form-group">
												<label>Nomor Rekening</label>
												<input type="number" name="nomor_rekening" class="form-control" value="">
											</div>
											<div class="form-group">
												<label>Nama Bank</label>
												<input type="text" name="nama_bank" class="form-control" value="">
											</div>
											<div class="form-group">
												<label>Nomor Telpon</label>
												<input type="text" name="nomor_telepon" class="form-control" value="">
											</div>
										</div>
										<div class="box-footer">
											<div class="pull-right" style="border:0px;">
												<button type="submit" class="btn btn-success">Save/Update</button>
											</div>
										</div>
									<?php }?>

								</form>
							</div>
							<div class="box-footer" style="border:0px;">


							</div>
						</div>
						<?php }?>
					</div>
					<div class="col-lg-6 col-sm-7 col-xs-12">
						<div class="box box-success box-solid" style="background:#FFF;padding-bottom:0px;">
							<div class="box-header">
								<i class="fa fa-users"></i>
								<h3 class="box-title">Employee Family</h3>
								<div class="box-tools pull-right">
									<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
									<button type="button" class="btn btn-default btn-xs" data-widget="remove"><i class="fa fa-times"></i></button>
								</div>
							</div>
							<div class="box-body">
								<ul>
									<?php $anak=0;$pasangan=0;?>
									<?php $__currentLoopData = $tb_employee_family; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php if(substr($dt->hubungan,0,4)=='Anak')$anak++;if(substr($dt->hubungan,0,4)!='Anak')$pasangan++;?>
									<li>
										<?php echo e($dt->hubungan); ?> <?php echo e($dt->nama_keluarga); ?> <i class="fa fa-calendar"></i> Lahir <?php echo date('d-m-Y',strtotime($dt->tanggal_lahir));?>
										<a class="delete-modal" data-delid="<?php echo e($dt->id); ?>" data-delid1="Family" data-delname="<?php echo e($dt->nama_keluarga); ?>">
											<span class="pull-right-container">
												<i class="fa fa-trash-o pull-right" style="color:#C00;cursor:pointer;"></i>
											</span>
										</a>
									</li>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<?php $new_anak=$anak+1;?>
								</ul>
							</div>
							<div class="box-footer" style="border:0px;">

								<form role="form" action="/Admin/Employee/Create/Family" method="post">
									<?php echo e(csrf_field()); ?>

									<input type="hidden" value="<?php echo e($id_employee); ?>" name="id_employee">
										<div class="box box-default collapsed-box" style="background:#FFF;border:0px;">
											<div class="box-header">
												<div class="box-tools pull-right">
													<label>New Family</label>&nbsp;&nbsp;
													<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-plus"></i></button>
												</div>
											</div>
											<div class="box-body">
												<div class="col-md-12">
													<div class="form-group">
														<label>Hubungan</label>
														<select name="hubungans" id="hubungans" class="form-control">
															<?php if($pasangan==0){?>
																<option value="<?php if($gender=='Laki-laki')echo "Istri";else echo "Suami";?>"><?php if($gender=='Laki-laki')echo "Istri";else echo "Suami";?></option>
															<?php }?>
															<option value="Anak ke-<?php echo e($new_anak); ?>">Anak ke-<?php echo e($new_anak); ?></option>
														</select>
													</div>
													<div class="form-group">
														<label>Nama Keluarga</label>
														<input type="text" name="nama_keluargas" id="nama_keluargas" class="form-control" placeholder="Entry ...">
													</div>
													<div class="form-group">
														<label>Tanggal Lahir</label>
														<input type="date" name="tanggal_lahir_keluarga" id="tanggal_lahir_keluarga" class="form-control">
													</div>
												</div>
											</div>
											<div class="box-footer">
												<div class="pull-right" style="border:0px;">
													<button type="submit" class="btn btn-success">Submit</button>
												</div>
											</div>
										</div>
								</form>

							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- /.col -->
			<div class="col-lg-4 col-sm-12 col-xs-12" id="address">
				<div class="box box-success box-solid" style="background:#FFF;padding-bottom:0px;">
					<div class="box-header">
						<i class="fa fa-map-marker"></i>
						<h3 class="box-title">Employee Address</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button type="button" class="btn btn-default btn-xs" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">
						<?php $n_address=0;?>
						<?php $__currentLoopData = $tb_address; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<?php $n_address++;?>
						<p style="text-align:left;">
							<?php echo e($dt->detail); ?>, 
							Kel.<?php echo e($dt->kelurahan); ?>, 
							Kab.<?php echo e($dt->kabupaten); ?> - 
							<?php echo e($dt->provinsi); ?>

							<a class="delete-modal" data-delid="<?php echo e($dt->id); ?>" data-delid1="Address" data-delname="<?php echo e($dt->kelurahan); ?>">
								<span class="pull-right-container">
									<i class="fa fa-trash-o pull-right" style="color:#C00;cursor:pointer;"></i>
								</span>
							</a>
						</p>
						<?php $peta=$dt->map_address;?>
						<?php if($dt->map_address!=''){?>
							<div style="padding:7px;border:1px solid #DDD;border-radius:5px;">
								<?php echo $peta;?>
							</div>
						<?php }else echo "<p style='text-align:center;'></p>";?>
						<br>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<?php if($n_address==0)echo "<br><p style='text-align:center;'>No Address Recorded</p>";?>
					</div>
					<?php if($n_address==0): ?>
						<div class="box-footer" style="border:0px;">

							<form role="form" action="/Admin/Employee/Create/Address" method="post">
								<?php echo e(csrf_field()); ?>

								<input type="hidden" value="<?php echo e($id_employee); ?>" name="id_employee">
									<div class="box box-default collapsed-box" style="background:#FFF;border:0px;">
										<div class="box-header">
											<div class="box-tools pull-right">
												<label>Add Address</label>&nbsp;&nbsp;
												<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-plus"></i></button>
											</div>
										</div>
										<div class="box-body">
											<div class="col-md-12">
												<div class="form-group">
													<label>Provinsi</label>
													<select name="provinsi" class="form-control" id="form_prov">
														<option></option>
														<?php $__currentLoopData = $data['tb_prov']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($dt->kode); ?>"><?php echo e($dt->nama); ?></option>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													</select>
												</div>
												<div class="form-group" id="form_kabs">
													<label>Kabupaten</label>
													<select name="kabupaten" class="form-control" id="form_kab">
														<option value=""></option>
													</select>
												</div>
												<div class="form-group" id="form_kecs">
													<label>Kecamatan</label>
													<select name="kecamatan" class="form-control" id="form_kec">
														<option value=""></option>
													</select>
												</div>
												<div class="form-group" id="form_dess">
													<label>Desa/Kelurahan</label>
													<select name="kelurahan" class="form-control" id="form_des">
														<option value=""></option>
													</select>
												</div>
												<div class="form-group" id="detail_address">
													<label>Detail Address</label>
													<textarea name="detail" class="form-control" rows="3" placeholder="Lingkungan/jalan/RT-RW/No.Rumah etc..."></textarea>
												</div>
												<div class="form-group">
													<label>Map Address </label> &nbsp;<a target="_blank" id="link_map">link</a>
													<textarea name="map_address" class="form-control" rows="3" placeholder="Copy from google map ..."></textarea>
												</div>

											</div>
										</div>
										<div class="box-footer">
											<div class="pull-right" style="border:0px;">
												<button type="submit" class="btn btn-success" id="saveAddress">Submit</button>
											</div>
										</div>
									</div>
							</form>

						</div>
					<?php endif; ?>
				</div>
				<div class="box box-success box-solid" style="background:#FFF;padding-bottom:0px;">
					<div class="box-header">
						<i class="fa fa-map-marker"></i>
						<h3 class="box-title">Employee Domiciles</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button type="button" class="btn btn-default btn-xs" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">
						<?php $n_domiciles=0;?>
						<?php $__currentLoopData = $tb_domiciles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<?php $n_domiciles++;?>
						<p style="text-align:left;">
							<?php echo e($dt->detail); ?>, 
							Kel.<?php echo e($dt->kelurahan); ?>, 
							Kab.<?php echo e($dt->kabupaten); ?> - 
							<?php echo e($dt->provinsi); ?>

							<a class="delete-modal" data-delid="<?php echo e($dt->id); ?>" data-delid1="Domicile" data-delname="<?php echo e($dt->kelurahan); ?>">
								<span class="pull-right-container">
									<i class="fa fa-trash-o pull-right" style="color:#C00;cursor:pointer;"></i>
								</span>
							</a>
						</p>
						<?php $peta=$dt->map_address;?>
						<?php if($dt->map_address!=''){?>
							<div style="padding:7px;border:1px solid #DDD;border-radius:5px;">
								<?php echo $peta;?>
							</div>
						<?php }else echo "<p style='text-align:center;'></p>";?>
						<br>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<?php if($n_domiciles==0)echo "<br><p style='text-align:center;'>No Address Recorded</p>";?>
					</div>
					<?php if($n_domiciles==0): ?>
						<div class="box-footer" style="border:0px;">

							<form role="form" action="/Admin/Employee/Create/Domicile" method="post">
								<?php echo e(csrf_field()); ?>

								<input type="hidden" value="<?php echo e($id_employee); ?>" name="id_employee">
									<div class="box box-default collapsed-box" style="background:#FFF;border:0px;">
										<div class="box-header">
											<div class="box-tools pull-right">
												<label>Domicile</label>&nbsp;&nbsp;
												<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-plus"></i></button>
											</div>
										</div>
										<div class="box-body">
											<div class="col-md-12">
												<?php $host1 = mysqli_connect("192.168.1.4","ems","123456","db_wilayah");?>
												<div class="form-group">
													<label>Provinsi</label>
													<select name="provinsi_domicile" class="form-control" id="form_prov3">
														<option></option>
														<?php 
														$qry=mysqli_query($host1,"SELECT kode,nama FROM wilayah_2020 WHERE CHAR_LENGTH(kode)=2 ORDER BY nama")or die(mysqli_error($host1));
														while($dt=mysqli_fetch_array($qry)){
															echo "<option value='".$dt['kode']."'>".$dt['nama']."</option>";
														}?>
													</select>
												</div>
												<div class="form-group" id="form_kabs3">
													<label>Kabupaten</label>
													<select name="kabupaten_domicile" class="form-control" id="form_kab3">
														<option value=""></option>
													</select>
												</div>
												<div class="form-group" id="form_kecs3">
													<label>Kecamatan</label>
													<select name="kecamatan_domicile" class="form-control" id="form_kec3">
														<option value=""></option>
													</select>
												</div>
												<div class="form-group" id="form_dess3">
													<label>Desa/Kelurahan</label>
													<select name="kelurahan_domicile" class="form-control" id="form_des3">
														<option value=""></option>
													</select>
												</div>
												<div class="form-group" id="detail_address3">
													<label>Detail Address</label>
													<textarea name="detail_domicile" class="form-control" rows="3" placeholder="Lingkungan/jalan/RT-RW/No.Rumah etc..."></textarea>
												</div>
												<div class="form-group">
													<label>Map Address </label> &nbsp;<a target="_blank" id="link_map3">link</a>
													<textarea name="map_address_domicile" class="form-control" rows="3" placeholder="Copy from google map ..."></textarea>
												</div>
	
											</div>
										</div>
										<div class="box-footer">
											<div class="pull-right" style="border:0px;">
												<button type="submit" class="btn btn-success" id="saveAddress3">Submit</button>
											</div>
										</div>
									</div>
							</form>
	
						</div>
					<?php endif; ?>
				</div>
				<div class="box box-success box-solid" style="background:#FFF;padding-bottom:0px;">
					<div class="box-header">
						<i class="fa fa-map-marker"></i>
						<h3 class="box-title">Emergency Contact</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button type="button" class="btn btn-default btn-xs" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">
						<ul>
							<?php $__currentLoopData = $tb_address_darurat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<li>
								<?php echo e($dt->nama_keluarga); ?> (<?php echo e($dt->hubungan); ?>) <i class="fa fa-phone"></i> <?php echo e($dt->nomor_kontak); ?><br>
								<?php echo e($dt->detail_kontak); ?>, 
								Kel.<?php echo e($dt->kelurahan_kontak); ?>, 
								Kab.<?php echo e($dt->kabupaten_kontak); ?> - 
								<?php echo e($dt->provinsi_kontak); ?>

								<a class="delete-modal" data-delid="<?php echo e($dt->id); ?>" data-delid1="Kontak" data-delname="<?php echo e($dt->nama_keluarga); ?>">
									<span class="pull-right-container">
										<i class="fa fa-trash-o pull-right" style="color:#C00;cursor:pointer;"></i>
									</span>
								</a>
							</li>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</ul>
					</div>
					<div class="box-footer" style="border:0px;">

						<form role="form" action="/Admin/Employee/Create/Kontak" method="post">
							<?php echo e(csrf_field()); ?>

							<input type="hidden" value="<?php echo e($id_employee); ?>" name="id_employee">
								<div class="box box-default collapsed-box" style="background:#FFF;border:0px;">
									<div class="box-header">
										<div class="box-tools pull-right">
											<label>New Emergency Contact</label>&nbsp;&nbsp;
											<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-plus"></i></button>
										</div>
									</div>
									<div class="box-body">
										<div class="col-md-12">
											<?php $host1 = mysqli_connect("192.168.1.4","ems","123456","db_wilayah");?>
											<div class="form-group">
												<label>Nama Keluarga</label>
												<input type="text" name="nama_keluarga" class="form-control" placeholder="Entry ...">
											</div>
											<div class="form-group">
												<label>Hubungan</label>
												<input type="text" name="hubungan" class="form-control" placeholder="Entry ...">
											</div>
											<div class="form-group">
												<label>Nomor Telepon</label>
												<input type="text" name="nomor_kontak" class="form-control" placeholder="Entry ...">
											</div>
											<div class="form-group">
												<label>Provinsi</label>
												<select name="provinsi_kontak" class="form-control" id="form_prov2">
													<option></option>
													<?php 
													$qry=mysqli_query($host1,"SELECT kode,nama FROM wilayah_2020 WHERE CHAR_LENGTH(kode)=2 ORDER BY nama")or die(mysqli_error($host1));
													while($dt=mysqli_fetch_array($qry)){
														echo "<option value='".$dt['kode']."'>".$dt['nama']."</option>";
													}?>
												</select>
											</div>
											<div class="form-group" id="form_kabs2">
												<label>Kabupaten</label>
												<select name="kabupaten_kontak" class="form-control" id="form_kab2">
													<option value=""></option>
												</select>
											</div>
											<div class="form-group" id="form_kecs2">
												<label>Kecamatan</label>
												<select name="kecamatan_kontak" class="form-control" id="form_kec2">
													<option value=""></option>
												</select>
											</div>
											<div class="form-group" id="form_dess2">
												<label>Desa/Kelurahan</label>
												<select name="kelurahan_kontak" class="form-control" id="form_des2">
													<option value=""></option>
												</select>
											</div>
											<div class="form-group" id="detail_address2">
												<label>Detail Address</label>
												<textarea name="detail_kontak" class="form-control" rows="3" placeholder="Lingkungan/jalan/RT-RW/No.Rumah etc..."></textarea>
											</div>

										</div>
									</div>
									<div class="box-footer">
										<div class="pull-right" style="border:0px;">
											<button type="submit" class="btn btn-success" id="saveAddress2">Submit</button>
										</div>
									</div>
								</div>
						</form>

					</div>
				</div>
				<div class="box box-success box-solid" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-book"></i>
						<h3 class="box-title">Education</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button type="button" class="btn btn-default btn-xs" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">
						<ul>
							<?php $__currentLoopData = $tb_education; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<li>
								<b><?php echo e($dt->level_education); ?></b>, <?php echo e($dt->prodi); ?> <?php echo e($dt->institute); ?>. Graduate on <?php echo e($dt->graduate_year); ?>

								<a class="delete-modal" data-delid="<?php echo e($dt->id); ?>" data-delid1="Education" data-delname="<?php echo e($dt->institute); ?>">
									<span class="pull-right-container">
										<i class="fa fa-trash-o pull-right" style="color:#C00;cursor:pointer;"></i>
									</span>
								</a>
							</li>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</ul>
					</div>
					<div class="box-footer" style="border:0px;">

						<form role="form" action="/Admin/Employee/Create/Education" method="post">
							<?php echo e(csrf_field()); ?>

							<input type="hidden" value="<?php echo e($id_employee); ?>" name="id_employee">
								<div class="box box-default collapsed-box" style="background:#FFF;border:0px;">
									<div class="box-header">
										<div class="box-tools pull-right">
											<label>Add Education</label>&nbsp;&nbsp;
											<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-plus"></i></button>
										</div>
									</div>
									<div class="box-body">
										<div class="form-group">
											<label>Department</label>
											<select class="form-control" name="level_education" id="level_education">
												<option value=""></option>
												<option value="SD">SD</option>
												<option value="SMP">SMP</option>
												<option value="SMA">SMA</option>
												<option value="SMK">SMK</option>
												<option value="D1">D1</option>
												<option value="D2">D2</option>
												<option value="D3">D3</option>
												<option value="S1">S1</option>
												<option value="S2">S2</option>
												<option value="S3">S3</option>
											</select>
										</div>
										<div class="form-group">
											<label>Institute</label>
											<input type="text" name="institute" class="form-control" placeholder="Entry ...">
										</div>
										<div class="form-group">
											<label>Program Study</label>
											<input type="text" name="prodi" class="form-control" placeholder="Entry ...">
										</div>
										<div class="form-group">
											<div class="col-xs-6" style="padding:0px 3px 0px 0px;">
												<label>Year</label>
												<input type="number" name="year" class="form-control">
											</div>
											<div class="col-xs-6" style="padding:0px 0px 0px 3px;">
												<label>Graduate</label>
												<input type="year" name="graduate_year" class="form-control">
											</div>
										</div>
										<div class="form-group">
											<label>Remark</label>
											<textarea name="remark" class="form-control" placeholder="Entry ..."></textarea>
										</div>
									</div>
									<div class="box-footer">
										<div class="pull-right" style="border:0px;">
											<button type="submit" class="btn btn-success">Submit</button>
										</div>
									</div>
								</div>
						</form>

					</div>
				</div>
				<div class="box box-success box-solid" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-list-alt"></i>
						<h3 class="box-title">Experience</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button type="button" class="btn btn-default btn-xs" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">
						<ul>
							<?php $__currentLoopData = $tb_experience; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<li>
								<?php echo e($dt->factory); ?><br> <?php echo e($dt->section); ?> <?php echo e($dt->year); ?> Years, Finish on <?php echo e($dt->finish_year); ?>

								<a class="delete-modal" data-delid="<?php echo e($dt->id); ?>" data-delid1="Experience" data-delname="<?php echo e($dt->factory); ?>">
									<span class="pull-right-container">
										<i class="fa fa-trash-o pull-right" style="color:#C00;cursor:pointer;"></i>
									</span>
								</a>
							</li>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</ul>
					</div>
					<div class="box-footer" style="border:0px;">

						<form role="form" action="/Admin/Employee/Create/Experience" method="post">
							<?php echo e(csrf_field()); ?>

							<input type="hidden" value="<?php echo e($id_employee); ?>" name="id_employee">
								<div class="box box-default collapsed-box" style="background:#FFF;border:0px;">
									<div class="box-header">
										<div class="box-tools pull-right">
											<label>Add Experience</label>&nbsp;&nbsp;
											<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-plus"></i></button>
										</div>
									</div>
									<div class="box-body">
										<div class="form-group">
											<label>Company/Factory</label>
											<input type="text" name="factory" class="form-control" placeholder="Entry ...">
										</div>
										<div class="form-group">
											<label>Position</label>
											<input type="text" name="section" class="form-control" placeholder="Entry ...">
										</div>
										<div class="form-group">
											<div class="col-xs-6" style="padding:0px 3px 0px 0px;">
												<label>Year</label>
												<input type="number" name="year" class="form-control">
											</div>
											<div class="col-xs-6" style="padding:0px 0px 0px 3px;">
												<label>Finish</label>
												<input type="year" name="finish_year" class="form-control">
											</div>
										</div>
										<div class="form-group">
											<label>Remark</label>
											<textarea name="remark" class="form-control" placeholder="Entry ..."></textarea>
										</div>
									</div>
									<div class="box-footer">
										<div class="pull-right" style="border:0px;">
											<button type="submit" class="btn btn-default">Submit</button>
										</div>
									</div>
								</div>
						</form>

					</div>
				</div>
				<div class="box box-success box-solid" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-pencil"></i>
						<h3 class="box-title">Skill</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button type="button" class="btn btn-default btn-xs" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">
						<ul>
							<?php $__currentLoopData = $tb_skill; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<li>
								<?php echo e($dt->skill_name); ?>

								<a class="delete-modal" data-delid="<?php echo e($dt->id); ?>" data-delid1="Skill" data-delname="<?php echo e($dt->skill_name); ?>">
									<span class="pull-right-container">
										<i class="fa fa-trash-o pull-right" style="color:#C00;cursor:pointer;"></i>
									</span>
								</a>
							</li>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</ul>
					</div>
					<div class="box-footer" style="border:0px;">

						<form role="form" action="/Admin/Employee/Create/Skill" method="post">
							<?php echo e(csrf_field()); ?>

							<input type="hidden" value="<?php echo e($id_employee); ?>" name="id_employee">
								<div class="box box-default collapsed-box" style="background:#FFF;border:0px;">
									<div class="box-header">
										<div class="box-tools pull-right">
											<label>Add Skill</label>&nbsp;&nbsp;
											<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-plus"></i></button>
										</div>
									</div>
									<div class="box-body">
										<div class="form-group">
											<label>Skill Name</label>
											<input type="text" name="skill_name" class="form-control" placeholder="Entry ...">
										</div>
										<div class="form-group">
											<div class="col-xs-6">
												<div class="radio">
													<label>
														<input type="radio" name="skill_type" value="Hard Skill" checked>
														Hard Skill
													</label>
												</div>
											</div>
											<div class="col-xs-6">
												<div class="radio">
													<label>
														<input type="radio" name="skill_type" value="Soft Skill">
														Soft Skill
													</label>
												</div>
											</div>
										</div>
									</div>
									<div class="box-footer">
										<div class="pull-right" style="border:0px;">
											<button type="submit" class="btn btn-warning">Submit</button>
										</div>
									</div>
								</div>
						</form>

					</div>
				</div>
			</div>
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

    <?php if($message = Session::get('success')): ?>
		<div class="alert alert-success alert-dismissible" style="position:absolute;width:350px;right:10px;top:65px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-info"></i> Success Alert</h4>
			<?php echo e($message); ?>

		</div>
    <?php endif; ?>
	<?php if($errors->any()): ?>
		<div class="alert alert-danger alert-dismissible" style="position:absolute;width:350px;right:10px;top:65px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-warning"></i> Saving Failed Alert!</h4>
				<?php if($errors->has('PIN')): ?>
					- PIN belum dipilih<br>
				<?php endif; ?>
				<?php if($errors->has('NIK')): ?>
					- NIK harus diisi<br>
				<?php endif; ?>
				<?php if($errors->has('employee_name')): ?>
					- Nama Employee tidak boleh kosong<br>
				<?php endif; ?>
				<?php if($errors->has('gender')): ?>
					- Pilih jenis kelamin Laki-laki atau Perempuan<br>
				<?php endif; ?>
				<?php if($errors->has('join_date')): ?>
					- Join Date harus diisi<br>
				<?php endif; ?>
				<?php if($errors->has('dept_id')): ?>
					- Department belum dipilih<br>
				<?php endif; ?>
				<?php if($errors->has('position_id')): ?>
					- Position belum dipilih<br>
				<?php endif; ?>
				<?php if($errors->has('leader_id')): ?>
					- Direct leader belum dipilih<br>
				<?php endif; ?>
				<?php if($errors->has('id_ktp')): ?>
					- ID KTP harus diisi<br>
				<?php endif; ?>
				<?php if($errors->has('detail')): ?>
					- Detail Address harus diisi<br>
				<?php endif; ?>
				<?php if($errors->has('group')): ?>
					- Group harus dipilih<br>
				<?php endif; ?>
				<?php if($errors->has('level_education')): ?>
					- Level Eductaion can not be Null<br>
				<?php endif; ?>
				<?php if($errors->has('institute')): ?>
					- Institute harus diisi<br>
				<?php endif; ?>
				<?php if($errors->has('prodi')): ?>
					- Program Study harus diisi<br>
				<?php endif; ?>
				<?php if($errors->has('year')): ?>
					- Lama Tahun harus diisi<br>
				<?php endif; ?>
				<?php if($errors->has('graduate_year')): ?>
					- Graduate Year Harus diisi<br>
				<?php endif; ?>
				<?php if($errors->has('factory')): ?>
					- Factory/Company harus diisi<br>
				<?php endif; ?>
				<?php if($errors->has('section')): ?>
					- Section Harus diisi<br>
				<?php endif; ?>
				<?php if($errors->has('finish_year')): ?>
					- Finish Year harus diisi<br>
				<?php endif; ?>
				<?php if($errors->has('skill_name')): ?>
					- Skill Name tidak boleh kosong<br>
				<?php endif; ?>
				<?php if($errors->has('posisi2')): ?>
					- Posisi tidak boleh kosong<br>
				<?php endif; ?>
				<?php if($errors->has('nama_keluargas')): ?>
					- Nama Keluarga tidak boleh kosong<br>
				<?php endif; ?>
				<?php if($errors->has('hubungans')): ?>
					- Hubungan tidak boleh kosong<br>
				<?php endif; ?>
				<?php if($errors->has('tanggal_lahir_keluarga')): ?>
					- Tanggal Lahir tidak boleh kosong<br>
				<?php endif; ?>
				<?php echo e($errors); ?>

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
	<script type="text/javascript">
		$(document).ready(function(){
 			// sembunyikan form kabupaten, kecamatan dan desa
			$("#form_kab").hide();
			$("#form_kec").hide();
			$("#form_des").hide();

			$("#form_kabs").hide();
			$("#form_kecs").hide();
			$("#form_dess").hide();
			$("#detail_address").hide();
			$("#link_map").hide();
			$("#saveAddress").hide();
			//$("#address").hide();
 
			// ambil data kabupaten ketika data memilih provinsi
			$('body').on("change","#form_prov",function(){
				var id = $(this).val();
				var data = "id="+id;
				$.ajax({
					type: 'POST',
					url: "/Admin/Employee/Kabupaten",
					data: data,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(hasil) {
						$("#form_kab").html(hasil);
						$("#form_kab").show();
						$("#form_kec").hide();
						$("#form_des").hide();

						$("#form_kabs").show();
						$("#form_kecs").hide();
						$("#form_dess").hide();
						$("#link_map").hide();
					}
				});
			});
 
			// ambil data kecamatan/kota ketika data memilih kabupaten
			$('body').on("change","#form_kab",function(){
				var id = $(this).val();
				var data = "id="+id;
				$.ajax({
					type: 'POST',
					url: "/Admin/Employee/Kecamatan",
					data: data,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(hasil) {
						$("#form_kec").html(hasil);
						$("#form_kec").show();
						$("#form_des").hide();

						$("#form_kecs").show();
						$("#form_dess").hide();
						$("#detail_address").hide();
						$("#link_map").hide();
					}
				});
			});
 
			// ambil data desa ketika data memilih kecamatan/kota
			$('body').on("change","#form_kec",function(){
				var id = $(this).val();
				var data = "id="+id;
				$.ajax({
					type: 'POST',
					url: "/Admin/Employee/Desa",
					data: data,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(hasil) {
						$("#form_des").html(hasil);
						$("#form_des").show();

						$("#form_dess").show();
						$("#detail_address").hide();
						$("#link_map").hide();
					}
				});
			});
			$('body').on("change","#form_des",function(){
				var des = $(this).val();
				var kec = $('#form_kec').val();
				var data = "desa="+des+"&kecamatan="+kec;
				$.ajax({
					type: 'POST',
					url: "/Admin/Employee/Link",
					data: data,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(hasil) {
						document.getElementById("link_map").setAttribute("href", hasil); 
						//$("#link_map").html(hasil);
						$("#detail_address").show();
						$("#link_map").show();
						$("#saveAddress").show();
					}
				});
			});
 
 
		});
	</script>	
	<script type="text/javascript">
		$(document).ready(function(){
 			// sembunyikan form kabupaten, kecamatan dan desa
			$("#form_kab2").hide();
			$("#form_kec2").hide();
			$("#form_des2").hide();

			$("#form_kabs2").hide();
			$("#form_kecs2").hide();
			$("#form_dess2").hide();
			$("#detail_address2").hide();
			$("#saveAddress2").hide();
			//$("#address").hide();
 
			// ambil data kabupaten ketika data memilih provinsi
			$('body').on("change","#form_prov2",function(){
				var id = $(this).val();
				var data = "id="+id;
				$.ajax({
					type: 'POST',
					url: "/Admin/Employee/Kabupaten",
					data: data,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(hasil) {
						$("#form_kab2").html(hasil);
						$("#form_kab2").show();
						$("#form_kec2").hide();
						$("#form_des2").hide();

						$("#form_kabs2").show();
						$("#form_kecs2").hide();
						$("#form_dess2").hide();
					}
				});
			});
 
			// ambil data kecamatan/kota ketika data memilih kabupaten
			$('body').on("change","#form_kab2",function(){
				var id = $(this).val();
				var data = "id="+id;
				$.ajax({
					type: 'POST',
					url: "/Admin/Employee/Kecamatan",
					data: data,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(hasil) {
						$("#form_kec2").html(hasil);
						$("#form_kec2").show();
						$("#form_des2").hide();

						$("#form_kecs2").show();
						$("#form_dess2").hide();
						$("#detail_address2").hide();
					}
				});
			});
 
			// ambil data desa ketika data memilih kecamatan/kota
			$('body').on("change","#form_kec2",function(){
				var id = $(this).val();
				var data = "id="+id;
				$.ajax({
					type: 'POST',
					url: "/Admin/Employee/Desa",
					data: data,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(hasil) {
						$("#form_des2").html(hasil);
						$("#form_des2").show();

						$("#form_dess2").show();
						$("#detail_address2").hide();
					}
				});
			});
			$('body').on("change","#form_des2",function(){
				$("#detail_address2").show();
				$("#saveAddress2").show();
			});
 
 
		});
	</script>	
	<script type="text/javascript">
		$(document).ready(function(){
 			// sembunyikan form kabupaten, kecamatan dan desa
			$("#form_kab3").hide();
			$("#form_kec3").hide();
			$("#form_des3").hide();

			$("#form_kabs3").hide();
			$("#form_kecs3").hide();
			$("#form_dess3").hide();
			$("#detail_address3").hide();
			$("#saveAddress3").hide();
			$("#link_map3").hide();
			//$("#address").hide();
 
			// ambil data kabupaten ketika data memilih provinsi
			$('body').on("change","#form_prov3",function(){
				var id = $(this).val();
				var data = "id="+id;
				$.ajax({
					type: 'POST',
					url: "/Admin/Employee/Kabupaten",
					data: data,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(hasil) {
						$("#form_kab3").html(hasil);
						$("#form_kab3").show();
						$("#form_kec3").hide();
						$("#form_des3").hide();

						$("#form_kabs3").show();
						$("#form_kecs3").hide();
						$("#form_dess3").hide();
						$("#detail_address3").hide();
						$("#link_map3").hide();
					}
				});
			});
 
			// ambil data kecamatan/kota ketika data memilih kabupaten
			$('body').on("change","#form_kab3",function(){
				var id = $(this).val();
				var data = "id="+id;
				$.ajax({
					type: 'POST',
					url: "/Admin/Employee/Kecamatan",
					data: data,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(hasil) {
						$("#form_kec3").html(hasil);
						$("#form_kec3").show();
						$("#form_des3").hide();

						$("#form_kecs3").show();
						$("#form_dess3").hide();
						$("#detail_address3").hide();
						$("#link_map3").hide();
					}
				});
			});
 
			// ambil data desa ketika data memilih kecamatan/kota
			$('body').on("change","#form_kec3",function(){
				var id = $(this).val();
				var data = "id="+id;
				$.ajax({
					type: 'POST',
					url: "/Admin/Employee/Desa",
					data: data,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(hasil) {
						$("#form_des3").html(hasil);
						$("#form_des3").show();

						$("#form_dess3").show();
						$("#detail_address3").hide();
						$("#link_map3").hide();
					}
				});
			});
			$('body').on("change","#form_des3",function(){
				$("#detail_address3").show();
				$("#saveAddress3").show();
				$("#link_map3").show();
			});
 
 
		});
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/m_employee/employee_update.blade.php ENDPATH**/ ?>