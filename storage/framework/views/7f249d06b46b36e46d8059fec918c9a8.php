
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
				<div class="box-header">
					<i class="fa fa-user"></i>&nbsp;<label style="font-size:18px;font-weight:normal;"><?php echo e($status); ?></label>
					<div class="pull-right">
						<a href="/Employees/0" class="btn btn-app">
							<i class="fa fa-users"></i> All
						</a>
						<a href="/Employees/Magang" class="btn btn-app">
							<i class="fa fa-user"></i> Magang
						</a>
						<a href="/Employees/Kontrak" class="btn btn-app">
							<i class="fa fa-user"></i> Kontrak
						</a>
						<a href="/Employees/Permanen" class="btn btn-app">
							<i class="fa fa-user"></i> Permanen
						</a>
					</div>
				</div>
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
							<th>Position</th>
							<th>Shift</th>
							<?php if($status!='Permanen'): ?>
							<th>End Contract</th>
							<th>Total Contract</th>
							<?php endif; ?>
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
								<td><?php echo e($dt->position_name); ?></td>
								<td><?php if($dt->id_shift>0)echo $dt->shift_code;else echo "Un Set";?></td>
								<?php if($status!='Permanen'): ?>
								<td><?php echo e($dt->finish_contract); ?></td>
								<td>
									<?php 
										// $start_contract=date('Y-m-d',strtotime('-1 days',strtotime($dt->join_date)));
										// $datetime2 = date_create($dt->finish_contract);
										// $datetime3 = date_create($start_contract);
										// $bulan = date_diff($datetime2, $datetime3);
										// $lama_tahun=$bulan->format('%y');
										// $lama_bulan=$bulan->format('%m');
										// $lama_contract=$lama_tahun*12+$lama_bulan;
										// if($lama_tahun>0)echo $lama_tahun.' Tahun ';if($lama_bulan>0)echo $lama_bulan.' Bulan';
									?>
								</td>
								<?php endif; ?>
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

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/user/m_employee/employees.blade.php ENDPATH**/ ?>