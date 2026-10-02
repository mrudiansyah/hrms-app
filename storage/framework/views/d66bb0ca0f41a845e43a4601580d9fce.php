
<?php $__env->startSection('Contents'); ?>
	<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
   <!-- Contents -->
   	<style>
		#tablesx th {
		border-top: 1px solid #999;
		border-bottom: 1px solid #999;
		background-color: #2F4F4F;
		color: white;
		}	
        .table1 tr:hover {
		  cursor:pointer;
        }
		#tables th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#tables tbody tr:hover{
			cursor:pointer;
		}
		#table2 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table2 tbody tr:hover{
			cursor:pointer;
		}
		#table3 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table3 tbody tr:hover{
			cursor:pointer;
		}
		#table4 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table4 tbody tr:hover{
			cursor:default;
		}
    </style>
	<?php
		date_default_timezone_set("Asia/Bangkok");
		$Today=date('Y-m-d');
		$AWeek=date('Y-m-d',strtotime('+ 14 days',strtotime($Today)));
		$AMonth=date('Y-m-d',strtotime('+ 1 Months',strtotime($Today)));
	?>

	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				KSK List
				<small>konfirmasi status karyawan</small>
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
						<i class="fa fa-user"></i>
						<h3 class="box-title" style="padding-bottom:25px;"><?php echo e($Judul); ?></h3>
						<div class="box-tools pull-right">
							<a href='/Employees/KSK/<?php echo e($periode); ?>'><button type="button" class="btn btn-default btn-xs"><i class="fa fa-home"></i> &nbsp;HOME</button></a>
							<a href='/Employee/KSK/Print/<?php echo e($id_ksk); ?>' target="_blank"><button type="button" class="btn btn-info btn-xs"><i class="fa fa-print"></i> &nbsp;PRINT</button></a>
							<a href='/Employee/KSK/Confirm/<?php echo e($id_ksk); ?>' id="confirmksk"><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-square-check"></i> &nbsp;CONFIRM</button></a>
						</div>
					</div>
					<div class="box-body" style="overflow-x:scroll;">
						<table id="table2" class="table table-hover tabel2">
							<thead>
								<tr>
									<th>NO</th>
									<th>NIK</th>
									<th>NAME</th>
									<th>JOIN DATE</th>
									<th>DEPT</th>
									<th>START</th>
									<th>END</th>
									<th>DURATION</th>
									<th>APPROVAL</th>
									<!--
									<th>LEGALIZE</th>
									-->
									<th>JUDGEMENTs</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;$status_confirm=0;?>
								<?php $__currentLoopData = $tb_ksk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php 
									$bulan=$dt->months%12;$tahun=($dt->months-$bulan)/12;
									$nik= substr($dt->NIK,0,3);
								?>
								<tr>
									<td>
										<?php $no++;echo $no;?>
									</td>
									<td><?php echo e($dt->NIK); ?></td>
									<td><?php echo e($dt->employee_name); ?></td>
									<td><?php echo e($dt->join_date); ?></td>
									<td><?php echo e($dt->dept_code); ?></td>
									<td><?php echo e($dt->first_contract); ?></td>
									<td><?php echo e($dt->finish_contract); ?></td>
									<td><?php if($tahun>0)echo $tahun.' Tahun ';if($bulan>0)echo $bulan.' Bulan';?></td>
									<td>
										<?php
										$id_ksk_detail=$dt->id;
										$posisi=0;
										if($id_employee==$dt->approval1)$posisi=1;
										elseif($id_employee==$dt->approval2)$posisi=2;
										elseif($id_employee==$dt->approval3)$posisi=3;
										elseif($id_employee==$dt->approval4)$posisi=4;
										elseif($id_employee==$dt->approval5)$posisi=5;
										elseif($id_employee==$dt->approval6)$posisi=6;
										$approval1=$dt->approval1;
										$approval2=$dt->approval2;
										$approval3=$dt->approval3;
										$approval4=$dt->approval4;
										$approval5=$dt->approval5;
										$approval6=$dt->approval6;
										if($approval1!='')$approve1="<span class='badge bg-gray' title='".$dt->approvalname1."'>1</span>";else $approve1="";
										if($approval2!='')$approve2="<span class='badge bg-gray' title='".$dt->approvalname2."'>2</span>";else $approve2="";
										if($approval3!='')$approve3="<span class='badge bg-gray' title='".$dt->approvalname3."'>3</span>";else $approve3="";
										if($approval4!='')$approve4="<span class='badge bg-gray' title='".$dt->approvalname4."'>4</span>";else $approve4="";
										if($approval5!='')$approve5="<span class='badge bg-gray' title='".$dt->approvalname5."'>5</span>";else $approve5="";
										if($approval6!='')$approve6="<span class='badge bg-gray' title='".$dt->approvalname6."'>6</span>";else $approve6="";
										$status=0;
										$judge1='';
										$judge2='';
										$judge3='';
										$judge4='';
										$judge5='';
										$judge6='';
										$detail_judges=isset($ksk_judges[$id_ksk_detail])?$ksk_judges[$id_ksk_detail]:[];
										$judge1=isset($detail_judges[$approval1])?$detail_judges[$approval1]:'';
										$judge2=isset($detail_judges[$approval2])?$detail_judges[$approval2]:'';
										$judge3=isset($detail_judges[$approval3])?$detail_judges[$approval3]:'';
										$judge4=isset($detail_judges[$approval4])?$detail_judges[$approval4]:'';
										$judge5=isset($detail_judges[$approval5])?$detail_judges[$approval5]:'';
										$judge6=isset($detail_judges[$approval6])?$detail_judges[$approval6]:'';
										if($judge1=='PERMANENCY')$approve1="<span class='badge bg-green'>1</span>";
										elseif($judge1=='EXTEND')$approve1="<span class='badge bg-yellow'>1</span>";
										elseif($judge1=='NOT EXTEND')$approve1="<span class='badge bg-red'>1</span>";
										elseif($judge1=='PKWT')$approve1="<span class='badge bg-aqua'>1</span>";
										if($judge2=='PERMANENCY')$approve2="<span class='badge bg-green'>2</span>";
										elseif($judge2=='EXTEND')$approve2="<span class='badge bg-yellow'>2</span>";
										elseif($judge2=='NOT EXTEND')$approve2="<span class='badge bg-red'>2</span>";
										elseif($judge2=='PKWT')$approve2="<span class='badge bg-aqua'>2</span>";
										if($judge3=='PERMANENCY')$approve3="<span class='badge bg-green'>3</span>";
										elseif($judge3=='EXTEND')$approve3="<span class='badge bg-yellow'>3</span>";
										elseif($judge3=='NOT EXTEND')$approve3="<span class='badge bg-red'>3</span>";
										elseif($judge3=='PKWT')$approve3="<span class='badge bg-aqua'>3</span>";
										if($judge4=='PERMANENCY')$approve4="<span class='badge bg-green'>4</span>";
										elseif($judge4=='EXTEND')$approve4="<span class='badge bg-yellow'>4</span>";
										elseif($judge4=='NOT EXTEND')$approve4="<span class='badge bg-red'>4</span>";
										elseif($judge4=='PKWT')$approve4="<span class='badge bg-aqua'>4</span>";
										if($judge5=='PERMANENCY')$approve5="<span class='badge bg-green'>5</span>";
										elseif($judge5=='EXTEND')$approve5="<span class='badge bg-yellow'>5</span>";
										elseif($judge5=='NOT EXTEND')$approve5="<span class='badge bg-red'>5</span>";
										elseif($judge5=='PKWT')$approve5="<span class='badge bg-aqua'>5</span>";
										if($judge6=='PERMANENCY')$approve6="<span class='badge bg-green'>6</span>";
										elseif($judge6=='EXTEND')$approve6="<span class='badge bg-yellow'>6</span>";
										elseif($judge6=='NOT EXTEND')$approve6="<span class='badge bg-red'>6</span>";
										elseif($judge6=='PKWT')$approve6="<span class='badge bg-aqua'>6</span>";
										if($approval1==$id_employee){
											$status=1;
										}else if($approval2==$id_employee&&$judge1!=''&&$dt->approval1_status=='1'){
											$status=1;
										}else if($approval3==$id_employee&&$judge2!=''&&$dt->approval2_status=='1'){
											$status=1;
										}else if($approval4==$id_employee&&$judge3!=''&&$dt->approval3_status=='1'){
											$status=1;
										}else if($approval5==$id_employee&&$judge4!=''&&$dt->approval4_status=='1'){
											$status=1;
										}else if($approval6==$id_employee&&$judge5!=''&&$dt->approval5_status=='1'){
											$status=1;
										}
										//Disabled
										//if($dt->visible_status==0&&$posisi<$dt->hide_by){
											if($approval1!=''&&$posisi<1&&$dt->visible_status==0&&$posisi<$dt->hide_by)$approve1="<span class='badge' style='background:#FFF;' title='".$dt->approvalname1."'>1</span>";
											if($approval2!=''&&$posisi<2&&$dt->visible_status==0&&$posisi<$dt->hide_by)$approve2="<span class='badge' style='background:#FFF;' title='".$dt->approvalname2."'>2</span>";
											if($approval3!=''&&$posisi<3&&$dt->visible_status==0&&$posisi<$dt->hide_by)$approve3="<span class='badge' style='background:#FFF;' title='".$dt->approvalname3."'>3</span>";
											if($approval4!=''&&$posisi<4&&$dt->visible_status==0&&$posisi<$dt->hide_by)$approve4="<span class='badge' style='background:#FFF;' title='".$dt->approvalname4."'>4</span>";
											if($approval5!=''&&$posisi<5&&$dt->visible_status==0&&$posisi<$dt->hide_by)$approve5="<span class='badge' style='background:#FFF;' title='".$dt->approvalname5."'>5</span>";
											if($approval6!=''&&$posisi<6&&$dt->visible_status==0&&$posisi<$dt->hide_by)$approve6="<span class='badge' style='background:#FFF;' title='".$dt->approvalname6."'>6</span>";
										//}

										echo $approve1.$approve2.$approve3.$approve4.$approve5.$approve6;
										
										if($approval1==$id_employee&&$judge1!=''){
											$status_confirm++;
										}else if($approval2==$id_employee&&$judge2!=''){
											$status_confirm++;
										}else if($approval3==$id_employee&&$judge3!=''){
											$status_confirm++;
										}else if($approval4==$id_employee&&$judge4!=''){
											$status_confirm++;
										}else if($approval5==$id_employee&&$judge5!=''){
											$status_confirm++;
										}else if($approval6==$id_employee&&$judge6!=''){
											$status_confirm++;
										}
										?>
									</td>
									<!--
									<td>
										<?php
										$id_ksk_detail=$dt->id;
										$legalize1=$dt->legalize1;
										$legalize2=$dt->legalize2;
										$legalize3=$dt->legalize3;
										$legalize4=$dt->legalize4;
										if($legalize1!='')$legaliz1="<span class='badge bg-gray' title='".$dt->legalizename1."'>1</span>";else $legaliz1="";
										if($legalize2!='')$legaliz2="<span class='badge bg-gray' title='".$dt->legalizename2."'>2</span>";else $legaliz2="";
										if($legalize3!='')$legaliz3="<span class='badge bg-gray' title='".$dt->legalizename3."'>3</span>";else $legaliz3="";
										if($legalize4!='')$legaliz4="<span class='badge bg-gray' title='".$dt->legalizename4."'>4</span>";else $legaliz4="";
										$status2=0;
										$judge7='';
										$judge8='';
										$judge9='';
										$judge10='';
										$judge7=isset($detail_judges[$legalize1])?$detail_judges[$legalize1]:'';
										$judge8=isset($detail_judges[$legalize2])?$detail_judges[$legalize2]:'';
										$judge9=isset($detail_judges[$legalize3])?$detail_judges[$legalize3]:'';
										$judge10=isset($detail_judges[$legalize4])?$detail_judges[$legalize4]:'';
										if($legalize1==$id_employee&&$dt->approval_status==1){
											$status2=1;
										}else if($legalize2==$id_employee&&$judge7!=''){
											$status2=1;
										}else if($legalize3==$id_employee&&$judge8!=''){
											$status2=1;
										}else if($legalize4==$id_employee&&$judge9!=''){
											$status2=1;
										}
										//Disabled
										if($dt->visible_status==0){
											if($legalize1!='')$legaliz1="<span class='badge' style='background:#FFF;' title='".$dt->legalizename1."'>1</span>";else $legaliz1="";
											if($legalize2!='')$legaliz2="<span class='badge' style='background:#FFF;' title='".$dt->legalizename2."'>2</span>";else $legaliz2="";
											if($legalize3!='')$legaliz3="<span class='badge' style='background:#FFF;' title='".$dt->legalizename3."'>3</span>";else $legaliz3="";
											if($legalize4!='')$legaliz4="<span class='badge' style='background:#FFF;' title='".$dt->legalizename4."'>4</span>";else $legaliz4="";
										}
										echo $legaliz1.$legaliz2.$legaliz3.$legaliz4;
										
										if($legalize1==$id_employee&&$judge7!=''){
											//$status_confirm++;
										}else if($legalize2==$id_employee&&$judge8!=''){
											//$status_confirm++;
										}else if($legalize3==$id_employee&&$judge9!=''){
											//$status_confirm++;
										}else if($legalize4==$id_employee&&$judge10!=''){
											//$status_confirm++;
										}
										?>
									</td>
									-->
									<td>
										<?php echo e($dt->judge); ?>

										<?php if($dt->judge=='EXTEND'): ?>
											(<?php echo e($dt->next_contract); ?> months)
										<?php endif; ?>
										<div class="pull-right">
											<?php if($dt->visible_status==1&&$my_index>=6): ?>
												<i class="fa fa-toggle-on visible-modal" title="Display status On, Leader & Sec.Head bisa melihat putusan KSK Diatasnya" data-delid="<?php echo e($dt->id); ?>" data-delname="<?php echo e($dt->employee_name); ?>"></i>&nbsp;
											<?php endif; ?>
											<?php if($dt->visible_status==0&&$my_index>=6): ?>
												<i class="fa fa-toggle-off visible-modal" title="Display status Off, Leader & Sec.Head tidak bisa melihat putusan KSK Diatasnya" data-delid="<?php echo e($dt->id); ?>" data-delname="<?php echo e($dt->employee_name); ?>"></i>&nbsp;
											<?php endif; ?>
											<?php
												$status_update=1;
												if($legalize3==$id_employee&&$judge10!=''){
													$status_update=0;
												}else if($legalize2==$id_employee&&$judge9!=''){
													$status_update=0;
												}else if($legalize1==$id_employee&&$judge8!=''){
													$status_update=0;
												}else if($approval6==$id_employee&&$judge7!=''){
													$status_update=0;
												}else if($approval5==$id_employee&&$judge6!=''){
													$status_update=0;
												}else if($approval4==$id_employee&&$judge5!=''){
													$status_update=0;
												}else if($approval3==$id_employee&&$judge4!=''){
													$status_update=0;
												}else if($approval2==$id_employee&&$judge3!=''){
													$status_update=0;
												}else if($approval1==$id_employee&&$judge2!=''){
													$status_update=0;
												}
											?>
											<button type="button" class="btn btn-info btn-xs info-modal" data-idkskdetail='<?php echo e($dt->id); ?>'>&nbsp;<i class="fa fa-info"></i>&nbsp;</button>
											<!-- <?php echo e($status); ?> <?php echo e($status2); ?><?php echo e($status_lock); ?>

											<?php if(($status==1||$status2==1)&&$status_lock==0){?>
												<button type="button" class="btn btn-primary btn-xs update-modal" data-idkskdetail='<?php echo e($dt->id); ?>' data-statusupdate='<?php echo e($status_update); ?>' data-judge="<?php echo e($dt->judge); ?>" data-nextcontract="<?php echo e($dt->next_contract); ?>" data-reason="<?php echo e($dt->reason); ?>" data-nik="<?php echo e($nik); ?>" data-months="<?php echo e($dt->months); ?>" data-performance="<?php echo e($dt->performance); ?>"> <i class="fa fa-edit"></i>
											</button><?php }?> -->
											<button type="button" class="btn btn-primary btn-xs update-modal" data-idkskdetail='<?php echo e($dt->id); ?>' data-statusupdate='<?php echo e($status_update); ?>' data-judge="<?php echo e($dt->judge); ?>" data-nextcontract="<?php echo e($dt->next_contract); ?>" data-reason="<?php echo e($dt->reason); ?>" data-nik="<?php echo e($nik); ?>" data-months="<?php echo e($dt->months); ?>" data-performance="<?php echo e($dt->performance); ?>"> <i class="fa fa-edit"></i>
											<!-- <a href="/Employee/KSK/Print/<?php echo e($dt->id_ksk); ?>" type="button" class="btn btn-info btn-xs" target="_blank"><i class="fa  fa-print"></i></a> -->
										</div>
									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
							<tfoot>

							</tfoot>
						</table>
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
	</div>

	<div class="modal fade" id="modal-update">
		<div class="modal-dialog box box-primary" style="width:350px;">
			<div class="modal-content">
			<form action="/Employee/KSK/Status" method="post">
			<input type="hidden" id="idkskdetail" name="id_ksk_detail">
			<input type="hidden" id="statusupdate" name="statusupdate">
			<input type="hidden" id="limitmonth" name="limitmonth">
			<?php echo e(csrf_field()); ?>

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Form Recomendation</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>Action</label>
						<select name="judge" id="judge" class="form-control">
							<option value="">&nbsp;</option>
							<option value="EXTEND" id="extend">EXTEND</option>
							<option value="NOT EXTEND">NOT EXTEND</option>
							<option value="PERMANENCY" id="permanency">PERMANENCY</option>
							<option value="PKWT" id="pkwt">PKWT</option>
						</select>
					</div>
					<div class="form-group">
						<label>Recomend Next Contract (Months)</label>
						<input type="number" name="next_contract" id="nextcontract" class="form-control">
					</div>
					<div class="form-group">
						<label>Reason</label>
						<textarea name="reason" id="reason" class="form-control"></textarea>
						<small>Note: Jika menggunakan karakter berikut (;/'*<>"--) berpotensi tidak bisa di simpan. Gunakan karakter simple seperti titik dan koma</small>
					</div>
					<div class="form-group">
						<label>Performance</label>
						<select name="performance" class="form-control" id="performance">
							<option value=""></option>
							<option value="A+">A+</option>
							<option value="A">A</option>
							<option value="B+">B+</option>
							<option value="B">B</option>
							<option value="C+">C+</option>
							<option value="C">C</option>

						</select>
					</div>
				</div>
				<div class="modal-footer" style="text-align:left;">
					<input type="submit" id="submit" class="btn btn-primary" value="Submit">
					<button type="button" class="btn btn-default pull-right cancelafter" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-info">
		<div class="modal-dialog box box-info" style="width:750px;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Recomendation Info</h4>
			</div>
			<div class="modal-body">
				<table id="kskinfo" style="width:100%;"></table>
			</div>
			<div class="modal-footer" style="text-align:left;">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
			</div>
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-visible">
		<div class="modal-dialog box box-primary" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Confirmation</h4>
				</div>
				<div class="modal-body">
					Change Visiblity <b id="delname1"></b> ?
					<input type="hidden" id="delid1">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary pull-left visible" data-dismiss="modal">Yes</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>


    <?php if($message = Session::get('success')): ?>
		<div class="alert alert-info alert-dismissible" style="position:absolute;width:350px;right:220px;top:60px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-info"></i> Success Alert</h4>
			<?php echo e($message); ?>

		</div>
    <?php endif; ?>


<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
	<!-- page script Tabel-->
	<script>
		$(function () {
			$('#table2').DataTable({
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
			$('#table3').DataTable({
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
	</script>
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<!--  on Load  -->

	<script type="text/javascript">
		$(document).on('click', '.update-modal', function() {
			var reason=$(this).data('reason');
			if(reason==''){
				document.getElementById("nextcontract").disabled = true;
				document.getElementById("submit").disabled = true;
			}
			var nik=$(this).data('nik');
			var months=$(this).data('months');

			// Pastikan nik berupa string
			let nikStr = String(nik);

			if (nikStr.includes('MG')) {
				document.getElementById("permanency").disabled = true;
				document.getElementById("pkwt").disabled = true;
				
				if (nikStr === 'MGH') {
					document.getElementById("extend").disabled = true;
				} else {
					if (months > 60) {
						document.getElementById("extend").disabled = true;
					} else {
						document.getElementById("extend").disabled = false;
					}
					var limitmonth = 60 - months;
					$('#limitmonth').val(limitmonth);
				}
			} else {
				document.getElementById("permanency").disabled = false;
				document.getElementById("pkwt").disabled = true;
				
				if (months > 59) {
					document.getElementById("extend").disabled = true;
				} else {
					document.getElementById("extend").disabled = false;
				}
				var limitmonth = 60 - months;
				$('#limitmonth').val(limitmonth);
			}
			var x=$(this).data('performance');
			if(x!=''){
				document.getElementById("performance").disabled = true;
			}else{
				document.getElementById("performance").disabled = false;
			}

			$('#idkskdetail').val($(this).data('idkskdetail'));
			$('#judge').val($(this).data('judge'));
			$('#nextcontract').val($(this).data('nextcontract'));
			$('#reason').val($(this).data('reason'));
			$('#statusupdate').val($(this).data('statusupdate'));
			$('#performance').val($(this).data('performance')).prop('selected', true);
			$('#modal-update').modal('show');
		});
		$(document).on('click', '.info-modal', function() {
			var x=$(this).data('idkskdetail');
			$.ajaxSetup({
				type:"POST",
				url: "/Status/KSK/Info",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:{idkskdetail:x},
				success: function(respond){
					$("#kskinfo").html(respond);
				}
			})
			$('#modal-info').modal('show');
		});
		$(document).on('change', '#judge', function() {
			$('#reason').val('');
			document.getElementById("submit").disabled = true;
			var judge=$('#judge').val();
			if(judge==''){
				document.getElementById("nextcontract").disabled = true;
			}else if(judge=='NOT EXTEND'||judge=='PERNANENCY'){
				document.getElementById("nextcontract").disabled = true;
				$('#nextcontract').val('');
			}else if(judge=='PKWT'){
				document.getElementById("nextcontract").disabled = false;
				$('#nextcontract').val(6);
			}else if(judge=='EXTEND'){
				document.getElementById("nextcontract").disabled = false;
				$('#nextcontract').val(1);
			}
		});
		$(document).on('keyup', '#nextcontract', function() {
			$('#reason').val('');
			document.getElementById("submit").disabled = true;
			var judge=$('#judge').val();
			var nextcontract=$('#nextcontract').val()*1;
			var limitmonth=$('#limitmonth').val()*1;
			if(limitmonth<nextcontract){
				alert('Maksimum Exten only '+limitmonth+' months');
				$('#limitmonth').val(limitmonth);
			}
		});
		$(document).on('keyup', '#reason', function() {
			var judge=$('#judge').val();
			var nextcontract=$('#nextcontract').val()*1;
			var limitmonth=$('#limitmonth').val()*1;
			var reason=$('#reason').val();
			if(judge==''){
				document.getElementById("submit").disabled = true;
			}else if(limitmonth<nextcontract){
				// document.getElementById("submit").disabled = true;
				document.getElementById("submit").disabled = false;
			}else{
				document.getElementById("submit").disabled = false;
			}
		});
	</script>
	<script>
		$( document ).ready(function() {
			var no="<?php echo e($no); ?>";
			var status_confirm="<?php echo e($status_confirm); ?>";
			if(no==status_confirm){
				$('#confirmksk').show();
			}else{
				$('#confirmksk').hide();
			} 
		});
	</script>
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.visible-modal', function() {
			$('#delid1').val($(this).data('delid'));
			$('#delname1').text($(this).data('delname'));
			$('#modal-visible').modal('show');
		});
		$('.modal-footer').on('click', '.visible', function() {
			var x=$('#delid1').val();
			var y="<?php echo e($posisi); ?>";
			window.location.href='/Status/KSK/Visible/'+x+'/'+y;
		});
	</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/m_employee/ksk_approval_detail.blade.php ENDPATH**/ ?>