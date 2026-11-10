@extends('layouts/home')
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
	<!-- <div class="content-wrapper"> -->
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Employee
				<small>setup</small>
			</h1>
		</section>
		@foreach($tb_employee as $row)
			<?php 
				$gender=$row->gender;
				$id_employee=$row->id;
			?>
		@endforeach
		<?php $id_ktp='';?>
		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-lg-4 col-sm-12 col-xs-12">
				<div class="row">
					<div class="col-lg-12 col-sm-12 col-xs-12">
						<?php if (request()->user()->hasRole('ess')){?>
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
								<form role="form" action="/Profile/SaveDetail" method="post">
									{{ csrf_field() }}
									<?php $i=0;?>
									@foreach($tb_detail as $dt)
									<?php $i++;?>
									<input type="hidden" value="{{$id_employee}}" name="id_employee">
									<div class="box-body">
										<div class="form-group">
											<label>Tempat Lahir</label>
											<input type="text" name="tempat_lahir" class="form-control" value="{{$dt->tempat_lahir}}">
										</div>
										<div class="form-group">
											<label>Tanggal Lahir</label>
											<input type="date" name="tanggal_lahir" class="form-control" value="{{$dt->tanggal_lahir}}">
										</div>
										<div class="form-group">
											<label>Agama</label>
											<select class="form-control" name="agama">
												<option value="{{$dt->agama}}">{{$dt->agama}}</option>
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
											<label>Ibu Kandung</label>
											<input type="text" name="ibu_kandung" class="form-control" value="{{$dt->ibu_kandung}}">
										</div>
										<div class="form-group">
											<label>Nomor KTP</label>
											<?php $id_ktp=$dt->nomor_ktp;?>
											<input type="number" name="nomor_ktp" class="form-control" value="{{$dt->nomor_ktp}}">
										</div>
										<div class="form-group">
											<label>Nomor KK</label>
											<input type="number" name="nomor_kk" class="form-control" value="{{$dt->nomor_kk}}">
											<!-- <input type="hidden" name="nomor_npwp" class="form-control" value="{{$dt->nomor_npwp}}">
											<input type="hidden" name="nomor_rekening" class="form-control" value="{{$dt->nomor_rekening}}">
											<input type="hidden" name="nama_bank" class="form-control" value="{{$dt->nama_bank}}"> -->
										</div>
										<div class="form-group">
											<label>Nomor BPJS Kesehatan</label>
											<input type="number" name="nomor_bpjs_kes" class="form-control" value="{{$dt->nomor_bpjs_kes}}">
										</div>
										<div class="form-group">
											<label>Nomor BPJS Ketenagakerjaan</label>
											<input type="number" name="nomor_bpjs_ket" class="form-control" value="{{$dt->nomor_bpjs_ket}}">
										</div>
										<div class="form-group">
											<label>Nomor NPWP</label>
											<input type="number" name="nomor_npwp" class="form-control" value="{{$dt->nomor_npwp}}">
										</div>
										<div class="form-group">
											<label>Status Pajak</label>
											<select class="form-control" name="kode_status">
												<option value="{{$dt->kode_status}}">{{$dt->kode_status}}</option>
												<option value="TK">TK (Tidak Kawin)</option>
												<option value="K0">K0 (Kawin Anak 0)</option>
												<option value="K1">K1 (Kawin Anak 1)</option>
												<option value="K2">K2 (Kawin Anak 2)</option>
												<option value="K3">K3 (Kawin Anak 3)</option>
												<option value="TK1">TK1 (Tidak Kawin Anak 1)</option>
												<option value="TK2">TK2 (Tidak Kawin Anak 2)</option>
												<option value="TK3">TK3 (Tidak Kawin Anak 3)</option>
											</select>
										</div>
										<div class="form-group">
											<label>Nomor Rekening</label>
											<input type="number" name="nomor_rekening" class="form-control" value="{{$dt->nomor_rekening}}">
										</div>
										<div class="form-group">
											<label>Nama Bank</label>
											<input type="text" name="nama_bank" class="form-control" value="{{$dt->nama_bank}}">
										</div>
										<div class="form-group">
											<label>Nomor Telpon</label>
											<input type="text" name="nomor_telepon" class="form-control" value="{{$dt->nomor_telepon}}">
										</div>
									</div>
									<div class="box-footer">
										<div class="pull-right" style="border:0px;">
											<button type="submit" class="btn btn-success">Save/Update</button>
										</div>
									</div>
									@endforeach
									<?php if($i==0){?>
										<input type="hidden" value="{{$id_employee}}" name="id_employee">
										<div class="box-body">
											<div class="form-group">
												<label>Tempat Lahir</label>
												<input type="text" name="tempat_lahir" class="form-control" value="">
											</div>
											<div class="form-group">
												<label>Tanggal Lahir</label>
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
												<label>Ibu Kandung</label>
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
											<label>Status Pajak</label>
											<select class="form-control" name="kode_status">
												<option value=""></option>
												<option value="TK">TK (Tidak Kawin)</option>
												<option value="K0">K0 (Kawin Anak 0)</option>
												<option value="K1">K1 (Kawin Anak 1)</option>
												<option value="K2">K2 (Kawin Anak 2)</option>
												<option value="K3">K3 (Kawin Anak 3)</option>
												<option value="TK1">TK1 (Tidak Kawin Anak 1)</option>
												<option value="TK2">TK2 (Tidak Kawin Anak 2)</option>
												<option value="TK3">TK3 (Tidak Kawin Anak 3)</option>
											</select>
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
				</div>
			</div>
			<div class="col-lg-4 col-sm-12 col-xs-12">
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
							@foreach($tb_employee_family as $dt)
							<?php if(substr($dt->hubungan,0,4)=='Anak')$anak++;if(substr($dt->hubungan,0,4)!='Anak')$pasangan++;?>
							<li>
								{{$dt->hubungan}} {{$dt->nama_keluarga}} <i class="fa fa-calendar"></i> Lahir <?php echo date('d-m-Y',strtotime($dt->tanggal_lahir));?>
								<a class="delete-modal" data-delid="{{$dt->id}}" data-delid1="Family" data-delname="{{$dt->nama_keluarga}}">
									<span class="pull-right-container">
										<i class="fa fa-trash-o pull-right" style="color:#C00;cursor:pointer;"></i>
									</span>
								</a>
							</li>
							@endforeach
							<?php $new_anak=$anak+1;?>
						</ul>
						<?php //echo $anak." ".$pasangan."<br>".$dt->hubungan."=".substr($dt->hubungan,0,4);?>
					</div>
					<div class="box-footer" style="border:0px;">

						<form role="form" action="/Profile/SaveFamily" method="post">
							{{ csrf_field() }}
							<input type="hidden" value="{{$id_employee}}" name="id_employee">
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
													<option value="Anak ke-{{$new_anak}}">Anak ke-{{$new_anak}}</option>
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
							@foreach($tb_education as $dt)
							<li>
								<b>{{$dt->level_education}}</b>, {{$dt->prodi}} {{$dt->institute}}. Graduate on {{$dt->graduate_year}}
								<a class="delete-modal" data-delid="{{$dt->id}}" data-delid1="Education" data-delname="{{$dt->institute}}">
									<span class="pull-right-container">
										<i class="fa fa-trash-o pull-right" style="color:#C00;cursor:pointer;"></i>
									</span>
								</a>
							</li>
							@endforeach
						</ul>
					</div>
					<div class="box-footer" style="border:0px;">

						<form role="form" action="/Profile/SaveEducation" method="post">
							{{ csrf_field() }}
							<input type="hidden" value="{{$id_employee}}" name="id_employee">
								<div class="box box-default collapsed-box" style="background:#FFF;border:0px;">
									<div class="box-header">
										<div class="box-tools pull-right">
											<label>Add Education</label>&nbsp;&nbsp;
											<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-plus"></i></button>
										</div>
									</div>
									<div class="box-body">
										<div class="form-group">
											<label>Pendidikan</label>
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
												<label>Tahun Mulai</label>
												<input type="number" name="year" class="form-control">
											</div>
											<div class="col-xs-6" style="padding:0px 0px 0px 3px;">
												<label>Tahun Lulus</label>
												<input type="number" name="graduate_year" class="form-control">
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
							@foreach($tb_experience as $dt)
							<li>
								{{$dt->factory}}<br> {{$dt->section}} {{$dt->year}} Years, Finish on {{$dt->finish_year}}
								<a class="delete-modal" data-delid="{{$dt->id}}" data-delid1="Experience" data-delname="{{$dt->factory}}">
									<span class="pull-right-container">
										<i class="fa fa-trash-o pull-right" style="color:#C00;cursor:pointer;"></i>
									</span>
								</a>
							</li>
							@endforeach
						</ul>
					</div>
					<div class="box-footer" style="border:0px;">

						<form role="form" action="/Profile/SaveExperience" method="post">
							{{ csrf_field() }}
							<input type="hidden" value="{{$id_employee}}" name="id_employee">
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
							@foreach($tb_skill as $dt)
							<li>
								{{$dt->skill_name}}
								<a class="delete-modal" data-delid="{{$dt->id}}" data-delid1="Skill" data-delname="{{$dt->skill_name}}">
									<span class="pull-right-container">
										<i class="fa fa-trash-o pull-right" style="color:#C00;cursor:pointer;"></i>
									</span>
								</a>
							</li>
							@endforeach
						</ul>
					</div>
					<div class="box-footer" style="border:0px;">

						<form role="form" action="/Profile/SaveSkill" method="post">
							{{ csrf_field() }}
							<input type="hidden" value="{{$id_employee}}" name="id_employee">
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
			<!-- /.col -->
			<div class="col-lg-4 col-sm-12 col-xs-12" id="address">
				<div class="box box-success box-solid" style="background:#FFF;padding-bottom:0px;">
					<div class="box-header">
						<i class="fa fa-map-marker"></i>
						<h3 class="box-title">Employee Adress</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button type="button" class="btn btn-default btn-xs" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">
						@foreach($tb_address as $dt)
						<p style="text-align:left;">
							{{$dt->detail}}, 
							Kel.{{$dt->kelurahan}}, 
							Kab.{{$dt->kabupaten}} - 
							{{$dt->provinsi}}
							<a class="delete-modal" data-delid="{{$dt->id}}" data-delid1="Address" data-delname="{{$dt->kelurahan}}">
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
						@endforeach
						<?php if(!isset($dt->detail))echo "<br><p style='text-align:center;'>No Address Recorded</p>";?>
					</div>
					<div class="box-footer" style="border:0px;">

						<form role="form" action="/Profile/SaveAddress" method="post" class="region-form">
							{{ csrf_field() }}
							<input type="hidden" value="{{$id_employee}}" name="id_employee">
								<div class="box box-default collapsed-box" style="background:#FFF;border:0px;">
									<div class="box-header">
										<div class="box-tools pull-right">
											<label>Add Address</label>&nbsp;&nbsp;
											<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-plus"></i></button>
										</div>
									</div>
									<div class="box-body">
										<div class="col-md-12">
											<!-- <div class="form-group">
												<label>ID-KTP</label>
												<input type="text" name="id_ktp" class="form-control" placeholder="Entry ...">
											</div> -->
											<div class="form-group">
												<input type="hidden" name="id_ktp" class="form-control" value="{{$id_ktp}}">
												<label>Provinsi</label>
												<select name="provinsi" class="form-control region-select" data-region-level="province" id="form_prov" required>
													<option value="">Pilih provinsi</option>
													@foreach($tb_provinsi as $provinsi)
														<option value="{{ $provinsi->kode }}">{{ $provinsi->nama }}</option>
													@endforeach
												</select>
											</div>
											<div class="form-group region-field" data-region-container="regency" id="form_kabs" hidden>
												<label>Kabupaten</label>
												<select name="kabupaten" class="form-control region-select" data-region-level="regency" id="form_kab" disabled required>
													<option value="">Pilih kabupaten/kota</option>
												</select>
											</div>
											<div class="form-group region-field" data-region-container="district" id="form_kecs" hidden>
												<label>Kecamatan</label>
												<select name="kecamatan" class="form-control region-select" data-region-level="district" id="form_kec" disabled required>
													<option value="">Pilih kecamatan</option>
												</select>
											</div>
											<div class="form-group region-field" data-region-container="village" id="form_dess" hidden>
												<label>Desa/Kelurahan</label>
												<select name="kelurahan" class="form-control region-select" data-region-level="village" id="form_des" disabled required>
													<option value="">Pilih desa/kelurahan</option>
												</select>
											</div>
											<div class="form-group" id="detail_address">
												<label>Detail Address</label>
												<textarea name="detail" class="form-control" rows="3" placeholder="Lingkungan/jalan/RT-RW/No.Rumah etc..." required></textarea>
											</div>
											<input type="hidden" name="map_address" value="">
											<!-- <div class="form-group">
												<label>Map Address </label> &nbsp;<a target="_blank" id="link_map">link</a>
												<textarea name="map_address" class="form-control" rows="3" placeholder="Copy from google map ..."></textarea>
											</div> -->

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
				</div>
				<div class="box box-info box-solid" style="background:#FFF;padding-bottom:0px;">
					<div class="box-header">
						<i class="fa fa-map-marker"></i>
						<h3 class="box-title">Employee Domicile</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button type="button" class="btn btn-default btn-xs" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">
						@foreach($tb_domiciles as $dt)
						<p style="text-align:left;">
							{{$dt->detail}}, 
							Kel.{{$dt->kelurahan}}, 
							Kab.{{$dt->kabupaten}} - 
							{{$dt->provinsi}}
							<a class="delete-modal" data-delid="{{$dt->id}}" data-delid1="Domicile" data-delname="{{$dt->kelurahan}}">
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
						@endforeach
						<?php if(!isset($dt->detail))echo "<br><p style='text-align:center;'>No Address Recorded</p>";?>
					</div>
					<div class="box-footer" style="border:0px;">

						<form role="form" action="/Profile/SaveDomicile" method="post" class="region-form">
							{{ csrf_field() }}
							<input type="hidden" value="{{$id_employee}}" name="id_employee">
								<div class="box box-default collapsed-box" style="background:#FFF;border:0px;">
									<div class="box-header">
										<div class="box-tools pull-right">
											<label>Add Address</label>&nbsp;&nbsp;
											<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-plus"></i></button>
										</div>
									</div>
									<div class="box-body">
										<div class="col-md-12">
											<!-- <div class="form-group">
												<label>ID-KTP</label>
												<input type="hidden" name="id_ktp" class="form-control" value="{{$id_ktp}}">
											</div> -->
											<div class="form-group">
												<input type="hidden" name="id_ktp" class="form-control" value="{{$id_ktp}}">
												<label>Provinsi</label>
												<select name="provinsi_domicile" class="form-control region-select" data-region-level="province" id="form_prov3" required>
													<option value="">Pilih provinsi</option>
													@foreach($tb_provinsi as $provinsi)
														<option value="{{ $provinsi->kode }}">{{ $provinsi->nama }}</option>
													@endforeach
												</select>
											</div>
											<div class="form-group region-field" data-region-container="regency" id="form_kabs3" hidden>
												<label>Kabupaten</label>
												<select name="kabupaten_domicile" class="form-control region-select" data-region-level="regency" id="form_kab3" disabled required>
													<option value="">Pilih kabupaten/kota</option>
												</select>
											</div>
											<div class="form-group region-field" data-region-container="district" id="form_kecs3" hidden>
												<label>Kecamatan</label>
												<select name="kecamatan_domicile" class="form-control region-select" data-region-level="district" id="form_kec3" disabled required>
													<option value="">Pilih kecamatan</option>
												</select>
											</div>
											<div class="form-group region-field" data-region-container="village" id="form_dess3" hidden>
												<label>Desa/Kelurahan</label>
												<select name="kelurahan_domicile" class="form-control region-select" data-region-level="village" id="form_des3" disabled required>
													<option value="">Pilih desa/kelurahan</option>
												</select>
											</div>
											<div class="form-group" id="detail_address3">
												<label>Detail Address</label>
												<textarea name="detail_domicile" class="form-control" rows="3" placeholder="Lingkungan/jalan/RT-RW/No.Rumah etc..." required></textarea>
											</div>
											<input type="hidden" name="map_address" value="">
											<!-- <div class="form-group">
												<label>Map Address </label> &nbsp;<a target="_blank" id="link_map">link</a>
												<textarea name="map_address" class="form-control" rows="3" placeholder="Copy from google map ..."></textarea>
											</div> -->
											<!-- <div class="form-group">
												<label>Map Address </label> &nbsp;<a target="_blank" id="link_map">link</a>
												<textarea name="map_address" class="form-control" rows="3" placeholder="Copy from google map ..."></textarea>
											</div> -->

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
				</div>
				<div class="box box-danger box-solid" style="background:#FFF;padding-bottom:0px;">
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
							@foreach($tb_address_darurat as $dt)
							<li>
								{{$dt->nama_keluarga}} ({{$dt->hubungan}}) <i class="fa fa-phone"></i> {{$dt->nomor_kontak}}<br>
								{{$dt->detail_kontak}}, 
								Kel.{{$dt->kelurahan_kontak}}, 
								Kab.{{$dt->kabupaten_kontak}} - 
								{{$dt->provinsi_kontak}}
								<a class="delete-modal" data-delid="{{$dt->id}}" data-delid1="Kontak" data-delname="{{$dt->nama_keluarga}}">
									<span class="pull-right-container">
										<i class="fa fa-trash-o pull-right" style="color:#C00;cursor:pointer;"></i>
									</span>
								</a>
							</li>
							@endforeach
						</ul>
					</div>
					<div class="box-footer" style="border:0px;">

						<form role="form" action="/Profile/SaveKontak" method="post" class="region-form">
							{{ csrf_field() }}
							<input type="hidden" value="{{$id_employee}}" name="id_employee">
								<div class="box box-default collapsed-box" style="background:#FFF;border:0px;">
									<div class="box-header">
										<div class="box-tools pull-right">
											<label>New Emergency Contact</label>&nbsp;&nbsp;
											<button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-plus"></i></button>
										</div>
									</div>
									<div class="box-body">
										<div class="col-md-12">
											<div class="form-group">
												<label>Nama Keluarga</label>
												<input type="text" name="nama_keluarga" class="form-control" placeholder="Entry ..." required>
											</div>
											<div class="form-group">
												<label>Hubungan</label>
												<input type="text" name="hubungan" class="form-control" placeholder="Entry ..." required>
											</div>
											<div class="form-group">
												<label>Nomor Telepon</label>
												<input type="text" name="nomor_kontak" class="form-control" placeholder="Entry ..." required>
											</div>
											<div class="form-group">
												<label>Provinsi</label>
												<select name="provinsi_kontak" class="form-control region-select" data-region-level="province" id="form_prov2" required>
													<option value="">Pilih provinsi</option>
													@foreach($tb_provinsi as $provinsi)
														<option value="{{ $provinsi->kode }}">{{ $provinsi->nama }}</option>
													@endforeach
												</select>
											</div>
											<div class="form-group region-field" data-region-container="regency" id="form_kabs2" hidden>
												<label>Kabupaten</label>
												<select name="kabupaten_kontak" class="form-control region-select" data-region-level="regency" id="form_kab2" disabled required>
													<option value="">Pilih kabupaten/kota</option>
												</select>
											</div>
											<div class="form-group region-field" data-region-container="district" id="form_kecs2" hidden>
												<label>Kecamatan</label>
												<select name="kecamatan_kontak" class="form-control region-select" data-region-level="district" id="form_kec2" disabled required>
													<option value="">Pilih kecamatan</option>
												</select>
											</div>
											<div class="form-group region-field" data-region-container="village" id="form_dess2" hidden>
												<label>Desa/Kelurahan</label>
												<select name="kelurahan_kontak" class="form-control region-select" data-region-level="village" id="form_des2" disabled required>
													<option value="">Pilih desa/kelurahan</option>
												</select>
											</div>
											<div class="form-group" id="detail_address2">
												<label>Detail Address</label>
												<textarea name="detail_kontak" class="form-control" rows="3" placeholder="Lingkungan/jalan/RT-RW/No.Rumah etc..." required></textarea>
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
			</div>
		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->
  	<!-- </div> -->
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
	@if ($errors->any())
		<div class="alert alert-danger alert-dismissible" style="position:absolute;width:350px;right:10px;top:65px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-warning"></i> Saving Failed Alert!</h4>
				@if($errors->has('PIN'))
					- PIN belum dipilih<br>
				@endif
				@if($errors->has('NIK'))
					- NIK harus diisi<br>
				@endif
				@if($errors->has('employee_name'))
					- Nama Employee tidak boleh kosong<br>
				@endif
				@if($errors->has('gender'))
					- Pilih jenis kelamin Laki-laki atau Perempuan<br>
				@endif
				@if($errors->has('join_date'))
					- Join Date harus diisi<br>
				@endif
				@if($errors->has('dept_id'))
					- Department belum dipilih<br>
				@endif
				@if($errors->has('position_id'))
					- Position belum dipilih<br>
				@endif
				@if($errors->has('leader_id'))
					- Direct leader belum dipilih<br>
				@endif
				@if($errors->has('id_ktp'))
					- ID KTP harus diisi<br>
				@endif
				@if($errors->has('detail'))
					- Detail Address harus diisi<br>
				@endif
				@if($errors->has('group'))
					- Group harus dipilih<br>
				@endif
				@if($errors->has('level_education'))
					- Level Eductaion can not be Null<br>
				@endif
				@if($errors->has('institute'))
					- Institute harus diisi<br>
				@endif
				@if($errors->has('prodi'))
					- Program Study harus diisi<br>
				@endif
				@if($errors->has('year'))
					- Lama Tahun harus diisi<br>
				@endif
				@if($errors->has('graduate_year'))
					- Graduate Year Harus diisi<br>
				@endif
				@if($errors->has('factory'))
					- Factory/Company harus diisi<br>
				@endif
				@if($errors->has('section'))
					- Section Harus diisi<br>
				@endif
				@if($errors->has('finish_year'))
					- Finish Year harus diisi<br>
				@endif
				@if($errors->has('skill_name'))
					- Skill Name tidak boleh kosong<br>
				@endif
				@if($errors->has('posisi2'))
					- Posisi tidak boleh kosong<br>
				@endif
				@if($errors->has('nama_keluargas'))
					- Nama Keluarga tidak boleh kosong<br>
				@endif
				@if($errors->has('hubungans'))
					- Hubungan tidak boleh kosong<br>
				@endif
				@if($errors->has('tanggal_lahir_keluarga'))
					- Tanggal Lahir tidak boleh kosong<br>
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
	<!-- <script type="text/javascript">
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
					url: "/Profile/SelectKab",
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
					url: "/Profile/SelectKec",
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
					url: "/Profile/SelectDes",
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
					url: "/Profile/SelectLink",
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
					url: "/Profile/SelectKab",
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
					url: "/Profile/SelectKec",
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
					url: "/Profile/SelectDes",
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
			//$("#address").hide();
 
			// ambil data kabupaten ketika data memilih provinsi
			$('body').on("change","#form_prov3",function(){
				var id = $(this).val();
				var data = "id="+id;
				$.ajax({
					type: 'POST',
					url: "/Profile/SelectKab",
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
					}
				});
			});
 
			// ambil data kecamatan/kota ketika data memilih kabupaten
			$('body').on("change","#form_kab3",function(){
				var id = $(this).val();
				var data = "id="+id;
				$.ajax({
					type: 'POST',
					url: "/Profile/SelectKec",
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
					}
				});
			});
 
			// ambil data desa ketika data memilih kecamatan/kota
			$('body').on("change","#form_kec3",function(){
				var id = $(this).val();
				var data = "id="+id;
				$.ajax({
					type: 'POST',
					url: "/Profile/SelectDes",
					data: data,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(hasil) {
						$("#form_des3").html(hasil);
						$("#form_des3").show();

						$("#form_dess3").show();
						$("#detail_address3").hide();
					}
				});
			});
			$('body').on("change","#form_des3",function(){
				$("#detail_address3").show();
				$("#saveAddress3").show();
			});
		});
	</script>	 -->
	<script>
		$(function () {
			const nextLevel = {
				province: 'regency',
				regency: 'district',
				district: 'village'
			};
			const endpoints = {
				province: '/Admin/Employee/Kabupaten',
				regency: '/Admin/Employee/Kecamatan',
				district: '/Admin/Employee/Desa'
			};
			const placeholders = {
				regency: 'Pilih kabupaten/kota',
				district: 'Pilih kecamatan',
				village: 'Pilih desa/kelurahan'
			};
			const descendants = {
				province: ['regency', 'district', 'village'],
				regency: ['district', 'village'],
				district: ['village']
			};

			$('.region-form').on('change', '.region-select', function () {
				const $select = $(this);
				const level = $select.data('region-level');
				const childLevel = nextLevel[level];
				if (!childLevel) return;

				const $form = $select.closest('.region-form');
				const code = $select.val();
				const $error = $form.find('.region-error');
				$error.hide().text('');

				descendants[level].forEach(function (descendant) {
					const $descendant = $form.find('[data-region-level="' + descendant + '"]');
					$descendant.prop('disabled', true).val('').empty()
						.append($('<option>', {value: '', text: placeholders[descendant]}));
					$form.find('[data-region-container="' + descendant + '"]').prop('hidden', true);
				});
				if (!code) return;

				$.ajax({
					type: 'POST',
					url: endpoints[level],
					data: {id: code},
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function (options) {
						const $child = $form.find('[data-region-level="' + childLevel + '"]');
						$child.html(options);
						$child.find('option').first().val('').text(placeholders[childLevel]);
						if ($child.find('option').length <= 1) {
							$error.text('Pilihan wilayah tidak tersedia untuk lokasi tersebut.').show();
							return;
						}
						$child.prop('disabled', false);
						$form.find('[data-region-container="' + childLevel + '"]').prop('hidden', false);
					},
					error: function () {
						$error.text('Data wilayah gagal dimuat. Silakan coba lagi.').show();
					}
				});
			});

			$('.region-form').each(function () {
				$(this).prepend(
					$('<div>', {
						class: 'alert alert-danger region-error',
						role: 'alert',
						style: 'display:none'
					})
				);
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
			window.location.href='/Profile/Delete/'+x+'/'+y;
		});
	</script>
@endsection
