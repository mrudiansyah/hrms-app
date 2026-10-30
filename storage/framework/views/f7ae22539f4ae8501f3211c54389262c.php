
<?php $__env->startSection('Contents'); ?>
   <!-- Contents -->
   <style>
        tr:hover {
          background-color: #DCDCDC;
		  cursor:pointer;
        }
   </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
		<h1 onclick="">
			Employees Shift
			<small>all departments</small>
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
			<div class="col-xs-6 col-sm-3 col-lg-3">
			<!-- small box -->
			<div class="small-box bg-green">
				<div class="inner">
				<h3><i id="pagis"></i><sup style="font-size: 20px">%</sup></h3>

				<p>Shift Pagi <b id="pagi"></b> Persons </p>
				</div>
				<div class="icon">
				<i class="ion ion-person"></i>
				</div>
				<a href="/MealsShifts/<?php echo e($Tgl); ?>/Pagi" class="small-box-footer"><i id='infopagi'></i></a>
			</div>
			</div>
			<div class="col-xs-6 col-sm-3 col-lg-3">
			<!-- small box -->
			<div class="small-box bg-blue">
				<div class="inner">
				<h3><i id="sores"></i><sup style="font-size: 20px">%</sup></h3>

				<p>Shift Sore <b id="sore"></b> Persons</p>
				</div>
				<div class="icon">
				<i class="ion ion-person"></i>
				</div>
				<a href="/MealsShifts/<?php echo e($Tgl); ?>/Sore" class="small-box-footer"><i id='infosore'></i></a>
			</div>
			</div>
			<div class="col-xs-6 col-sm-3 col-lg-3">
			<!-- small box -->
			<div class="small-box bg-yellow">
				<div class="inner">
				<h3><i id="malams"></i><sup style="font-size: 20px">%</sup></h3>

				<p>Shift Malam <b id="malam"></b> Persons</p>
				</div>
				<div class="icon">
				<i class="ion ion-person"></i>
				</div>
				<a href="/MealsShifts/<?php echo e($Tgl); ?>/Malam" class="small-box-footer"><i id='infomalam'></i></a>
			</div>
			</div>
			<div class="col-xs-6 col-sm-3 col-lg-3">
			<!-- small box -->
			<div class="small-box bg-red">
				<div class="inner">
				<h3><i id="liburs"></i><sup style="font-size: 20px">%</sup></h3>

				<p><b id="libur"></b> Persons</p>
				</div>
				<div class="icon">
				<i class="ion ion-person"></i>
				</div>
				<a href="/MealsShifts/<?php echo e($Tgl); ?>/Libur" class="small-box-footer">Libur</a>
			</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
			<div class="box box-primary" style="background:#FFF;">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-calender"></i>Date : </h3>
					<input type="date" id="tgl" name="tgl" value="<?php echo e($Tgl); ?>">
					<input type="hidden" id="show" value="<?php echo e($show); ?>">
					<div class="box-tools pull-right">
						<?php if($show!=0){
							if($show=='Pagi')$bg='btn-success';
							if($show=='Sore')$bg='btn-primary';
							if($show=='Malam')$bg='btn-warning';
							?>
							<a href="/MealsShift/Temp/<?php echo e($Tgl); ?>/<?php echo e($show); ?>" type="button" class="btn <?php echo e($bg); ?> btn-md"><i class="fa fa-upload"></i>&nbsp; Export Melas</a>
						<?php }?>
					</div>
				</div>

				<div class="box-body">
				<table id="table1" class="table table-bordered">
					<thead>
						<tr>
							<th>No</th>
							<th>NIK</th>
							<th>Employee Name</th>
							<th>Dept</th>
							<th>Position</th>
							<th>Group</th>
							<th>Schedule</th>
							<th>Shift</th>
							<th>Actual</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							$no=0;
							$pagi=0;
							$sore=0;
							$malam=0;
							$libur=0;
							$unset=0;
							$pagi_hadir=0;
							$sore_hadir=0;
							$malam_hadir=0;
						?>
						<?php $__currentLoopData = $tb_employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<?php 
								if($dt->id_shift>0){
									$shift="";
									$host= mysqli_connect("192.168.1.4","ems","123456","db_ems");
									$host1 = mysqli_connect("192.168.121.4:83306","cahyudin","123456","adms_db");

									date_default_timezone_set("Asia/Jakarta");
									//$Tgl=date('Y-m-d');

									$tgl1 = new DateTime($dt->start_implement);
									$tgl2 = new DateTime($Tgl);
									$diffdays = $tgl2->diff($tgl1)->days;

									$cycle=$dt->cycle;
									if($cycle==0){
										$diffcycle=0;
										$modcycle=0;
									}
									else {
										$diffcycle=Floor($diffdays/$cycle);
										$modcycle=$diffdays%$cycle;
									}
									$awal=$modcycle;
									$modcycle++;

									$group=$dt->group;
									$query="select * from tb_cycles where tb_cycles.group='$group' and days='$modcycle'";
									$qry=mysqli_query($host,$query)or die(mysqli_error($host));
									while($dt3=mysqli_fetch_array($qry)){
					
										$shift=$dt3['shift'];
										$check_in=date('H:i',strtotime($dt3['check_in']));
										$check_out=date('H:i',strtotime($dt3['check_out']));
										$advance=$dt3['advance'];
										$cross=$dt3['cross'];
					
										//Return Checkin Range
										if(isset($check_in)&&$check_in!=''){
											$cdate=date('Y-m-d',strtotime($Tgl));
											if($advance==1){
												$date = date_create($cdate);
												date_add($date, date_interval_create_from_date_string('-1 days'));
												$cdate= date_format($date, 'Y-m-d');
											}
											$ncdatein=$cdate." ".$check_in;
											//Reduce 2 Hour
											$date = date_create($ncdatein);
											date_add($date, date_interval_create_from_date_string('-2 hours'));
											$ncdateindown= date_format($date, 'Y-m-d H:i:s');
											//Increas 5 Hour
											$date = date_create($ncdatein);
											date_add($date, date_interval_create_from_date_string('5 hours'));
											$ncdateinup= date_format($date, 'Y-m-d H:i:s');
											//echo $ncdatein.' ';
										}
										//Return Checkout Range
										if(isset($check_out)&&$check_out!=''){
											$cdate=date('Y-m-d',strtotime($Tgl));
											if($cross==1){
												$date = date_create($cdate);
												date_add($date, date_interval_create_from_date_string('+1 days'));
												$cdate= date_format($date, 'Y-m-d');
											}
											$ncdateout=$cdate." ".$check_out;
											//Reduce 2 Hour
											$date = date_create($ncdateout);
											date_add($date, date_interval_create_from_date_string('-2 hours'));
											$ncdateoutdown= date_format($date, 'Y-m-d H:i:s');
											//Increas 5 Hour
											$date = date_create($ncdateout);
											date_add($date, date_interval_create_from_date_string('4 hours 30 minutes'));
											$ncdateoutup= date_format($date, 'Y-m-d H:i:s');
											//echo $ncdateout.' ';
										}
										$qry2=mysqli_query($host1,"select checkinout.checktime,checkinout.userid,userinfo.name from checkinout left join userinfo on checkinout.userid=userinfo.userid where userinfo.userid='$dt->PIN' and checkinout.checktime>='$ncdateindown' and checkinout.checktime<='$ncdateoutup' order by checkinout.checktime asc")or die(mysqli_error($host1));
										$hadir=0;
										$masuk='';
										$keluar='';
										while($dt2=mysqli_fetch_array($qry2)){
											$ctime=$dt2['checktime'];
											//Conditionl IN-OUT
											if($ctime>=$ncdateindown and $ctime<=$ncdateinup){$in_status="1";$masuk=$ctime;}
											if($ctime>=$ncdateoutdown and $ctime<=$ncdateoutup){$out_status="1";$keluar=$ctime;}
											if($in_status="1"||$out_status="1")$hadir=1;
										}

									}
									$mulai=date('Y-m-d',strtotime($awal.' days',strtotime($Tgl)));
									if($shift==0)$sekarang='Libur';
									else $sekarang=$check_in." ~ ".$check_out;
									//echo $sekarang;
									if($group==1&&$shift==1)$kondisi='Pagi';
									else if($shift==1)$kondisi="Malam";
									else if($shift==2)$kondisi="Pagi";
									else if($shift==3)$kondisi="Sore";
									else if($sekarang=='Libur')$kondisi="Libur";
									else $kondisi="Unset";
								}
								
								if($kondisi=='Malam'){$bg='bg-yellow';$malam++;}
								else if($kondisi=='Sore'){$bg='bg-blue';$sore++;}
								else if($kondisi=='Pagi'){$bg='bg-green';$pagi++;}
								else if($kondisi=='Libur'){$bg='bg-red';$libur++;}
								else if($dt->id_shift>0){$bg='bg-white';}
							?>
							<?php 
								if($hadir==1&&$kondisi=='Pagi')$pagi_hadir++;
								if($hadir==1&&$kondisi=='Sore')$sore_hadir++;
								if($hadir==1&&$kondisi=='Malam')$malam_hadir++;
							?>							
							<?php if($kondisi==$show||$show==0){?>
							<tr onclick=window.location.href='Employee/<?php echo $dt->id;?>/<?php echo $dt->PIN;?>'>
								<td><?php $no++;$pin=$dt->PIN;$userid=$dt->id;echo $no;?></td>
								<td><?php echo e($dt->NIK); ?></td>
								<td><?php echo e($dt->employee_name); ?></td>
								<td><?php echo e($dt->dept_code); ?></td>
								<td><?php echo e($dt->position_name); ?></td>
								<td>
									<?php 
										if($dt->id_shift>0){
											if($dt->group=='1')echo "Non Shift";
											else echo $dt->nshift.'Shift - '.$dt->shift_code;
										}
										else{echo "Un Set";$unset++;}
									?>
								</td>
								<td><?php if($dt->id_shift>0)echo $sekarang;?></td>
								<td>
									<?php if($dt->id_shift>0){?>
										<span class="badge <?php echo e($bg); ?>"><?php echo $kondisi;?></span>
										<a href="Employee/Checktime/<?php echo e($dt->id); ?>/<?php echo e($dt->PIN); ?>/<?php echo e($Tgl_awal); ?>/<?php echo e($Tgl_akhir); ?>"><i class="fa fa-edit"></i></a>
									<?php }?>
								</td>
								<td>
									<?php if($hadir==1&&$dt->id_shift>0){
										if($masuk!='')$masuk_act=date('H:i',strtotime($masuk));
										else $masuk_act='-';
										if($keluar!='')$keluar_act=date('H:i',strtotime($keluar));
										else $keluar_act='-';
										echo "<label class='label label-primary' title='".$masuk."'>".$masuk_act."</label>";
										echo "&nbsp;<label class='label label-info' title='".$keluar."'>".$keluar_act."</label>";
									}?>
								</td>
							</tr>
							<?php }?>
							<?php 
								$total=$pagi+$sore+$malam+$libur+$unset;
								$pagis=number_format($pagi/$total*100,2);
								$sores=number_format($sore/$total*100,2);
								$malams=number_format($malam/$total*100,2);
								$liburs=number_format($libur/$total*100,2);
								$unsets=number_format($unset/$total*100,2);
								$pagi_hadirs=number_format($pagi_hadir/$pagi*100,2);
							?>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</tbody>
					<tfoot>
						<tr>
							<th>No</th>
							<th>NIK</th>
							<th>Employee Name</th>
							<th>Dept</th>
							<th>Position</th>
							<th>Group</th>
							<th>Working Time</th>
							<th>Shift</th>
							<th>Actual</th>
						</tr>
					</thead>
				</table>
				<!-- Start Hidden -->
					<input type="hidden" id="shiftpagi" value="<?php echo e($pagi); ?>">
					<input type="hidden" id="shiftsore" value="<?php echo e($sore); ?>">
					<input type="hidden" id="shiftmalam" value="<?php echo e($malam); ?>">
					<input type="hidden" id="shiftlibur" value="<?php echo e($libur); ?>">
					<input type="hidden" id="shiftunset" value="<?php echo e($unset); ?>">
					<input type="hidden" id="shiftpagis" value="<?php echo e($pagis); ?>">
					<input type="hidden" id="shiftsores" value="<?php echo e($sores); ?>">
					<input type="hidden" id="shiftmalams" value="<?php echo e($malams); ?>">
					<input type="hidden" id="shiftliburs" value="<?php echo e($liburs); ?>">
					<input type="hidden" id="shiftunsets" value="<?php echo e($unsets); ?>">
					<input type="hidden" id="pagihadir" value="<?php echo e($pagi_hadir); ?>">
					<input type="hidden" id="pagihadirs" value="<?php echo e($pagi_hadirs); ?>">
					<input type="hidden" id="sorehadir" value="<?php echo e($sore_hadir); ?>">
					<input type="hidden" id="malamhadir" value="<?php echo e($malam_hadir); ?>">
				<!-- End Hidden -->
				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->

			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->


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
		//"pageLength"  : 25,
		'autoWidth'   : false
		})
		$('#table2').DataTable({
		'paging'      : true,
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		'autoWidth'   : true
		})
	})
	</script>
	<!-- page script alert-->
	<script>
		$('body').on("change","#tgl",function(){
			var tgl=document.getElementById('tgl').value;
			var show=document.getElementById('show').value;
			window.location.href="/MealsShifts/"+tgl+"/"+show;
		});
	</script>
	<script>
		$( document ).ready(function() {
			var pagi=document.getElementById('shiftpagi').value;
			var sore=document.getElementById('shiftsore').value;
			var malam=document.getElementById('shiftmalam').value;
			var libur=document.getElementById('shiftlibur').value;
			var unset=document.getElementById('shiftunset').value;
			var pagis=document.getElementById('shiftpagis').value;
			var sores=document.getElementById('shiftsores').value;
			var malams=document.getElementById('shiftmalams').value;
			var liburs=document.getElementById('shiftliburs').value;
			var unsets=document.getElementById('shiftunsets').value;
			var pagihadir=document.getElementById('pagihadir').value;
			var pagihadirs=document.getElementById('pagihadirs').value;
			var pagiabsen=(pagi-pagihadir)*1;
			var infopagi='Finger Print: '+pagihadir+' persons; Other: '+pagiabsen+' persons';

			var sorehadir=document.getElementById('sorehadir').value;
			var soreabsen=(sore-sorehadir)*1;
			var infosore='Finger Print: '+sorehadir+' persons; Other: '+soreabsen+' persons';

			var malamhadir=document.getElementById('malamhadir').value;
			var malamabsen=(malam-malamhadir)*1;
			var infomalam='Finger Print: '+malamhadir+' persons; Other: '+malamabsen+' persons';
			
			$("#pagi").text(pagi);
			$("#sore").text(sore);
			$("#malam").text(malam);
			$("#libur").text(libur);
			$("#pagis").text(pagis);
			$("#sores").text(sores);
			$("#malams").text(malams);
			$("#liburs").text(liburs);
			$("#infopagi").text(infopagi);
			$("#infosore").text(infosore);
			$("#infomalam").text(infomalam);
		});
	</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\admin\m_meal\meal_shift.blade.php ENDPATH**/ ?>