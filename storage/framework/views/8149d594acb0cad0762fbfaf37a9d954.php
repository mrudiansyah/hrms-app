
<?php $__env->startSection('Contents'); ?>
	<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
	<!-- Contents -->
   	<style>
		#tablesx th {
		border-top: 1px solid #999;
		border-bottom: 1px solid #999;
		background-color: #2F4F4F;
		color: white;
		}	
        .table1 tr:hover {
		  cursor:pointer;
        }
		#tables th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#tables tbody tr:hover{
			cursor:pointer;
		}
		#table2 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table2 tbody tr:hover{
			cursor:pointer;
		}
		#table3 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table3 tbody tr:hover{
			cursor:pointer;
		}
		#table4 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table4 tbody tr:hover{
			cursor:default;
		}
    </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Competence
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-lg-6 col-md-12 col-xs-12">
				<div class="box box-success" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-user"></i>
						<h3 class="box-title">Matric Competence</h3>
						<div class="box-tools pull-right">
							&nbsp;
						</div>
					</div>
					<div class="box-body">
						<div class="pull-right">
							&nbsp;
						</div>
					</div>
					<div class="box-body" style="overflow-x: scroll;">
						<table id="tables" class="table table-hover">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th>NIK</th>
									<th>Name</th>
									<!-- <th>Dept</th> -->
									<th>Position</th>
									<th>Competence</th>
								</tr>
							</thead>
							<tbody id="konten">
								<?php $no=0;?>
								<?php $__currentLoopData = $tb_competence_matric; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td><?php echo e($dt->NIK); ?></td>
									<td><?php echo e($dt->sign_name_1); ?></td>
									<!-- <td><?php echo e($dt->dept_code); ?></td> -->
									<td><?php echo e($dt->position); ?></td>
									<td><?php echo e($dt->ave_item); ?>

										<div class="pull-right">
											<a href="<?php echo e($site); ?>/SkillMatric/Employees/<?php echo e($dt->id_employee); ?>"><button title="Approval" type="button" class="btn btn-primary btn-xs">&nbsp;<i class="fa fa-angle-double-right"></i>&nbsp;</button></a>
										</div>
									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
			</div>
			<?php if($NIK!=''): ?>
			<div class="col-lg-6 col-md-12 col-xs-12">
				<div class="box box-success" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-user"></i>
						<h3 class="box-title"><?php echo e($nama_karyawan); ?> <?php echo e($NIK); ?></h3>
						<div class="box-tools pull-right">
							<a href="<?php echo e($site); ?>/SkillMatric/Print/<?php echo e($data['id_employee']); ?>" target="_blank"><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-print"></i> &nbsp;Preview</button></a>
						</div>
					</div>
					<div class="box-body">
						<div class="pull-right" style="height:45px;">
							&nbsp;
						</div>
					</div>
					<div class="box-body" style="overflow-x: scroll;">
						<table id="table2" class="table table-hover">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th>Periode</th>
									<!-- <th>Total</th> -->
									<th>Competence</th>
									<th>Employee</th>
									<th>Leader 1</th>
									<th>Leader 2</th>
								</tr>
							</thead>
							<tbody id="konten">
								<?php $no=0;?>
								<?php $__currentLoopData = $tb_competence_matric_1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td><?php echo e($dt->periode); ?></td>
									<!-- <td><?php echo e($dt->sum_item); ?></td> -->
									<td><?php echo number_format($dt->ave_item,2);?></td>
									<td>
										<?php if($dt->sign_status_1==1): ?>
											<i class="fa fa-check-square-o"></i>
										<?php else: ?>
											<?php if($id_employee==$dt->id_employee){
												echo "<b id='e".$dt->id."' style='color:blue;' class='signup' data-id='".$dt->id."' data-kolom='1'><i class='fa fa-square-o'></i></b>";
											}else{
												echo "<i class='fa fa-square-o signup'></i>";
											}?>
										<?php endif; ?>
										<!-- <?php echo e($dt->sign_name_1); ?> -->
									</td>
									<td>
										<?php if($dt->sign_status_2==1): ?>
											<i class="fa fa-check-square-o"></i>
										<?php else: ?>
											<?php if($id_employee==$dt->sign_id_2&&$dt->sign_status_1==1){
												echo "<b id='a".$dt->id."' style='color:blue;' class='signup' data-id='".$dt->id."' data-kolom='2'><i class='fa fa-square-o'></i></b>";
											}else{
												echo "<i class='fa fa-square-o signup' data-id='".$dt->id."' data-kolom='2'></i>";
											}?>
										<?php endif; ?>
										<!-- <?php echo e($dt->sign_name_2); ?> -->
									</td>
									<td>
										<?php if($dt->sign_status_3==1): ?>
											<i class="fa fa-check-square-o"></i>
										<?php else: ?>
											<?php if($id_employee==$dt->sign_id_3&&$dt->sign_status_2==1){
												echo "<b id='l".$dt->id."' style='color:blue;' class='signup' data-id='".$dt->id."' data-kolom='3'><i class='fa fa-square-o'></i></b>";
											}else{
												echo "<i class='fa fa-square-o signup'></i>";
											}?>
										<?php endif; ?>
										<!-- <?php echo e($dt->sign_name_3); ?> -->
									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<?php endif; ?>
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
			'paging'      : false,
			'lengthChange': true,
			'searching'   : false,
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
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<script>
		$(document).on('click', '.signup', function() {
			if(confirm("Yakin?")){
				var sekarang = new Date();
				var datetime = sekarang.getFullYear() + '-' + 
					String(sekarang.getMonth() + 1).padStart(2, '0') + '-' + 
					String(sekarang.getDate()).padStart(2, '0') + ' ' + 
					String(sekarang.getHours()).padStart(2, '0') + ':' + 
					String(sekarang.getMinutes()).padStart(2, '0') + ':' + 
					String(sekarang.getSeconds()).padStart(2, '0');
				var a = $(this).data('id');
				var b = $(this).data('kolom');
				var dynamicVars = {};
				dynamicVars['sign_status_' + b] = '1'; 				
				dynamicVars['sign_date_' + b] = datetime; 				
				var datas = Object.assign({}, { id: a,kolom:b }, dynamicVars);

				
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					type: 'POST',
					url: '<?php echo e($site); ?>/SkillMatric/Sign',
					data: datas,
					success: function(respond){
						//alert(respond);
						location.reload();
					},
					error: function(xhr, status, error) {
						console.error('Error:', error);
						alert('Terjadi kesalahan: ' + error);
					}
				});
			}
		});
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\skill_matric\competence_matric.blade.php ENDPATH**/ ?>