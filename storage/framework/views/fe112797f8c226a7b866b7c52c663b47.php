
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
			Employees
		</h1>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-xs-12">
			<div class="box box-primary" style="background:#FFF;">
				<div class="box-body">
				<table id="tables" class="table table-bordered">
					<thead>
						<tr>
							<th>No</th>
							<th>NIK</th>
							<th>Employee Name</th>
							<th>Gender</th>
							<th>Join Date</th>
							<th>Dept</th>
							<th>Cost Center</th>
							<th>Segment</th>
							<th>Position</th>
							<th>Shift</th>
							<th>Direct leader</th>
						</tr>
					</thead>
					<tbody>
						<?php $no=0;?>
						<?php $__currentLoopData = $tb_employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr onclick=window.location.href='/Employee/<?php echo $dt->id;?>/<?php echo $dt->PIN;?>'>
								<td><?php $no++;$pin=$dt->PIN;$userid=$dt->id;echo $no;?></td>
								<td><?php echo e($dt->NIK); ?></td>
								<td><?php echo e($dt->employee_name); ?></td>
								<td><?php echo e($dt->gender); ?></td>
								<td><?php echo e($dt->join_date); ?></td>
								<td><?php echo e($dt->dept_code); ?></td>
								<td><?php echo e($dt->cc_code); ?></td>
								<td><?php echo e($dt->segment_name); ?></td>
								<td><?php echo e($dt->position_name); ?></td>
								<td><?php if($dt->id_shift>0)echo $dt->shift_code;else echo "Un Set";?></td>
								<td>
									<?php echo e($dt->leader_name); ?>

									<div class="pull-right">
										<a title="Show" href='/Leader/<?php echo $dt->id;?>'><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i></button></a>
									</div>
								</td>
							</tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</tbody>
				</table>
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
		$(document).ready(function() {
		  var table = $('#tables').DataTable({
			'paging'      : true,
			'lengthChange': false,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
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
  <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/user/m_employee/leader.blade.php ENDPATH**/ ?>