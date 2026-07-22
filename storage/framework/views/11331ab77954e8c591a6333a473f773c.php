
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
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Setup Utility
				<small>&nbsp;</small>
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-xs-12 col-md-12 col-lg-12">
					<div class="box box-danger">
						<div class="box-header">
							<h3 class="box-title">Lock Transaction</h3>
						</div>
						<!-- /.box-header -->
						<div class="box-body table-responsive">
						<table class="table table-hover">
							<tr style="background:#CCCCCC;">
								<th style="width:3px;">No</th>
								<th style="width:60px;">STATUS</th>
								<th style="width:60px;">LIMIT</th>
								<th style="width:180px;">FEATURE</th>
								<th>DESCRIPTION</th>
							</tr>
							<?php $no=0;?>
							<?php $__currentLoopData = $data['tb_utilities']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td style="text-align:center;">
										<?php if($dt->status==1): ?>
											<i class="update_approval fa fa-check-square-o" data-id="<?php echo e($dt->id); ?>" data-status="0"></i>
										<?php else: ?>
											<i class="update_approval fa fa-square-o" data-id="<?php echo e($dt->id); ?>" data-status="1""></i>
										<?php endif; ?>
									</td>
									<td><input style="width:60px;" type="number" class="limit_transaksi" id="limit_transaksi_<?php echo e($dt->id); ?>" data-id="<?php echo e($dt->id); ?>" value="<?php echo e($dt->limit_transaksi); ?>" <?php if($dt->limit_transaksi==0||$dt->status==0)echo "disabled";?>></td>
									<td><?php echo e($dt->atribut); ?></td>
									<td><?php echo e($dt->description); ?></td>
								</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</table>
						</div>
						<!-- /.box-body -->
					</div>
					<!-- /.box -->
				</div>
			</div>
			<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->
	<?php if($errors->any()): ?>
		<div class="alert alert-danger alert-dismissible" style="position:absolute;width:350px;right:10px;top:65px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-warning"></i> Saving Failed Alert!</h4>
				<?php echo e($errors); ?>

		</div>
	<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
	<script>
		$(document).on('click', '.update_approval', function() {
			//alert(c);
            var a=$(this).data('id');
            var b=$(this).data('status');

			if (confirm('Apakah Anda yakin?')) {
				$('#modal-loading').modal('show');
				var datas = {
					id:a,
					status:b
				}
				$.ajaxSetup({
					type:"POST",
					url: "<?php echo e($site); ?>/Setup/Update",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					data:datas,
					success: function(respond){
						//alert(respond);
						location.reload();
					}
				})
			}
		});
	</script>
	<script>
		$(document).on('click', '.limit_transaksi', function() {
			//alert(c);
            var a=$(this).data('id');
            var b='#limit_transaksi_'+a;
			var c=$(b).val();
			if (confirm('Apakah Anda yakin?')) {
				$('#modal-loading').modal('show');
				var datas = {
					id:a,
					limit_transaksi:c
				}
				$.ajaxSetup({
					type:"POST",
					url: "<?php echo e($site); ?>/Setup/UpdateLimit",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					data:datas,
					success: function(respond){
						//alert(respond);
						location.reload();
					}
				})
			}
		});
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
	
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/setup/setup_hr.blade.php ENDPATH**/ ?>