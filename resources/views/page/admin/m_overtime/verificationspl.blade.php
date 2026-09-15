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
		  cursor:pointer;
        }
		#table2 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table2 tbody tr:hover{
			cursor:pointer;
		}
    </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Form SPL
				<small>Surat Perintah Lembur</small>
			</h1>
			<ol class="breadcrumb">
				<li>
				<a href="#">
					<i class="fa fa-calendar"></i> 
					<?php 
						date_default_timezone_set("Asia/Jakarta");
						echo date('l, d M Y H:i');
					?>
				</a>
				</li>
			</ol>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-calendar"></i>
						<h3 class="box-title">Data Tables</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-info btn-xs" id="updateFinger"><i class="fa fa-clock-o"></i> Capture Finger</button>
							<?php if($submenu=='verification2'){?>
								<button type="button" class="btn btn-default btn-xs" onclick="window.location.href='/Admin/Overtime/Verifications/0'"><i class="fa fa-times"></i></button>
							<?php }?>
						</div>
					</div>
					<div class="box-body">
						<table id="table2" class="table table-hover">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th style="width:70px;">SPLN0.</th>
									<th style="width:70px;">Date</th>
									<th style="width:70px;">NIK</th>
									<th>Name</th>
									<th style="width:90px;">Actual OT</th>
									<th>Start</th>
									<th>End</th>
									<th style="width:70px;">Hours</th>
									<th style="width:70px;">Convertion</th>
									<th style="width:90px;">Status</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								@foreach($tb_overtime_detail as $dt)
								<?php $no++;$id_ot=$dt->id_ot;$id_overtime=$dt->id_overtime;?>
								<tr id="R{{$no}}" <?php if($dt->ot_category==2)echo  "style='background:burlywood;'";?>>
									<td><?php echo $no;?></td>
									<td>{{$dt->id_overtime}}</td>
									<td>{{$dt->date_on}}</td>
									<td>{{$dt->NIK}}</td>
									<td>{{$dt->employee_name}}</td>
									<td title="{{$dt->start_act}} to {{$dt->finish_act}}">
										<?php if($dt->sign_after=='1'||$dt->sign_after=='3')echo date('H:i',strtotime($dt->start_act)).' ~ '.date('H:i',strtotime($dt->finish_act));?>
									</td>
									<td>
									<?php
										$checkin_act='';
										$cin='';
										$checkout_act='';
										$cout='';
										$pass=1;
										if($pass==0){
											$host1 = mysqli_connect("192.168.1.4","ems","123456","db_ems");


											$checkin_act='';
											$checkout_act='';
											$cin='';
											$cout='';

											$ncdatein=$dt->start_act;
											//Reduce 2 Hour
											$date = date_create($ncdatein);
											date_add($date, date_interval_create_from_date_string('-2 hours'));
											$ncdateindown= date_format($date, 'Y-m-d H:i:s');
											//Increas 2 Hour
											$date = date_create($ncdatein);
											date_add($date, date_interval_create_from_date_string('2 hours'));
											$ncdateinup= date_format($date, 'Y-m-d H:i:s');
											//echo $ncdatein.' ';

											$ncdateout=$dt->finish_act;
											//Reduce 3 Hour
											$date = date_create($ncdateout);
											date_add($date, date_interval_create_from_date_string('-3 hours'));
											$ncdateoutdown= date_format($date, 'Y-m-d H:i:s');
											//Increas 3 Hour
											$date = date_create($ncdateout);
											date_add($date, date_interval_create_from_date_string('3 hours'));
											$ncdateoutup= date_format($date, 'Y-m-d H:i:s');
											//echo $ncdateout.' ';

											$lenbadge=strlen($dt->badgenumber);
											$nullbadge=9-$lenbadge;
											$j='';
											for($i=1;$i<=$nullbadge;$i++){
												$j.='0';
											}
											$badge=$j.$dt->badgenumber;

											$sambungan=0;
											$qry_finger=mysqli_query($host1,"select * from tb_utilities where atribut='iclock_connection'")or die(mysqli_error($host1));
											while($dt_finger=mysqli_fetch_array($qry_finger)){$sambungan=$dt_finger['status'];}
											if($sambungan==1){
												$host = mysqli_connect("192.168.121.4:83306","cahyudin","123456","adms_db");

												//$qry5=mysqli_query($host,"select checktime from checkinout where userid='$dt->PIN' and checktime>='$ncdateindown' and checktime<='$ncdateinup' order by checktime asc limit 1")or die(mysqli_error($host));
												$qry5=mysqli_query($host,"select checktime from checkinout left join userinfo on userinfo.userid=checkinout.userid where badgenumber='$badge' and checktime>='$ncdateindown' and checktime<='$ncdateinup' order by checktime asc limit 1")or die(mysqli_error($host));
												while($dt5=mysqli_fetch_array($qry5)){
													$checkin_act=$dt5['checktime'];
													$status='Present';
													//if($cin=='')
													$cin=$checkin_act;
												}
												$qry5=mysqli_query($host,"select checktime from checkinout left join userinfo on userinfo.userid=checkinout.userid where badgenumber='$badge' and checktime>='$ncdateoutdown' and checktime<='$ncdateoutup' order by checktime desc limit 1")or die(mysqli_error($host));
												while($dt5=mysqli_fetch_array($qry5)){
													$checkout_act=$dt5['checktime'];
													$status='Present';
													//if($cout=='')
													$cout=$checkout_act;
												}


												$qry6=mysqli_query($host1,"select * from tb_employee_freedays where id_employee='$dt->id_employee' and date_off='$dt->date_on'")or die(mysqli_error($host1));
												while($dt6=mysqli_fetch_array($qry6)){
													$category=$dt6['category'];
													$description=$dt6['description'];
													$status=$category;
												}
											}
											//Absen Manual Start
											if($checkin_act==''){
												//$qry7=mysqli_query($host1,"select * from tb_checktimes where badgenumber='$dt->badgenumber' and checktime>='$ncdateindown' and checktime<='$ncdateinup' order by checktime asc limit 1")or die(mysqli_error($host1));
												$qry7=mysqli_query($host1,"select * from tb_checktimes where NIK='$dt->NIK' and checktime>='$ncdateindown' and checktime<='$ncdateinup' order by checktime asc limit 1")or die(mysqli_error($host1));
												while($dt7=mysqli_fetch_array($qry7)){
													$checkin_act=$dt7['checktime'];
													$status=$dt7['status_kerja'];
													//if($checkin_act<$cin)
													$cin=$checkin_act;
												}
											}
											if($checkout_act==''){
												$qry7=mysqli_query($host1,"select * from tb_checktimes where NIK='$dt->NIK' and checktime>='$ncdateoutdown' and checktime<='$ncdateoutup' order by checktime desc limit 1")or die(mysqli_error($host1));
												while($dt7=mysqli_fetch_array($qry7)){
													$checkout_act=$dt7['checktime'];
													$status=$dt7['status_kerja'];
													//if($checkout_act>$cout)
													$cout=$checkout_act;
												}
											}
											//Absen Manual End

											if($checkin_act!=''&&$cin!=''){
												if($status=='Present'){
													if($checkin_act<=$dt->start_act)echo "<span class='badge bg-green' title='Masuk: {{$cin}}'>".date('H:i',strtotime($cin))."</span>";
													else echo "<span class='badge bg-yellow' title='Masuk: {{$cin}}'>".date('H:i',strtotime($cin))."</span>&nbsp;";
												}else{
													echo "<span class='badge bg-blue' title='Masuk: {{$cin}}'>".date('H:i',strtotime($cin))."</span>&nbsp;";
												}
											}
										}
										//New Fixed Column
										if($pass==1){
											$checkin_act = '';
											$checkin_act =$dt->checkin;
											if( $checkin_act!=''){
												if ($dt->in_finger == '1') {
													if ($checkin_act <= $dt->start_act) {
														echo "<span class='badge bg-green'>" . date('H:i', strtotime($checkin_act)) . '</span>';
													} else {
														echo "<span class='badge bg-yellow'>" . date('H:i', strtotime($checkin_act)) . '</span>&nbsp;';
													}
												} else {
													echo "<span class='badge bg-blue'>" . date('H:i', strtotime($checkin_act)) . '</span>&nbsp;';
												}
											}
										}


									?>
									</td>
									<td>
									<?php
										if($checkout_act!=''&&$cout!=''&&$pass==0){
											if($status=='Present'){
												if($checkout_act>=$dt->finish_act)echo "<span class='badge bg-green' title='Pulang: {{$cout}}'>".date('H:i',strtotime($cout))."</span>";
												else echo "<span class='badge bg-yellow' title='Pulang: {{$cout}}'>".date('H:i',strtotime($cout))."</span>&nbsp;";
											}else{
												echo "<span class='badge bg-blue' title='Pulang: {{$cout}}'>".date('H:i',strtotime($cout))."</span>&nbsp;";
											}
										}
										if($pass==1){
											$checkout_act = '';
											$checkout_act = $dt->checkout;
											if($checkout_act!=''){
												if ($dt->out_finger == '1') {
													if ($checkout_act >= $dt->finish_act) {
														echo "<span class='badge bg-green'>" . date('H:i', strtotime($checkout_act)) . '</span>';
													} else {
														echo "<span class='badge bg-yellow'>" . date('H:i', strtotime($checkout_act)) . '</span>&nbsp;';
													}
												} else {
													echo "<span class='badge bg-blue'>" . date('H:i', strtotime($checkout_act)) . '</span>&nbsp;';
												}
											}
										}
									?>
									</td>
									<td>{{$dt->hours_act}}</td>
									<td>{{$dt->hours_convertion}}</td>
									<td><input type="hidden" id="satuan{{$no}}" value="{{$dt->status}}">
										<?php 
										//||$dt->sign_after=='0'
										if($dt->sign_before=='1'&&$dt->status_dicatat=='1'){
											$tgl_awal=date('Y-m-d',strtotime($dt->start_act));
											$jam_awal=date('H:i',strtotime($dt->start_act));
											$start_act=$tgl_awal.'T'.$jam_awal;
											$tgl_akhir=date('Y-m-d',strtotime($dt->finish_act));
											$jam_akhir=date('H:i',strtotime($dt->finish_act));
											$finish_act=$tgl_akhir.'T'.$jam_akhir;
											if($dt->sign_after=='1'||$dt->sign_after=='0'||$dt->sign_after=='3'){?>
											<button type="button" class="btn btn-primary btn-xs update-modal" data-dateon='{{$dt->date_on}}' data-id_employee='{{$dt->id_employee}}' data-idafter='{{$dt->id}}' data-startact='{{$start_act}}' data-finishact='{{$finish_act}}' data-otcategory='{{$dt->ot_category}}' data-hoursact='{{$dt->hours_act}}' data-hoursconvertion='{{$dt->hours_convertion}}' data-otisoma='{{$dt->minutes_break}}' data-harikerja="{{$dt->hari_kerja}}"><i class="fa fa-edit"></i></button>
											<?php if($dt->hari_kerja!=7){?><button title="Yes" id="yes-dip{{$no}}" type="button" class="btn btn-success btn-xs yes-dip" data-nilai="{{$no}}" data-detailid="{{$dt->id}}"><i class="fa fa-check"></i></button><?php }?>
											<?php 
											}?>
											<button title="No" id="no-dip{{$no}}" type="button" class="btn btn-danger btn-xs no-dip" data-nilai="{{$no}}" data-detailid="{{$dt->id}}"><i class="fa fa-close"></i></button>
										<?php }else echo "<i class='fa fa-info-circle' title='Approval Overtime in belum lengkap'> Waiting".$dt->status_dicatat."</i>";?>
									</td>
								</tr>
								@endforeach
							</tbody>
							<tfoot>
								<tr>
									<th>No</th>
									<th>SPL No.</th>
									<th>Date</th>
									<th>NIK</th>
									<th>Name</th>
									<th>Actual OT</th>
									<th>Start</th>
									<th>End</th>
									<th>Hours</th>
									<th>Convertion</th>
									<th>Status</th>
								</tr>
							</tfoot>
						</table>
					</div>
					<!-- /.box-body -->
					<div class="box-footer">
						<!-- pointer-events: none -->
						<input type="hidden" id="jumlahno" value="{{$no}}">
						<?php if(isset($id_ot)){?>
							<?php echo "<a title='Recorded & Paid Sign' id='btnpai' class='btn btn-app sign-modal' style='color:#00C;pointer-events: none;' data-delid='".$id_ot."' data-delid1='paid' data-delname='".$id_overtime."'><i class='fa fa-check-square-o'></i> Confirm</a>";?>
						<?php }?>
					</div>
				</div>

			</div>
		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->

	<div class="modal fade" id="modal-update">
		<div class="modal-dialog box box-primary" style="width:350px;">
			<div class="modal-content">
			<form action="/Admin/Overtime/Detail/After" method="post">
			<input type="hidden" id="id_employee" name="id_employee">
			<input type="hidden" id="idafter" name="id_after">
			<input type="hidden" id="date_on" name="date_on">

			{{ csrf_field() }}
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Overtime Confirmation</h4>
				</div>
				<div class="modal-body" style="height:400px;">
					<div class="form-group">
						<label>Actual/Realisation</label>
						<select name="sign_after" id="signafter" class="form-control">
							<option value="1">Come</option>
							<option value="2">Not Come</option>
						</select>
					</div>
					<div class="form-group come">
						<label>Plan Start</label>
						<input type="datetime-local" name="start_act" id="startplan" class="form-control">
					</div>
					<div class="form-group come">
						<label>Plan Finish</label>
						<input type="datetime-local" name="finish_act" id="finishplan" class="form-control">								
					</div>

						<div class="form-group come">
							<div class="col-xs-5" style="padding:0px;padding-right:3px;">
								<label>Break Hours</label>
								<select name="otisoma" id="isoma" class="form-control">
									<option value="0">0 Minute</option>
									<option value="30">30 Minutes</option>
									<option value="45">45 Minutes</option>
									<option value="90">90 Minutes</option>
								</select>
							</div>
							<div class="col-xs-4" style="padding:0px;padding-left:3px;">
								<label>Act Hours</label>
								<input type="number" id="hoursdraft" class="form-control" step="0.25" disabled>
								<div style="color:#F00;padding-right:10px;" id="info2">&nbsp;</div>
							</div>
							<div class="col-xs-3" style="padding:0px;padding-left:3px;">
								<label>Fix Hours</label>
								<input type="number" name="hours_plan" id="hoursplan" class="form-control" step="0.50" disabled>
								<div style="color:#F00;padding-right:10px;" id="info2">&nbsp;</div>
							</div>
							
						</div>

						<div class="form-group come" style="padding-top:80px;">
							<div class="col-xs-7" style="padding:0px;padding-right:3px;">
								<label>Type</label>
								<select name="ot_category" id="otcategory" class="form-control">
									<option value=""></option>
									<option value="1">Lembur Awal/Ahhir</option>
									<option value="2">Lembur Hari Libur</option>
									<!-- 
									<option value="3">Lembur Hari Libur (6 HK Panjang)</option>
									<option value="4">Lembur Hari Libur (6 HK Pendek)</option>
									-->
								</select>
							</div>
							<div class="col-xs-5" style="padding:0px;padding-left:3px;">
								<label>Convertion</label>
								<input type="number" step="0.5" id="hoursconvertion" name="hours_convertion" class="form-control" disabled>
							</div>
						</div>

				</div>
				<div class="modal-footer" style="text-align:left;">
					<button type="button" class="btn btn-primary confirmafter">Confirm</button>
					<button type="button" class="btn btn-default pull-right cancelafter" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>

	<div class="modal fade" id="modal-sign">
		<div class="modal-dialog box box-info" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Sign Confirmation</h4>
				</div>
				<div class="modal-body">
					Aru you sure to Sign : <b id="delname"></b> ?
					<input type="hidden" id="delid">
					<input type="hidden" id="delid1">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary pull-left sign" data-dismiss="modal">Yes, Sign</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
	</div>


    @if ($message = Session::get('success'))
		<div class="alert alert-info alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
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
	<!--  on Load  -->
	<script>
		$( document ).ready(function() {
			$(document).on('change', '#signafter', function() {
				if($(this).val()=='2'){
					$(".come").hide();
				}else{
					$(".come").show();
				}
			});

			var posisi='6';
			var n=document.getElementById('jumlahno').value;
			var nilaimin=90;
			for(i=1;i<=n;i++){
				var satuan=document.getElementById('satuan'+i).value;
				//alert(satuan);
				if(satuan>=posisi){
					document.getElementById('R'+i).style.background = '#adff2f';
				}
				if(satuan>=9){
					document.getElementById('R'+i).style.background = '#f08080';
				}
				if(nilaimin>satuan){
					nilaimin=satuan;
				}
			}
			if(nilaimin>=posisi){
				document.getElementById('btnpai').style.pointerEvents='auto';
			}
		});
	</script>

	<!-- Tabel Configuration -->
	<script>
		$(function () {
			$('#table2').DataTable({
			'paging'      : false,
			'lengthChange': false,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 15,
			'autoWidth'   : true,
			})
		})
		$(function () {
			$('#table3').DataTable({
			'paging'      : false,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : false,
			})
		})
	</script>
	<!-- Show Form Update -->
	<script type="text/javascript">
		$(document).on('click', '.update-modal', function() {
			$('#id_employee').val($(this).data('id_employee'));
			$('#idafter').val($(this).data('idafter'));
			$('#otcategory').val($(this).data('otcategory'));
			$('#startplan').val($(this).data('startact'));
			$('#finishplan').val($(this).data('finishact'));
			$('#isoma').val($(this).data('otisoma'));
			$('#hoursplan').val($(this).data('hoursact'));
			$('#old_hour').val($(this).data('hoursact'));
			$('#otcategory').val($(this).data('otcategory'));
			$('#hoursconvertion').val($(this).data('hoursconvertion'));
			var harikerja=$(this).data('harikerja')
			//if(harikerja==6)alert('Informasi: Member ini masuk dalam 3 shift, sesuaikan Type OT dengan semestinya');
			$('#modal-update').modal('show');
		});
	</script>
	<!-- DUration Alert -->
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<!-- SetUp Hours -->
	<script>
		$("#startplan").change(function(){
			var Awal=new Date($('#startplan').val());
			var Akhir=new Date($('#finishplan').val());
			
			var Thn=Akhir.getFullYear();
			var Bln=Akhir.getMonth()+1;
			var Tgl=Akhir.getDate();
			var Hari = Akhir.getDay();
			
			var Break1=new Date(Thn+'-'+Bln+'-'+Tgl+' 02:00:00');
			if(Hari==5){var Break2=new Date(Thn+'-'+Bln+'-'+Tgl+' 11:30:00');}
			else{var Break2=new Date(Thn+'-'+Bln+'-'+Tgl+' 12:00:00');}
			var Break3=new Date(Thn+'-'+Bln+'-'+Tgl+' 18:00:00');

			//Setting Isoma
			if(Awal<Break1 && Akhir>Break1){$('#isoma').val('45');}
			else if(Awal<Break2 && Akhir>Break2){
				if(Hari==5){$('#isoma').val('90');}
				else{$('#isoma').val('45');}
				//else{$('#isoma').val('30');}
			}
			else if(Awal<Break3 && Akhir>Break3){$('#isoma').val('30');}
			else{$('#isoma').val('0');}

			var n=((Akhir-Awal)/60000/60)-($('#isoma').val()/60);
			var sisa=n%1;
			//Setting Category
			//if(Hari>0 && Hari<6){
				if(Hari>0 && Hari<6 && n<6){
				$('#otcategory').val(1);
			}else{
				$('#otcategory').val(2);
			}

			if(sisa<0.5){
				var add=0;
			}else if(sisa<0.75){
				var add=0.5;
			}else{
				var add=1;
			}
			var fix=n-sisa+add;
			if(n<1){fix=0;}

			$('#hoursdraft').val(n);
			$('#hoursplan').val(fix);

			var Awal=new Date($('#startplan').val());
			var Akhir=new Date($('#finishplan').val());
			
			var Thn=Akhir.getFullYear();
			var Bln=Akhir.getMonth()+1;
			var Tgl=Akhir.getDate();
			var Hari = Akhir.getDay();
			
			var Break1=new Date(Thn+'-'+Bln+'-'+Tgl+' 02:00:00');
			if(Hari==5){var Break2=new Date(Thn+'-'+Bln+'-'+Tgl+' 11:30:00');}
			else{var Break2=new Date(Thn+'-'+Bln+'-'+Tgl+' 12:00:00');}
			var Break3=new Date(Thn+'-'+Bln+'-'+Tgl+' 18:00:00');

			//Setting Isoma
			if(Awal<Break1 && Akhir>Break1){$('#isoma').val('45');}
			else if(Awal<Break2 && Akhir>Break2){
				if(Hari==5){$('#isoma').val('90');}
				else{$('#isoma').val('45');}
				//else{$('#isoma').val('30');}
			}
			else if(Awal<Break3 && Akhir>Break3){$('#isoma').val('30');}
			else{$('#isoma').val('0');}

			var n=((Akhir-Awal)/60000/60)-($('#isoma').val()/60);
			var sisa=n%1;
			//Setting Category
			if(Hari>0 && Hari<6 && n<6){
				$('#otcategory').val(1);
			}else{
				$('#otcategory').val(2);
			}

			if(sisa<0.5){
				var add=0;
			}else if(sisa<0.75){
				var add=0.5;
			}else{
				var add=1;
			}
			var fix=n-sisa+add;
			if(n<1){fix=0;}

			$('#hoursdraft').val(n);
			$('#hoursplan').val(fix);

			var category=$('#otcategory').val();
			var plan=$('#hoursplan').val();
			var oldhour=$('#old_hour').val();
			var conv=0;
			if(category=='1' && plan>=1){
				conv=conv+(1.5)+((plan-1)*2);
			}
			if(category=='2'){
				conv=conv+(plan*2);
				if(plan>=9){
					conv=conv+1;
				}
				if(plan>=10){
					conv=conv+((plan-9)*2);
				}
			}
			if(category=='3'){
				conv=conv+(plan*2);
				if(plan>=8){
					conv=conv+1;
				}
				if(plan>=9){
					conv=conv+((plan-8)*2);
				}
			}
			if(category=='4'){
				conv=conv+(plan*2);
				if(plan>=6){
					conv=conv+1;
				}
				if(plan>=7){
					conv=conv+((plan-6)*2);
				}
			}
			$('#hoursconvertion').val(conv);
			if(plan<=0||plan*1>oldhour*1){
				document.getElementById("confirm").disabled = false;
				//document.getElementById("confirm").disabled = true;
			}else{
				document.getElementById("confirm").disabled = false;
			}
		});
		$("#finishplan").change(function(){
			var Awal=new Date($('#startplan').val());
			var Akhir=new Date($('#finishplan').val());
			
			var Thn=Akhir.getFullYear();
			var Bln=Akhir.getMonth()+1;
			var Tgl=Akhir.getDate();
			var Hari = Akhir.getDay();
			
			var Break1=new Date(Thn+'-'+Bln+'-'+Tgl+' 02:00:00');
			if(Hari==5){var Break2=new Date(Thn+'-'+Bln+'-'+Tgl+' 11:30:00');}
			else{var Break2=new Date(Thn+'-'+Bln+'-'+Tgl+' 12:00:00');}
			var Break3=new Date(Thn+'-'+Bln+'-'+Tgl+' 18:00:00');

			//Setting Isoma
			if(Awal<Break1 && Akhir>Break1){$('#isoma').val('45');$('#isoma2').val('45');}
			else if(Awal<Break2 && Akhir>Break2){
				if(Hari==5){$('#isoma').val('90');$('#isoma2').val('90');}
				else{$('#isoma').val('45');$('#isoma2').val('45');}
			}
			else if(Awal<Break3 && Akhir>Break3){$('#isoma').val('30');$('#isoma2').val('30');}
			else{$('#isoma').val('0');$('#isoma2').val('0');}

			var n=((Akhir-Awal)/60000/60)-($('#isoma').val()/60);
			//Setting Category
			if(Hari>0 && Hari<6 && n<6){
				$('#otcategory').val(1);
				$('#otcategory2').val(1);
			}else{
				$('#otcategory').val(2);
				$('#otcategory2').val(2);
			}

			var sisa=n%1;
			if(sisa<0.5){
				var add=0;
			}else if(sisa<0.75){
				var add=0.5;
			}else{
				var add=1;
			}
			var fix=n-sisa+add;
			if(n<1){fix=0;}

			$('#hoursdraft').val(n);
			$('#hoursplan').val(fix);

			var Awal=new Date($('#startplan').val());
			var Akhir=new Date($('#finishplan').val());
			
			var Thn=Akhir.getFullYear();
			var Bln=Akhir.getMonth()+1;
			var Tgl=Akhir.getDate();
			var Hari = Akhir.getDay();
			
			var Break1=new Date(Thn+'-'+Bln+'-'+Tgl+' 02:00:00');
			if(Hari==5){var Break2=new Date(Thn+'-'+Bln+'-'+Tgl+' 11:30:00');}
			else{var Break2=new Date(Thn+'-'+Bln+'-'+Tgl+' 12:00:00');}
			var Break3=new Date(Thn+'-'+Bln+'-'+Tgl+' 18:00:00');

			//Setting Isoma
			if(Awal<Break1 && Akhir>Break1){$('#isoma').val('45');$('#isoma2').val('45');}
			else if(Awal<Break2 && Akhir>Break2){
				if(Hari==5){$('#isoma').val('90');$('#isoma2').val('90');}
				else{$('#isoma').val('45');$('#isoma2').val('45');}
			}
			else if(Awal<Break3 && Akhir>Break3){$('#isoma').val('30');$('#isoma2').val('30');}
			else{$('#isoma').val('0');$('#isoma2').val('0');}

			var n=((Akhir-Awal)/60000/60)-($('#isoma').val()/60);
			//Setting Category
			if(Hari>0 && Hari<6 && n<6){
				$('#otcategory').val(1);
				$('#otcategory2').val(1);
			}else{
				$('#otcategory').val(2);
				$('#otcategory2').val(2);
			}

			var sisa=n%1;
			if(sisa<0.5){
				var add=0;
			}else if(sisa<0.75){
				var add=0.5;
			}else{
				var add=1;
			}
			var fix=n-sisa+add;
			if(n<1){fix=0;}

			$('#hoursdraft').val(n);
			$('#hoursplan').val(fix);

			var category=$('#otcategory').val();
			var plan=$('#hoursplan').val();
			var oldhour=$('#old_hour').val();
			var conv=0;
			if(category=='1' && plan>=1){
				conv=conv+(1.5)+((plan-1)*2);
			}
			if(category=='2'){
				conv=conv+(plan*2);
				if(plan>=9){
					conv=conv+1;
				}
				if(plan>=10){
					conv=conv+((plan-9)*2);
				}
			}
			if(category=='3'){
				conv=conv+(plan*2);
				if(plan>=8){
					conv=conv+1;
				}
				if(plan>=9){
					conv=conv+((plan-8)*2);
				}
			}
			if(category=='4'){
				conv=conv+(plan*2);
				if(plan>=6){
					conv=conv+1;
				}
				if(plan>=7){
					conv=conv+((plan-6)*2);
				}
			}
			$('#hoursconvertion').val(conv);
			if(plan<=0||plan*1>oldhour*1){
				document.getElementById("confirm").disabled = false;
				//document.getElementById("confirm").disabled = true;
			}else{
				document.getElementById("confirm").disabled = false;
			}
		});
		$("#isoma").change(function(){
			var now=new Date($('#startplan').val());
			var bitDate=new Date($('#finishplan').val());
			var n=((bitDate-now)/60000/60)-($('#isoma').val()/60);
			$('#hoursplan').val(n);

			var category=$('#otcategory').val();
			var plan=$('#hoursplan').val();
			var conv=0;
			if(category=='1' && plan>=1){
				conv=conv+(1.5)+((plan-1)*2);
			}
			if(category=='2'){
				conv=conv+(plan*2);
				if(plan>=8){
					conv=conv+1;
				}
				if(plan>=9){
					conv=conv+((plan-8)*2);
				}
			}
			if(category=='3'){
				conv=conv+(plan*2);
				if(plan>=8){
					conv=conv+1;
				}
				if(plan>=9){
					conv=conv+((plan-8)*2);
				}
			}
			if(category=='4'){
				conv=conv+(plan*2);
				if(plan>=6){
					conv=conv+1;
				}
				if(plan>=7){
					conv=conv+((plan-6)*2);
				}
			}
			$('#hoursconvertion').val(conv);
		});
		$("#otcategory").change(function(){
			var category=$('#otcategory').val();
			var plan=$('#hoursplan').val();
			var conv=0;
			if(category=='1' && plan>=1){
				conv=conv+(1.5)+((plan-1)*2);
			}
			if(category=='2'){
				conv=conv+(plan*2);
				if(plan>=8){
					conv=conv+1;
				}
				if(plan>=9){
					conv=conv+((plan-8)*2);
				}
			}
			if(category=='3'){
				conv=conv+(plan*2);
				if(plan>=8){
					conv=conv+1;
				}
				if(plan>=9){
					conv=conv+((plan-8)*2);
				}
			}
			if(category=='4'){
				conv=conv+(plan*2);
				if(plan>=6){
					conv=conv+1;
				}
				if(plan>=7){
					conv=conv+((plan-6)*2);
				}
			}
			$('#hoursconvertion').val(conv);
			//alert(conv);
		});
	</script>
	<!-- Update Verification -->
	<script>
		$(document).on('click', '.confirmafter', function() {
		
			$.ajaxSetup({
				type:"POST",
				url: "/Admin/Overtime/Verification/Update",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			var date_on=$("#date_on").val();
			var id_employee=$("#id_employee").val();
			var id_after=$("#idafter").val();
			var start_act=$("#startplan").val();
			var finish_act=$("#finishplan").val();
			var ot_category=$("#otcategory").val();
			var minutes_break=$("#isoma").val();
			var hours_plan=$("#hoursplan").val();
			var hours_convertion=$("#hoursconvertion").val();
			var sign_after=$("#signafter").val();

			$.ajax({
				data:{date_on:date_on,id_employee:id_employee,id_after:id_after,start_act:start_act,finish_act:finish_act,ot_category:ot_category,minutes_break:minutes_break,hours_plan:hours_plan,hours_convertion:hours_convertion,sign_after:sign_after},
				success: function(respond){
					//alert('Confirm Realisasi Overtime Berhasil');
					//$('#modal-update').modal('hide');
					window.location.reload();
				}
			})
		});

	</script>
	<!-- setUp Yes/No -->
	<script>
		$(document).on('click', '.yes-dip', function() {
			var x=$(this).data('nilai');
			
			var posisi='6';
			document.getElementById('satuan'+x).value = posisi;

			var n=document.getElementById('jumlahno').value;
			var nilaimin=90;
			for(i=1;i<=n;i++){
				var satuan=document.getElementById('satuan'+i).value;
				if(nilaimin>satuan){
					nilaimin=satuan;
				}
			}
			//alert(nilaimin);
			if(nilaimin>=posisi){
				document.getElementById('btnpai').style.pointerEvents='auto';
			}

			
			$.ajaxSetup({
				type:"POST",
				url: "/Admin/Overtime/Detail/Status",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			var detailid=$(this).data('detailid');
			var detailstatus='6';
			//alert(detailstatus);
			$.ajax({
				data:{detailid:detailid,detailstatus:detailstatus},
				success: function(respond){
					document.getElementById('R'+x).style.background = '#adff2f';
					document.getElementById('yes-dip'+x).disabled = true; 
					document.getElementById('no-dip'+x).disabled = false; 
				}
			})


		});
		$(document).on('click', '.no-dip', function() {
			var x=$(this).data('nilai');

			var posisi='6';
			document.getElementById('satuan'+x).value = '9'+posisi; 

			var n=document.getElementById('jumlahno').value;
			var nilaimin=90;
			for(i=1;i<=n;i++){
				var satuan=document.getElementById('satuan'+i).value;
				if(nilaimin>satuan){
					nilaimin=satuan;
				}
			}
			if(nilaimin>=posisi){
				document.getElementById('btnpai').style.pointerEvents='auto';
			}

			$.ajaxSetup({
				type:"POST",
				url: "/Admin/Overtime/Detail/Status",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			var detailid=$(this).data('detailid');
			var detailstatus='9'+posisi;
			$.ajax({
				data:{detailid:detailid,detailstatus:detailstatus},
				success: function(respond){
				document.getElementById('R'+x).style.background = '#f08080';
				document.getElementById('yes-dip'+x).disabled= false; 
				document.getElementById('no-dip'+x).disabled = true; 
				}
			})

		});
	</script>

	<!-- Sign Confirmation -->
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.sign-modal', function() {
			$('#delid').val($(this).data('delid'));
			$('#delid1').val($(this).data('delid1'));
			$('#delname').text($(this).data('delname'));
			$('#modal-sign').modal('show');
		});
		$('.modal-footer').on('click', '.sign', function() {
			var x=$('#delid').val();
			var y=$('#delid1').val();
			window.location.href='/Admin/Overtime/Approval/Sign/'+x+'/'+y;
		});
	</script>
	<script>
		$(document).on('click', '#updateFinger', function() {
		
			$.ajaxSetup({
				type:"POST",
				url: "/Admin/Overtime/Verification/UpdateFinger",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			var id="<?php echo $idot;?>";

			$.ajax({
				data:{id:id},
				success: function(respond){
					//alert(respond);
					window.location.reload();
				}
			})
		});

	</script>

@endsection

