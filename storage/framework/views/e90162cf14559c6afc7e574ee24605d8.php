
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
						<h3 class="box-title">Rekap Finger Print</h3>
					</div>
					<div class="box-body">
						<div class="row" style="padding:20px;">
							<div class="col-xs-12">
								<div class="box box-info box-solid" style="border:0px;">
									<div class="box-header with-border">
										<h3 class="box-title">Periode</h3>
										<div class="box-tools pull-right" style="color:#000;">
											<input class="periode" type="date" id="awal" value="<?php echo e($start); ?>"> to 
											<input class="periode" type="date" id="akhir" value="<?php echo e($end); ?>">
											<input class="periode" type="hidden" id="pins" value="<?php echo e($PIN); ?>">
										</div>
									</div>
									<!-- /.box-header -->
									<div class="box-body">
										<table id="tables" class="table table-hover">
											<thead>
												<tr>
													<th style="width:30px;">No</th>
													<th>PIN</th>
													<th>Finger Print</th>
													<th>Employee Name</th>
													<th>Checktime</th>
													<th>NIK</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
											<?php $no=0;?>
											<?php $__currentLoopData = $tb_absen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<tr>
													<td><?php $no++;echo $no;?></td>
													<td><?php echo e($dt->userid); ?></td>
													<td><?php echo e($dt->badgenumber); ?></td>
													<td><?php echo e($dt->name); ?></td>
													<td><?php echo e($dt->checktime); ?></td>
													<?php $assesment=0;$NIK=0;$id_employee='';?>
														<?php
															$PIN=$dt->userid;
															$tgl=date('Y-m-d',strtotime($dt->checktime));
															$host = mysqli_connect("192.168.1.4","ems","123456","db_ems");
															$host1 = mysqli_connect("192.168.1.4","ems","123456","db_ems_covid");
															$qry=mysqli_query($host,"select * from tb_employees where badgenumber='$badgenumber'")or die(mysqli_error($host));
															while($dt1=mysqli_fetch_array($qry)){
																$NIK=$dt1['NIK'];
																$id_employee=$dt1['id'];
															}
															if($NIK==0)echo "<td>&nbsp;</td>";
															else echo "<td>".$NIK."</td>";

														?>	
													<td>
														&nbsp;
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
		$('body').on("change",".periode",function(){
			var pin=document.getElementById('pins').value;
			var awal=document.getElementById('awal').value;
			var akhir=document.getElementById('akhir').value;
			window.location.href="/EMS/Absency/Finger/"+pin+"/"+awal+"/"+akhir;
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


<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/tms/absencyfingers.blade.php ENDPATH**/ ?>