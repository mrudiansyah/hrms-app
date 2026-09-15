
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
						<h3 class="box-title">Legalization Monitoring</h3>
						<div class="box-tools pull-right">
							<?php $today=date('Y-m-d');?>
							<?php if(request()->user()->hasRole('legal')): ?>
								<button class="btn btn-primary btn-md upload-modal" data-id_file="" data-policy_name="" data-description="" data-publish_date="<?php echo e($today); ?>">
									<i class="fa fa-plus"></i> &nbsp;Add New
								</button>
								<button class="btn btn-default btn-md" onclick="window.location.href='/PolicyArsifControl'">
									<i class="fa fa-book"></i> &nbsp;Arsif
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
									<th>Remark</th>
									<th>Status</th>
									<th>Remain</th>
									<th>Action</th>
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
									<td><?php echo e($dt->description); ?></td>
									<td>
										<?php
											$tgl1 = new DateTime( $today);
											$tgl2 = new DateTime($dt->expiry_date);
											$diffdays = $tgl2->diff($tgl1)->days;
											if($dt->expiry_date<=$today){
												$teks='Expired';
												$status='red';
												$progress=0;
											}elseif($diffdays<=$dt->critical_day){
												$teks='Critical';
												$status='red';
												$progress=number_format($diffdays/365*100,0);
											}elseif($diffdays<=$dt->warning_day){
												$teks='Warning';
												$status='yellow';
												$progress=number_format($diffdays/365*100,0);
											}else{
												$teks='Active';
												$status='green';
												$progress=number_format($diffdays/365*100,0);
											}
										?>
										<b class='label pull-left bg-<?php echo e($status); ?>'><?php echo e($teks); ?></b>
									</td>
									<td>
										<div class="progress">
											<div class="progress-bar progress-bar-<?php echo e($status); ?>" role="progressbar" aria-valuenow="<?php echo e($diffdays); ?>" aria-valuemin="0" aria-valuemax="365" style="width: <?php echo e($progress); ?>%">
											<span class="sr-only">80% Complete</span>
											</div>
										</div>
									</td>
									<td>
										<?php if(request()->user()->hasRole('legal')): ?>
											<button class="btn btn-warning btn-xs upload-modal" data-id="<?php echo e($dt->id); ?>" data-category="<?php echo e($dt->category); ?>" data-permit_name="<?php echo e($dt->permit_name); ?>" data-description="<?php echo e($dt->description); ?>" data-expiry_date="<?php echo e($dt->expiry_date); ?>" data-status="<?php echo e($dt->status); ?>">
												<i class="fa fa-edit"></i>
											</button>
											<?php if($dt->file_name): ?>
												<a href="/Show/<?php echo e($dt->file_name); ?>" target="_blank" class="btn btn-info btn-xs">
													<i class="fa fa-file-pdf-o"></i>
												</a>
											<?php endif; ?>
											<button class="btn btn-danger btn-xs delete-modal" data-delid="<?php echo e($dt->id); ?>" data-delname="<?php echo e($dt->permit_name); ?>">
												<i class="fa fa-trash"></i>
											</button>
										<?php endif; ?>
									</td>
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
	<div class="modal fade" id="modal-delete">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Delete Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Delete : <b id="delname"></b> ?
					<input type="hidden" id="delid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left delete" data-dismiss="modal">Yes, Delete</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-upload">
		<div class="modal-dialog box box-primary" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Form Upoad File</h4>
				</div>
				<div class="modal-body">
					<form role="form" action="" method="post">
						<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
						<?php echo e(csrf_field()); ?>

						<div class="box-body">
							<div class="form-group">
								<label>Category</label>
								<select id="category" class="form-control pilihan">
									<?php $__currentLoopData = $data['tb_category']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($dt->category); ?>"><?php echo e($dt->category); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
							</div>
							<div class="form-group">
								<label>Document Name</label>
								<input type="text" name="permit_name" id="permit_name" class="form-control">
								<input type="hidden" name="id_file" id="id_file" class="form-control">
							</div>
							<div class="form-group">
								<label>Expired Date</label>
								<input type="date" name="expiry_date" id="expiry_date" class="form-control">
							</div>
							<div class="form-group">
								<label>Description</label>
								<input type="text" name="description" id="description" class="form-control">
							</div>
							<div class="form-group">
								<label>Status</label>
								<select id="status" class="form-control pilihan">
									<option value="Active">Active</option>
									<option value="Warning">Warning</option>
									<option value="Critical">Critical</option>
									<option value="Expired">Expired</option>
									<option value="OnProccess">OnProccess</option>
									<option value="Extended">Extended</option>
								</select>
							</div>
							<div class="form-group">
								<label>File Upload (PDF/Image)</label>
								<input type="file" name="file_upload" id="file_upload" class="form-control" accept="application/pdf,image/*">
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary pull-left uploadFile" data-dismiss="modal">Save</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
	<script type="text/javascript">
		$(document).on('click', '.upload-modal', function() {
            $("#id_file").val($(this).data('id'));
            $("#category").val($(this).data('category'));
             $("#permit_name").val($(this).data('permit_name'));
            $("#description").val($(this).data('description'));
            $("#expiry_date").val($(this).data('expiry_date'));
            $("#status").val($(this).data('status'));
			$("#file_upload").val(""); // Reset file input
			$('#modal-upload').modal('show');
		});

		$('.modal-footer').on('click', '.uploadFile', function() {
			var formData = new FormData();
			formData.append('id', $("#id_file").val());
			formData.append('category', $("#category").val());
			formData.append('permit_name', $("#permit_name").val());
			formData.append('description', $("#description").val());
			formData.append('expiry_date', $("#expiry_date").val());
			formData.append('status', $("#status").val());
			
			var fileInput = document.getElementById('file_upload');
			if (fileInput.files.length > 0) {
				formData.append('file_upload', fileInput.files[0]);
			}
			
			$.ajaxSetup({
				type: "POST",
				url: "<?php echo e($site); ?>/PolicyControl/Save",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			$.ajax({
				data: formData,
				processData: false,  // Penting untuk FormData
				contentType: false,  // Penting untuk FormData
				success: function(respond) {
					//alert(respond);
					location.reload();
				}
			});
		});
		// Delete Data
		$(document).on('click', '.delete-modal', function() {
			$('#delid').val($(this).data('delid'));
			$('#delname').text($(this).data('delname'));
			$('#modal-delete').modal('show');
		});

		$('.modal-footer').on('click', '.delete', function() {
			var id = $('#delid').val();
			$.ajax({
				type: "POST",
				url: "<?php echo e($site); ?>/PolicyControl/Delete",
				data: {
					id: id,
					_token: $('meta[name="csrf-token"]').attr('content')
				},
				dataType: 'json', // Tambahkan ini untuk memastikan response diparse sebagai JSON
				success: function(response) {
					if (response.success) {
						alert(response.message);
						location.reload(); // Refresh halaman setelah berhasil
					} else {
						alert(response.message);
					}
				},
				error: function(xhr, status, error) {
					alert('Terjadi kesalahan: ' + xhr.responseText);
				}
			});
		});	
	</script>
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


<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/policy/policy_control.blade.php ENDPATH**/ ?>