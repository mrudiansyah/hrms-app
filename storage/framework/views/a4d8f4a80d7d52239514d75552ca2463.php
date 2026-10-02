
<?php $__env->startSection('Contents'); ?>
   <!-- Contents -->
   <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Summary Overtime
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
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<!-- <i class="fa fa-calendar"></i><h3 class="box-title">Report Overtime</h3> -->
						<a title="Show Detail" href='/Overtimes/Summaries/<?php echo e($thn); ?>-<?php echo e($bln); ?>/0'><button type="button" class="btn btn-primary btn-md"><i class="fa fa-folder-open-o"></i> Detail</button></a>
						<div class="pull-right">
							<a title="Add Summary" href='/Overtimes/AddSummary/Trial/<?php echo e($periode); ?>'><button type="button" class="btn btn-info btn-md"><i class="fa fa-file-o"></i> Create</button></a>
							<!-- <a title="Update" href='/OvertimesUpdateTrial/<?php echo e($thn); ?>/<?php echo e($bln); ?>/<?php echo e($dept_id); ?>'><button type="button" class="btn btn-info btn-md"><i class="fa fa-file-text-o"></i> &nbsp;Update</button></a> -->
							<!-- <a title="Add Summary" href='/Overtimes/AddSummary/<?php echo e($periode); ?>'><button type="button" class="btn btn-primary btn-md"><i class="fa fa-file-o"></i> Create</button></a> -->
							<!-- <a title="Send Mail" href='/Mail/Slips/<?php echo e($thn); ?>/<?php echo e($bln); ?>/0'><button type="button" class="btn btn-danger btn-md"><i class="fa fa-envelope-o"></i> Send Mail</button></a> -->
							<a title="Send Slip" href='/Overtimes/Share/<?php echo e($periode); ?>'><button type="button" class="btn btn-danger btn-md"><i class="fa fa-envelope-o"></i> Share Slip</button></a>
						</div>
				</div>
					<div class="box-body">
						<div class="row" style="padding:20px;">
							<div class="col-xs-12">
								<div class="box box-info box-solid" style="border:0px;">
									<div class="box-header with-border">
										<h3 class="box-title">Summary Overtime</h3>
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
													<th style="width:70px;">Hours</th>
													<th style="width:70px;">Convertion</th>
													<th style="width:70px;">Overtime Ammount</th>
													<th style="width:70px;">Meals</th>
													<th style="width:70px;">Rapels</th>
													<th style="width:70px;">PPH21</th>
													<th style="width:70px;">Insentive PPH21</th>
													<th style="width:70px;">Final Ammount</th>
												</tr>
											</thead>
											<tbody>
											<?php $no=0;?>
											<?php $__currentLoopData = $tb_slip; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<tr>
													<td><?php $no++;echo $no;?></td>
													<td><?php echo e($dt->divisi); ?></td>
													<td><?php echo number_format($dt->st_hours,2);?></td>
													<td><?php echo number_format($dt->stotal_convertion,2);?></td>
													<td><?php echo number_format($dt->st_ammount,0);?></td>
													<td><?php echo number_format($dt->stotal_meal,0);?></td>
													<td><?php echo number_format($dt->srapel,0);?></td>
													<td><?php echo number_format($dt->spph21,0);?></td>
													<td><?php echo number_format($dt->spph21_insentive,0);?></td>
													<td>
														<?php echo number_format($dt->stotal_paid,0);?>
														<div class="pull-right">
															<a title="Show" href='/Overtimes/Summary/<?php echo e($thn); ?>-<?php echo e($bln); ?>/<?php echo e($dt->dept_id); ?>'><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-folder-open-o"></i></button></a>
														</div>
															
													</td>
												</tr>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</tbody>
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
			window.location.href="/Overtimes/Summary/"+periode+"/0";
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


<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/user/m_overtime/overtimesummaries.blade.php ENDPATH**/ ?>