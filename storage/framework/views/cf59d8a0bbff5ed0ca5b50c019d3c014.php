
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
							<?php if($submenu=='dept'){?>
								<a class='btn btn-app' href='/Admin/Overtime/Preview/<?php echo e($idspl); ?>' target="_blank"><i class='fa fa-print'></i>Print</a>
								<a class='btn btn-app' href='/Admin/Overtime/Depts/0'><i class='fa fa-close'></i>Close</a>
							<?php }?>
						</div>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12">
								&nbsp;
							</div>
						</div>
						<table id="table2" class="table table-hover" style="min-width:100%;">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th style="width:70px;">SPLN0.</th>
									<th style="width:70px;">Date</th>
									<th style="width:70px;">NIK</th>
									<th style="width:120px;">Name</th>
									<th>Reason/Target</th>
									<th style="width:80px;background:#CCC;">Plan OT</th>
									<th style="width:40px;background:#CCC;">Sign</th>
									<th style="width:80px;background:#feff01;">Actual OT</th>
									<th style="width:70px;background:#feff01;">Confirm</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;$signstate=0;?>
								<?php $__currentLoopData = $tb_overtime_detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td>
										<?php $no++;echo $no;?>
									</td>
									<td><?php echo e($dt->id_overtime); ?></td>
									<td><?php echo e($dt->date_on); ?></td>
									<td><?php echo e($dt->NIK); ?></td>
									<td><?php echo e($dt->employee_name); ?></td>
									<td><?php echo e($dt->reason); ?></td>
									<td>
										<?php echo date('H:i',strtotime($dt->start_plan)).' ~ '.date('H:i',strtotime($dt->finish_plan));?>
									</td>
									<td><input type="checkbox"<?php if($dt->sign_before==1)echo ' checked';?> class='fa fa-square-o confirm-modal' data-before='<?php echo e($dt->id); ?>'>
									</td>
									<td>
										<?php if($dt->sign_after=='1'||$dt->sign_after=='3')echo date('H:i',strtotime($dt->start_act)).' ~ '.date('H:i',strtotime($dt->finish_act));?>
									</td>
									<td title="<?php echo e($dt->hours_act); ?>">
										<?php 
										
										if($dt->sign_before=='1'&&$dt->status_dicatat=='1'){
											$tgl_awal=date('Y-m-d',strtotime($dt->start_act));
											$jam_awal=date('H:i',strtotime($dt->start_act));
											$start_act=$tgl_awal.'T'.$jam_awal;
											$tgl_akhir=date('Y-m-d',strtotime($dt->finish_act));
											$jam_akhir=date('H:i',strtotime($dt->finish_act));
											$finish_act=$tgl_akhir.'T'.$jam_akhir;
											if($dt->sign_after=='1')echo "<b style='color:#0C0;'>Confirm</b>";
											if($dt->sign_after=='2')echo "<b style='color:#F00;'>Not Come</b>";
											if($dt->sign_after=='3')echo "<b style='color:#00F;' title='".$dt->employee_plan."'>Change</b>";
											?>
												<div class="pull-right"><button type="button" class="btn btn-primary btn-xs update-modal" data-id_employee='<?php echo e($dt->id_employee); ?>' data-idafter='<?php echo e($dt->id); ?>' data-startact='<?php echo e($start_act); ?>' data-finishact='<?php echo e($finish_act); ?>' data-otcategory='<?php echo e($dt->ot_category); ?>' data-hoursact='<?php echo e($dt->hours_act); ?>' data-hoursconvertion='<?php echo e($dt->hours_convertion); ?>' data-otisoma='<?php echo e($dt->minutes_break); ?>'><i class="fa fa-edit"></i></button></div>
											<?php 
										}else {
											$signstate++;
											echo "<i class='fa fa-info-circle' title='Approval Overtime in belum lengkap'> Waiting</i>";
										}
										?>
									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
							<tfoot>
								<tr>
									<th>No</th>
									<th>SPL No.</th>
									<th>Date</th>
									<th>NIK</th>
									<th>Name</th>
									<th>Reason/Target</th>
									<th>Plan OT</th>
									<th>
										Sign
									</th>
									<th>Actual OT</th>
									<th>
										Confirm
									</th>
								</tr>
							</tfoot>
						</table>
					</div>
					<!-- /.box-body -->
					<div class="box-footer">
						<?php if(isset($idspl)){?>
							<a href="/Admin/Overtime/Detail/Before/<?php echo e($idspl); ?>"><button type="button" class="btn btn-primary preview-btn">Sign All</button></a>
							<?php if($signstate==0)echo "<a href='/Admin/Overtime/Detail/After/".$idspl."'><button type='button' class='btn btn-warning preview-btn'>Confirm All</button></a>";?>
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
			<form method="post">
			<input type="hidden" id="id_employee" name="id_employee">
			<input type="hidden" id="idafter" name="id_after">

			<?php echo e(csrf_field()); ?>

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Overtime Confirmation</h4>
				</div>
				<div class="modal-body" style="height:520px;">
					<div class="form-group">
						<label>Actual/Realisation</label>
						<select name="sign_after" id="signafter" class="form-control">
							<option value="1">Come</option>
							<option value="2">Canceled</option>
							<option value="3">Change Employee</option>
						</select>
					</div>
					<div class="form-group" id="tambahan">
						<label>Employee</label>
						<select id="id_employee_baru" name="id_employee_baru" class="form-control selectpicker" data-live-search="true">
							<option value=""></option>
							<?php $__currentLoopData = $tb_employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<option value="<?php echo e($dt2->id); ?>"><?php echo e($dt2->employee_name); ?> (<?php echo e($dt2->dept_code); ?>)</option>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

						</select>
						<div class="pull-right" style="color:#F00;padding-right:10px;" id="info">&nbsp;</div>
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
								<input type="hidden" name="otisoma" id="isoma">
								<select name="otisoma2" id="isoma2" class="form-control">
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
								<input type="hidden" id="old_hour" class="form-control" step="0.50" disabled>
								<div style="color:#F00;padding-right:10px;" id="info2">&nbsp;</div>
							</div>
							
						</div>

						<div class="form-group come" style="padding-top:80px;">
							<div class="col-xs-7" style="padding:0px;padding-right:3px;">
								<label>Type</label>
								<input type="hidden" name="ot_category" id="otcategory">
								<select name="ot_category2" id="otcategory2" class="form-control" disabled>
									<option value=""></option>
									<option value="1">Lembur Awal/Ahhir</option>
									<option value="2">Lembur Hari Libur</option>
								</select>
							</div>
							<div class="col-xs-5" style="padding:0px;padding-left:3px;">
								<label>Convertion</label>
								<input type="number" step="0.5" id="hoursconvertion" name="hours_convertion" class="form-control" disabled>
								<input type="hidden" id="isOver" value="0">
							</div>
						</div>
						<div class="form-group come">
							<label>&nbsp;</label>
							<textarea name="reason_over" id="reasonOver" class="form-control" rows="2" placeholder="Reason Over ..."></textarea>
						</div>

				</div>
				<div class="modal-footer" style="text-align:left;padding:20px;">
					<button type="button" class="btn btn-primary confirmafter" id="confirm">Confirm</button>
					<button type="button" class="btn btn-default pull-right cancelafter" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>

    <?php if($message = Session::get('success')): ?>
		<div class="alert alert-info alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-info"></i> Success Alert</h4>
			<?php echo e($message); ?>

		</div>
    <?php endif; ?>
	<?php if($errors->any()): ?>
		<div class="alert alert-danger alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-warning"></i> Saving Failed Alert!</h4>
				<?php if($errors->has('date_off')): ?>
					- Date harus diisi<br>
				<?php endif; ?>
		</div>
	<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
	<script>
		$( document ).ready(function() {
			$(".sembunyi").hide();
			$("#tambahan").hide();
			$("#reasonOver").hide();
			$(document).on('change', '#signafter', function() {
				if($(this).val()=='2'){
					$(".come").hide();
				}else{
					$(".come").show();
				}
				if($(this).val()=='3'){
					$("#tambahan").show();
				}else{
					$("#tambahan").hide();
				}
			});
		});
	</script>

	<!-- Tabel Configuration -->
	<script>
		$(function () {
			$('#table2').DataTable({
			'paging'      : false,
			'lengthChange': false,
			'searching'   : false,
			'ordering'    : false,
			'info'        : false,
			"pageLength"  : 15,
			'autoWidth'   : true,
			"scrollX"	  : true
			})
		})
		$(function () {
			$('#table3').DataTable({
			'paging'      : false,
			'lengthChange': false,
			'searching'   : false,
			'ordering'    : false,
			'info'        : false,
			"pageLength"  : 15,
			'autoWidth'   : true,
			"scrollX"	  : true
			})
		})
	</script>
	<!-- Update Sign Before -->
	<script type="text/javascript">
		// Confirm
		$(document).on('click', '.confirm-modal', function() {
			$.ajaxSetup({
				type:"POST",
				url: "/Admin/Overtime/Detail/Before",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			var x=$(this).data('before');

			$.ajax({
				data:{id_before:x},
				success: function(respond){
					//alert('Success Sign for Overtime Planning');
				}
			})

		});
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
			$('#isoma2').val($(this).data('otisoma'));
			$('#hoursplan').val($(this).data('hoursact'));
			$('#old_hour').val($(this).data('hoursact'));
			$('#otcategory').val($(this).data('otcategory'));
			$('#otcategory2').val($(this).data('otcategory'));
			$('#hoursconvertion').val($(this).data('hoursconvertion'));
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
		function updateJam(){
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
			var sisa=n%1;
			//Setting Category
			if(Hari>0 && Hari<6 && n<6){
				$('#otcategory').val(1);
				$('#otcategory2').val(1);
			}else{
				$('#otcategory').val(2);
				$('#otcategory2').val(2);
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
			if(Awal<Break1 && Akhir>Break1){$('#isoma').val('45');$('#isoma2').val('45');}
			else if(Awal<Break2 && Akhir>Break2){
				if(Hari==5){$('#isoma').val('90');$('#isoma2').val('90');}
				else{$('#isoma').val('45');$('#isoma2').val('45');}
			}
			else if(Awal<Break3 && Akhir>Break3){$('#isoma').val('30');$('#isoma2').val('30');}
			else{$('#isoma').val('0');$('#isoma2').val('0');}

			//Setting Category
			if(Hari>0 && Hari<6){
				$('#otcategory').val(1);
				$('#otcategory2').val(1);
			}else{
				$('#otcategory').val(2);
				$('#otcategory2').val(2);
			}

			var n=((Akhir-Awal)/60000/60)-($('#isoma').val()/60);
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
			var reasonover=$('#reasonOver').val();
			if(plan<=0||plan*1>oldhour*1){
				if(reasonover==''){
					document.getElementById("confirm").disabled = true;
				}else{
					document.getElementById("confirm").disabled = false;
				}
				$('#isOver').val(1);
				$("#reasonOver").show();
			}else{
				document.getElementById("confirm").disabled = false;
				$('#isOver').val(0);
				$("#reasonOver").hide();
			}
		}
		function updateJam_ref(){
			var Awal=new Date($('#startplan').val());
			var Akhir=new Date($('#finishplan').val());
			
			var Thn=Akhir.getFullYear();
			var Bln=Akhir.getMonth()+1;
			var Tgl=Akhir.getDate();
			var Hari = Akhir.getDay();
			var Tgla=Awal.getDate();
			var Blna=Awal.getMonth()+1;
			var Thna=Awal.getFullYear();
			
			var Break1=new Date(Thn+'-'+Bln+'-'+Tgl+' 02:00:00');
			if(Hari==5){var Break2=new Date(Thn+'-'+Bln+'-'+Tgl+' 11:30:00');}
			else{var Break2=new Date(Thn+'-'+Bln+'-'+Tgl+' 12:00:00');}
			var Break3=new Date(Thna+'-'+Blna+'-'+Tgla+' 18:00:00');
			//alert (Awal+' '+Break3+' '+Akhir);
			var malamSenin=new Date(Thna+'-'+Blna+'-'+Tgla+' 20:00:00');
			//Setting Isoma
			if(Awal<Break1 && Akhir>Break1){$('#isoma').val('45');$('#isoma2').val('45');}
			else if(Awal<Break2 && Akhir>Break2){
				if(Hari==5){$('#isoma').val('90');$('#isoma2').val('90');}
				else{$('#isoma').val('45');$('#isoma2').val('45');}
				//else{$('#isoma').val('30');$('#isoma2').val('30');}
			}
			else if(Awal<Break3 && Akhir>Break3){$('#isoma').val('30');$('#isoma2').val('30');}
			else{$('#isoma').val('0');$('#isoma2').val('0');}

			var n=((Akhir-Awal)/60000/60)-($('#isoma').val()/60);
			var sisa=n%1;
			//Setting Category
			if(Hari>0 && Hari<6 && n<6){
				$('#otcategory').val(1);
				$('#otcategory2').val(1);
			}else{
				if(Awal>=malamSenin && Hari==0){
					$('#otcategory').val(1);
					$('#otcategory2').val(1);
				}else{
					$('#otcategory').val(2);
					$('#otcategory2').val(2);
				}
			}

			if(sisa<0.5){
				var add=0;
			}else if(sisa<0.75){
				var add=0.5;
			}else{
				var add=1;
			}
			var fix=n-sisa+add;
			if(n<0.75){fix=0;}

			$('#hoursdraft').val(n);
			$('#hoursplan').val(fix);

			var category=$('#otcategory').val();
			var plan=$('#hoursplan').val();
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
			if(plan<=0||plan>16){
				document.getElementById("addmp").disabled = true;
			}else{
				document.getElementById("addmp").disabled = false;
			}
			if(category==1 && plan>3){
				$('#info2').text("Limit Harian 3 Jam");
			}else{
				$('#info2').text("");
			}
			
			var dateon=new Date($('#dateon').val());
			var dateonTgl=dateon.getDate();
			if(dateonTgl!=Tgla){
				alert('Informasi, Tanggal SPL dengan Plan Start tidak sama');
			}

		}
		$("#startplan").change(function(){
			updateJam();
		});
		$("#finishplan").change(function(){
			updateJam();
		});
		$("#reasonOver").keyup(function(){
			//alert("Masuk");
			var reasonover=$('#reasonOver').val();
			if(reasonover==''){
				document.getElementById("confirm").disabled = true;
			}else{
				document.getElementById("confirm").disabled = false;
			}
		});
		function updateIsoma(){
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
		}
		$("#isoma").change(function(){
			updateIsoma();
		});
		function updateCategory(){
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
		}
		$("#otcategory").change(function(){
			updateCategory();
		});
		$("#id_employee_baru").change(function(){
			updateCategory();
		});
	</script>
	<!-- Update Sign After -->
	<script>
		$(document).on('click', '.confirmafter', function() {
		
			$.ajaxSetup({
				type:"POST",
				url: "/Admin/Overtime/Detail/After",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			var id_employee=$("#id_employee").val();
			var id_employee_baru=$("#id_employee_baru").val();
			var id_after=$("#idafter").val();
			var start_act=$("#startplan").val();
			var finish_act=$("#finishplan").val();
			var ot_category=$("#otcategory").val();
			var minutes_break=$("#isoma").val();
			var hours_plan=$("#hoursplan").val();
			var hours_convertion=$("#hoursconvertion").val();
			var sign_after=$("#signafter").val();
			var is_over=$("#isOver").val();
			var reason_over=$("#reasonOver").val();

			$.ajax({
				data:{id_employee:id_employee,is_over:is_over,reason_over:reason_over,id_employee_baru:id_employee_baru,id_after:id_after,start_act:start_act,finish_act:finish_act,ot_category:ot_category,minutes_break:minutes_break,hours_plan:hours_plan,hours_convertion:hours_convertion,sign_after:sign_after},
				success: function(respond){
					//alert('Confirm Realisasi Overtime Berhasil');
					//$('#modal-update').modal('hide');
					if(respond!='<html><body><p>Success</p></body></html>'){
						alert(respond)
					}
					window.location.reload();
				}
			})
		});

	</script>
	<script>

	</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/m_overtime/realisationspl.blade.php ENDPATH**/ ?>