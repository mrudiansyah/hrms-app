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
		.absolute {
			position: absolute;
			height:80px;
			left:20px;
			max-width:130px;
			top:-10px;
		}		
		.relative {
			left:0px;
			position: relative;
			height:60px;
		}	
	</style>
  	<!-- Contents -->
	<div class="content-wrapper">
		<section class="content-header">
			<h1 onclick="">
				Approval SPL
				<small>Surat Perintah Lembur</small>
			</h1>
		</section>	
		<section class="content">
		<div class="box box-primary">
			@foreach($tb_overtime as $dt)
			<?php 
				if($dt->status_paid=='1')$posisi_approval='6';
				elseif($dt->status_approve=='1')$posisi_approval='5';
				elseif($dt->status_dicatat=='1')$posisi_approval='4';
				elseif($dt->status_diketahui=='1')$posisi_approval='3';
				elseif($dt->status_disetujui=='1'){$posisi_approval='2';}
				elseif($dt->status_diperintah=='1')$posisi_approval='1';
				else $posisi_approval='0';
				$posisi_approval++;
			?>
			<div class="box-header with-border">
				<label>Preview SPL: {{$dt->id_overtime}}</label>
				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				</div>
			</div>
			<div class="box-body">
			<div style="height:70px;">&nbsp;
				<?php echo "<a href='/Admin/Overtime/Preview/".$dt->id."' target='_blank' class='btn btn-app'><i class='fa fa-print'></i> Printview</a>";?>
				<div class="pull-right">

					<!-- pointer-events: none -->
					<?php $showaccess=0;?>
					<?php if($id_loger==$dt->diperintah&&$dt->status_diperintah=='0'&&$status_before=='0'){$showaccess++;echo "<a title='Order Sign' id='btndips' class='btn btn-app sign-modal' style='color:#00C;' data-delid='".$dt->id."' data-delid1='diperintah' data-delname='".$dt->id_overtime."'><i class='fa fa-check-square-o'></i> Confirm</a>";}?>
					<?php if($id_loger==$dt->diperintah&&$dt->status_diperintah=='0'&&$status_before=='0')echo "<a class='btn btn-app denied-modal' style='color:#C00;' data-denid='".$dt->id."' data-denid1='diperintah' data-denname='".$dt->id_overtime."'><i class='fa fa-close'></i> Denied</a>";?>
					<?php if($id_loger==$dt->diperintah&&$dt->status_diperintah!='0'&&$dt->status_disetujui!='1'&&$dt->status_diketahui!='1'&&$dt->status_dicatat!='1')echo "<a href='/Admin/Overtime/Approval/Review/".$dt->id."/diperintah' class='btn btn-app' style='color:#00C;'><i class='fa fa-edit'></i> Review</a>";?>

					<?php if($id_loger==$dt->disetujui&&$dt->status_diperintah=='1'&&$dt->status_disetujui=='0'){$showaccess++;echo "<a title='Approved Sign' id='btndis' class='btn btn-app sign-modal' style='color:#00C;' data-delid='".$dt->id."' data-delid1='disetujui' data-delname='".$dt->id_overtime."'><i class='fa fa-check-square-o'></i> Confirm</a>";}?>
					<?php if($id_loger==$dt->disetujui&&$dt->status_diperintah=='1'&&$dt->status_disetujui=='0')echo "<a class='btn btn-app denied-modal' style='color:#C00;' data-denid='".$dt->id."' data-denid1='disetujui' data-denname='".$dt->id_overtime."'><i class='fa fa-close'></i> Denied</a>";?>
					<?php if($id_loger==$dt->disetujui&&$dt->status_diperintah=='1'&&$dt->status_disetujui!='0'&&$dt->status_diketahui!='1'&&$dt->status_dicatat!='1')echo "<a href='/Admin/Overtime/Approval/Review/".$dt->id."/disetujui' class='btn btn-app' style='color:#00C;'><i class='fa fa-edit'></i> Review</a>";?>

					<?php if($id_loger==$dt->diketahui&&$dt->status_disetujui=='1'&&$dt->status_diketahui=='0'){$showaccess++;echo "<a title='Seen Sign' id='btndik' class='btn btn-app sign-modal' style='color:#00C;' data-delid='".$dt->id."' data-delid1='diketahui' data-delname='".$dt->id_overtime."'><i class='fa fa-check-square-o'></i> Confirm</a>";}?>
					<?php if($id_loger==$dt->diketahui&&$dt->status_disetujui=='1'&&$dt->status_diketahui=='0')echo "<a class='btn btn-app denied-modal' style='color:#C00;' data-denid='".$dt->id."' data-denid1='diketahui' data-denname='".$dt->id_overtime."'><i class='fa fa-close'></i> Denied</a>";?>
					<?php if($id_loger==$dt->diketahui&&$dt->status_disetujui=='1'&&$dt->status_diketahui!='0'&&$dt->status_dicatat!='1'&&$dt->status_approve=='0')echo "<a href='/Admin/Overtime/Approval/Review/".$dt->id."/diketahui' class='btn btn-app' style='color:#00C;'><i class='fa fa-edit'></i> Review</a>";?>

					<?php if($id_loger==$dt->dicatat&&($dt->status_diketahui=='1'||($dt->diketahui==''&&$dt->status_disetujui=='1')||($dt->disetujui==''&&$dt->status_diperintah=='1'))&&$dt->status_dicatat=='0'&&$dt->status_approve=='0'){$showaccess++;echo "<a title='Recorded Sign' id='btndic' class='btn btn-app sign-modal' style='color:#00C;' data-delid='".$dt->id."' data-delid1='dicatat' data-delname='".$dt->id_overtime."'><i class='fa fa-check-square-o'></i> Confirm</a>";}?>
					<?php if($id_loger==$dt->dicatat&&($dt->status_diketahui=='1'||($dt->diketahui==''&&$dt->status_disetujui=='1')||($dt->disetujui==''&&$dt->status_diperintah=='1'))&&$dt->status_dicatat=='0'&&$dt->status_approve=='0')echo "<a class='btn btn-app denied-modal' style='color:#C00;' data-denid='".$dt->id."' data-denid1='dicatat' data-denname='".$dt->id_overtime."'><i class='fa fa-close'></i> Denied</a>";?>
					<?php if($id_loger==$dt->dicatat&&($dt->status_diketahui=='1'||($dt->diketahui==''&&$dt->status_disetujui=='1')||($dt->disetujui==''&&$dt->status_diperintah=='1'))&&$dt->status_dicatat!='0'&&$dt->status_approve=='0')echo "<a href='/Admin/Overtime/Approval/Review/".$dt->id."/dicatat' class='btn btn-app' style='color:#00C;'><i class='fa fa-edit'></i> Review</a>";?>

					<?php if($id_loger==$dt->approve&&$dt->status_dicatat=='1'&&$dt->status_approve=='0'&&$status_after=='0'){$showaccess++;echo "<a title='Checked & Approved Sign' id='btnapp' class='btn btn-app sign-modal' style='color:#00C;' data-delid='".$dt->id."' data-delid1='approve' data-delname='".$dt->id_overtime."'><i class='fa fa-check-square-o'></i> Confirm</a>";}?>
					<?php if($id_loger==$dt->approve&&$dt->status_dicatat=='1'&&$dt->status_approve=='0'&&$status_after=='0')echo "<a class='btn btn-app denied-modal' style='color:#C00;' data-denid='".$dt->id."' data-denid1='approve' data-denname='".$dt->id_overtime."'><i class='fa fa-close'></i> Denied</a>";?>
					<?php //if($id_loger==$dt->approve&&$dt->status_dicatat=='1'&&$dt->status_approve!='0'&&$dt->status_paid=='0')echo "<a href='/Admin/Overtime/Approval/Review/".$dt->id."/approve' class='btn btn-app' style='color:#00C;'><i class='fa fa-edit'></i> Review</a>";?>

					<?php if($id_loger==$dt->paid&&$dt->status_approve=='1'&&$dt->status_paid=='0'){$showaccess++;echo "<a title='Recorded & Paid Sign' id='btnpai' class='btn btn-app sign-modal' style='color:#00C;' data-delid='".$dt->id."' data-delid1='paid' data-delname='".$dt->id_overtime."'><i class='fa fa-check-square-o'></i> Confirm</a>";}?>
					<?php if($id_loger==$dt->paid&&$dt->status_approve=='1'&&$dt->status_paid=='0')echo "<a class='btn btn-app denied-modal' style='color:#C00;' data-denid='".$dt->id."' data-denid1='paid' data-denname='".$dt->id_overtime."'><i class='fa fa-close'></i> Denied</a>";?>
					<?php //if($id_loger==$dt->paid&&$dt->status_approve=='1'&&$dt->status_paid!='0')echo "<a href='/Admin/Overtime/Approval/Review/".$dt->id."/paid' class='btn btn-app' style='color:#00C;'><i class='fa fa-edit'></i> Review</a>";?>

					<a href="/Admin/Overtime/Plan/0" class="btn btn-app"<?php if(isset($id_employee)&&$id_employee=='122')echo " onclick='window.close();'";?>><i class="fa fa-sign-out"></i> Back</a>
				</div>
			</div>
			<div style="padding:30px;background:#525659;">
			<div style="padding:15px;background:#FFF;overflow-x: scroll;">
				<table cellspacing="0">
					<tr>
						<td style="border:1px solid #000;padding:10px 5px;">
							<table style="width:100%;" class="tabeljudul">
								<tr>
									<td style="width:240px;"><img src="{{ asset('public/gambar/logosai.png') }}" style="width:180px;"></td>
									<td style="text-align:center;font-size:16px;"><b><u>OVERTIME (OT) ORDER FORM</u></b><br><i>Surat Perintah Lembur  ( SPL )</i></td>
									<td style="width:300px;">
										<table style="width:100%;">
											<tr>
												<td style="width:120px">Npmpr OT/SPL</td>
												<td>: {{$dt->id_overtime}}</td>
											</tr>
											<tr>
												<td>Divisi</td>
												<td>: {{$dt->dept_name}}</td>
											</tr>
											<tr>
												<td>Day, Date/Hari, Tgl</td>
												<td>: <?php echo date('l,d F Y',strtotime($dt->ot_date));?></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="border-left:1px solid #000;border-right:1px solid #000;padding:20px;">
							<ol style="font-size:11px;">
								<li>Except permanent overtime, all overtime planing must be requested and conducted by using or based on current overtime order form Kecuali lembur tetap semua rencana pelaksanaan kerja lembur wajib dimohonkan serta dilaksanakan dengan menggunakan atau berdasarkan Surat Perintah Lembur yang berlaku.</li>
								<li>Overtime with no overtime order form, will not be paid  Lembur yang tidak menggunakan Surat Perintah Lembur tidak akan dibayar.</li>
								<li>Planning of overtime must be submitted to HRD Departement, not more than 1 (one) hour before overtime is conducted  Rencana  pelaksanaan  lembur  wajib  diajukan  selambat - lambatnya 1 (satu) jam sebelum lembur tersebut dilaksanakan</li>
								<li>In emergency cases, overtime order can be submitted 1 x 24 hours after overtime coducted Dalam keadaan darurat dimana ketentuan no. 3 tidak dapat dilaksanakan, maka Surat Perintah Lembur dapat dibuat dan diajukan paling lambat 1 x 24 jam setelah lembur dilaksanakan.</li>
								<li>If break the provisions no. 3 & 4 then cost of overtime will not be paid Apabila melanggar ketentuan no 3 & 4, maka biaya lembur tidak akan dibayar.</li>
							</ol>
						</td>
					</tr>
					<tr>
						<td style="font-size:12px;border:0px solid #000;padding:0px;font-weight: normal">
							<table style="width:100%;" cellspacing="0" class="tabelot">
								<tr>
									<th rowspan="2" style="width:30px;"><b>NO</b><br><i>No</i></th>
									<th rowspan="2" style="width:120px;"><b>NAME</b><br><i>Name</i></th>
									<th rowspan="2" style="width:80px;"><b>NIK</b><br><i>NIK</i></th>
									<th rowspan="2" style="width:100px;"><b>DIV/SUB DIV</b><br><i>Div. & Sub Div.</i></th>
									<th rowspan="2" style="width:80px;"><b>STATUS</b><br><i>PIC/Subtitusi</i></th>
									<th rowspan="2" style="min-width:180px;"><b>REASON & TARGET</b><br><i>Alasan & Hasil Lembur yang harus dicapai</i></th>
									<th colspan="3"><b>OVER TIME PLANNING (Rencana)<b></th>
									<th colspan="3"><b>OVER TIME ACTUAL (Kenyataan)<b></th>
									<th rowspan="2" style="width:40px;"><b>ACTUAL HOURS</b><br><i>Realisasi Jam OT</i></th>
									<th rowspan="2" style="width:60px;"><b>OVER TIME CONVERTION</b><br><i>Konfersi Jam OT</i></th>
									<th rowspan="2" style="width:60px;"><b>ACTION</b><br><i>Yes/No</i></th>
								</tr>
								<tr>
									<th style="width:50px;"><b>START</b><br><i>mulai Jam</i></th>
									<th style="width:50px;"><b>FINISH</b><br><i>selesai Jam</i></th>
									<th style="width:50px;"><b>EMP. SIGNED</b><br><i>ttd Karyawan</i></th>
									<th style="width:50px;"><b>START</b><br><i>mulai Jam</i></th>
									<th style="width:50px;"><b>FINISH</b><br><i>selesai Jam</i></th>
									<th style="width:50px;"><b>EMP. SIGNED</b><br><i>ttd Karyawan</i></th>
								</tr>
								<?php $no=0;$hours_acum=0;$convertion_acum=0;$subtitusi=0;?>
								@foreach($tb_overtime_detail as $dt2)
								<?php 
									$no++;$hours_acum=$hours_acum+$dt2->hours_act;$convertion_acum=$convertion_acum+$dt2->hours_convertion;
									
									
									$short=date('Y-m-d',strtotime($dt2->start_plan));
									$sekarang=date('w',strtotime($short));
									$text="-".$sekarang." days";
									$batas_awal=date('Y-m-d H:i:s',strtotime($text,strtotime($short.' 00:00:00')));
									$short2=date('Y-m-d',strtotime($batas_awal));
									$batas_akhir=date('Y-m-d H:i:s',strtotime('+6 days',strtotime($short2.' 23:59:59')));

									//$jamot=0;
									$host = mysqli_connect("192.168.1.4","ems","123456","db_ems");
									$qry=mysqli_query($host,"SELECT sum(hours_plan) as jamot  FROM tb_overtime_details WHERE id_employee = '".$dt2->id_employee."' and start_plan>='".$batas_awal."' and start_plan<='".$batas_akhir."' and ot_category='1'")or die(mysqli_error($host));
									while($dtjam=mysqli_fetch_array($qry)){
										$jamot=$dtjam['jamot'];
									}
						
								?>
								<tr id="R{{$no}}">
									<td id="C1B{{$no}}">
										{{$no}}
									</td>
									<td id="C2B{{$no}}" style="text-align:left;">{{$dt2->employee_name}}</td>
									<td id="C3B{{$no}}">{{$dt2->NIK}}</td>
									<td id="C4B{{$no}}">{{$dt->dept_name}}</td>
									<td>
										@if($dt2->pic_or_subtitusi=="Subtitusi")
											<label class="label label-danger">{{$dt2->pic_or_subtitusi}}</label>
											<?php $subtitusi++;?>
										@elseif($dt2->pic_or_subtitusi=="PIC")
											<label class="label label-success">Original</label>
										@endif
									</td>
									<td>
										{{$dt2->reason}}
									</td>
									<td><?php echo date('H:i',strtotime($dt2->start_plan));?></td>
									<td><?php echo date('H:i',strtotime($dt2->finish_plan));?></td>
									<td><?php if($dt2->sign_before=='1')echo "Confirm";?></td>
									<td><?php if($dt2->sign_after=='0')echo "";else echo date('H:i',strtotime($dt2->start_act));?></td>
									<td><?php if($dt2->sign_after=='0')echo "";else echo date('H:i',strtotime($dt2->finish_act));?></td>
									<td><?php if($dt2->sign_after=='1')echo "Confirm";?></td>
									<td>
										<?php if($dt2->sign_after=='0')echo "";else echo date('H:i',strtotime($dt2->hours_act));?>
										<?php if($dt2->ot_category==1&&$dt2->hours_plan>4)echo "<sub class='pull-right label label-warning'>".$dt2->hours_plan."H</sub>";?>
									</td>
									<td style="padding-right:5px;">
										<?php if($dt2->sign_after=='0')echo "";else echo date('H:i',strtotime($dt2->hours_convertion));?>
										<?php if($jamot>18)echo "<sub class='pull-right label label-warning'>a week: ".$jamot."H</sub>";?>
									</td>
									<td>
										<?php
											if($dt2->isOver==1&&$dt2->id_confirm==0){
												echo "<i title='".$dt2->reason_over."'>Over</i>&nbsp;&nbsp;";
												if($id_loger==$dt->diperintah||$id_loger==$dt->disetujui||$id_loger==$dt->diketahui)
												echo "<a href='/Admin/Overtime/Confirm/".$dt2->id."/".$id_loger."' type='button' class='btn btn-warning btn-xs'><i class='fa fa-check'></i></a>";
											}
										?>
										<input type="hidden" id="satuan{{$no}}" value="{{$dt2->status}}">
										<?php 
											if($dt2->status>90)$posisi2=substr($dt2->status,1,1);
											else $posisi2=$dt2->status;
											if($dt2->status=='91')$cancelby='Cancel by Order';
											elseif($dt2->status=='92')$cancelby='Cancel by Approved';
											elseif($dt2->status=='93')$cancelby='Cancel by Seen';
											elseif($dt2->status=='94')$cancelby='Cancel by Recorded';
											elseif($dt2->status=='95')$cancelby='Cancel by Approval';
											elseif($dt2->status=='96')$cancelby='Cancel by Paid';
											else $cancelby='';
											//echo $posisi_approval.' & '.$posisi2;
											if($dt2->status>9&&$posisi_approval>$posisi2)echo "";elseif($showaccess>0){?>
											<button title="Yes" id="yes-dip{{$no}}" type="button" class="btn btn-success btn-xs yes-dip" data-nilai="{{$no}}" data-detailid="{{$dt2->id}}"><i class="fa fa-check"></i></button>
											<button title="No" id="no-dip{{$no}}" type="button" class="btn btn-danger btn-xs no-dip" data-nilai="{{$no}}" data-detailid="{{$dt2->id}}"><i class="fa fa-close"></i></button>
										<?php }if($dt2->status>90)echo "<i class='fa fa-info-circle' title='".$cancelby."'> Hold</i>";?>
									</td>
								</tr>
								@endforeach	
								<?php for($i=$no+1;$i<=$no+3;$i++){?>
								<tr>
									<td style="font-size:12px;padding:3px;">{{$i}}</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
								<?php }?>
								<tr>
									<td style="border-right:0px;" colspan="3">&nbsp;</td>
									<td style="border-left:0px;border-right:0px;"><input type="text" value="{{$no}}" style="width:50px;height:20px;font-size:16px;text-align:center;"></td>
									<td colspan="2" style="border-left:0px;text-align:left;">Person/s (<i>Orang</i>)</td>
									<td colspan="4">&nbsp;</td>
									<td colspan="2"><b>Total OT Hours</b><br><i>Total Jam Lembur</i></td>
									<td style="font-size:14px;"><?php if($dt2->sign_after==0)echo "";else echo number_format($hours_acum,'2');?></td>
									<td style="font-size:14px;">
										<?php if($dt2->sign_after==0)echo "";else echo number_format($convertion_acum,'2');?>
									</td>
									<td>
										<input type="hidden" id="statusapproval" value="{{$posisi_approval}}">
										<input type="hidden" id="jumlahno" value="{{$no}}">
									</td>
								</tr>
								<tr>
									<td colspan="8" style="padding:3px;font-size:14px;">
										Overtime plan legalized / Pengesahan Rencana Lembur
									</td>
									<td colspan="7" style="font-size:14px;">
										Overtime realization legalized / Pengesahan Realisasi Lembur
									</td>
								</tr>
								<tr>
									<td colspan="8" style="padding:10px;font-weight:normal;">
										<table style="width:100%;" class="tabeljudul">
											<tr>
												<td style="width:25%;">Order by<br>Diperintah oleh</td>
												<td style="width:25%;">
													<?php if($dt->disetujui!='')echo "Approved by<br>Disetujui oleh";?>
												</td>
												<td style="width:25%;">
													<?php if($dt->diketahui!='')echo "Seen by<br>Diketahui oleh";?>
												</td>
												<td style="width:25%;">Seen by<br>Diketahui oleh</td>
											</tr>
											<tr>
												<td class="relative" style="height:50px;">
													<?php 
														$reject=asset('public/approval/rejected.png');
														if($dt->status_diperintah=='1'){
															$ttd_app=asset('public/approval/'.$dt->diperintah.'.png');
															$confirm=asset('public/approval/confirm.png');
															$filename = 'c:/xampp/htdocs/public/approval/'.$dt->diperintah.'.png';
															if (file_exists($filename)){?>
																<img src='{{ $ttd_app }}' class="absolute">
															<?php }else{?>
																<img src='{{ $confirm }}' style='width:100px;height:40px;'>
															<?php }
														}elseif($dt->status_diperintah=='3'){?>
															<?php $lock=base_path().'/public/approval/lock.png';?>
															@if($dt->autoCancel==1)
																<!-- <img src='{{ $lock }}' style='width:100px;height:40px;'> -->
																Locked
															@else
																<img src='{{ $reject }}' style='width:100px;height:40px;'>
															@endif
														<?php }
													?>
													
												</td>
												<td class="relative">
													<?php 
														if($dt->status_disetujui=='1'){
															$ttd_app=asset('public/approval/'.$dt->disetujui.'.png');
															$confirm=asset('public/approval/confirm.png');
															$reject=asset('public/approval/rejected.png');
															$filename = 'c:/xampp/htdocs/public/approval/'.$dt->disetujui.'.png';
															if (file_exists($filename)){?>
																<img src='{{ $ttd_app }}' class="absolute">
															<?php }else{?>
																<img src='{{ $confirm }}' style='width:100px;height:40px;'>
															<?php }
														}elseif($dt->status_disetujui=='3'){?>
															<img src='{{ $reject }}' style='width:100px;height:40px;'>
														<?php }
													?>
												</td>
												<td class="relative">
													<?php if($dt->diketahui!=''){?>
													<?php if($dt->status_diketahui=='1'){
														if(!file_exists('/public/approval/'.$dt->diketahui.'png')){
															echo "<img src='/public/approval/".$dt->diketahui.".png' class='absolute'>";
														}else{
															echo "<img src='/public/approval/".$dt->diketahui.".png' style='width:100px;height:40px;'>";
														}
													}?>
													<?php if($dt->status_diketahui=='3')echo "<img src='/public/approval/rejected.png' style='width:100px;height:40px;'>";?>
													<?php }?>
												</td>
												<td class="relative">
													<?php if($dt->status_dicatat=='1'){
														if(!file_exists('/public/approval/'.$dt->dicatat.'png')){
															echo "<img src='/public/approval/".$dt->dicatat.".png' class='absolute'>";
														}else{
															echo "<img src='/public/approval/".$dt->dicatat.".png' style='width:100px;height:40px;'>";
														}
													}?>
													<?php if($dt->status_dicatat=='3')echo "<img src='/public/approval/rejected.png' style='width:100px;height:40px;'>";?>
												</td>
											</tr>
											<tr>
												<td>
													<u><?php if($dt->date_diperintah!='')echo date('d-M-Y H:i',strtotime($dt->date_diperintah));?></u><br>
													<b>{{$diperintah}}</b><br>({{$diperintah_j}})
												</td>
												<td>
													<?php if($dt->disetujui!=''){?>
														<u><?php if($dt->date_disetujui!='')echo date('d-M-Y H:i',strtotime($dt->date_disetujui));?></u><br>
														<b>{{$disetujui}}</b><br> ({{$disetujui_j}})
													<?php }?>
												</td>
												<td>
													<?php if($dt->diketahui!=''){?>
														<u><?php if($dt->date_diketahui!='')echo date('d-M-Y H:i',strtotime($dt->date_diketahui));?></u><br>
														<b>{{$diketahui}}</b><br>({{$diketahui_j}})
													<?php }?>
												</td>
												<td>
													<u><?php if($dt->date_dicatat!='')echo date('d-M-Y H:i',strtotime($dt->date_dicatat));?></u><br>
													<b>{{$dicatat}}</b><br>({{$dicatat_j}})
												</td>
											</tr>
										</table>
									</td>
									<td colspan="7">
										<table style="width:100%;" class="tabeljudul">
											<tr>
												<td style="width:50%;">Checked & Approved by<br>Diperiksa & Disetujui oleh</td>
												<td style="width:50%;">Recorded & Paid by<br>Dicatat & Dibayar oleh</td>
											</tr>
											<tr>
												<td style="height:60px;" class="relative">
													<?php if($dt->status_approve=='1'){
														if(!file_exists('/public/approval/'.$dt->approve.'png')){
															echo "<img src='/public/approval/".$dt->approve.".png' class='absolute'>";
														}else{
															echo "<img src='/public/approval/".$dt->approve.".png' style='width:100px;height:40px;'>";
														}
													}?>
													<?php if($dt->status_approve=='3')echo "<img src='/approval/rejected.png' style='width:100px;height:40px;'>";?>
												</td>
												<td class="relative">
													<?php if($dt->status_paid=='1'){
														if(!file_exists('/public/approval/'.$dt->paid.'png')){
															echo "<img src='/public/approval/".$dt->paid.".png' class='absolute'>";
														}else{
															echo "<img src='/public/approval/".$dt->paid.".png' style='width:100px;height:40px;'>";
														}
													}?>
													<?php if($dt->status_paid=='3')echo "<img src='/public/approval/rejected.png' style='width:100px;height:40px;'>";?>
												</td>
											</tr>
											<tr>
												<td>
													<u><?php if($dt->date_approve!='')echo date('d-M-Y H:i',strtotime($dt->date_approve));?></u><br>
													<b>{{$approve}}</b><br>({{$approve_j}})
												</td>
												<td>
													<u><?php if($dt->date_paid!='')echo date('d-M-Y H:i',strtotime($dt->date_paid));?></u><br>
													<b>{{$paid}}</b><br>({{$paid_j}})
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<div style="font-size:11px;border:0px;padding:5px;">
					<input type="hidden" id="subtitusi" value="{{$subtitusi}}">
					1. Original (White/Putih : Finance/Cashier   2. Copy 1 (Blue/Biru) : Relateddivision / Bagian yg lembur   3. Copy 2 (Green/Hijau) : Personnel / Payroll   4. Copy 3 (Yellow/Kuning) : Security / Keamanan 												
				</div>
				<div style="font-size:11px;border:1px solid #000;padding:5px;min-height:60px;">
					<label>Notes:</label>
					<ul>
						@foreach($tb_pesan as $dt)
							<li>{{$dt->penulis}}: {{$dt->pesan}}</li>
						@endforeach
					</ul>
					<div class="row" style="padding:0px 20px;">
						<form action="/Admin/Overtime/Note" method="post">
						{{ csrf_field() }}
						<input type="hidden" name="id_ot" value="{{$idSPL}}">
						<div class="form-group">
								<div class="row" style="padding:5px 20px;"><textarea name="pesan" class="form-control" rows="3" placeholder="Message ..."></textarea></div>
						</div>
						<div class="form-group pull-right">
							<input type="submit" class="btn btn-primary" value="Save Note">
						</div>
						</form>
					</div>
				</div>
			</div>
			</div>
			</div>
			@endforeach
		</div>
		</section>
  	</div>
    <!-- /.Content -->

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

	<div class="modal fade" id="modal-denied">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Reject Confirmation</h4>
				</div>
				<div class="modal-body">
					Aru you sure to Reject : <b id="denname"></b> ?
					<input type="hidden" id="denid">
					<input type="hidden" id="denid1">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left reject" data-dismiss="modal">Yes, Reject</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
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
	<!--  on Load  -->
	<script>
		$( document ).ready(function() {
			var subtitusi=document.getElementById('subtitusi').value;
			if(subtitusi>0){
				alert('Note: Di SPL ini terdapat member subtitusi, pastikan karyawan tersebut memiliki kompetensi di perkerjaan tersebut...!')
			}
			var posisi=document.getElementById('statusapproval').value;
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
			if(nilaimin==posisi){
				if(nilaimin=='1'){
					document.getElementById('btndips').style.pointerEvents='auto';
				}
				if(nilaimin=='2'){
					document.getElementById('btndis').style.pointerEvents='auto';
				}
				if(nilaimin=='3'){
					document.getElementById('btndik').style.pointerEvents='auto';
				}
				if(nilaimin=='4'){
					document.getElementById('btndic').style.pointerEvents='auto';
				}
				if(nilaimin=='5'){
					document.getElementById('btnapp').style.pointerEvents='auto';
				}
				if(nilaimin=='6'){
					document.getElementById('btnpai').style.pointerEvents='auto';
				}
			}
			//alert(nilaimin);
		});
	</script>
	<!-- Display Time Notification -->
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
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
	<!-- Denied Confirmation -->
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.denied-modal', function() {
			$('#denid').val($(this).data('denid'));
			$('#denid1').val($(this).data('denid1'));
			$('#denname').text($(this).data('denname'));
			$('#modal-denied').modal('show');
		});
		$('.modal-footer').on('click', '.reject', function() {
			var x=$('#denid').val();
			var y=$('#denid1').val();
			window.location.href='/Admin/Overtime/Approval/Denied/'+x+'/'+y;
		});
	</script>
	<!-- setUp Yes/No -->
	<script>
		$(document).on('click', '.yes-dip', function() {
			var x=$(this).data('nilai');
			
			var posisi=document.getElementById('statusapproval').value;
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
			if(nilaimin==posisi){
				if(nilaimin=='1'){
					document.getElementById('btndips').style.pointerEvents='auto';
				}
				if(nilaimin=='2'){
					document.getElementById('btndis').style.pointerEvents='auto';
				}
				if(nilaimin=='3'){
					document.getElementById('btndik').style.pointerEvents='auto';
				}
				if(nilaimin=='4'){
					document.getElementById('btndic').style.pointerEvents='auto';
				}
				if(nilaimin=='5'){
					document.getElementById('btnapp').style.pointerEvents='auto';
				}
				if(nilaimin=='6'){
					document.getElementById('btnpai').style.pointerEvents='auto';
				}
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
			var detailstatus=document.getElementById('statusapproval').value;;
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

			var posisi=document.getElementById('statusapproval').value;
			document.getElementById('satuan'+x).value = '9'+posisi; 

			var n=document.getElementById('jumlahno').value;
			var nilaimin=90;
			for(i=1;i<=n;i++){
				var satuan=document.getElementById('satuan'+i).value;
				if(nilaimin>satuan){
					nilaimin=satuan;
				}
			}
			if(nilaimin==posisi){
				if(nilaimin=='1'){
					document.getElementById('btndips').style.pointerEvents='auto';
				}
				if(nilaimin=='2'){
					document.getElementById('btndis').style.pointerEvents='auto';
				}
				if(nilaimin=='3'){
					document.getElementById('btndik').style.pointerEvents='auto';
				}
				if(nilaimin=='4'){
					document.getElementById('btndic').style.pointerEvents='auto';
				}
				if(nilaimin=='5'){
					document.getElementById('btnapp').style.pointerEvents='auto';
				}
				if(nilaimin=='6'){
					document.getElementById('btnpai').style.pointerEvents='auto';
				}
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
@endsection