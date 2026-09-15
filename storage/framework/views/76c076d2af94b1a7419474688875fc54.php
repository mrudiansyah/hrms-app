
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
				Employee Permit
				<small>form ijin</small>
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
							<i class="fa fa-list"></i>
							<h3 class="box-title" id="judul">Employee Permit</h3>
							<!-- <div class="pull-right">
								<a class="btn btn-app table2">
									<?php //if(isset($qty_permit_new)&&$qty_permit_new>0)echo "<span class='badge bg-yellow'>".$qty_permit_new."</span>";?>
									<i class="fa fa-file-o"></i> Approved
								</a>
								<a class="btn btn-app table3">
									<i class="fa fa-check-square-o"></i> Complete
								</a>
							</div> -->
						</div>
						<div class="box-body" style="min-height:200px;overflow-x:scroll;">
							<div id="tabel2">
								<table id="table2" class="table table-striped" border="0">
									<thead>
										<tr>
											<th style="width:30px;">NO</th>
											<th>DOC.DATE</th>
											<th>FORM</th>
											<th>NIK</th>
											<th>EMPLOYEE NAME</th>
											<th>DATE</th>
											<th>TIME</th>
											<th>KEPERLUAN</th>
											<th>APPROVED</th>
										</tr>
									</thead>
									<tbody>
									<?php $no=0;?>
										<?php $__currentLoopData = $tb_permit_new; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr>
											<td>
												<?php 
													$no++;
													$tgl_in=date('Y-m-d',strtotime($dt->start_izin));
													$jam_in=date('H:i',strtotime($dt->start_izin));
													$start=$tgl_in.'T'.$jam_in;
													$tgl_out=date('Y-m-d',strtotime($dt->finish_izin));
													$jam_out=date('H:i',strtotime($dt->finish_izin));
													$finish=$tgl_out.'T'.$jam_out;
													echo $no;
												?>
											</td>
											<td><?php echo e($dt->doc_date); ?></td>
											<td><?php echo ucfirst($dt->category);?></td>
											<td><?php echo e($dt->NIK); ?></td>
											<td><?php echo e($dt->employee_name); ?></td>
											<td><?php echo date('d-M',strtotime($dt->apply_date));?></td>
											<td><?php echo date('H:i',strtotime($dt->start_izin));if($dt->category=='C'||$dt->category=='D')echo " ~ ".date('H:i',strtotime($dt->finish_izin));?></td>
											<td>
												<?php echo e($dt->keperluan); ?> <?php echo e($dt->keluhan); ?> <?php echo e($dt->berobat_ke); ?>

											</td>
											<td>
												<?php echo e($dt->nama_atasan); ?>

												<div class="pull-right">
													<?php if($dt->status_disetujui=='1'){?>
													<button title="Approve" type="button" class="approve-modal btn btn-success btn-xs" data-sysid="<?php echo e($dt->id); ?>" data-employeename="<?php echo e($dt->employee_name); ?>" data-start="<?php echo e($start); ?>" data-finish="<?php echo e($finish); ?>" data-minutes="<?php echo e($dt->minutes); ?>" data-category="<?php echo e($dt->category); ?>"><i class="fa fa-check-square-o"></i></button>
													<?php }?>
													<a href="/Permit/Preview/<?php echo e($dt->id); ?>" title="Cetak" type="button" class="btn btn-info btn-xs" target="_balnk"><i class="fa fa-print"></i></a>
												</div>
											</td>
										</tr>

										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<tbody>
								</table>
							</div>
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
	<!-- Modal Approve -->
	<div class="modal fade" id="modal-approve">
		<div class="modal-dialog box box-success" style="width:300px;">
			<form action="/Permit/Scurity" method="post">
			<?php echo e(csrf_field()); ?>

				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Security Confirmation</h4>
					</div>
					<div class="modal-body" style="height:250px;">
						<input type="hidden" name="akses" value="security">
						<input type="hidden" name="sysid" id="sysid">
						<input type="hidden" id="isoma">
						<input type="hidden" id="hoursplan" name="minutes">
						<input type="hidden" id="category" name="category">
						<div class="form-group">
							<label>Employee Name</label>
							<input type="text" name="employee_name" id="employeename" class="form-control" disabled>
						</div>
						<div class="form-group">
							<label>Start</label>
							<input type="datetime-local" name="start_izin" class="form-control" id='start'>
						</div>
						<div class="form-group" id="selesai">
							<label>Finish</label>
							<input type="datetime-local" name="finish_izin" class="form-control" id='finish'>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-success">Simpan</button>
						<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
					</div>
				</div>
			</form>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- Modal Refuse -->
	<div class="modal fade" id="modal-refuse">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Refuse Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Approve : <b id="refusename"></b> ?
					<input type="hidden" id="refuseid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left refuse" data-dismiss="modal">Yes, Refuse</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
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
	<!-- Durasi Alert --->
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<!-- Approve -->
	<script type="text/javascript">
		// Approve Data
		$(document).on('click', '.approve-modal', function() {
			$('#sysid').val($(this).data('sysid'));
			$('#employeename').val($(this).data('employeename'));
			$('#start').val($(this).data('start'));
			$('#finish').val($(this).data('finish'));
			$('#hoursplan').val($(this).data('minutes'));
			$('#category').val($(this).data('category'));
			$('#modal-approve').modal('show');

			var category=$(this).data('category');
			var x = document.getElementById("selesai");
			if(category=='B'){
				x.style.display = "none";
			}else{
				x.style.display = "block";
				
			}

			var Awal=new Date($('#start').val());
			var Akhir=new Date($('#finish').val());
			
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
				else{$('#isoma').val('45')}
			}
			else if(Awal<Break3 && Akhir>Break3){$('#isoma').val('30');}
			else{$('#isoma').val('0');}

		});
	</script>
	<!-- Refuse -->
	<script type="text/javascript">
		// Refuse Data
		$(document).on('click', '.refuse-modal', function() {
			$('#refuseid').val($(this).data('refuseid'));
			$('#refusename').text($(this).data('refusename'));
			$('#modal-refuse').modal('show');
		});
		$('.modal-footer').on('click', '.refuse', function() {
			var x=$('#refuseid').val();
			window.location.href='/Permit/Scurity/'+x+'/2';
		});
	</script>
	<!--- on Load -->
	<script>
		$( document ).ready(function() {
			$("#tabel2").show();
			$("#tabel3").hide();
			$("#tabel4").hide();
			$("#judul").text('New Applied Permit');
			$(document).on('click', '.table2', function() {
				$("#tabel2").show(1000);
				$("#tabel3").hide(50);
				$("#tabel4").hide(50);
				$("#judul").text('New Applied Permit');
			});
			$(document).on('click', '.table3', function() {
				$("#tabel3").show(1000);
				$("#tabel2").hide(50);
				$("#tabel4").hide(50);
				$("#judul").text('Approved Permit');
			});
			$(document).on('click', '.table4', function() {
				$("#tabel4").show(1000);
				$("#tabel3").hide(50);
				$("#tabel2").hide(50);
				$("#judul").text('Refused Permit');
			});
		});
	</script>
	<script>
      $(document).ready(function() {
        var table = $('#tables').DataTable({
          'paging'      : true,
          'lengthChange': false,
          'searching'   : true,
          'ordering'    : true,
          'info'        : true,
          "pageLength"  : 25,
          'autoWidth'   : true,
          "lengthMenu"  : [[5,10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
        });
      
        new $.fn.dataTable.Buttons( table, {
          //buttons: ['copy', 'excel', 'print']
			buttons: [
				{ extend: 'copyHtml5', footer: true },
				{ extend: 'excelHtml5', footer: true },
				{ extend: 'print', footer: true }
			]

        } );
      
        table.buttons( 0, null ).container().prependTo(
          table.table().container()
        );
      } );


  	</script>
	<script>
		$("#start").change(function(){
			var Awal=new Date($('#start').val());
			var Akhir=new Date($('#finish').val());
			
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
				else{$('#isoma').val('45')}
			}
			else if(Awal<Break3 && Akhir>Break3){$('#isoma').val('30');}
			else{$('#isoma').val('0');}

			var n=((Akhir-Awal)/60000)-($('#isoma').val());
			$('#hoursplan').val(n);
			if(n>=360){
				alert('Waktu lebih dari 6 Jam, Redirect ke Form A');
				$("#category").val('A');
			}
			else if(n>180){
				alert('Waktu lebih dari 3 Jam, Redirect ke Form B');
				$("#category").val('B');
			}
		});
		$("#finish").change(function(){
			var Awal=new Date($('#start').val());
			var Akhir=new Date($('#finish').val());
			
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
				else{$('#isoma').val('45')}
			}
			else if(Awal<Break3 && Akhir>Break3){$('#isoma').val('30');}
			else{$('#isoma').val('0');}

			var n=((Akhir-Awal)/60000)-($('#isoma').val());
			$('#hoursplan').val(n);
			if(n>=360){
				alert('Waktu lebih dari 6 Jam, Redirect ke Form A');
				$("#category").val('A');
			}
			else if(n>180){
				alert('Waktu lebih dari 3 Jam, Redirect ke Form B');
				$("#category").val('B');
			}
		});
	</script>
	<script>
		$('body').on("change","#awal",function(){
			var start=document.getElementById('awal').value;
			var finish=document.getElementById('akhir').value;
			window.location.href="/Permit/Scurities/"+start+"/"+finish;
		});
		$('body').on("change","#akhir",function(){
			var start=document.getElementById('awal').value;
			var finish=document.getElementById('akhir').value;
			window.location.href="/Permit/Scurities/"+start+"/"+finish;
		});
	</script>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\admin\m_permit\permit_scurity.blade.php ENDPATH**/ ?>