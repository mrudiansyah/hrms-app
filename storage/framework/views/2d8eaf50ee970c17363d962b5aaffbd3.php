
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
							<?php if(request()->user()->hasRole('hr_access')): ?>
								<button class="btn btn-primary btn-md upload-modal" data-id_file="" data-policy_name="" data-description="" data-publish_date="<?php echo e($today); ?>">
									<i class="fa fa-plus"></i> &nbsp;Add New
								</button>
								<button class="btn btn-default btn-md" onclick="window.location.href='/PolicyArsif'">
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
									<th style="width:50px;">No</th>
									<th style="width:70px;">Nama Kebijakan</th>
									<th>Keterangan Singkat</th>
									<th style="width:70px;">Tanggal Terbit</th>
									<th style="width:70px;">Uploaded By</th>
									<th style="width:70px;">Uploaded Time</th>
									<th style="width:50px;">Document</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								<?php $__currentLoopData = $data['tb_policy']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td>
										<?php $no++;echo $no;?>
									</td>
									<td><?php echo e($dt->policy_name); ?></td>
									<td><?php echo e($dt->description); ?></td>
									<td><?php echo e(date('d F Y',strtotime($dt->publish_date))); ?></td>
									<td><?php echo e($dt->uploaded_by); ?></td>
									<td><?php echo e($dt->uploaded_at); ?></td>
									<td>
										<a href="<?php echo e(route('policy.show', ['id' => $dt->id])); ?>" class="btn btn-info btn-xs" target="_blank">
											<i class="fa fa-image"></i>
										</a>
										<button class="btn btn-primary btn-xs download-btn" data-id="<?php echo e($dt->id); ?>">
											<i class="fa fa-download"></i>
										</button>
										<?php if(request()->user()->hasRole('hr_access')): ?>
											<button class="btn btn-success btn-xs" onclick="window.location.href='/PolicyLevel/<?php echo e($dt->id); ?>';">
												<i class="fa fa-user"></i>
											</button>
											<button class="btn btn-warning btn-xs upload-modal" data-id_file="<?php echo e($dt->id); ?>" data-policy_name="<?php echo e($dt->policy_name); ?>" data-description="<?php echo e($dt->description); ?>" data-publish_date="<?php echo e($dt->publish_date); ?>">
												<i class="fa fa-edit"></i>
											</button>
											<button class="btn btn-danger btn-xs nonactive" data-id="<?php echo e($dt->id); ?>">
												<i class="fa fa-upload"></i>
											</button>
											<button class="btn btn-danger btn-xs delete-modal" data-delid="<?php echo e($dt->id); ?>" data-delname="<?php echo e($dt->policy_name); ?>">
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
								<label>Nama Kebijakan</label>
								<input type="hidden" name="id_file" id="id_file" class="form-control">
								<input type="text" name="policy_name" id="policy_name" class="form-control">
							</div>
							<div class="form-group">
								<label>Keterangan Singkat</label>
								<input type="text" name="description" id="description" class="form-control">
							</div>
							<div class="form-group">
								<label>Tanggal terbit</label>
								<input type="date" name="publish_date" id="publish_date" class="form-control">
							</div>
							<div class="form-group">
								<label>Upload PDF File</label>
								<input type="file" name="file_name" id="file_name">								
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
            $("#id_file").val($(this).data('id_file'));
            $("#policy_name").val($(this).data('policy_name'));
            $("#description").val($(this).data('description'));
            $("#publish_date").val($(this).data('publish_date'));
			$('#modal-upload').modal('show');
		});

		$('.modal-footer').on('click', '.uploadFile', function() {
			var formData = new FormData();
			formData.append('id_file', $("#id_file").val());
			formData.append('policy_name', $("#policy_name").val());
			formData.append('description', $("#description").val());
			formData.append('publish_date', $("#publish_date").val());
			
			// Tambahkan file ke FormData
			var fileInput = document.getElementById('file_name');
			if (fileInput.files.length > 0) {
				formData.append('file_name', fileInput.files[0]);
			}

			$.ajaxSetup({
				type: "POST",
				url: "<?php echo e($site); ?>/Policy/Save",
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
					// alert(respond);
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
				url: "<?php echo e($site); ?>/Policy/Delete",
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

		$(document).on('click', '.download-btn', function() {
			var id = $(this).data('id');
			// Membuat form tersembunyi untuk submit
			var form = document.createElement('form');
			form.method = 'POST';
			form.action = "<?php echo e($site); ?>/Policy/Download";
			
			// Tambahkan CSRF token
			var csrf = document.createElement('input');
			csrf.type = 'hidden';
			csrf.name = '_token';
			csrf.value = $('meta[name="csrf-token"]').attr('content');
			form.appendChild(csrf);
			
			// Tambahkan input id
			var input = document.createElement('input');
			input.type = 'hidden';
			input.name = 'id';
			input.value = id;
			form.appendChild(input);
			
			// Tambahkan form ke body dan submit
			document.body.appendChild(form);
			form.submit();
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
	<script>
		$(document).on('click', '.nonactive', function() {
		
			var id = $(this).data('id');
			$.ajax({
				type: "POST",
				url: "<?php echo e($site); ?>/Policy/Nonactive",
				data: {
					id: id,
					_token: $('meta[name="csrf-token"]').attr('content')
				},
				success: function(response) {
					location.reload(); // Refresh halaman setelah berhasil
				},
			});
		});

	</script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/policy/policy.blade.php ENDPATH**/ ?>