
<?php $__env->startSection('Contents'); ?>
   <!-- Contents -->
   <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
   <?php $user=Auth::user()->name;?>
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

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-xs-12 col-md-12 col-lg-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-file-pdf-o"></i>
						<h3 class="box-title">Kebijakan Direksi</h3>
						<div class="box-tools pull-right">
							<?php $today=date('Y-m-d');?>
							<?php if(request()->user()->hasRole('legal')): ?>
								<button class="btn btn-default btn-md" onclick="window.location.href='/PolicyControl'">
									<i class="fa fa-home"></i> &nbsp;Back
								</button>
							<?php endif; ?>
						</div>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12">
								&nbsp;
							</div>
						</div>
						<table id="tables" class="table table-hover">
							<thead>
								<tr>
									<th>No</th>
									<th>Category</th>
									<th>Document Name</th>
									<th>Expired Date</th>
									<th>Status</th>
									<th>Remark</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								<?php $__currentLoopData = $data['tb_legal_permit']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td>
										<?php $no++;echo $no;?>
									</td>
									<td><?php echo e($dt->category); ?></td>
									<td><?php echo e($dt->permit_name); ?></td>
									<td><?php echo e(date('d F Y',strtotime($dt->expiry_date))); ?></td>
									<td><?php echo e($dt->status); ?></td>
									<td><?php echo e($dt->description); ?></td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
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

<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
	<script>
		$(document).ready(function() {
			var table = $('#tables').DataTable({
			'paging'      : false,
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

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/policy/policy_control_arsif.blade.php ENDPATH**/ ?>