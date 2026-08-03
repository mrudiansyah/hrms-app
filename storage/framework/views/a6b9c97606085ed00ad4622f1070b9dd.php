
<?php $__env->startSection('Contents'); ?>
	<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-lg-5 col-md-6 col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-list"></i>
						<h3 class="box-title">My Training</h3>
						<div class="box-tools pull-right">
							&nbsp;
						</div>
					</div>
					<div class="box-body">
						<table id="tables" class="table">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th>Training</th>
									<th>Date</th>
									<th>Time</th>
									<th>Status Training</th>
									<th>&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								<?php $__currentLoopData = $tb_training_invitation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td><?php echo e($dt->training_name); ?></td>
									<td><?php echo e($dt->tanggal); ?></td>
									<td>
										<?php 
											if($dt->start!=''&&$dt->finish!=''){
												echo date('H:i',strtotime($dt->start));
												echo "~";
												echo date('H:i',strtotime($dt->finish));
											}
										?>
									</td>
									<td>
										<?php 
											if($dt->grade_status==1)echo "<label class='label label-success'>Lulus</label>";
											elseif($dt->grade_status==0&&$dt->post_test!='')echo "<label class='label label-danger'>Gagal</label>";
										?>
									</td>
									<td>
										<?php if($dt->id_test>0): ?>
											<div class="pull-right">
												<a href="/Training/Schedule/<?php echo e($dt->id); ?>" title="Participant" type="button" class="participant btn btn-primary btn-xs"><i class="fa fa-folder-o"></i> Detail</a>
											</div>
										<?php endif; ?>
									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->


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
	</script>
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<script>
		document.addEventListener("contextmenu", function(e){
			e.preventDefault();
		}, false);
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/training/training_invitation.blade.php ENDPATH**/ ?>