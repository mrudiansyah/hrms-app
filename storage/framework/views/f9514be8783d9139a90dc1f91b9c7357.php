
<?php $__env->startSection('Contents'); ?>
<?php
//$serverName = "192.168.1.4"; //serverName\instanceName
//$connectionInfo = array( "Database"=>"db_ems_memo", "UID"=>"EMS", "PWD"=>"1nd()n3514572");
//$conn = sqlsrv_connect( $serverName, $connectionInfo);

?>
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
			<div class="col-xs-12 col-md-6 col-lg-5">

				<form role="form" action="/PolicyLevel/Save" method="post">
					<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
					<?php echo e(csrf_field()); ?>


					<div class="box box-success" style="background:#FFF;">
						<div class="box-header">
							<i class="fa fa-file-pdf-o"></i>
							<h3 class="box-title"><?php echo e($data['policy_name']); ?></h3>
							<input type="hidden" id="id_policy" name="id_policy" value="<?php echo e($data['id_policy']); ?>">
						</div>
						<div class="box-body">
							<table id="table1" class="table table-hover">
								<thead>
									<tr>
										<th style="width:50px;">No</th>
										<th>Akses Level</th>
										<th>UpdatedBy</th>
										<th>DateTime</th>
									</tr>
								</thead>
								<tbody>
									<?php $no=0;$qty_status=0;?>
									<?php $__currentLoopData = $data['tb_policy_level']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr>
										<td>
											<?php $no++;echo $no;?>
										</td>
										<td>
											<div class="checkbox-item">
												<input type="hidden" name="id_level_<?php echo e($no); ?>" value="<?php echo e($dt->id_level); ?>">
												<input type="checkbox" class="status" id="status_<?php echo e($no); ?>" name="status_<?php echo e($no); ?>" value="1" <?php if($dt->status==1){$qty_status++;echo "checked";}?>>
												<label for="id_tabel2_<?php echo e($no); ?>"><?php echo e($dt->nama_level); ?></label>
											</div>
										</td>
										<td><?php echo e($dt->updated_by); ?></td>
										<td><?php echo e($dt->updated_at); ?></td>
									</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</tbody>
								<tfoot>
									<tr>
										<td></td>
										<td>
											<input type="hidden" id="qty_status" value="<?php echo e($qty_status); ?>">
											<input type="hidden" id="qty_all" value="<?php echo e($no); ?>">
											<i class="fa fa-square-o" id="enableBtn"> <b>All Level</b></i>
											<i class="fa fa-check-square-o" id="disableBtn"> <b>All Level</b></i>
										</td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td></td>
										<td>
											<i class="fa fa-check-square-o" id="disableBtn"> <b>Only HR</b></i>
										</td>
										<td></td>
										<td></td>
									</tr>
								</tfoot>
							</table>
						</div>
						<!-- /.box-body -->
						<div class="box-footer">
							<input type="hidden" id="qty" name="qty" value="<?php echo e($no); ?>">
							<a class="btn btn-md btn-default" onclick="window.location.href='/Policy'">Back</a>
							<button type="submit" class="btn btn-md btn-success pull-right">Update</button>
						</div>
					</div>
				</form>

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
					<input type="text" id="delid">
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
	$(document).ready(function() {
		var qty_status=$('#qty_status').val();
		var qty_all=$('#qty_all').val();
		const element = document.getElementById('enableBtn');
		const element2 = document.getElementById('disableBtn');
		if(qty_status==qty_all){
			element.style.display = 'none';
			element2.style.display = 'block';
		}else{
			element.style.display = 'block';
			element2.style.display = 'none';
		}
		$('#enableBtn').click(function() {
			$('.status').prop('checked', true);
			const element = document.getElementById('enableBtn');
			const element2 = document.getElementById('disableBtn');

			if (element) {
				element.style.display = 'none';
				element2.style.display = 'block';
			}			
		});
		
		$('#disableBtn').click(function() {
			$('.status').prop('checked', false);
			const element = document.getElementById('enableBtn');
			const element2 = document.getElementById('disableBtn');

			if (element2) {
				element.style.display = 'block';
				element2.style.display = 'none';
			}			
		});
	});
	</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/policy/policy_level.blade.php ENDPATH**/ ?>