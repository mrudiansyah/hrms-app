@extends('layouts/admin')
@section('Contents')
   <!-- Contents -->
   	<meta name="csrf-token" content="{{ csrf_token() }}">
	<style>
	canvas {
		-moz-user-select: none;
		-webkit-user-select: none;
		-ms-user-select: none;
	}
	</style>

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
		#table3 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		vertical-align:middle;
		text-align:left;
		}	
		#table4 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table5 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
    </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Approval SPL
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
		<section class="content">
			<?php $grafik=1;if($grafik==1){?>
                <?php if(isset($tb_sumot)){?>
                    <div class="box box-success" style="border:0px;">
                        <div class="box-header">
                            <i class="fa fa-bar-chart"></i>
                        <h3 class="box-title">Overtime vs Sales</h3>
                            <div class="box-tools pull-right">
                                <button title="Edit Sales" type="button" class="sales-modal btn btn-info btn-xs" data-periodesales="{{$periode}}" data-salesammount="{{$salesammount}}"><i class="fa fa-wrench"></i> Sales: <?php echo number_format($salesammount,0);?></button>
                                <a href="/Grafik/{{$periode}}/1" type="button" class="btn btn-primary btn-xs"><i class="fa fa-refresh"></i>&nbsp; Pareto {{$periode}}</a>
                            </div>
                        </div>
                        <div class="box-body" style="padding:0px 100px 30px 100px;">
                
                            <div style="width: 100%">
                                <canvas id="canvas"></canvas>
                            </div>

                            

                        </div>
                        <!-- /.box-body -->
                    </div>
                <?php }?>
			<?php }?>
			<div class="row">

				<div class="col-xs-12">
					<div class="box box-primary" style="background:#FFF;">
						<div class="box-header">
							<i class="fa fa-list"></i>
							<h3 class="box-title" id="judul">SPL List</h3>
							<div class="pull-right">
								<?php if (isset($status_lock)&&request()->user()->hasRole('hr_access')) {?>
									<?php if($status_lock==0){?>
										<a class="btn btn-app lock-modal"><i class="fa fa-unlock"></i> Un Lock</a>
										<a class="btn btn-app refresh-modal"><i class="fa fa-refresh"></i> Reopen</a>
									<?php }else{?>
										<a class="btn btn-app lock-modal"><i class="fa fa-lock"></i> Lock</a>
									<?php }?>
								<?php }?>
								<a <?php if($submenu=='verification2'){?>href="/Admin/Overtime/Verifications/0"<?php }?> class="btn btn-app table2">
									<?php if(isset($jmlot_plan)&&$jmlot_plan>0)echo "<span class='badge bg-yellow'>".$jmlot_plan."</span>";?>
									<i class="fa fa-file-o"></i> New SPL
								</a>
								<?php if($submenu=='realisation'){?>
									<a class="btn btn-app table3">
										<?php if(isset($jmlot_actual)&&$jmlot_actual>0)echo "<span class='badge bg-yellow'>".$jmlot_actual."</span>";?>
										<i class="fa fa-edit"></i> Proccess
									</a>
								<?php }?>
								<?php if($submenu=='verification'||$submenu=='verification2'){?>
									<a href="/Admin/Overtime/Verifications2/0" class="btn btn-app table5">
										<?php if(isset($jmlot_actual)&&$jmlot_actual>0)echo "<span class='badge bg-yellow'>".$jmlot_actual."</span>";?>
										<i class="fa fa-edit"></i> Verify
									</a>
								<?php }?>
								<a class="btn btn-app table4">
									<i class="fa fa-check-square-o"></i> Complete
								</a>
							</div>
						</div>
						<div class="box-body" style="min-height:200px;overflow-x: scroll;">
							<div id="tabel2">
								<table id="table2" class="table table-striped" border="0">
									<thead>
										<tr>
											<th style="width:60px;">NO</th>
											<th style="width:90px;">NO.SPL</th>
											<th style="width:90px;">OT.DATE</th>
											<th>DEPARTMENT</th>
											<th style="width:70px;background:#DDD;">ORDER</th>
											<th style="width:70px;background:#DDD;">APPROVED</th>
											<th style="width:70px;background:#DDD;">SEEN</th>
											<th style="width:70px;background:#DDD;">RECORDED</th>
											<th style="width:70px;background:#0F0;">APPROVE</th>
											<th style="width:70px;background:#0F0;">PAID</th>
										</tr>
									</thead>
									<tbody>
									<?php $no=0;?>
									@foreach($tb_overtime_plan as $dt)
										<?php 
											if($submenu=='approval')$tujuan="/Admin/Overtime/Approval/";
											if($submenu=='realisation')$tujuan="/Admin/Overtime/Dept/";
											if($submenu=='verification')$tujuan="/Admin/Overtime/Verification/";
										?>
										<tr <?php if($dt->autoCancel==1)echo "style='background:#f08080;';";elseif($dt->is_over==1)echo "style='background:#fece02;';";?>>
											<td>
												<?php $no++;echo $no;?>
												<div class="pull-right">
													<?php if($dt->autoCancel==0){?>
														<?php if($submenu=="realisation"&&$dt->status_diperintah=='0'){?>
															<a title="Show" href='/Admin/Overtime/Draft/<?php echo $dt->id;?>'><button type="button" class="btn btn-warning btn-xs"><i class="fa fa-refresh"></i></button></a>
														<?php }?>
														<?php if($submenu=='verification'&&$dt->status_approve=='0')echo '&nbsp;';else{?>
															<a title="Show" href='{{$tujuan}}{{$dt->id}}'<?php if(isset($id_employee)&&($id_employee=='122'||$id_employee=='101'))echo " target='_blank'";?>><button type="button" class="btn btn-primary btn-xs" id="d{{$no}}" onclick="<?php echo "myFunction(".$no.");"?>"><i class="fa fa-edit"></i></button></a>
														<?php }?>
													<?php }else{?>
														<?php if (isset($status_lock)&&request()->user()->hasRole('hr_access')&&$status_lock==0) {?>
															<button title="Open" type="button" class="open-modal btn btn-success btn-xs" data-openid="{{$dt->id}}" data-openname="{{$dt->id_overtime}}"><i class="fa fa-refresh"></i></button>
														<?php }else{?>
															<button class="btn btn-default btn-xs" onclick="alert('SPL ini sudah lebih dari 24 Jam sejak dibuat {{$dt->created_at}}');"><i class="fa fa-info-circle" title="Created_at {{$dt->created_at}}"></i></button>
														<?php }?>
													<?php }?>
													<a title="Print" href='/Admin/Overtime/Preview/{{$dt->id}}' target="_blank"><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></button></a>
												</div>
											</td>
											<td>{{$dt->id_overtime}}</td>
											<td>{{$dt->ot_date}}</td>
											<td>{{$dt->dept_name}}</td>
											<td>
												<?php 
													if($dt->status_diperintah==1)echo "<i class='fa fa-check-square-o'></i>";
													elseif($dt->status_diperintah==1&&$submenu=='verification')echo "<a href='".$tujuan.$dt->id."'><i class='fa fa-square-o'></i></a>";
													else echo "<i class='fa fa-square-o' title='".$dt->nm1."'></i>";
												?>
											</td>
											<td>
												<?php 
													if($dt->disetujui>0){
														//echo $dt->employee_name;
														if($dt->status_disetujui==1)echo "<i class='fa fa-check-square-o'></i>";
														elseif($dt->status_disetujui==1&&$submenu=='verification')echo "<a href='".$tujuan.$dt->id."'><i class='fa fa-square-o'></i></a>";
														else echo "<i class='fa fa-square-o' title='".$dt->nm2."'></i>";
													}
												?>
											</td>
											<td>
												<?php
													if($dt->diketahui>0){
														if($dt->status_diketahui==1)echo "<i class='fa fa-check-square-o'></i>";
														elseif($dt->status_diketahui==1&&$submenu=='verification')echo "<a href='".$tujuan.$dt->id."'><i class='fa fa-square-o'></i></a>";
														else echo "<i class='fa fa-square-o' title='".$dt->nm3."'></i>";
													}
												?>
											</td>
											<td>
												<?php 
													if($dt->status_dicatat==1)echo "<i class='fa fa-check-square-o'></i>";
													elseif($dt->status_dicatat==1&&$submenu=='verification')echo "<a href='".$tujuan.$dt->id."'><i class='fa fa-square-o'></i></a>";
													else echo "<i class='fa fa-square-o'></i>";
												?>
											</td>
											<td>
												<?php 
													if($dt->status_approve==1)echo "<i class='fa fa-check-square-o'></i>";
													elseif($dt->status_approve==1&&$submenu=='verification')echo "<a href='".$tujuan.$dt->id."'><i class='fa fa-square-o'></i></a>";
													else {
														//$host= mysqli_connect("192.168.1.4","ems","123456","db_ems");
														//$idot=$dt->id;
														//$qry=mysqli_query($host,"select * from tb_overtime_details where id_ot='$idot' and sign_after='0' and status<90")or die(mysqli_error($host));
														//$qtynull=mysqli_num_rows($qry);
														if($dt->status_dicatat==0)echo "<i class='fa fa-square-o' style='color:black;'></i>";
														//else if($qtynull>0)echo "<i class='fa fa-square-o' style='color:red;'></i>";
														else echo "<i class='fa fa-square-o' style='color:blue;'></i>";
													}
	
												?>
											</td>
											<td>
												<?php 
													if($dt->status_paid==1)echo "<i class='fa fa-check-square-o'></i>";
													elseif($submenu=='verification')echo "<a href='".$tujuan.$dt->id."'><i class='fa fa-square-o'></i></a>";
													else echo "<i class='fa fa-square-o'></i>";
												?>
												<div class="pull-right">
													<?php if($submenu=='verification'){?>
														<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="{{$dt->id}}" data-delname="{{$dt->id_overtime}}"><i class="fa fa-trash"></i></button>
													<?php }?>
												</div>
											</td>
										</tr>
									@endforeach
									<tbody>
								</table>
							</div>
							<?php if($submenu=='realisation'){?>
								<div id="tabel3">
									<table id="table3" class="table table-striped" border="0">
										<thead>
											<tr>
												<th style="width:60px;">NO</th>
												<th style="width:90px;">NO.SPL</th>
												<th style="width:90px;">OT.DATE</th>
												<th>DEPARTMENT</th>
												<th style="width:70px;background:#DDD;">ORDER</th>
												<th style="width:70px;background:#DDD;">APPROVED</th>
												<th style="width:70px;background:#DDD;">SEEN</th>
												<th style="width:70px;background:#DDD;">RECORDED</th>
												<th style="width:70px;background:#0F0;">APPROVE</th>
												<th style="width:70px;background:#0F0;">PAID</th>
											</tr>
										</thead>
										<tbody>
										<?php $no=0;?>
										@foreach($tb_overtime_actual as $dt)
											<?php 
												if($submenu=='approval')$tujuan="/Admin/Overtime/Approval/";
												if($submenu=='realisation')$tujuan="/Admin/Overtime/Dept/";
												if($submenu=='verification')$tujuan="/Admin/Overtime/Verification/";
											?>
											<tr <?php if($dt->autoCancel==1)echo "style='background:#f08080;';"?>>
												<td>
													<?php $no++;echo $no;?>
													<div class="pull-right">
														<?php if($dt->autoCancel==0){?>
															<?php if($submenu=="realisation"&&$dt->status_diperintah=='0'){?>
																<a title="Show" href='/Admin/Overtime/Draft/<?php echo $dt->id;?>'><button type="button" class="btn btn-warning btn-xs"><i class="fa fa-refresh"></i></button></a>
															<?php }?>
															<a title="Show" href='{{$tujuan}}{{$dt->id}}'><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i></button></a>
														<?php }?>
														<a title="Print" href='/Admin/Overtime/Preview/{{$dt->id}}' target="_blank"><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></button></a>
													</div>
												</td>
												<td>{{$dt->id_overtime}}</td>
												<td>{{$dt->ot_date}}</td>
												<td>{{$dt->dept_name}}</td>
												<td>
													<?php 
														if($dt->status_diperintah==1)echo "<i class='fa fa-check-square-o'></i>";
														else echo "<i class='fa fa-square-o'></i>";
													?>
												</td>
												<td>
													<?php 
														if($dt->disetujui>0){
															if($dt->status_disetujui==1)echo "<i class='fa fa-check-square-o'></i>";
															else echo "<i class='fa fa-square-o'></i>";
														}
													?>
												</td>
												<td>
													<?php 
														if($dt->diketahui>0){
															if($dt->status_diketahui==1)echo "<i class='fa fa-check-square-o'></i>";
															else echo "<i class='fa fa-square-o'></i>";
														}
													?>
												</td>
												<td>
													<?php 
														if($dt->status_dicatat==1)echo "<i class='fa fa-check-square-o'></i>";
														else echo "<i class='fa fa-square-o'></i>";
													?>
												</td>
												<td>
													<?php 
														if($dt->status_approve==1)echo "<i class='fa fa-check-square-o'></i>";
														else echo "<i class='fa fa-square-o'></i>";
													?>
												</td>
												<td>
													<?php 
														if($dt->status_paid==1)echo "<i class='fa fa-check-square-o'></i>";
														else echo "<i class='fa fa-square-o'></i>";
													?>
												</td>
											</tr>
										@endforeach
										<tbody>
									</table>
								</div>
							<?php }?>
							<?php if($submenu=='verification'||$submenu=='verification2'){?>
								<div id="tabel5">
									<table id="table5" class="table table-striped" border="0">
										<thead>
											<tr>
												<th style="width:60px;">NO</th>
												<th style="width:90px;">NO.SPL</th>
												<th style="width:90px;">OT.DATE</th>
												<th>DEPARTMENT</th>
												<th style="width:70px;background:#DDD;">ORDER</th>
												<th style="width:70px;background:#DDD;">APPROVED</th>
												<th style="width:70px;background:#DDD;">SEEN</th>
												<th style="width:70px;background:#DDD;">RECORDED</th>
												<th style="width:70px;background:#0F0;">APPROVE</th>
												<th style="width:70px;background:#0F0;">PAID</th>
											</tr>
										</thead>
										<tbody>
										<?php $no=0;?>
										@foreach($tb_overtime_actual as $dt)
											<?php 
												if($submenu=='approval')$tujuan="/Admin/Overtime/Approval/";
												if($submenu=='realisation')$tujuan="/Admin/Overtime/Dept/";
												if($submenu=='verification2')$tujuan="/Admin/Overtime/Verification/";
											?>
											<tr <?php if($dt->autoCancel==1)echo "style='background:#f08080;';";elseif($dt->is_over==1)echo "style='background:#fece02;';";?>>
												<td>
													<?php $no++;echo $no;?>
													<div class="pull-right">
														<?php if($dt->autoCancel==0){?>
															<?php if($submenu=="realisation"&&$dt->status_diperintah=='0'){?>
																<a title="Show" href='/Admin/Overtime/Draft/<?php echo $dt->id;?>'><button type="button" class="btn btn-warning btn-xs"><i class="fa fa-refresh"></i></button></a>
															<?php }?>
															<?php if($submenu=='verification2'&&$dt->status_approve=='0')echo '&nbsp;';else{?>
																<a title="Show" href='{{$tujuan}}{{$dt->id}}'<?php if(isset($id_employee)&&($id_employee=='122'||$id_employee=='101'))echo " target='_blank'";?>><button type="button" class="btn btn-primary btn-xs" id="d{{$no}}" onclick="<?php echo "myFunction(".$no.");"?>"><i class="fa fa-edit"></i></button></a>
															<?php }?>
														<?php }else{?>
															<?php if (isset($status_lock)&&request()->user()->hasRole('hr_access')&&$status_lock==0) {?>
																<button title="Open" type="button" class="open-modal btn btn-success btn-xs" data-openid="{{$dt->id}}" data-openname="{{$dt->id_overtime}}"><i class="fa fa-refresh"></i></button>
															<?php }else{?>
																<button class="btn btn-default btn-xs" onclick="alert('SPL ini sudah lebih dari 24 Jam sejak dibuat {{$dt->created_at}}');"><i class="fa fa-info-circle" title="Created_at {{$dt->created_at}}"></i></button>
															<?php }?>
														<?php }?>
														<a title="Print" href='/Admin/Overtime/Preview/{{$dt->id}}' target="_blank"><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></button></a>
													</div>
												</td>
												<td>{{$dt->id_overtime}}</td>
												<td>{{$dt->ot_date}}</td>
												<td>{{$dt->dept_name}}</td>
												<td>
													<?php 
														if($dt->status_diperintah==1)echo "<i class='fa fa-check-square-o'></i>";
														elseif($dt->status_diperintah==1&&$submenu=='verification')echo "<a href='".$tujuan.$dt->id."'><i class='fa fa-square-o'></i></a>";
														else echo "<i class='fa fa-square-o' title='".$dt->nm1."'></i>";
													?>
												</td>
												<td>
													<?php 
														if($dt->disetujui>0){
															//echo $dt->employee_name;
															if($dt->status_disetujui==1)echo "<i class='fa fa-check-square-o'></i>";
															elseif($dt->status_disetujui==1&&$submenu=='verification')echo "<a href='".$tujuan.$dt->id."'><i class='fa fa-square-o'></i></a>";
															else echo "<i class='fa fa-square-o'></i>";
														}
													?>
												</td>
												<td>
													<?php
														if($dt->diketahui>0){
															if($dt->status_diketahui==1)echo "<i class='fa fa-check-square-o'></i>";
															elseif($dt->status_diketahui==1&&$submenu=='verification')echo "<a href='".$tujuan.$dt->id."'><i class='fa fa-square-o'></i></a>";
															else echo "<i class='fa fa-square-o'></i>";
														}
													?>
												</td>
												<td>
													<?php 
														if($dt->status_dicatat==1)echo "<i class='fa fa-check-square-o'></i>";
														elseif($dt->status_dicatat==1&&$submenu=='verification')echo "<a href='".$tujuan.$dt->id."'><i class='fa fa-square-o'></i></a>";
														else echo "<i class='fa fa-square-o'></i>";
													?>
												</td>
												<td>
													<?php 
														if($dt->status_approve==1)echo "<i class='fa fa-check-square-o'></i>";
														elseif($dt->status_approve==1&&$submenu=='verification')echo "<a href='".$tujuan.$dt->id."'><i class='fa fa-square-o'></i></a>";
														else {
															$host= mysqli_connect("192.168.1.4","ems","123456","db_ems");
															$idot=$dt->id;
															$qry=mysqli_query($host,"select * from tb_overtime_details where id_ot='$idot' and sign_after='0' and status<90")or die(mysqli_error($host));
															$qtynull=mysqli_num_rows($qry);
															if($dt->status_dicatat==0)echo "<i class='fa fa-square-o' style='color:black;'></i>";
															else if($qtynull>0)echo "<i class='fa fa-square-o' style='color:red;'></i>";
															else echo "<i class='fa fa-square-o' style='color:blue;'></i>";
														}
		
													?>
												</td>
												<td>
													<?php 
														if($dt->status_paid==1)echo "<i class='fa fa-check-square-o'></i>";
														elseif($submenu=='verification')echo "<a href='".$tujuan.$dt->id."'><i class='fa fa-square-o'></i></a>";
														else echo "<i class='fa fa-square-o'></i>";
													?>
													<div class="pull-right">
														<?php if($submenu=='verification'){?>
															<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="{{$dt->id}}" data-delname="{{$dt->id_overtime}}"><i class="fa fa-trash"></i></button>
														<?php }?>
													</div>
												</td>
											</tr>
										@endforeach
										<tbody>
									</table>
								</div>
							<?php }?>
							<?php 
							$aktual=1;
							if($aktual==1){?>
							<div id="tabel4">
								<div class="row" style="padding-bottom:20px;">
									<div class="col-lg-2 col-md-3 col-xs-12">
										<label>Periode</label>
										<input type="month" class="form-control" id="periode" name="periode" value="{{$periode}}">
										<input type="hidden" class="form-control" id="cabang" value="{{$cabang}}">
									</div>
								</div>
								<table id="table4" class="table table-striped" border="0">
									<thead>
										<tr>
											<th style="width:50px;">NO</th>
											<th style="width:90px;">NO.SPL</th>
											<th style="width:90px;">OT.DATE</th>
											<th>DEPARTMENT</th>
											<th style="width:70px;background:#DDD;">ORDER</th>
											<th style="width:70px;background:#DDD;">APPROVED</th>
											<th style="width:70px;background:#DDD;">SEEN</th>
											<th style="width:70px;background:#DDD;">RECORDED</th>
											<th style="width:70px;background:#0F0;">APPROVE</th>
											<th style="width:70px;background:#0F0;">PAID</th>
										</tr>
									</thead>
									<tbody>
									<?php $no=0;?>
									@foreach($tb_overtime as $dt)
										<?php 
											if($submenu=='approval')$tujuan="/Admin/Overtime/Approval/";
											if($submenu=='realisation')$tujuan="/Admin/Overtime/Dept/";
											if($submenu=='verification')$tujuan="/Admin/Overtime/Verification/";
										?>
										<tr>
											<td>
												<?php $no++;echo $no;?>
												<div class="pull-right">
													<?php if($submenu=="realisation"&&$dt->status_diperintah=='0'){?>
														<a title="Show" href='/Admin/Overtime/Draft/<?php echo $dt->id;?>'><button type="button" class="btn btn-warning btn-xs"><i class="fa fa-refresh"></i></button></a>
													<?php }?>
													<?php if($submenu=='verification' || $submenu=='verification2'){?>
														<a title="Review" href='/Admin/Overtime/Approval/Review/<?php echo $dt->id;?>/paid'><button type="button" class="btn btn-warning btn-xs"><i class="fa fa-refresh"></i></button></a>
													<?php }?>
													<a title="Print" href='/Admin/Overtime/Preview/{{$dt->id}}' target="_blank"><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></button></a>
												</div>
											</td>
											<td>{{$dt->id_overtime}}</td>
											<td>{{$dt->ot_date}}</td>
											<td>{{$dt->dept_name}}</td>
											<td>
												<?php 
													if($dt->status_diperintah==1)echo "<i class='fa fa-check-square-o'></i>";
													else echo "<i class='fa fa-square-o'></i>";
												?>
											</td>
											<td>
												<?php 
													if($dt->disetujui>0){
														if($dt->status_disetujui==1)echo "<i class='fa fa-check-square-o'></i>";
														else echo "<i class='fa fa-square-o'></i>";
													}
												?>
											</td>
											<td>
												<?php 
													if($dt->diketahui>0){
														if($dt->status_diketahui==1)echo "<i class='fa fa-check-square-o'></i>";
														else echo "<i class='fa fa-square-o'></i>";
													}
												?>
											</td>
											<td>
												<?php 
													if($dt->status_dicatat==1)echo "<i class='fa fa-check-square-o'></i>";
													else echo "<i class='fa fa-square-o'></i>";
												?>
											</td>
											<td>
												<?php 
													if($dt->status_approve==1)echo "<i class='fa fa-check-square-o'></i>";
													else echo "<i class='fa fa-square-o'></i>";
												?>
											</td>
											<td>
												<?php 
													if($dt->status_paid==1)echo "<i class='fa fa-check-square-o'></i>";
													else echo "<i class='fa fa-square-o'></i>";
												?>
												<div class="pull-right">
													<?php if($submenu=='verification'){?>
														<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="{{$dt->id}}" data-delname="{{$dt->id_overtime}}"><i class="fa fa-trash"></i></button>
													<?php }?>
												</div>
											</td>
										</tr>
									@endforeach
									<tbody>
								</table>
							</div>
							<?php }?>
						</div>
						<!-- /.box-body -->
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
	<div class="modal fade" id="modal-sales">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<form action="/Admin/Overtime/Sales" method="post">
				<input type="hidden" id="periodesales" name="periodesales">

				{{ csrf_field() }}
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Update Sales Ammount</h4>
					</div>
					<div class="modal-body">
						<div class="form-group come">
							<label>Sales Ammount</label>
							<input type="number" name="salesammount" id="salesammount" class="form-control">
						</div>

					</div>
					<div class="modal-footer" style="text-align:left;padding:20px;">
						<input type="submit" class="btn btn-primary confirmafter" value="Update">
						<button type="button" class="btn btn-default pull-right cancelafter" data-dismiss="modal">Cancel</button>
					</div>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-lock">
		<div class="modal-dialog box box-primary" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Confirmation</h4>
				</div>
				<div class="modal-body">
					Change Lock Status ?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary pull-left lock" data-dismiss="modal">Yes</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-open">
		<div class="modal-dialog box box-success" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Delete Confirmation</h4>
				</div>
				<div class="modal-body">
					Open Approval Access <b id="openname"></b> ?
					<input type="hidden" id="openid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success pull-left open" data-dismiss="modal">Continue</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-refresh">
		<div class="modal-dialog box box-primary" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Confirmation</h4>
				</div>
				<div class="modal-body">
					Reopen Approval Status ?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary pull-left refresh" data-dismiss="modal">Reopen</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>


    @if ($message = Session::get('success'))
		<div class="alert alert-info alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-info"></i> Success Alert</h4>
			{{$message}}
		</div>
    @endif

