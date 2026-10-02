
<?php $__env->startSection('Contents'); ?>
   <!-- Contents -->
<div class="content-wrapper">
	<section class="content-header">
		<h1 onclick="">
			Finger Print
			<small>Record</small>
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
		<div class="box box-primary">
			<div class="box-header with-border">
				<b style="font-size:21px;"><?php echo ucwords(strtolower($employee_name));?></b>
			<div class="box-tools pull-right">
				<a href="/Absency/Finger/<?php echo e($badgenumber); ?>/<?php echo e($awal); ?>/<?php echo e($akhir); ?>" class="btn btn-info btn-md"><i class="fa fa-clock-o"></i> &nbsp;Database iClock</a>
			</div>
			<!-- /.box-tools -->
			</div>
			<!-- /.box-header -->
			<div class="box-body" style="padding:20px;overflow-x:scroll;">

				<table id="tables" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>No</th>
							<th>Date</th>
							<th>Day</th>
							<th>Group</th>
							<th>Schedule</th>
							<th>
								<i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
								Checkin
							</th>
							<th>
								<i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
								Checkout
							</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						<?php $__currentLoopData = $checktime_records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php echo e($record['day']); ?></td>
									<td><?php echo e($record['date']); ?></td>
									<td><?php echo e(date('l',strtotime($record['date']))); ?></td>
									<td><?php echo e($record['shift_code']); ?></td>
									<td>
										<?php if($record['check_in']!=0&&$record['check_out']!=0): ?>
											<?php echo e(date('H:i',strtotime($record['check_in']))); ?> - <?php echo e(date('H:i',strtotime($record['check_out']))); ?>

										<?php endif; ?>
									</td>
									<td><?php if($record['checkin_act']!=0): ?><?php echo e(date('H:i:s',strtotime($record['checkin_act']))); ?><?php endif; ?></td>
									<td><?php if($record['checkout_act']!=0): ?><?php echo e(date('H:i:s',strtotime($record['checkout_act']))); ?><?php endif; ?></td>
									<td>
										<?php if($record['status']=='Present'): ?><span class="badge bg-green"><?php echo e($record['status']); ?></span>
										<?php elseif($record['status']=='Absent'): ?><span class="badge bg-light"><?php echo e($record['status']); ?></span>
										<?php elseif($record['status']=='Holiday'): ?><span class="badge bg-red"><?php echo e($record['status']); ?></span>
										<?php elseif($record['status']=='Leave'): ?><span class="badge bg-blue"><?php echo e($record['status']); ?></span>
										<?php elseif($record['status']=='Change'): ?><span class="badge bg-yellow"><?php echo e($record['status']); ?></span><?php endif; ?>
									</td>
								</tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</tbody>
				</table>

			</div>
		
		</div>
	</section>
</div>
    <!-- /.Content -->

	<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
	<!-- page script Tabel-->
	<script>
		$(document).ready(function() {
			var table = $('#tables').DataTable({
				'paging'      : true,
				'lengthChange': false,
				'searching'   : true,
				'ordering'    : true,
				'info'        : true,
				"pageLength"  : 50,
				'autoWidth'   : false,
				"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]]
        //"iDisplayLength": 50
				//dom: 'Bfrtip',buttons: ['print']
			});
		
			new $.fn.dataTable.Buttons( table, {
				buttons: ['copy', 'excel', 'print']
			} );
		
			table.buttons( 0, null ).container().prependTo(
				table.table().container()
			);
		} );


	</script>
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.update-modal', function() {
			$('#idemployee').val($(this).data('idemployee'));
			$('#implementasi').val($(this).data('implement'));
			$('#modal-update').modal('show');
		});
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/tms/employee_checktime.blade.php ENDPATH**/ ?>