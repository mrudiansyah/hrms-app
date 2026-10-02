
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
					<div class="box-header">
						<i class="fa fa-calendar"></i>
						<h3 class="box-title">Pareto Overtime</h3>
					</div>
					<div class="box-body">
						<div class="row" style="padding:20px;">
							<div class="col-xs-12 col-sm-12 col-lg-6">
								<div class="box box-info box-solid" style="border:0px;">
									<div class="box-header with-border">
										<h3 class="box-title">Pareto/Dept</h3>
										<div class="box-tools pull-right" style="color:black">
											<input class="tgl" type="date" id="tglawal" value="<?php echo e($Tglawal); ?>">
											<input class="tgl" type="date" id="tglakhir" value="<?php echo e($Tglakhir); ?>">
										</div>
									</div>
									<!-- /.box-header -->
									<div class="box-body">
										<table id="tables" class="table table-hover">
											<thead>
												<tr>
													<th style="width:30px;">No</th>
													<th>Department</th>
													<th style="width:70px;">Hours</th>
													<th style="width:70px;">Ammount</th>
												</tr>
											</thead>
											<tbody>
												<?php $no=0;$total=0;?>
												<?php $__currentLoopData = $tb_sumot; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<?php if(isset($dept_id)&&$dept_id==$dt->dept_id)$warna="style='background:#EEE;'";else $warna='';?>
												<tr <?php echo $warna;?>>
													<td><?php $total=$total+$dt->total_ammount;$no++;echo $no;?></td>
													<td><?php echo e($dt->dept_name); ?></td>
													<td><?php echo e($dt->total_act); ?></td>
													<td>
														<?php
															echo "Rp " . number_format($dt->total_ammount,0,',','.')
														?>
														<div class="pull-right">
															<a title="Show" href='/Overtimes/Reason/<?php echo e($Periode); ?>/<?php echo e($dt->dept_id); ?>/<?php echo e($reason); ?>'><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-folder-open-o"></i></button></a>
														</div>
													</td>
												</tr>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</tbody>
											<tfoot>
												<tr>
													<th colspan="3">&nbsp;</th>
													<th><?php echo "Rp " . number_format($total,0,',','.');?></th>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>

							</div>
							<div class="col-xs-12 col-sm-12 col-lg-6">
								<div class="box box-info box-solid" style="border:0px;">
									<div class="box-header with-border" style="height:45px;">
										<h3 class="box-title">Pareto/Reason</h3>
										<div class="box-tools pull-right">
											<?php if($dept_admin=='7'||$dept_admin=='11'){?>
											<div class="btn-group">
												<button type="button" class="btn btn-primary"><?php echo e($Reason); ?></button>
												<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
													<span class="caret"></span>
													<span class="sr-only">Toggle Dropdown</span>
												</button>
												<ul class="dropdown-menu" role="menu">
													<li><a href="/Overtimes/Reason/<?php echo e($Periode); ?>/<?php echo e($dept_id); ?>/1">Reason OT</a></li>
													<li><a href="/Overtimes/Reason/<?php echo e($Periode); ?>/<?php echo e($dept_id); ?>/2">Job OT</a></li>
													<li><a href="/Overtimes/Reason/<?php echo e($Periode); ?>/<?php echo e($dept_id); ?>/3">Customer</a></li>
													<li class="divider"></li>
													<li><a href="/Overtimes/Reason/<?php echo e($Periode); ?>/<?php echo e($dept_id); ?>/0">General</a></li>
												</ul>
											</div>
											<?php }?>
										</div>
									</div>
									<!-- /.box-header -->
									<div class="box-body">
										<table id="tables2" class="table table-hover">
											<thead>
												<tr>
													<th style="width:30px;">No</th>
													<th>Reason</th>
													<th style="width:50px;">Hours</th>
													<th style="width:70px;">Ammount</th>
												</tr>
											</thead>
											<tbody>
												<?php $no=0;$total=0;?>
												<?php $__currentLoopData = $tb_sumot_b; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<tr>
													<td><?php $total=$total+$dt->total_ammount;$no++;echo $no;?></td>
													<td><?php echo e($dt->reason); ?></td>
													<td><?php echo e($dt->total_act); ?></td>
													<td>
														<?php
															//echo "Rp " . number_format($dt->total_ammount,0,',','.')
															echo $dt->total_ammount;
														?>
													</td>
												</tr>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</tbody>
											<tfoot>
												<tr>
													<th colspan="3">&nbsp;</th>
													<th><?php echo "Rp " . number_format($total,0,',','.');?></th>
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
		$('body').on("change",".tgl",function(){
			var tglawal=document.getElementById('tglawal').value;
			var tglakhir=document.getElementById('tglakhir').value;
			var dept="<?php echo e($dept_id); ?>";
			window.location.href="/Overtimes/Department/"+tglawal+"/"+tglakhir+"/"+dept;
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
          'paging'      : true,
          'lengthChange': false,
          'searching'   : true,
          'ordering'    : true,
          'info'        : true,
          "pageLength"  : 10,
          'autoWidth'   : false,
          "lengthMenu"  : [[5,10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
          "scrollX"     : true
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
          'paging'      : true,
          'lengthChange': false,
          'searching'   : true,
          'ordering'    : true,
          'info'        : true,
          "pageLength"  : 10,
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


<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/user/m_overtime/overtimeemployee_cutoff.blade.php ENDPATH**/ ?>