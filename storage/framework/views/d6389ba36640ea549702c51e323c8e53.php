
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
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Formulir Pengajuan Pembukaan Akses EMS	
				<small>&nbsp;</small>

			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-xs-12 col-md-12 col-lg-6">
					<div class="box box-primary" style="background:#FFF;">
						<div class="box-header">
							<i class="fa fa-user"></i>
							<h3 class="box-title">Data Karyawan</h3>
							<?php $spesial=0;?>
						</div>
						<div class="box-body">
							<div class="row">
								<?php $akses2=0;$salah=0;?>
								<?php $__currentLoopData = $data['table2']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php $dept=$dt->department;?>
									<div class="col-xs-12 col-md-12 col-lg-6">
										<div class="form-group">
											<label>NIK</label>
											<input type="text" value="<?php echo e($dt->nik); ?>" name="NIK" id="nik" class="form-control" disabled>
											<input type="hidden" id="id_memo" name="id_memo" value="<?php echo e($dt->id_memo); ?>">
										</div>
										<div class="form-group">
											<label>Employee Name</label>
											<input type="text" value="<?php echo e($dt->nama); ?>" name="employee_name" id="employeename" class="form-control" disabled>
										</div>
										<div class="form-group">
											<label>Position</label>
											<input type="text" value="<?php echo e($dt->position); ?>" name="position" id="position" class="form-control" disabled>
										</div>
										<div class="form-group">
											<label>Department</label>
											<input type="text" value="<?php echo e($dt->department); ?>" name="department" id="department" class="form-control" disabled>
										</div>
										<div class="form-group">
											<label>Tanggal Permohonan</label>
											<input type="datetime-local" value="<?php echo e(date('Y-m-d H:i:s',strtotime($dt->created_at))); ?>" name="tgl_permohonan" id="tgl_permohonan" class="form-control" disabled>
										</div>
									</div>
									<div class="col-xs-12 col-md-12 col-lg-6">
										<div class="form-group">
											<label>Tanggal Mulai</label>
											<input type="datetime-local" value="<?php echo e(date('Y-m-d H:i:s',strtotime($dt->start))); ?>" name="tgl_mulai" id="tgl_mulai" class="form-control" disabled>
										</div>
										<div class="form-group">
											<label>Tanggal Selesai</label>
											<input type="datetime-local" min="<?php echo e(date('Y-m-d H:i:s',strtotime($dt->start))); ?>" value="<?php echo e(date('Y-m-d H:i:s',strtotime($dt->end))); ?>" name="tgl_selesai" id="tgl_selesai" class="form-control" <?php if($data['status_draft']==0&&$data['progress']!=3)echo "disabled";?>>
										</div>
										<div class="form-group">
											<label>Jenis Akses</label>
											<div class="row">
												<div class="col-lg-5">
													<div class="checkbox">
														<label>
															<input type="checkbox" name="spl" id="spl" <?php if($dt->e_spl==1)echo "checked";?> <?php if($data['status_draft']==0&&$data['progress']!=3)echo "disabled";?>>
															SPL/Assignment
														</label>
													</div>
												</div>
												<div class="col-lg-3">
													<div class="checkbox">
														<label>
															<input type="checkbox" name="leave" id="leave" <?php if($dt->e_leave==1)echo "checked";?> <?php if($data['status_draft']==0&&$data['progress']!=3)echo "disabled";?>>
															Leave
														</label>
													</div>
												</div>
												<div class="col-lg-4">
													<div class="checkbox">
														<label>
															<input type="checkbox" name="permit" id="permit" <?php if($dt->e_permit==1)echo "checked";?> <?php if($data['status_draft']==0&&$data['progress']!=3)echo "disabled";?>>
															Permit
														</label>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label>Alasan Permohonan</label>
											<textarea rows="4" name="alasan" id="alasan" class="form-control"><?php echo e($dt->alasan); ?></textarea>
										</div>
										<div class="form-group">
											&nbsp;
										</div>
										<div class="form-group">

											<div class="box-tools pull-right">
												<button type="button" class="btn btn-default btn-md" onclick="window.location.href='/GeneralMemo/2';"><i class="fa fa-home"></i> &nbsp;Back</button>&nbsp;
												<button id="save" type="button" class="btn btn-success btn-md update_draft pull-right" data-id_memo="<?php echo e($data['id_memo']); ?>" data-status="0" <?php if($data['status_draft']==0)echo "disabled";?>><i class="fa fa-floppy-o"></i> &nbsp;Save</button>&nbsp;
												<?php if($data['status_draft']==1): ?>
													&nbsp;
													<!-- <button type="button" class="btn btn-success btn-md update_draft pull-right" data-id_memo="<?php echo e($data['id_memo']); ?>" data-status="0" <?php if($data['status_draft']==1)echo "disabled";?>><i class="fa fa-floppy-o"></i> &nbsp;Save</button> -->
													<!-- <a class="btn btn-info btn-md" href="<?php echo e(route('memos.create',['id_memo' => $data['id_memo']])); ?>"><i class="fa fa-plus"></i> &nbsp;Entry</a> -->
												<?php else: ?>
													<?php if($data['admin']==$data['created_by']): ?>
														<button type="button" class="btn btn-warning btn-md update_draft" data-id_memo="<?php echo e($data['id_memo']); ?>" data-status="1"><i class="fa fa-refresh"></i> &nbsp;Roll Back</button>
													<?php endif; ?>
													<!-- <a class="btn btn-default btn-md" href="/GeneralMemo/Preview/<?php echo e($data['id_memo']); ?>" target="_blank"><i class="fa fa-print"></i> &nbsp;preview</a> -->
												<?php endif; ?>
											</div>

											<!-- <?php if($data['status_draft']==1): ?>
												<button type="button" class="btn btn-success btn-md update_draft pull-right" data-id_memo="<?php echo e($data['id_memo']); ?>" data-status="0"><i class="fa fa-floppy-o"></i> &nbsp;Save</button>
											<?php else: ?>
												<button type="button" disabled class="btn btn-success btn-md update_draft pull-right" data-id_memo="<?php echo e($data['id_memo']); ?>" data-status="0"><i class="fa fa-floppy-o"></i> &nbsp;Save</button>
											<?php endif; ?> -->
										</div>
									</div>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</div>

						</div>
						<!-- /.box-body -->
					</div>
					<div class="box box-warning" style="background:#FFF;">
						<div class="box-header">
							<i class="fa fa-edit"></i>
							<h3 class="box-title">Approval</h3>
						</div>
						<div class="box-body">
							<?php if($data['status_draft']=='0'): ?>
								<div class="row">
									<div class="col-xs-12 col-md-4 col-lg-4">
										<div class="box box-default">
										<?php $__currentLoopData = $data['table3']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt3): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<?php if($dt3->approval_group=='1'): ?>
												<?php 
													$text = $data['my_pos'];
													$count = substr_count($text, '#');
													$string = $text;
													$array = explode("#", $string);	
													$akses=0;
													$akses2=0;
													for($i=1;$i<=$count;$i++){
														if($dt3->id_template_approval==$array[$i]){
															//echo $dt3->id_template_approval.'-'.$array[$i];
															$akses=1;
															$akses2=1;
														}
													}									

												?>
												<div class="box-header">
													<h3 class="box-title">DEPT. <?php echo e($dept); ?></h3>
													<div class="pull-right">
														<?php if($akses==1): ?>
															<i class="fa fa-user" style="color:blue;"></i>
														<?php endif; ?>
													</div>
												</div>
												<!-- /.box-header -->
												<div class="box-body table-responsive">
													<?php if($data['progress']>=$dt3->seq_approval): ?>
														<div class="pull-left">
															<?php $x=$data['progress']-1;?>
															<?php if($dt3->seq_approval<$x): ?>
																<i class="fa fa-check-square"></i>
															<?php else: ?>
																<?php if($dt3->approval_status==1): ?>
																	<i class="update_approval fa fa-check-square-o" data-id="<?php echo e($dt3->id); ?>" data-status="0" data-akses="<?php echo e($akses); ?>"></i>
																<?php else: ?>
																	<i class="update_approval fa fa-square-o" data-id="<?php echo e($dt3->id); ?>" data-status="1" data-akses="<?php echo e($akses); ?>"></i>
																<?php endif; ?>
															<?php endif; ?>
															<br>
															<?php echo e($dt3->employee_name); ?>

															<br>
															<?php echo e($dt3->approved_date); ?>&nbsp;
														</div>
													<?php else: ?>
														<i class="fa fa-lock"></i><br><?php echo e($dt3->employeename); ?><br>&nbsp;
													<?php endif; ?>
													
												</div>
												<!-- /.box-body -->
											<?php endif; ?>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										</div>
										<!-- /.box -->
									</div>
									<div class="col-xs-12 col-md-4 col-lg-4">
										<div class="box box-default">
										<?php $__currentLoopData = $data['table3']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt3): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<?php if($dt3->approval_group=='2'): ?>
												<?php 
													$text = $data['my_pos'];
													$count = substr_count($text, '#');
													$string = $text;
													$array = explode("#", $string);	
													$akses=0;
													$akses2=0;
													for($i=1;$i<=$count;$i++){
														if($dt3->id_template_approval==$array[$i]){
															//echo $dt3->id_template_approval.'-'.$array[$i];
															$akses=1;
															$akses2=1;
														}
													}									
												?>
												<div class="box-header">
													<h3 class="box-title">HRGA</h3>
													<div class="pull-right">
														<?php if($akses==1): ?>
															<i class="fa fa-user" style="color:blue;"></i>
														<?php endif; ?>
													</div>
												</div>
												<!-- /.box-header -->
												<div class="box-body table-responsive">
													<?php if($data['progress']>=$dt3->seq_approval): ?>
														<div class="pull-left">
															<?php $x=$data['progress']-1;?>
															<?php if($dt3->seq_approval<$x): ?>
																<i class="fa fa-check-square"></i>
															<?php else: ?>
																<?php if($dt3->approval_status==1): ?>
																	<i class="update_approval fa fa-check-square-o" data-id="<?php echo e($dt3->id); ?>" data-status="0" data-akses="<?php echo e($akses); ?>"></i>
																<?php else: ?>
																	<i class="update_approval fa fa-square-o" data-id="<?php echo e($dt3->id); ?>" data-status="1" data-akses="<?php echo e($akses); ?>"></i>
																<?php endif; ?>
															<?php endif; ?>
															<br>
															<?php echo e($dt3->employee_name); ?>

															<br>
															<?php echo e($dt3->approved_date); ?>&nbsp;
														</div>
													<?php else: ?>
														<i class="fa fa-lock"></i><br><?php echo e($dt3->employee_name); ?><br>&nbsp;
													<?php endif; ?>
												</div>
												<!-- /.box-body -->
											<?php endif; ?>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										</div>
										<!-- /.box -->
									</div>
									<div class="col-xs-12 col-md-4 col-lg-4">
										<div class="box box-default">
										<?php $__currentLoopData = $data['table3']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt3): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<?php if($dt3->approval_group=='3'): ?>
												<?php 
													$text = $data['my_pos'];
													$count = substr_count($text, '#');
													$string = $text;
													$array = explode("#", $string);	
													$akses=0;
													$akses2=0;
													for($i=1;$i<=$count;$i++){
														if($dt3->id_template_approval==$array[$i]){
															//echo $dt3->id_template_approval.'-'.$array[$i];
															$akses=1;
															$akses2=1;
														}
													}									
												?>
												<div class="box-header">
													<h3 class="box-title"><?php echo e($dt3->position); ?></h3>
													<div class="pull-right">
														<?php if($akses==1): ?>
															<i class="fa fa-user" style="color:blue;"></i>
															<?php $spesial=1;?>
														<?php endif; ?>
													</div>
												</div>
												<!-- /.box-header -->
												<div class="box-body table-responsive">
													<?php if($data['progress']>=$dt3->seq_approval): ?>
														<div class="pull-left">
															<?php $x=$data['progress']-1;?>
															<?php if($dt3->seq_approval<$x): ?>
																<i class="fa fa-check-square"></i>
															<?php else: ?>
																<?php if($dt3->approval_status==1): ?>
																	<i class="update_approval fa fa-check-square-o" data-id="<?php echo e($dt3->id); ?>" data-status="0" data-akses="<?php echo e($akses); ?>"></i>
																<?php else: ?>
																	<i class="update_approval fa fa-square-o" data-id="<?php echo e($dt3->id); ?>" data-status="1" data-akses="<?php echo e($akses); ?>"></i>
																<?php endif; ?>
															<?php endif; ?>
															<br>
															<?php echo e($dt3->employee_name); ?>

															<br>
															<?php echo e($dt3->approved_date); ?>&nbsp;
														</div>
													<?php else: ?>
														<i class="fa fa-lock"></i><br><?php echo e($dt3->employee_name); ?><br>&nbsp;
													<?php endif; ?>
												</div>
												<!-- /.box-body -->
											<?php endif; ?>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										</div>
										<!-- /.box -->
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>

				</div>
				<div class="col-xs-12 col-md-12 col-lg-6">
					&nbsp;
				</div>
			</div>
			<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->
	<input type="text" id="akses2" value="<?php echo e($akses2); ?>">
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
					<input type="hidden" id="delid2">
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
	<div class="modal fade" id="modal-import">
		<div class="modal-dialog box box-primary" style="width:400px;">
				<div class="modal-content">
					<form method="post" action="/GeneralMemo/Import" enctype="multipart/form-data">
						<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Import File Excel</h4>
						</div>
						<div class="modal-body">
							<?php echo e(csrf_field()); ?>

							<label></label>
							<div class="form-group">
								<input type="file" name="file" required="required">
							</div>
						</div>
						<div class="modal-footer">
							<a href="/GeneralMemo/Template/<?php echo e($data['id_memo']); ?>" id="template" target="_blank" class="btn btn-default btn-md"><i class="fa fa-download"></i> &nbsp;Template</a>
							<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
							<input type="submit" class="btn btn-primary pull-left" value="Import">
						</div>
					</form>		
				</div>

				<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
    </div>
	<div class="modal fade" id="modal-loading">
		<div class="modal-dialog box box-info" style="padding:0;margin:0;height:100%;width:100%;opacity: 0;">
			<div class="modal-content">
				<div class="box-body">
					Proses Update, Mohon Tunggu....!
				</div>
				<div class="overlay">
					<i class="fa fa-refresh fa-spin"></i>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
	</div>
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
		$(document).ready(function() {
			var x="<?php echo $spesial;?>"
			if(x==1){
				const myButton = document.getElementById('save');
				myButton.disabled = false;				
			}
		});
	</script>
	<script type="text/javascript">
		$(document).on('click', '.import-modal', function() {
			$('#modal-import').modal('show');
		});
		$(document).on('click', '.update_draft', function() {
            var a=$(this).data('id_memo');
            var b=$(this).data('status');

			const checkbox1 = document.getElementById("spl");
			const checkbox2 = document.getElementById("leave");
			const checkbox3 = document.getElementById("permit");
			const textarea = document.getElementById("alasan");


			// Cek apakah checkbox dicentang
			if (checkbox1.checked) {
				var c1='1';
			} else {
				var c1='0';
			}
			if (checkbox2.checked) {
				var c2='1';
			} else {
				var c2='0';
			}
			if (checkbox3.checked) {
				var c3='1';
			} else {
				var c3='0';
			}
			const d = textarea.value; // Mengambil isi textarea
			var end_date=$('#tgl_selesai').val();

			if (confirm('Apakah Anda yakin?')) {
				var pesan='';
				if(b==0){
					if((c1==1||c2==1||c3==1) && d!=''){
						var pesan='';
					}else if(c1==0&&c2==0&&c3==0){
						var pesan='Silahkan pilih jenis akses terlebih dahulu'
					}else{
						var pesan='Kolom alasan masih kosong';
					}
				}
				if(pesan!=''){
					alert(pesan);
				}else{
					var datas = {
						id_memo:a,
						status:b,
						spl:c1,
						leave:c2,
						permit:c3,
						alasan:d,
						end_date:end_date
					}
					$.ajaxSetup({
						type:"POST",
						url: "<?php echo e($site); ?>/GeneralMemo/Confirm",
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
			}

		});
		$(document).on('click', '.update_status', function() {
            var a=$(this).data('id');
            var b=$(this).data('status');
			var c=$('#akses2').val();

			if(c==1){
				if (confirm('Apakah Anda yakin?')) {
					var datas = {
						id:a,
						status:b
					}
					$.ajaxSetup({
						type:"POST",
						url: "<?php echo e($site); ?>/GeneralMemo/UpdateStatus",
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
			}else{
				alert("No Access to Update");
			}
		});
		$(document).on('click', '.update_approval', function() {
			//alert(c);
            var a=$(this).data('id');
            var b=$(this).data('status');
            var c=$(this).data('akses');

			if(c==1){
				if (confirm('Apakah Anda yakin?')) {
					$('#modal-loading').modal('show');
					var datas = {
						id:a,
						status:b
					}
					$.ajaxSetup({
						type:"POST",
						url: "<?php echo e($site); ?>/GeneralMemo/UpdateApproval",
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
			}else{
				alert("No Access to Sign");
			}
		});
	</script>
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.delete-modal', function() {
			$('#delid').val($(this).data('delid'));
			$('#delname').text($(this).data('delname'));
			$('#delid2').val($(this).data('delid2'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#delid').val();
			var y=$('#delid2').val();
            var datas = {
                id:x,
				tb_name:y
            }
			$.ajaxSetup({
				type:"POST",
				url: "<?php echo e($site); ?>/GeneralMemo/Delete",
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


<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/general_memo/tb_memo_access.blade.php ENDPATH**/ ?>