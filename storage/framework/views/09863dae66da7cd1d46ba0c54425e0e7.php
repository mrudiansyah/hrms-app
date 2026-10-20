
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
				<?php echo e($judul); ?>

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
							<h3 class="box-title" id="judul" style="padding-bottom:25px;">New Applied</h3>
							<div class="pull-right">
								<a class="btn btn-app" href="/Leave/Approves/0/0">
									<?php if(isset($qty_leave_new)&&$qty_leave_new>0)echo "<span class='badge bg-yellow'>".$qty_leave_new."</span>";?>
									<i class="fa fa-file-o"></i> New Leave
								</a>
								<?php if(request()->user()->hasRole('leave_legalize_dirhr')): ?>
								<a class="btn btn-app table5">
									<?php if(isset($qty_leave_proccess)&&$qty_leave_proccess>0)echo "<span class='badge bg-yellow'>".$qty_leave_proccess."</span>";?>
									<i class="fa fa-check-square"></i> Legalize HR
								</a>
								<?php endif; ?>
								<a class="btn btn-app table3">
									<i class="fa fa-check-square-o"></i> Approved
								</a>
								<a class="btn btn-app table4">
									<i class="fa fa-trash"></i> Abort
								</a>
							</div>
						</div>
						<div class="box-body" style="min-height:200px;overflow-x:scroll;">
							<div id="tabel2">
								<table id="tables2" class="table table-striped" border="0">
									<thead>
										<tr>
											<th style="width:30px;">NO</th>
											<th>APPLIED</th>
											<th>CATEGORY</th>
											<th>NIK</th>
											<th>EMPLOYEE</th>
											<th>DEPT</th>
											<th>START</th>
											<th>FINISH</th>
											<th>WORKING</th>
											<th>REASON/CONTACT.</th>
											<th>APPROVED#1</th>
											<th>APPROVED#2</th>
										</tr>
									</thead>
									<tbody>
									<?php $no=0;?>
										<?php $__currentLoopData = $tb_leave_new; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td><?php echo e($dt->doc_date); ?></td>
											<td><?php echo ucfirst($dt->category);?></td>
											<td><?php echo e($dt->NIK); ?></td>
											<td><?php echo e($dt->employee_name); ?></td>
											<td><?php echo e($dt->dept_code); ?></td>
											<td><?php echo e($dt->start_leave); ?></td>
											<td><?php echo e($dt->finish_leave); ?></td>
											<td><?php echo e($dt->start_working); ?></td>
											<td>
												<?php if($dt->category=='annual')echo $dt->remark;else echo $dt->reason.', '.$dt->remark;?>
											</td>
											<td>
												<?php if($dt->status_approved=='0'&&$dt->approved!=0){?>
													<small><i class="fa fa-square-o" style="color:#00F;" title="Need Approve"></i></small>	
												<?php }else{?>
													<small><i class="fa fa-check-square-o" style="color:#00F;" title="Done"></i></small>
												<?php }?>
												<?php echo e($dt->leader_name); ?>

											</td>
											<td>
												<?php if($dt->approved2!='0'&&$dt->status_approved2=='0'){?>
													<small><i class="fa fa-square-o" style="color:#00F;" title="Need Approve"></i></small>	
												<?php }?>
												<?php echo e($dt->leader_name2); ?>

											</td>
										</tr>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<tbody>
								</table>
							</div>
							<div id="tabel5">
								<table id="tables1" class="table table-striped" border="0">
									<thead>
										<tr>
											<th style="width:30px;">NO</th>
											<th>APPLIED</th>
											<th>CATEGORY</th>
											<th>NIK</th>
											<th>EMPLOYEE</th>
											<th>DEPT</th>
											<th>START</th>
											<th>FINISH</th>
											<th>WORKING</th>
											<th>REASON/CONTACT</th>
											<th>APPROVED#1</th>
											<th>APPROVED#2</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
									<?php $no=0;?>
										<?php $__currentLoopData = $tb_leave_proccess; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td><?php echo e($dt->doc_date); ?></td>
											<td><?php echo ucfirst($dt->category);?></td>
											<td><?php echo e($dt->NIK); ?></td>
											<td><?php echo e($dt->employee_name); ?></td>
											<td><?php echo e($dt->dept_code); ?></td>
											<td><?php echo e($dt->start_leave); ?></td>
											<td><?php echo e($dt->finish_leave); ?></td>
											<td><?php echo e($dt->start_working); ?></td>
											<td>
												<?php if($dt->category=='annual')echo $dt->remark;else echo $dt->reason.', '.$dt->remark;?>
											</td>
											<td>
											<?php if($dt->status_approved=='0'&&$dt->approved!=0){?>
													<small><i class="fa fa-square-o" style="color:#00F;" title="Need Approve"></i></small>	
												<?php }elseif($dt->status_approved=='1'){?>
													<small><i class="fa fa-check-square-o" style="color:#00F;" title="Done"></i></small>
												<?php }?>
												<?php echo e($dt->leader_name); ?>

											</td>
											<td>
												<?php if($dt->approved2!='0'&&$dt->approved2!=NULL&&$dt->status_approved2=='0'){?>
													<small><i class="fa fa-square-o" style="color:#00F;" title="Need Approve"></i></small>	
												<?php }?>
												<?php if($dt->approved2!='0'&&$dt->status_approved2=='1'){?>
													<small><i class="fa fa-check-square-o" style="color:#00F;" title="Done"></i></small>	
												<?php }?>
												<?php echo e($dt->leader_name2); ?>

											</td>
											<td>
												<div class="pull-right">
												<?php if(($dt->status_approved2=='1')||($dt->approved2=='0'&&$dt->status_approved=='1')||($dt->approved2==NULL&&$dt->status_approved=='1')||$dt->approved==0){?>
													<?php if($id_employee==$dt->legalized||$dt->legalized=='122'): ?>
														<button title="Approve" type="button" class="approve-modal btn btn-success btn-xs" data-approveid="<?php echo e($dt->id); ?>" data-approvename="<?php echo e($dt->employee_name); ?>" data-approvestatus="1"><i class="fa fa-check-square-o"></i></button>
													<?php endif; ?>
													<?php }else if((request()->user()->hasRole('hr_access')) &&($dt->approved2 == 879 || $dt->approved == 879)){  ?>
														<!-- <button title="Approve" type="button" class="approve-modal btn btn-success btn-xs" data-approveid="<?php echo e($dt->id); ?>" data-approvename="<?php echo e($dt->employee_name); ?>" data-approvestatus="1"><i class="fa fa-check-square-o"></i></button> -->
													<?php
													}?>
													<?php if($id_employee==$dt->legalized||$dt->legalized=='122'): ?>
														<button title="Cancel" type="button" class="refuse-modal btn btn-danger btn-xs" data-refuseid="<?php echo e($dt->id); ?>" data-refusename="<?php echo e($dt->employee_name); ?>"><i class="fa fa-times"></i></button>
													<?php endif; ?>
												</div>
											</td>
										</tr>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<tbody>
								</table>
							</div>
							<div id="tabel3">
								<div class="row" style="padding-bottom:10px;">
									<div class="col-lg-2 col-xs-6">
										From: 
										<input type="date" class="form-control" id="start" name="start" value="<?php echo e($start); ?>"> 
									</div>
									<div class="col-lg-2 col-xs-6">
										To:
										<input type="date" class="form-control" id="finish" name="finish" value="<?php echo e($finish); ?>">
										<input type="hidden" class="form-control" id="category" value="<?php echo e($category); ?>">
									</div>
								</div>
								<table id="tables" class="table table-striped" border="0">
									<thead>
										<tr>
											<th style="width:30px;">NO</th>
											<th>APPLIED</th>
											<th>CATEGORY</th>
											<th>NIK</th>
											<th>EMPLOYEE</th>
											<th>DEPT</th>
											<th>START</th>
											<th>FINISH</th>
											<th>WORKING</th>
											<th>REASON/CONTACT</th>
											<th>APPROVED#1</th>
											<th>APPROVED#2</th>
											<th>DAYS</th>
										</tr>
									</thead>
									<tbody>
									<?php $no=0;?>
										<?php $__currentLoopData = $tb_leave_approve; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td><?php echo e($dt->doc_date); ?></td>
											<td><?php echo ucfirst($dt->category);?></td>
											<td><?php echo e($dt->NIK); ?></td>
											<td><?php echo e($dt->employee_name); ?></td>
											<td><?php echo e($dt->dept_code); ?></td>
											<td><?php echo e($dt->start_leave); ?></td>
											<td><?php echo e($dt->finish_leave); ?></td>
											<td><?php echo e($dt->start_working); ?></td>
											<td>
												<?php if($dt->category=='annual')echo $dt->remark;else echo $dt->reason.', '.$dt->remark;?>
											</td>
											<td>
												<small><i class="fa fa-check-square-o" style="color:#00F;" title="Done"></i></small>
												<?php echo e($dt->leader_name); ?>

											</td>
											<td>
												<?php if($dt->status_approved2=='1'){?>
													<small><i class="fa fa-check-square-o" style="color:#00F;" title="Done"></i></small>	
												<?php }?>
												<?php echo e($dt->leader_name2); ?>

											</td>
											<td>
												<?php echo e($dt->leave_count); ?>

												<div class="pull-right">
													<button title="Approve" type="button" class="approve-modal btn btn-success btn-xs" data-approveid="<?php echo e($dt->id); ?>" data-approvename="<?php echo e($dt->employee_name); ?>" data-approvestatus="0"><i class="fa fa-refresh"></i></button>
													<button title="Cancel" type="button" class="refuse-modal btn btn-danger btn-xs" data-refuseid="<?php echo e($dt->id); ?>" data-refusename="<?php echo e($dt->employee_name); ?>"><i class="fa fa-times"></i></button>
												</div>
											</td>
										</tr>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<tbody>
								</table>
							</div>
							<div id="tabel4">
								<table id="table4" class="table table-striped" border="0">
									<thead>
										<tr>
											<th style="width:30px;">NO</th>
											<th>APPLIED</th>
											<th>CATEGORY</th>
											<th>NIK</th>
											<th>EMPLOYEE</th>
											<th>START</th>
											<th>FINISH</th>
											<th>WORKING</th>
											<th>REASON/CONTACT</th>
											<th>APPROVED#1</th>
											<th>APPROVED#2</th>
										</tr>
									</thead>
									<tbody>
									<?php $no=0;?>
										<?php $__currentLoopData = $tb_leave_refuse; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td><?php echo e($dt->doc_date); ?></td>
											<td><?php echo ucfirst($dt->category);?></td>
											<td><?php echo e($dt->NIK); ?></td>
											<td><?php echo e($dt->employee_name); ?></td>
											<td><?php echo e($dt->start_leave); ?></td>
											<td><?php echo e($dt->finish_leave); ?></td>
											<td><?php echo e($dt->start_working); ?></td>
											<td>
												<?php if($dt->category=='annual')echo $dt->remark;else echo $dt->reason.', '.$dt->remark;?>
											</td>
											<td>
												<?php echo e($dt->leader_name); ?>

											</td>
											<td>
												<?php echo e($dt->leader_name2); ?>

												<div class="pull-right">
													<button title="Approve" type="button" class="approve-modal btn btn-success btn-xs" data-approveid="<?php echo e($dt->id); ?>" data-approvename="<?php echo e($dt->employee_name); ?>" data-approvestatus="0"><i class="fa fa-refresh"></i></button>
													<button title="Cancel" type="button" class="refuse-modal btn btn-danger btn-xs" data-refuseid="<?php echo e($dt->id); ?>" data-refusename="<?php echo e($dt->employee_name); ?>"><i class="fa fa-times"></i></button>
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

	<div class="modal fade" id="modal-approve">
		<div class="modal-dialog box box-success" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Approve Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Approve : <b id="approvename"></b> ?
					<input type="hidden" id="approveid">
					<input type="hidden" id="approvestatus">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success pull-left approve" data-dismiss="modal">Yes, Approve</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-refuse">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Refuse Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Refuse : <b id="refusename"></b> ?
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
		$(function () {
			$('#table5').DataTable({
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
	<script type="text/javascript">
		// Approve Data
		$(document).on('click', '.approve-modal', function() {
			//$('#approveid').val($(this).data('approveid'));
			//$('#approvename').text($(this).data('approvename'));
			//$('#approvestatus').val($(this).data('approvestatus'));
			//$('#modal-approve').modal('show');
			var x=$(this).data('approveid');
			var y=$(this).data('approvestatus');
			window.location.href='/Leave/Legalize/'+x+'/'+y;
		});
		$('.modal-footer').on('click', '.approve', function() {
			var x=$('#approveid').val();
			var y=$('#approvestatus').val();
			//alert(y);
			window.location.href='/Leave/Legalize/'+x+'/'+y;
		});
	</script>
	<script type="text/javascript">
		// Refuse Data
		$(document).on('click', '.refuse-modal', function() {
			$('#refuseid').val($(this).data('refuseid'));
			$('#refusename').text($(this).data('refusename'));
			$('#modal-refuse').modal('show');
		});
		$('.modal-footer').on('click', '.refuse', function() {
			var x=$('#refuseid').val();
			window.location.href='/Leave/Legalize/'+x+'/2';
		});
	</script>
	<script>
		$( document ).ready(function() {
			$("#tabel2").hide();
			$("#tabel3").hide();
			$("#tabel4").hide();
			$("#tabel5").show();
			$("#judul").text('Approval');
			$(document).on('click', '.table2', function() {
				$("#tabel2").show(1000);
				$("#tabel3").hide(50);
				$("#tabel4").hide(50);
				$("#tabel5").hide(50);
				$("#judul").text('New Created');
			});
			$(document).on('click', '.table3', function() {
				$("#tabel3").show(1000);
				$("#tabel2").hide(50);
				$("#tabel4").hide(50);
				$("#tabel5").hide(50);
				$("#judul").text('Legalized');
			});
			$(document).on('click', '.table4', function() {
				$("#tabel4").show(1000);
				$("#tabel3").hide(50);
				$("#tabel2").hide(50);
				$("#tabel5").hide(50);
				$("#judul").text('Refused');
			});
			$(document).on('click', '.table5', function() {
				$("#tabel4").hide(50);
				$("#tabel3").hide(50);
				$("#tabel2").hide(50);
				$("#tabel5").show(1000);
				$("#judul").text('Approval');
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
      $(document).ready(function() {
        var table = $('#tables1').DataTable({
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
      $(document).ready(function() {
        var table = $('#tables2').DataTable({
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
		$('body').on("change","#periode",function(){
			var periode=document.getElementById('periode').value;
			window.location.href="/Leave/Legalize/"+periode;
		});
	</script>
	<script>
		$('body').on("change","#start",function(){
			var start=document.getElementById('start').value;
			var finish=document.getElementById('finish').value;
			var category=document.getElementById('category').value;
			window.location.href="/Leave/Legalizes/"+start+"/"+finish+"/"+category;
		});
		$('body').on("change","#finish",function(){
			var start=document.getElementById('start').value;
			var finish=document.getElementById('finish').value;
			var category=document.getElementById('category').value;
			window.location.href="/Leave/Legalizes/"+start+"/"+finish+"/"+category;
		});
	</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/m_leave/legalize_leave.blade.php ENDPATH**/ ?>