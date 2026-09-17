
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
				General Memo
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
						<h3 class="box-title">New Memo <small>(Tanggal >= <?php echo e($data['today']); ?>)</small></h3>
						<div class="box-tools pull-right">
							<button class="btn btn-primary btn-md create-modal" data-id_memo="" data-id_template_memo="" data-date_information="<?php echo e($data['today']); ?>" data-additional_information="">
								<i class="fa fa-plus"></i> &nbsp;Create
							</button>
							<a class="btn btn-default btn-md" href="/ArchieveMemo/<?php echo e($data['category']); ?>/0">
								<i class="fa fa-file-archive-o"></i> &nbsp;Archieve
							</a>
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
									<th style="width:70px;">No.Registration</th>
									<th style="width:70px;">Memo Category</th>
									<th style="width:70px;">Tanggal</th>
									<th>Remark</th>
									<th style="width:50px;">Approval</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								<?php $__currentLoopData = $data['table2']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td>
										<?php $no++;echo $no;?>
									</td>
									<td><?php echo e($dt->memo_number); ?></td>
									<td><?php echo e($dt->description); ?></td>
									<td><?php echo e($dt->date_information); ?></td>
									<td><?php echo e($dt->additional_note); ?></td>
									<td>
										<?php if($dt->is_completed==3): ?>
											<span class='badge bg-green'>1</span>
											<span class='badge bg-green'>2</span>
											<span class='badge bg-green'>3</span>
										<?php elseif($dt->is_completed==2): ?>
											<span class='badge bg-green'>1</span>
											<span class='badge bg-green'>2</span>
											<span class='badge bg-white'>3</span>
										<?php elseif($dt->is_completed==1): ?>
											<span class='badge bg-green'>1</span>
											<span class='badge bg-white'>2</span>
											<span class='badge bg-white'>3</span>
										<?php else: ?>
											<span class='badge bg-white'>1</span>
											<span class='badge bg-white'>2</span>
											<span class='badge bg-white'>3</span>
										<?php endif; ?>
										<div class="pull-right">
											<?php if($dt->is_draft=='1'): ?>
												<button class="btn btn-warning btn-xs create-modal" data-id_memo="<?php echo e($dt->id); ?>" data-id_template_memo="<?php echo e($dt->id_template_memo); ?>" data-date_information="<?php echo e($dt->date_information); ?>" data-additional_information="<?php echo e($dt->additional_note); ?>">
													<i class="fa fa-edit"></i>
												</button>
												<button class="btn btn-danger btn-xs delete-modal" data-delid="<?php echo e($dt->id); ?>" data-delid2="tb_memo" data-delname="<?php echo e($dt->memo_number); ?>">
													<i class="fa fa-trash"></i>
												</button>
											<?php endif; ?>
											<button class="btn btn-primary btn-xs">
												<a href="/GeneralMemo/Detail/<?php echo e($dt->id); ?>" style="color:white;"><i class="fa fa-folder-o"></i></a>
											</button>
										</div>
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
	<div class="modal fade" id="modal-create">
		<div class="modal-dialog box box-primary" style="width:300px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Form Memo</h4>
					<input type="hidden" name="id_memo" id="id_memo" class="form-control">
				</div>
				<div class="modal-body">
					<form role="form" action="" method="post">
						<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
						<?php echo e(csrf_field()); ?>

						<div class="box-body">
							<div class="form-group">
								<label>Date</label>
								<?php $today=date('Y-m-d');?>
								<input type="date" name="date_information" id="date_information" class="form-control" min="<?php echo e($today); ?>">
							</div>
							<div class="form-group">
								<label>Category</label>
								<select name="id_template_memo" class="form-control" id="id_template_memo">
									<option value=""></option>
									<?php $__currentLoopData = $data['table3']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt3): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($dt3->id); ?>"><?php echo e($dt3->description); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
							</div>
							<div class="form-group">
								<label>General Note <small>(optional)</small></label>
								<input type="text" name="additional_note" id="additional_note" class="form-control">
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary pull-left createMemo" data-dismiss="modal">Save</button>
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
		$(document).on('click', '.create-modal', function() {
            $("#id_memo").val($(this).data('id_memo'));
            $("#date_information").val($(this).data('date_information'));
            $("#additional_note").val($(this).data('additional_note'));
            $("#id_template_memo").val($(this).data('id_template_memo'));
			$('#modal-create').modal('show');
		});

		$('.modal-footer').on('click', '.createMemo', function() {
            var a=$("#id_memo").val();
			var b=$("#date_information").val();
			var c=$("#additional_note").val();
			var d=$("#id_template_memo").val();
            var datas = {
                id_memo:a,
				date_information:b,
				additional_note:c,
				id_template_memo:d
            }
			$.ajaxSetup({
				type:"POST",
				url: "<?php echo e($site); ?>/GeneralMemo/Create",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:datas,
				success: function(respond){
					//alert(respond);
					if(respond==''){
						location.reload();
					}else{
						window.location.href='/GeneralMemo/Detail/' + respond;
					}
				}
			})
		});
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


<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/general_memo/memo.blade.php ENDPATH**/ ?>