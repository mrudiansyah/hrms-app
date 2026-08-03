
<?php $__env->startSection('Contents'); ?>
	<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
	<?php $__currentLoopData = $tb_training_participant; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<?php if($dt->free_test!='')$doc='1';else $doc=0;?>
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	<!-- Main content -->
	<section class="content">
	<div class="row">
		<div class="col-md-12" id="prev-doc">
		</div>
		<div class="col-lg-4 col-md-6 col-xs-12">
			<div class="box box-primary" style="background:#FFF;">
				<div class="box-header">
					<i class="fa fa-list"></i>
					<h3 class="box-title">Training Actual</h3>
					<div class="box-tools pull-right">
						<a href="/Training/Invitation" title="Schedule" type="button" class="btn btn-default btn-xs"><i class="fa fa-angle-double-left"></i> &nbsp; Kembali</a>
					</div>
				</div>
				<div class="box-body">
					<table class="table table-hover">
						<thead>
							<tr>
								<th style="width:30px;">No</th>
								<th>Training Name</th>
								<th>Category</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody>
							<?php $no=0;?>
							<?php $__currentLoopData = $tb_training_actual; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td><?php $no++;echo $no;?></td>
								<td><?php echo e($dt->training_name); ?></td>
								<td><?php echo e($dt->skill_type); ?></td>
								<td><?php echo date('d-M-Y H:i',strtotime($dt->tanggal_aktual.' '.$dt->start_aktual)).' ~ '.date('H:i',strtotime($dt->tanggal_aktual.' '.$dt->finish_aktual));?></td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
				</div>
				<!-- /.box-body -->
				<div class="box-header">
					<i class="fa fa-folder-o"></i>
					<h3 class="box-title">Materi</h3>
					<div class="box-tools pull-right">
						<div class="box-tools">
							<a href="/FreeTest/<?php echo e($id_participant); ?>" <?php if($data['pre_test']!='')echo "disabled";?> title="Free Test" type="button" class="btn btn-primary btn-xs">Start Pre Test</a>
						</div>
					</div>
				</div>
				<div class="box-body">
					<table id="table4" class="table table-hover">
						<thead>
							<tr>
								<th style="width:30px;">No</th>
								<th>File Name</th>
							</tr>
						</thead>
						<tbody id="supporting">
							<?php $no=0;?>
							<?php $__currentLoopData = $tb_related_document; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td><?php $no++;echo $no;?></td>
								<td>
									<?php echo e($dt->file_name); ?>

									<input type="hidden" name="doc_id" id="doc_id" value=<?php echo e($doc); ?>>
									<?php if($doc==1): ?>
									<div class="pull-right">
										<?php
										$panjang=strlen($dt->file_name);
										$mulai=$panjang-4;
										$extensi=substr($dt->file_name,$mulai,4);
										if($extensi=='.mp4'||$extensi=='.pdf'||$extensi=='pptx'){?>
											
											<button class="btn btn-primary btn-xs" id="preview" onclick="GetPreview(<?php echo e($dt->id_doc); ?>)">Lihat Materi</button>
										<?php }?>
										<!-- 
										<a href="/Training/Document/Download/<?php echo e($dt->id); ?>" title="Download" type="button" class="btn btn-info btn-xs"><i class="fa fa-download"></i></a>
										-->
									</div>
									<?php endif; ?>
								</td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
				</div>
				<!-- /.box-body -->
				<div class="box-body">
					<div class="pull-right">
						&nbsp;
					</div>
				</div>
				<div class="box box-primary" style="background:#FFF;">
				<div class="box-header">
					<i class="fa fa-graduation-cap"></i>
					<h3 class="box-title">Evaluation</h3>
					<div class="box-tools pull-right">
						<div class="box-tools">
							<a href="/PostTest/<?php echo e($id_participant); ?>" <?php if(($data['pre_test']==''||$data['post_test']!='')&&$data['grade_status']==1)echo "disabled";?> title="Post Test" type="button" class="btn btn-primary btn-xs">Start Post Test</a>
						</div>
					</div>
				</div>
				<div class="box-body" style="overflow-x: scroll;">
					<table id="table3" class="table table-hover">
						<thead>
							<tr>
								<th>Nilai Free Test</th>
								<th>Nilai Post Test</th>
								<th>Nilai Minimum</th>
								<th>Hasil Training</th>
							</tr>
						</thead>
						<tbody id="konten">
							<?php $no=0;?>
							<?php $__currentLoopData = $tb_training_participant; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td>
									<?php echo e($dt->free_test); ?>

								</td>
								<td>
									<?php echo e($dt->post_test); ?>

									<?php if($dt->progress=='1')echo "<i class='fa fa-angle-double-up' style='color:green;'></i>";?>
									<?php if($dt->progress=='-1')echo "<i class='fa fa-angle-double-down' style='color:red;'></i>";?>
									<?php if($dt->progress=='0')echo "<i class='fa fa-frown-o' style='color:blue;'></i>";?>
								</td>
								<td><?php echo e($dt->passing_grade); ?></td>
								<td>
									<input type="hidden" name="grade" id="grade" value="<?php echo e($dt->grade_status); ?>">
									<input type="hidden" name="post_test" id="post_test" value="<?php echo e($dt->post_test); ?>">
									<?php 
										if($dt->grade_status==1)echo "<label class='label label-success'>Lulus</label>";
										elseif($dt->grade_status==0&&$dt->post_test!='')echo "<label class='label label-danger'>Gagal</label>";
									?>
									<div class="pull-right">
										&nbsp;
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
		</div>
	</div>
	<!-- /.row -->
	</section>
	<!-- /.content -->