@endsection
@section('Scripts')
	<!-- Grafik -->
	<?php 
		if($grafik==1){

	?>
	<script src="{{ asset('/public/assets/js/Chart.min.js') }}"></script>
	<script src="{{ asset('/public/assets/js/utils.js') }}"></script>
	<script>
		var chartData = {
			labels: [
				<?php if(isset($tb_sumot)){
					foreach($tb_sumot as $dt){
						$kolom=date('M-Y',strtotime($dt->periode));
						echo "'".$kolom."',";
					}
				}?>
			],
			datasets: [{
				type: 'line',
				label: '1% from Sales',
				borderColor: '#000',
				borderWidth: 2,
				fill: false,
				data: [
					<?php 
						if(isset($tb_sumot)){
							foreach($tb_sumot as $dt){
								echo "'".$dt->target_persentase."',";
							}
						}
					?>
				],
				yAxisID: 'y-axis-1',
				
			}, {
				type: 'bar',
				label: 'Overtime Amount',
				backgroundColor: '#f39c12',
				data: [
					<?php if(isset($tb_sumot)){
						foreach($tb_sumot as $dt){
							echo "'".$dt->aktual_persentase."',";
						}
					}?>
				],
				yAxisID: 'y-axis-1',
				//borderColor: 'white',
				//borderWidth: 2
			}]

		};
		window.onload = function() {
			var ctx = document.getElementById('canvas').getContext('2d');
			window.myMixedChart = new Chart(ctx, {
				type: 'bar',
				data: chartData,
				options: {
					responsive: true,
					title: {
						display: true,
						text: ''
					},
					tooltips: {
						mode: 'index',
						intersect: true
					},
					responsive: true,
					scales: {
						xAxes: [{
							stacked: true,
							display: true,
							scaleLabel: {
								display: true,
								labelString: 'Department'
							}
						}],
						yAxes: [{
							type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
							stacked: true,
							display: true,
							
							scaleLabel: {
								display: true,
								labelString: 'percent (%)'
							},
							position: 'left',
							id: 'y-axis-1',
							<?php if(isset($tb_sumot)){?>
								ticks: {
									min: 0,
									callback: function (value) {
									return value.toLocaleString('de-DE', {style:'decimal'});}
								}
							<?php }?>
						}],
					}


				}
			});
			
		};

	</script>
	<?php }?>
	<!--  on Load  -->
	<script>
		$( document ).ready(function() {
			$("#tabel3").hide();
			$("#tabel4").hide();
			<?php if($submenu=='verification2'){?>
				$("#tabel5").show();
				$("#tabel2").hide();
			<?php }else{?>
				$("#tabel5").hide();
				$("#tabel2").show();
			<?php }?>
			$("#judul").text('New SPL');
			$(document).on('click', '.table2', function() {
				$("#tabel2").show(1000);
				$("#tabel5").hide(50);
				$("#tabel3").hide(50);
				$("#tabel4").hide(50);
				$("#judul").text('New SPL');
			});
			$(document).on('click', '.table3', function() {
				$("#tabel3").show(1000);
				$("#tabel5").hide(50);
				$("#tabel2").hide(50);
				$("#tabel4").hide(50);
				$("#judul").text('SPL Under Proccess');
			});
			$(document).on('click', '.table4', function() {
				$("#tabel4").show(1000);
				$("#tabel5").hide(50);
				$("#tabel3").hide(50);
				$("#tabel2").hide(50);
				$("#judul").text('Completed SPL');
			});
			$(document).on('click', '.table5', function() {
				$("#tabel5").show(1000);
				$("#tabel4").hide(50);
				$("#tabel3").hide(50);
				$("#tabel2").hide(50);
				$("#judul").text('Completed SPL');
			});
		});
	</script>
	<!-- page script Tabel-->
	<script>
		$(function () {
			$('#table2').DataTable({
				'paging'      : false,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : false,
			"pageLength"  : 10,
			'autoWidth'   : false,
			})
		})
		$(function () {
			$('#table3').DataTable({
			'paging'      : false,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : false,
			"pageLength"  : 10,
			'autoWidth'   : false,
			})
		})
		$(function () {
			$('#table4').DataTable({
			'paging'      : true,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : false,
			})
		})	
		$(function () {
			$('#table5').DataTable({
			'paging'      : true,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 100,
			'autoWidth'   : false,
			})
		})	
	</script>
	<!-- Durasi Alert --->
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>

	<script>
		$('body').on("change","#periode",function(){
			var periode=document.getElementById('periode').value;
			var cabang=document.getElementById('cabang').value;
			window.location.href="/Admin/Overtime/"+cabang+"/"+periode;
		});
	</script>
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.delete-modal', function() {
			$('#delid').val($(this).data('delid'));
			$('#delname').text($(this).data('delname'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#delid').val();
			window.location.href='/Admin/Overtime/Remove/'+x;
		});
	</script>
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.sales-modal', function() {
			$('#periodesales').val($(this).data('periodesales'));
			$('#salesammount').val($(this).data('salesammount'));
			$('#modal-sales').modal('show');
		});
	</script>
	<script>
		function myFunction(no) {
			document.getElementById('d'+no).style.background = '#CCCCCC';
		}		
	</script>
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.lock-modal', function() {
			$('#modal-lock').modal('show');
		});
		$('.modal-footer').on('click', '.lock', function() {
			window.location.href='/Admin/Overtime/lock';
		});
	</script>
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.open-modal', function() {
			$('#openid').val($(this).data('openid'));
			$('#openname').text($(this).data('openname'));
			$('#modal-open').modal('show');
		});
		$('.modal-footer').on('click', '.open', function() {
			var x=$('#openid').val();
			window.location.href='/Admin/Overtime/Open/'+x;
		});
	</script>
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.refresh-modal', function() {
			$('#modal-refresh').modal('show');
		});
		$('.modal-footer').on('click', '.refresh', function() {
			window.location.href='/Admin/Overtime/Opens';
		});
	</script>
@endsection
