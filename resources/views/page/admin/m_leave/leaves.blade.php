@extends('layouts/admin')
@section('Contents')
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<style>
		ol li{
			padding-bottom:5px;
		}
		.tabelot th{
			text-align:center;
			font-weight:normal;
			border:1px solid #000;
		}
		.tabelot td{
			text-align:center;
			border:1px solid #000;
			height:30px;
		}
		.tabelbingkai{
			border:1px solid #000;
		}
		.tabeljudul td{
			border:0px;
		}
		.tabelsubjudul td{
			border:0px;
			padding-top:5px;
		}
		.tabelisi td{
			border:1px solid #000;
			padding:3px;
		}
		.absolute {
			position: absolute;
			width:100px;
			left:-10px;
			right:-10px;
			top:0px;
		}		
		.relative {
			left:0px;
			position: relative;
			height:30px;
		}	
	</style>
  	<!-- Contents -->
	<div class="content-wrapper">
		<section class="content-header">
			<h1 onclick="">
				Employee Leaves
				<small>non workingday</small>
			</h1>
		</section>	
		<section class="content">
		<div class="box box-primary">
			<div class="box-header with-border">
				<div class="row" style="height:70px;">
					<div class="col-lg-3 col-sm-6 col-xs-9">
						<?php if(isset($tb_employee_leave)){?>
							<a class='btn btn-app' href="/Leave"><i class='fa fa-home'></i>Back</a>
						<?php }else{?>
							<label>Select Employee</label>
							<select name="id_employee" id="idemployee" class="form-control selectpicker" data-live-search="true">
								<option value=""></option>
								@foreach($tb_employee as $dt2)
									<option value="{{$dt2->id}}">{{$dt2->employee_name}}</option>
								@endforeach

							</select>
						<?php }?>
					</div>
					<div class="col-lg-9 col-sm-6 col-xs-3">
						@foreach($tb_employee_leave as $dt)
							<?php
								$min_leave=0;
								if($dt->start>$Tgl){
									// Opsi Protect Advance Use
										$start_active=$dt->start;
										// $start_active=$Tgl;
									// End Opsi
									$min_leave=1;
								}else{
									if($lock_backdate==1){
										$min_leave=1;
										$start_active=$Tgl;
									}
								}
								//$min_leave=0;

							?>
							<?php $minExtend=date('Y-m-d',strtotime('-1 months',strtotime($dt->extend)));?>
							<?php if(isset($tb_employee_leave)){?>
								<?php if($status=='0'&&$dt->extend>=$Tgl){?>
									<?php if(request()->user()->hasRole('hr_access')){?>
										<a class='btn btn-app pull-right' href="/Leave/Active/{{$id_cuti}}"><i class='fa fa-wrench'></i>Activated</a>
									<?php }?>
								<?php }elseif(($status==1&&$dt->end>=$Tgl)||($dt->extend>=$Tgl&&$dt->isExtend=='1')){?>
									<a class='btn btn-app pull-right update-modal'><i class='fa fa-edit'></i>Apply</a>
								<?php }?>
								<?php if(request()->user()->hasRole('hr_access')&&$minExtend<=$Tgl&&$Tgl<=$dt->extend){?>
									<!-- <a class='btn btn-app pull-right' href="/Leave/Extend/{{$id_cuti}}"><i class='fa fa-clock-o'></i>Extend</a> -->
								<?php }?>
							<?php }?>
							<input type="hidden" id="leavecode" value="{{$code}}">
						@endforeach
					</div>
				</div>
			</div>
			<div class="box-body">
				<?php if(isset($tb_employee_leave)){?>
				<div style="padding:30px;background:#525659;">
				<div style="padding:15px;background:#FFF;overflow-x:scroll;">
						@foreach($tb_employee_leave as $dt)
						<input type="hidden" id="idkaryawan" value="{{$dt->id_employee}}">
						<?php $id_employee=$dt->id_employee;$leader_id=$dt->leader_id;$id_leave=$dt->id;?>
						<table cellspacing="0" style="width:100%;">
							<tr>
								<td style="border:1px solid #000;padding:10px 5px;">
									<table style="width:100%;" class="tabeljudul">
										<tr>
											<td style="width:240px;"><img src="{{ asset('/public/gambar/logosai.png') }}" style="width:300px;"></td>
											<td style="text-align:center;"><b style="font-size:42px;">K A R T U&nbsp;&nbsp;&nbsp;C U T I&nbsp;&nbsp;&nbsp;K A R Y A W A N</b><br><b style="font-size:24px;">PT SUMMIT ADYAWINSA INDONESIA</b></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td style="border:1px solid #000;">
									<table style="width:100%;" class="tabeljudul">
										<tr>
											<td style="width:35%;text-align:left;padding-left:30px;font-size:16px;">
												TAHUN : &nbsp;&nbsp;<?php echo date('Y',strtotime($dt->start)).' ~ '.date('Y',strtotime($dt->end));?>
											</td>
											<td style="width:25%;text-align:center;font-size:24px;">
												<b>CUTI TAHUNAN</b>
											</td>
											<td style="width:40%;text-align:right;padding-right:30px;font-size:16px;">
												PERIODE CUTI : &nbsp;&nbsp;<b>{{$dt->year}}</b>&nbsp;&nbsp; (<?php echo date('d F Y',strtotime($dt->start)).' ~ '.date('d F Y',strtotime($dt->end));?>)
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td style="border:1px solid #000;padding:0px;">
									<table style="width:100%;">
										<tr>
											<td style="width:60%;border:0px;border-right:1px solid #000;padding:20px 20px 30px 20px;text-align:left;">
												<table style="width:100%;" class="tabelsubjudul">
													<tr><td style="font-size:18px;text-align:center;" colspan='7'><label>DATA KARYAWAN</label></td></tr>
													<tr>
														<td style="width:180px;">NAMA KARYAWAN</td>
														<td style="width:10px;">:</td>
														<td style="border-bottom:1px dotted #000;"><b>{{$dt->employee_name}}</b></td>
														<td style="width:30px;">&nbsp;</td>
														<td style="width:80px;">NIK</td>
														<td style="width:10px;">:</td>
														<td style="border-bottom:1px dotted #000;"><b>{{$dt->NIK}}</b></td>
													</tr>
													<tr>
														<td>DIVISI/SUB DIVISI</td>
														<td>:</td>
														<td style="border-bottom:1px dotted #000;"><b>{{$dt->dept_name}}</b></td>
														<td>&nbsp;</td>
														<td>JABATAN</td>
														<td>:</td>
														<td style="border-bottom:1px dotted #000;"><b>{{$dt->position_name}}</b></td>
													</tr>
													<tr>
														<td>
															NAMA ATASAN LANGSUNG
															<div class="pull-right">
																<?php if($leader_name==''){?>
																	<!-- <a href="/Admin/Employee/Update/{{$dt->id_employee}}" target="_blank"><i class="fa fa-edit"></i></a> -->
																<?php }?>
															</div>
														</td>
														<td>:</td>
														<td style="border-bottom:1px dotted #000;">
															<b>{{$leader_name}}</b>
														</td>
														<td>&nbsp;</td>
														<td colspan='3'>
															<div class="pull-left">
																<?php if (request()->user()->hasRole('hr_access')){?>
																	<br>
																	<div class="form-group"><input type="checkbox" data-idleave="{{$id_leave}}" id="qtyapproval" <?php if($dt->qty_approval==2)echo "checked";?>>&nbsp;2 Approval</div>
																<?php }?>
															</div>
															
														</td>
													</tr>
												</table>
											</td>
											<td style="text-align:center;border:0px;padding:20px 20px 20px 20px;text-align:left;">
												<table style="width:100%;" class="tabelsubjudul">
													<tr><td style="font-size:18px;text-align:center;" colspan='6'><label>DATA CUTI KARYAWAN</label></td></tr>
													<tr>
														<td style="width:240px;">HAK CUTI PERIODE INI</td>
														<td style="width:10px;">:</td>
														<td style="text-align:center;border-bottom:1px dotted #000;">
															<b><?php $cuti = 12; echo $cuti;?></b>
														</td>
														<td style="width:40px;text-align:right;">hari</td>
													</tr>
													<tr>
														<td>
															SISA CUTI PERIODE YANG LALU
															<div class="pull-right">
																<?php if (request()->user()->hasRole('hr_access')){?>
																	<i class="fa fa-edit" data-idot="{{$dt->id}}" style="cursor:pointer;" id="sisacuti"></i>
																<?php }?>
															</div>

														</td>
														<td>:</td>
														<td style="text-align:center;border-bottom:1px dotted #000;">
															<b> <?php 
															$sisaCuti = $dt->sisa;
															echo $sisaCuti;
															?> </b>
														</td>
														<td style="text-align:right;">
															hari
														</td>
													</tr>
													<tr>
														<td>EXPIRED SISA CUTI</td>
														<td>:</td>
														<td style="text-align:center;border-bottom:1px dotted #000;"><b>
															<?php 
																$lebih = $dt->sisa;
																$expCuti = $lebih - $sisa_count;
																$expCuti < 0 ? $expCuti = 0 : $expCuti;
																$expiredDate = date(strtotime("+3 months", strtotime($dt->start)));
																if(date('Y-m-d h:i:s',$expiredDate) < NOW()){
																	$expiredText = "Expired";
																	echo $expCuti.' <span class="text-danger" style="position:absolute; margin-left:50px;">'.$expiredText.'</span>';
																} else{
																	$expiredText = '';
																	echo '';

																} 
																?></b>
																</td>
														<td style="text-align:right;">hari</td>
													</tr>
													<tr>
														<td>EXPIRED DATE SISA CUTI</td>
														<td>:</td>
														<td style="text-align:center;border-bottom:1px dotted #000;"><b>
															<?php 
																$kurang = $dt->kurang;
																$lebih = $dt->sisa;
																$allowance = $dt->allowance;
																$outstanding = $dt->allowance;
																$effectiveDate = date('d F Y', strtotime("+3 months", strtotime($dt->start)));
																echo $effectiveDate;?></b>
															
																</td>
														<td style="text-align:right;"></td>
													</tr>
													<tr>
														<td>
															HUTANG CUTI PERIODE YANG LALU
															<div class="pull-right">
																<?php if (request()->user()->hasRole('hr_access')){?>
																	<i class="fa fa-edit" data-idot="{{$dt->id}}" style="cursor:pointer;" id="kurangcuti"></i>
																<?php }?>
															</div>

														</td>
														<td>:</td>
														<td style="text-align:center;border-bottom:1px dotted #000;"><b>{{$dt->kurang}}</b></td>
														<td style="text-align:right;">hari</td>
													</tr>
													<tr>
														<td>TOTAL HAK CUTI PERIODE INI</td>
														<td>:</td>
														<td style="text-align:center;border-bottom:1px dotted #000;">
															<b><?php 
																$kurang=$dt->kurang;
																$lebih=$dt->sisa;
																// $allowance=$dt->allowance;
																if(date('Y-m-d h:i:s',$expiredDate) < NOW()){
																	$allowance = $cuti + ($lebih - $expCuti) - $kurang;
																}else{
																$allowance = ($cuti + $lebih) - $kurang;
															}
															$outstanding=$allowance;

																echo $allowance;
															?>
															</b>
														</td>
														<td style="text-align:right;">hari</td>
													</tr>
													
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td style="border:1px solid #000;text-align:center;border:0px;">
									<table style="width:100%;" class="tabelisi">
										<tr>
											<td colspan="3" style="text-align:center;padding:3px;">TANGGAL</td>
											<td rowspan='2'>JUMLAH<br>CUTI</td>
											<td rowspan='2' style="width:230px;">ALASAN, & ALAMAT/NO. TELEPON<br>SELAMA CUTI</td>
											<td colspan="5" style="text-align:center;padding:3px;">TANDA TANGAN</td>
										</tr>
										<tr>
											<td>MULAI CUTI</td>
											<td>AKHIR CUTI</td>
											<td>KEMBALI KERJA</td>
											<td>PERMOHONAN<br>(KARYAWAN)</td>
											<td colspan="2">PERSETUJUAN<br>(OLEH ATASAN)</td>
											<td>PENGESAHAN<br>(OLEH HRD)</td>
											<td>LAPORAN KERJA<br>(KARYAWAN)</td>
										</tr>
										<?php $no=0;?>
										@foreach($tb_anual as $dt2)
										<?php 
											$no++;
											if($dt2->status_approved<2 && $dt2->status_approved2<2 && $dt2->status_legalized<2 ){
												$outstanding= $outstanding-$dt2->leave_count;
												$warna="";
												$efek="";
											}else{
												$warna=" style='background:#CCC;'";
												$efek=" style='text-decoration: line-through;'";
											}
										?>
										<?php if($dt2->status_legalized<2){?>
											<tr <?php echo $warna;?>>
												<td>{{$dt2->start_leave}}</td>
												<td>{{$dt2->finish_leave}}</td>
												<td>{{$dt2->start_working}}</td>
												<td <?php echo $efek;?> >{{$dt2->leave_count}} / {{$outstanding}}</td>
												<td>{{$dt2->reason}}, {{$dt2->remark}}</td>
												<td class="relative">
													<?php 
														$ttd_app=asset('public/approval/'.$dt2->id_employee.'.png');
														$confirm=asset('public/approval/confirm.png');
														$reject=asset('public/approval/rejected.png');
														$filename = 'c:/xampp/htdocs/public/approval/'.$dt2->id_employee.'.png';
														if (file_exists($filename)){?>
															<i style="color:#FFF;">&nbsp;</i>
															<img src='{{ $ttd_app }}' class="absolute">
														<?php }else{echo "Confirm";}
													?>
												</td>
												<td class="relative" style="width:75px;">
													<?php 
														if($dt2->status_approved=='1'){
															$ttd_app=asset('public/approval/'.$dt2->approved.'.png');
															$confirm=asset('public/approval/confirm.png');
															$reject=asset('public/approval/rejected.png');
															$filename = 'c:/xampp/htdocs/public/approval/'.$dt2->approved.'.png';
															if (file_exists($filename)){?>
																<i style="color:#FFF;">&nbsp;</i>
																<img src='{{ $ttd_app }}' class="absolute" title="{{$dt2->approved_name}}, {{$dt2->date_approved}}">
															<?php }else{echo "Confirm";}
														}elseif($dt2->status_approved=='2'){echo "Not Approve";}
													?>
												</td>
												<td class="relative" style="width:75px;">
													<?php 
														if($dt2->status_approved2=='1'){
															$ttd_app=asset('public/approval/'.$dt2->approved2.'.png');
															$confirm=asset('public/approval/confirm.png');
															$reject=asset('public/approval/rejected.png');
															$filename = 'c:/xampp/htdocs/public/approval/'.$dt2->approved2.'.png';
															if (file_exists($filename)){?>
																<i style="color:#FFF;">Confirm</i>
																<img src='{{ $ttd_app }}' class="absolute" title="{{$dt2->approved1_name}}, {{$dt2->date_approved2}}">
															<?php }else{echo "Confirm";}
														}elseif($dt2->status_approved2=='2'){echo "Not Approve";}
													?>
												</td>
												<td><?php if($dt2->status_legalized==1)echo "Confirm";else if($dt2->status_legalized==2)echo "Not Approve";?></td>
												<td><?php if($dt2->status_reported==1)echo "Confirm";?></td>
											</tr>
										<?php }?>
										@endforeach
										<?php for($i=$no;$i<12;$i++){?>
											<tr>
												<td>&nbsp;</td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
											</tr>
										<?php }?>
									</table>
								</td>
							</tr>

						</table>
						@endforeach
					</div>
					<div style="padding:0px;background:#525659;">&nbsp;</div>
					<div style="padding:15px 15px 0px 15px;background:#FFF;overflow-x:scroll;">
						<table cellspacing="0" style="width:100%;">
							<tr>
								<td style="border:1px solid #000;padding:10px 3px;">
									<ol style="font-size:12px;">Ketentuan pengambilan cuti:
										<li>Setiap karyawan yang telah mencapai usia kerja 12 bulan berturut-turut, berhak atas 12 hari cuti yang wajib dialokasikan untuk keperluan cuti bersama tahunan sebanyak 6 hari. Dan 6 hari sisanya dapat diambil dengan seijin/persetujuan atasan langsung dan atau Manajer Divisi dari karyawan yang berkepentingan.</li>
										<li>Setiap karyawan yang akan mengajukan cuti, wajib mengajukan permohonan persetujuan cuti kepada atasanlangsung dan atau Manajer Divisi, paling lambat 7 (tujuh) hari kerja sebelum pelaksanaan cuti & setiap habis melaksanakan cuti, karyawan yang telah melaksanakan cuti diwajibkan untuk melapor kembali kerja dengan cara menandatangani kolom lapor kerja kembali</li>
									</ol>
									
								</td>
							</tr>
						</table>
					</div>
					<div style="padding:15px;background:#FFF;overflow-x:scroll;">
						<table cellspacing="0" style="width:100%;">
							<tr>
								<td style="border:1px solid #000;padding:3px;text-align:center;font-size:24px;">
									<b>CUTI DOKTER</b>
								</td>
							</tr>
							<tr>
								<td style="border:1px solid #000;padding:0px;text-align:center;font-size:6px;">
									&nbsp;
								</td>
							</tr>
							<tr>
								<td style="border:1px solid #000;text-align:center;border:0px;">
									<table style="width:100%;" class="tabelisi">
										<tr>
											<td colspan="3" style="text-align:center;padding:3px;">TANGGAL</td>
											<td rowspan='2'>JUMLAH<br>CUTI</td>
											<td rowspan='2' style="width:300px;">ALAMAT/NO. TELEPON<br>SELAMA CUTI</td>
											<td colspan="4" style="text-align:center;padding:3px;">TANDA TANGAN</td>
										</tr>
										<tr>
											<td>MULAI CUTI</td>
											<td>AKHIR CUTI</td>
											<td>KEMBALI KERJA</td>
											<td>PERMOHONAN<br>(OLEH PIHAK YG CUTI)</td>
											<td>PERSETUJUAN<br>(OLEH ATASAN)</td>
											<td>PENGESAHAN<br>(OLEH HRD)</td>
											<td>LAPORAN KEMBALI KERJA<br>(OLEH PIHAK YG CUTI)</td>
										</tr>
										<?php $no=0;?>
										@foreach($tb_docter as $dt2)
										<?php $no++;?>
										<tr>
											<td>{{$dt2->start_leave}}</td>
											<td>{{$dt2->finish_leave}}</td>
											<td>{{$dt2->start_working}}</td>
											<td>{{$dt2->leave_count}}</td>
											<td>{{$dt2->remark}}</td>
											<td>Confirm</td>
											<td><?php if($dt2->status_approved==1)echo "Confirm";?></td>
											<td><?php if($dt2->status_legalized==1)echo "Confirm";?></td>
											<td><?php if($dt2->status_reported==1)echo "Confirm";?></td>
										</tr>
										@endforeach
										<?php for($i=$no;$i<6;$i++){?>
											<tr>
												<td>&nbsp;</td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
											</tr>
										<?php }?>
									</table>
								</td>
							</tr>

						</table>
					</div>
					<div style="padding:0px 15px 15px 15px;background:#FFF;overflow-x:scroll;">
						<table cellspacing="0" style="width:100%;">
							<tr>
								<td style="border:1px solid #000;padding:3px;text-align:center;font-size:24px;">
									<b>CUTI KHUSUS</b>
								</td>
							</tr>
							<tr>
								<td style="border:1px solid #000;padding:0px;text-align:center;font-size:6px;">
									&nbsp;
								</td>
							</tr>
							<tr>
								<td style="border:1px solid #000;text-align:center;border:0px;">
									<table style="width:100%;" class="tabelisi">
										<tr>
											<td colspan="3" style="text-align:center;padding:3px;">TANGGAL</td>
											<td rowspan='2'>JUMLAH<br>CUTI</td>
											<td rowspan='2' style="width:150px;">LATAR BELAKANG/<br>ALASAN CUTI</td>
											<td rowspan='2' style="width:150px;">ALAMAT/NO. TELEPON<br>SELAMA CUTI</td>
											<td colspan="4" style="text-align:center;padding:3px;">TANDA TANGAN</td>
										</tr>
										<tr>
											<td>MULAI CUTI</td>
											<td>AKHIR CUTI</td>
											<td>KEMBALI KERJA</td>
											<td>PERMOHONAN<br>(OLEH PIHAK YG CUTI)</td>
											<td>PERSETUJUAN<br>(OLEH ATASAN)</td>
											<td>PENGESAHAN<br>(OLEH HRD)</td>
											<td>LAPORAN KEMBALI KERJA<br>(OLEH PIHAK YG CUTI)</td>
										</tr>
										<?php $no=0;?>
										@foreach($tb_special as $dt2)
										<?php $no++;
											if($dt2->status_approved<2&&$dt2->status_approved2<2&&$dt2->status_legalized<2){
												$warna="";
												$efek="";
											}else{
												$warna=" style='background:#CCC;'";
												$efek=" style='text-decoration: line-through;'";
											}
										
										?>
										<tr<?php echo $warna;?>>
											<td>{{$dt2->start_leave}}</td>
											<td>{{$dt2->finish_leave}}</td>
											<td>{{$dt2->start_working}}</td>
											<td>{{$dt2->leave_count}}</td>
											<td>{{$dt2->reason}}</td>
											<td>{{$dt2->remark}}</td>
											<td>Confirm</td>
											<td><?php if($dt2->status_approved==1)echo "Confirm";if($dt2->status_approved==2)echo "Not Approved";?></td>
											<td><?php if($dt2->status_legalized==1)echo "Confirm";if($dt2->status_legalized==2)echo "Cancel";?></td>
											<td><?php if($dt2->status_reported==1)echo "Confirm";if($dt2->status_reported==2)echo "Cancel";?></td>
										</tr>
										@endforeach
										<?php for($i=$no;$i<6;$i++){?>
											<tr>
												<td>&nbsp;</td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
											</tr>
										<?php }?>
									</table>
								</td>
							</tr>

						</table>
					</div>
				</div>
				<?php }?>

			</div>
		</div>
		</section>
  	</div>
    <!-- /.Content -->
	<?php if(isset($id_employee)){?>
	<div class="modal fade" id="modal-update">
		<div class="modal-dialog box box-primary" style="width:350px;">
			<div class="modal-content">
			<form action="/Leave/Add" method="post" enctype="multipart/form-data">
			<input type="hidden" name="id_employee" value="{{$id_employee}}">
			<input type="hidden" name="id_leave" value="{{$id_leave}}">
			<input type="hidden" name="id_doc" id="0">
			<input type="hidden" name="leader_id" value="{{$leader_id}}">

			{{ csrf_field() }}
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Leave Form</h4>
				</div>
				<div class="modal-body">
					<div class="col-xs-8" style="padding:0px;padding-right:3px;">
						<div class="form-group">
							<label>Pilih Cuti</label>
							<select name="category" class="form-control" id="category">
								<option value="annual"<?php $sisa_anual=$allowance-$anual_count;if($sisa_anual<=0||$diffyears<0)echo " disabled";?>>Cuti Tahunan</option>
								<option value="special">Cuti Khusus</option>
								<!-- <option value="docter">Cuti Dokter</option> -->
							</select>
							<input type="hidden" id="sisa_anual" value="{{$sisa_anual}}">
						</div>
					</div>
					<div class="col-xs-4" style="padding:0px;padding-left:3px;">
						<div class="form-group">
							<label>Jumlah Cuti</label>
							<input type="number" name="leave_count" min="0" id="leavecount2" class="form-control" disabled>
							<input type="number" name="leave_count" min="0" id="leavecount" class="form-control" <?php if($sisa_anual<=0)echo " disabled";?>>
						</div>
					</div>
					<div class="col-xs-6" style="padding:0px;padding-right:3px;">
						<div class="form-group">
							<label>Mulai Cuti</label>
							<input type="date" id="startleave" name="start_leave" class="form-control leaverange" <?php if($min_leave==1)echo "";?>>
							<input type="hidden" id="tglmin" class="form-control" value="{{$MinTgl}}">
						</div>
					</div>
					<div class="col-xs-6" style="padding:0px;padding-left:3px;">
						<div class="form-group">
							<label>Akhir Cuti</label>
							<input type="date" name="finish_leave" id="finishleave" class="form-control leaverange" <?php if($lock_backdate==1)echo "min='".$Tgl."'";?>>								
						</div>
					</div>

					<div class="form-group">
						<label>Mulai Bekerja</label>
						<!-- <input type="date" name="start_working" class="form-control" min="{{$Tgl}}"> -->
						<input type="date" name="start_working" class="form-control">								
					</div>
					<div class="form-group">
						<label>Alasan Cuti</label>
						<input type="text" name="reason" class="form-control">								
					</div>
					<div class="form-group">
						<label>Telpon/Alamat Saat Cuti</label>
						<input type="text" name="remark" class="form-control">								
					</div>
					<div class="form-group tambahan" style="padding-top:10px;">
						<label>
							Upload Document 
							( <a  data-placement="bottom" data-html="true" data-toggle="popover" title="<i class='fa fa-files-o'></i> Contoh Upload Dokumen" data-content="
								Cuti Dadakan: Overlapping Pekerjaan
								<br>Cuti Nikah : Surat Nikah
								<br>Cuti Khitan : Surat Khitan
								<br>Cuti Baptis : Surat Baptis
								<br>Cuti Meninggal : KK & Surat Kematian
								<br>Cuti Melahirkan : Surat Ket Dokter<br>Cuti Kelahiran : Surat Kelahiran
							"><i class="fa fa-files-o"></i> Contoh Dokumen </a>)
						</label>
						<input type="file" name="overlap_doc" id="overlap">								
					</div>

				</div>
				<div class="modal-footer" style="text-align:left;">
					<input type="submit" class="btn btn-primary" id="confirm" value="Confirm">
					<button type="button" class="btn btn-default pull-right cancelafter" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<?php }?>

    @if ($message = Session::get('success'))
		<div class="alert alert-success alert-dismissible" style="position:absolute;width:350px;right:10px;top:65px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-info"></i> Success Alert</h4>
			{{$message}}
		</div>
    @endif

@endsection

@section('Scripts')
	<!-- Display Time Notification -->
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<script>
		$( document ).ready(function() {
			$(".tambahan").hide();
			document.getElementById("confirm").disabled = true;
		});
		$(document).on('change', '#idemployee', function() {
			var x=$('#idemployee').val();
			window.location.href='/Leave/Employee/'+x;
		});
		$(document).on('change', '#category', function() {
			var x=$('#category').val();
			var y=$('#leavecount').val()*1;
			var z=$('#sisa_anual').val()*1;
			if(x=='annual'&&y>z){
				$('#leavecount').val(z);
			}
		});
		$(document).on('change', '#leavecount', function() {
			var x=$('#category').val();
			var y=$('#leavecount').val()*1;
			var z=$('#sisa_anual').val()*1;
			if(x=='annual'&&y>z){
				$('#leavecount').val(z);
				$('#leavecount2').val(z);
			}else{
				$('#leavecount2').val(y);
				$('#leavecount').val(y);
			}
			if(y>0){
				document.getElementById("confirm").disabled = false;
			}else{
				document.getElementById("confirm").disabled = true;
			}
		});
		$(document).on('click', '#qtyapproval', function() {
			var x=$(this).data('idleave');
			$.ajaxSetup({
				type:"POST",
				url: "/Leave/Update/Opsi",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:{id_leave:x},
				success: function(respond){
					if(respond=='Sukses')
					location.reload();
					else
					alert(respond);
				}
			})
		});
		$(document).on('click', '#sisacuti', function() {
			var x=$(this).data('idot');
			var y=prompt("Masukan sisa cuti periode lalu","0");
			if(x>0){
				window.location.href='/Leave/Sisa/'+x+'/'+y;
			}
		});
		$(document).on('click', '#kurangcuti', function() {
			var x=$(this).data('idot');
			var y=prompt("Masukan hutang cuti periode lalu","0");
			if(x>0){
				window.location.href='/Leave/Kurang/'+x+'/'+y;
			}
		});
		$(document).on('change', '#startleave', function() {
			var x=$('#startleave').val();
			var y=$('#tglmin').val();
			var z=$('#leavecount').val()*1;
			var category=$('#category').val();
			
			const d = new Date(x);
			let day = d.getDay();
			if(day==0){
				alert('Jangan pilih Minggu, Jadwal kerja Anda dimulai Hari Senin meskipun masuknya Minggu malam');
				$('#startleave').val('');
			}

			//alert(category);
			if(category!='annual'){
				alert("Cuti Khusus/Cuti Dokter membutuhkan dokumen yang harus diupload");
				$(".tambahan").show();
				document.getElementById("confirm").disabled = true;
			}else if(x<y){
				alert("Pengajuan cuti normal adalah H-7, untuk melanjutkan silahkan upload dokumen sebagai overlaping pekerjaan untuk atasan Anda");
				$(".tambahan").show();
				document.getElementById("confirm").disabled = true;
			}else{
				$(".tambahan").hide();
				document.getElementById("confirm").disabled = false;
			}
			$('#finishleave').val('');
			document.getElementById("confirm").disabled = true;


		});
		$(document).on('change', '#finishleave', function() {
			var start=$('#startleave').val();
			var finish=$('#finishleave').val();
			var idemployee=$('#idkaryawan').val();
			var y=$('#tglmin').val();
			
			const d = new Date(finish);
			let day = d.getDay();
			if(day==0){
				alert('Jangan pilih Minggu, Jadwal kerja Anda dimulai Hari Senin meskipun masuknya Minggu malam');
				$('#finishleave').val('');
			}else{

				if(finish<start){
					alert("Finish Leave tidak bisa lebih kecil dari Start Leave");
					document.getElementById("confirm").disabled = true;
					$('#finishleave').val('');
				}else if(start<y){
					alert("Pengajuan cuti normal adalah H-7, untuk melanjutkan silahkan upload dokumen sebagai overlaping pekerjaan untuk atasan Anda");
					$(".tambahan").show();
					document.getElementById("confirm").disabled = true;
				}else{
					$(".tambahan").hide();
					document.getElementById("confirm").disabled = false;
				}
				$.ajaxSetup({
					type:"POST",
					url: "/Leave/Count",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					data:{start:start,finish:finish,idemployee:idemployee},
					success: function(respond){
						
						$("#leavecount").val(respond);
						$("#leavecount2").val(respond);
					}
				})

			}

		});
		$(document).on('change', '.leaverange', function() {
			var start=$('#startleave').val();
			var finish=$('#finishleave').val();
			var idemployee=$('#idkaryawan').val();
			if(finish!=''&&start!=''){
				$.ajaxSetup({
					type:"POST",
					url: "/Leave/LeaveLimitCheck",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					data:{start:start,finish:finish,idemployee:idemployee},
					success: function(respond){
						if(respond==''){
							document.getElementById("confirm").disabled = false;
						}else{
							document.getElementById("confirm").disabled = true;
							alert(respond);
						}
					}
				})
			}

		});
		$(document).on('change', '#overlap', function() {
			var x=$(this).val();
			if(x==''){
				document.getElementById("confirm").disabled = true;
			}else{
				document.getElementById("confirm").disabled = false;
			}
		});
		
	</script>
	<!-- Show Form Update -->
	<script type="text/javascript">
		$(document).on('click', '.update-modal', function() {
			var x=$('#leavecode').val();
			if(x>0){
				let code = prompt("Please, ask a code from your direct leader", "");
				if(code==x){
					$('#modal-update').modal('show');
				}else{
					alert("Invalid Code, Please ask a valid code from your Direct Leader")
				}
			}else{
				$('#modal-update').modal('show');
			}
		});
	</script>
	<script>
		$(document).ready(function(){
		$('[data-toggle="popover"]').popover();
		});
	</script>

@endsection