<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
	<!-- page script Tabel-->
	<script type="text/Javascript">
	$(document).ready(function (e) {
		var grade = $("#grade").val();
		var post_test = $("#post_test").val();
		if(grade == 1){
			swal(
				"Success",
				"Your test is passed",
				"success",
			)
		}else if(grade == 0 && post_test != ''){
			swal(
				'Information',
				"You're not passed, Better luck next time",
				"warning",
			)		
		}else{
		}
	/* Get the documentElement (<html>) to display the page in fullscreen */
	var doc_id = $("#doc_id").val();
	if(doc_id > 0){
		// openFullscreen();
		$("#fullscreen").click();
	}
	/* View in fullscreen */
	
	$(function () {
		$('#table2').DataTable({
		'paging'      : false,
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		"pageLength"  : 10,
		'autoWidth'   : false,
		})
	})
	
});
	function GetPreview(id_doc){
		var token =  $('meta[name="csrf-token"]').attr('content')
		var data = { _token : token , id_doc : id_doc}
		$.ajax({
			
			type: "POST",
			url: "<?php echo e(url( 'Training.DocPreview')); ?>",
			data: data,
			success: function (data) {
				$("#prev-doc").html(data);
				var elem = document.getElementById("prev-doc");
				openFullscreen(elem);
			}
		});
	}
	function openFullscreen(elem) {
		if (elem.requestFullscreen) {
			elem.requestFullscreen();
		} else if (elem.webkitRequestFullscreen) { /* Safari */
			elem.webkitRequestFullscreen();
		} else if (elem.msRequestFullscreen) { /* IE11 */
			elem.msRequestFullscreen();
		}
		}

		/* Close fullscreen */
		function closeFullscreen() {
		if (document.exitFullscreen) {
			document.exitFullscreen();
		} else if (document.webkitExitFullscreen) { /* Safari */
			document.webkitExitFullscreen();
		} else if (document.msExitFullscreen) { /* IE11 */
			document.msExitFullscreen();
		}
		}

		document.addEventListener('fullscreenchange', exitHandler);
		document.addEventListener('webkitfullscreenchange', exitHandler);
		document.addEventListener('mozfullscreenchange', exitHandler);
		document.addEventListener('MSFullscreenChange', exitHandler);

		function exitHandler() {
			if (!document.fullscreenElement && !document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
				$("#prev-doc").html("");

				///fire your event
			}
		}  
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
		function previewDocs(id){

		}
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/training/training_participant.blade.php ENDPATH**/ ?>