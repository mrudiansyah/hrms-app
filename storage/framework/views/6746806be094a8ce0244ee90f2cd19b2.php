
<?php $__env->startSection('Contents'); ?>
   <!-- Contents -->
   <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Information
				<small>report/summary</small>
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
					<div class="box-body">
						<div class="row" style="padding:20px;">
							<div class="col-xs-12">
								<div class="box box-info box-solid" style="border:0px;">
									<div class="box-header with-border">
										<h3 class="box-title">Absensi Rate</h3>
										<div class="box-tools pull-right">
											<input type="month" class="form-control" id="periode" name="periode" value="<?php echo e($periode); ?>">
										</div>
									</div>
									<!-- /.box-header -->
									<div class="box-body">
										<table id="tables" class="table table-hover">
											<thead>
												<tr>
													<th style="width:30px;">No</th>
													<th>Department</th>
													<th>Total Employee</th>
													<th>Hari Kerja</th>
													<th>Total Plan</th>
													<th>Total Absen</th>
													<th>Absensi Rate</th>
													<th>&nbsp;</th>
												</tr>
											</thead>
											<tbody>
											<?php $no=0;$divisi='';$qty_employee=0;$qty_plan=0;$qty_absen=0;?>
											<?php $__currentLoopData = $tb_absen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<tr>
													<td>
														<?php 
															$no++;echo $no;$total_plan=$dt->total_employee*$dt->present_plan;
															$qty_employee=$qty_employee+$dt->total_employee;
															$qty_plan=$qty_plan+$total_plan;
															$qty_absen=$qty_absen+$dt->total_absen;
															$x=(($dt->total_employee*$dt->present_plan)-$dt->total_absen)/($dt->total_employee*$dt->present_plan)*100;
														?>
													</td>
													<td><?php echo e($dt->dept_code); ?> <div class="pull-right"><small><?php if($divisi==$dt->divisi)echo "(".$dt->hari_kerja." Hari Kerja)";?></small></div></td>
													<td><?php echo number_format($dt->total_employee,0);?></td>
													<td><?php echo number_format($dt->present_plan,0);?></td>
													<td><?php echo number_format($total_plan,0);?></td>
													<td><?php echo number_format($dt->total_absen,0);?></td>
													<td>
														<?php echo number_format($x,2);?>%
													</td>
													<td>
														<?php if($x>=98): ?>
															<i class="fa fa-thumbs-up"></i>
														<?php else: ?>
															<i class="fa  fa-thumbs-o-down"></i>
														<?php endif; ?>
														<div class="pull-right">
															<a title="Show" href='/AbsensiRate/<?php echo e($periode); ?>/<?php echo e($dt->dept_id); ?>'><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-folder-open-o"></i></button></a>
														</div>
															
													</td>
												</tr>
												<?php $divisi=$dt->divisi?>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php $__currentLoopData = $tb_absen2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<tr>
													<td>
														<?php 
															$no++;echo $no;$total_plan=$dt->total_employee*$dt->present_plan;
															$qty_employee=$qty_employee+$dt->total_employee;
															$qty_plan=$qty_plan+$total_plan;
															$qty_absen=$qty_absen+$dt->total_absen;
															$x=(($dt->total_employee*$dt->present_plan)-$dt->total_absen)/($dt->total_employee*$dt->present_plan)*100;
														?>
													</td>
													<td><?php echo e($dt->dept_code); ?> <div class="pull-right"><small><?php if($divisi==$dt->divisi)echo "(".$dt->hari_kerja." Hari Kerja)";?></small></div></td>
													<td><?php echo number_format($dt->total_employee,0);?></td>
													<td><?php echo number_format($dt->present_plan,0);?></td>
													<td><?php echo number_format($total_plan,0);?></td>
													<td><?php echo number_format($dt->total_absen,0);?></td>
													<td><?php echo number_format($x,2);?>%</td>
													<td>
														<?php if($x>=98): ?>
															<i class="fa fa-thumbs-up"></i>
														<?php else: ?>
															<i class="fa  fa-thumbs-o-down"></i>
														<?php endif; ?>
													
													</td>
												</tr>
												<?php $divisi=$dt->divisi?>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</tbody>
											<tfoot>
												<tr>
													<th colspan="2">Total</th>
													<th><?php echo e($qty_employee); ?></th>
													<th>&nbsp;</th>
													<th><?php echo e($qty_plan); ?></th>
													<th><?php echo e($qty_absen); ?></th>
													<th>
														<?php 
															if($qty_plan==0)$overall_rate=0;
															else $overall_rate=(($qty_plan)-$qty_absen)/($qty_plan)*100;
															echo number_format($overall_rate,2).'%';
															if($overall_rate>=98)$overall_kriteria='Baik';
															else $overall_kriteria='Buruk';
														?>
													</th>
													<th>
														<?php if($overall_rate>=98): ?>
															<i class="fa fa-thumbs-up"></i>
														<?php else: ?>
															<i class="fa  fa-thumbs-o-down"></i>
														<?php endif; ?>
													
													</th>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>

							</div>
						</div>
					</div>
					<!-- /.box-body -->
					<div class="box-footer">
						&nbsp;
					</div>
				</div>

			</div>
		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->


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
		$('body').on("change","#periode",function(){
			var periode=document.getElementById('periode').value;
			window.location.href="/AbsensiRate/"+periode+"/0";
		});
	</script>
	<!-- Tabel Configuration -->
	<script>
		$(function () {
			$('#table2').DataTable({
			'paging'      : true,
			'lengthChange': false,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : true,
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
	</script>
    <script>
      $(document).ready(function() {
        var table = $('#tables').DataTable({
          'paging'      : false,
          'lengthChange': false,
          'searching'   : true,
          'ordering'    : true,
          'info'        : true,
          "pageLength"  : 25,
          'autoWidth'   : false,
          "lengthMenu"  : [[5,10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
          "scrollX"     : true,
		  "order"       : [[6, 'asc']]
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
        var table2 = $('#tables2').DataTable({
          'paging'      : false,
          'lengthChange': false,
          'searching'   : true,
          'ordering'    : true,
          'info'        : true,
          "pageLength"  : 25,
          'autoWidth'   : false,
          "lengthMenu"  : [[5,10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
          "scrollX"     : true
        });
      
        new $.fn.dataTable.Buttons( table2, {
          //buttons: ['copy', 'excel', 'print']
			buttons: [
				{ extend: 'copyHtml5', footer: true },
				{ extend: 'excelHtml5', footer: true },
				{ extend: 'print', footer: true }
			]

        } );
      
        table2.buttons( 0, null ).container().prependTo(
          table2.table().container()
        );
      } );


  	</script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/user/m_absency/absencysummaries.blade.php ENDPATH**/ ?>