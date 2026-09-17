
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
				Overtime Request
				<small>&nbsp;</small>
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-xs-12 col-md-12 col-lg-12">
					<div class="box box-primary" style="background:#FFF;">
						<div class="box-header">
							<i class="fa fa-file-text-o"></i>
							<h3 class="box-title">MEMO <?php echo e($data['from_dept']); ?> ~ <?php echo e($data['to_dept']); ?></h3>
							<div class="box-tools pull-right">
								<button type="button" class="btn btn-default btn-md" onclick="window.location.href='/GeneralMemo/1';"><i class="fa fa-home"></i> &nbsp;Back</button>
								<?php if($data['status_draft']==1): ?>
									<button type="button" class="btn btn-primary btn-md import-modal" id="import"><i class="fa fa-upload"></i> &nbsp;Import</button>
									<button type="button" class="btn btn-success btn-md update_draft" data-id_memo="<?php echo e($data['id_memo']); ?>" data-status="0"><i class="fa fa-edit"></i> &nbsp;Confirm</button>
								<?php else: ?>
									<?php if($data['admin']==$data['created_by']): ?>
										<button type="button" class="btn btn-warning btn-md update_draft" data-id_memo="<?php echo e($data['id_memo']); ?>" data-status="1"><i class="fa fa-refresh"></i> &nbsp;Roll Back</button>
									<?php endif; ?>
									<!-- <a class="btn btn-default btn-md" href="/GeneralMemo/Preview/<?php echo e($data['id_memo']); ?>" target="_blank"><i class="fa fa-print"></i> &nbsp;preview</a> -->
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
									<tr style="background:#CCCCCC;">
										<th style="width:50px;">No</th>
										<th>MemoNum</th>
										<th>Part No.</th>
										<th>Part Name</th>
										<th>Line</th>
										<th>GSPH/JPH</th>
										<th>Process</th>
										<th>Date OT</th>
										<th>Start</th>
										<th>Finish</th>
										<th>Qty</th>
										<th>Reason OT</th>
										<th>Remark</th>
										<th>Status</th>
										<th>Info</th>
									</tr>
								</thead>
								<tbody>
									<?php $no=0;$akses2=0;$salah=0;?>
									<?php $__currentLoopData = $data['table2']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr <?php if($dt->status=='0')echo "style='background:#ebeeee;'";?>>
										<td>
											<?php $no++;echo $no;?>
										</td>
										<td><?php echo e($dt->memo_number); ?></td>
										<td><?php echo e($dt->part_no); ?></td>
										<td><?php echo e($dt->part_name); ?></td>
										<td><?php echo e($dt->lines); ?></td>
										<td><?php echo e($dt->jph_gsph); ?></td>
										<td><?php echo e($dt->process); ?></td>
										<td><?php echo e(date('d-M-Y',strtotime($dt->date_ot))); ?></td>
										<td><?php echo e(date('H:i',strtotime($dt->start_ot))); ?></td>
										<td><?php echo e(date('H:i',strtotime($dt->finish_ot))); ?></td>
										<td><?php echo e($dt->plan_qty); ?></td>
										<td>
											<?php if($dt->check_reason==''): ?>
												<span class="label label-warning"><?php echo e($dt->reason_ot); ?></span>
												<?php $salah++;?>
											<?php else: ?>
												<?php echo e($dt->reason_ot); ?>

											<?php endif; ?>
										</td>
										<td><?php echo e($dt->remark); ?></td>
										<td>
											<div class="pull-left">
												<?php if($dt->status==1): ?>
													<i class="update_status fa fa-check-square-o" data-id="<?php echo e($dt->id); ?>" data-status="0"></i>
												<?php else: ?>
													<i class="update_status fa fa-square-o" data-id="<?php echo e($dt->id); ?>" data-status="1"></i>
												<?php endif; ?>
											</div>
											<div class="pull-right">
												<?php if($data['status_draft']==1||$dt->check_reason==''): ?>
													<a href="<?php echo e(route('memos.edit', $dt->id)); ?>" class="btn btn-primary btn-xs">
														<i class="fa fa-edit"></i>
													</a>
												<?php endif; ?>
												<?php if($data['status_draft']=='1'): ?>
													<button class="btn btn-danger btn-xs delete-modal" data-delid="<?php echo e($dt->id); ?>" data-delid2="tb_memo_ot" data-delname="<?php echo e($dt->part_no); ?>">
														<i class="fa fa-trash"></i>
													</button>
												<?php endif; ?>
											</div>
										</td>
										<td>
											<?php if($dt->status=='0'): ?>
												Canceled by <?php echo e($dt->canceled_by); ?>

											<?php endif; ?>
										</td>
									</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</tbody>
							</table>
						</div>
						<!-- /.box-body -->
						<div class="box-footer">
							<?php if($salah>0): ?>
								* Note: Anda memasukan Reason OT yang tidak terdaftar di sistem, berlabel <span class="label label-warning">kuning</span>. 
								Silahkan klik ( <a href="/Admin/OTCategory" target="_blank">list</a> ) untuk melihat reason yang terdaftar. 
							<?php endif; ?>
						</div>
					</div>

				</div>
			</div>
			<?php if($data['status_draft']=='0'): ?>
				<div class="row">
					<div class="col-xs-12 col-md-4 col-lg-4">
						<div class="box box-danger">
							<div class="box-header">
								<h3 class="box-title">DEPT. <?php echo e($data['from_dept']); ?></h3>
							</div>
							<!-- /.box-header -->
							<div class="box-body table-responsive">
							<table class="table table-hover">
								<tr style="background:#CCCCCC;">
									<th style="width:180px;">POSITION</th>
									<th>APPROVAL</th>
								</tr>
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
										<tr>
											<td>
												<?php echo e($dt3->position); ?>

											</td>
											<td>
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
														<?php echo e($dt3->approved_date); ?>

													</div>
													<div class="pull-right">
														<?php if($akses==1): ?>
															<i class="fa fa-user" style="color:blue;"></i>
														<?php endif; ?>
													</div>
												<?php else: ?>
													<i class="fa fa-lock"></i>
												<?php endif; ?>
											</td>
											</td>
										</tr>
									<?php endif; ?>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</table>
							</div>
							<!-- /.box-body -->
						</div>
						<!-- /.box -->
					</div>
					<div class="col-xs-12 col-md-4 col-lg-4">
						<div class="box box-warning">
							<div class="box-header">
								<h3 class="box-title">DEPT. <?php echo e($data['to_dept']); ?></h3>
							</div>
							<!-- /.box-header -->
							<div class="box-body table-responsive">
							<table class="table table-hover">
								<tr style="background:#CCCCCC;">
									<th style="width:180px;">POSITION</th>
									<th>APPROVAL</th>
								</tr>
								<?php $__currentLoopData = $data['table3']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt3): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php if($dt3->approval_group=='2'): ?>
										<?php 
											$text = $data['my_pos'];
											$count = substr_count($text, '#');
											$string = $text;
											$array = explode("#", $string);	
											$akses=0;
											for($i=1;$i<=$count;$i++){
												if($dt3->id_template_approval==$array[$i]){
													//echo $dt3->id_template_approval.'-'.$array[$i];
													$akses=1;
													$akses2=1;
												}
											}									

										?>
										<tr>
											<td>
												<?php echo e($dt3->position); ?>

											</td>
											<td>
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
														<?php echo e($dt3->approved_date); ?>

													</div>
													<div class="pull-right">
														<?php if($akses==1): ?>
															<i class="fa fa-user" style="color:blue;"></i>
														<?php endif; ?>
													</div>
												<?php else: ?>
													<i class="fa fa-lock"></i>
												<?php endif; ?>
											</td>
										</tr>
									<?php endif; ?>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</table>
							</div>
							<!-- /.box-body -->
						</div>
						<!-- /.box -->
					</div>
					<div class="col-xs-12 col-md-4 col-lg-4">
						<div class="box box-info">
							<div class="box-header">
								<h3 class="box-title">DEPT. SUPPORTING</h3>
							</div>
							<!-- /.box-header -->
							<div class="box-body table-responsive">
							<table class="table table-hover">
								<tr style="background:#CCCCCC;">
									<th style="width:180px;">POSITION</th>
									<th>APPROVAL</th>
								</tr>
								<?php $__currentLoopData = $data['table3']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt3): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php if($dt3->approval_group=='3'): ?>
										<?php 
											$text = $data['my_pos'];
											$count = substr_count($text, '#');
											$string = $text;
											$array = explode("#", $string);	
											$akses=0;
											for($i=1;$i<=$count;$i++){
												if($dt3->id_template_approval==$array[$i]){
													//echo $dt3->id_template_approval.'-'.$array[$i];
													$akses=1;
													$akses2=1;
												}
											}									

										?>
										<tr>
											<td>
												<?php echo e($dt3->position); ?>

											</td>
											<td>
												<?php if($data['status_completed']==1): ?>
													<div class="pull-left">
														<?php if($dt3->approval_status==1): ?>
															<i class="update_approval fa fa-check-square-o" data-id="<?php echo e($dt3->id); ?>" data-status="0" data-akses="<?php echo e($akses); ?>"></i>
														<?php else: ?>
															<i class="update_approval fa fa-square-o" data-id="<?php echo e($dt3->id); ?>" data-status="1" data-akses="<?php echo e($akses); ?>"></i>
														<?php endif; ?>
														<br>
														<?php echo e($dt3->employee_name); ?>

														<br>
														<?php echo e($dt3->approved_date); ?>

													</div>
													<div class="pull-right">
														<?php if($akses==1): ?>
															<i class="fa fa-user" style="color:blue;"></i>
														<?php endif; ?>
													</div>
												<?php else: ?>
													<i class="fa fa-lock"></i>
												<?php endif; ?>

											</td>
										</tr>
									<?php endif; ?>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</table>
							</div>
							<!-- /.box-body -->
						</div>
						<!-- /.box -->
					</div>
				</div>
			<?php endif; ?>
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
	<script type="text/javascript">
		$(document).on('click', '.import-modal', function() {
			$('#modal-import').modal('show');
		});
		$(document).on('click', '.update_draft', function() {
            var a=$(this).data('id_memo');
            var b=$(this).data('status');

			if (confirm('Apakah Anda yakin?')) {
				var datas = {
					id_memo:a,
					status:b
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


<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/general_memo/tb_memo_ot.blade.php ENDPATH**/ ?>