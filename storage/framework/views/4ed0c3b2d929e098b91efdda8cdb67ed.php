
<?php $__env->startSection('Contents'); ?>
   <!-- Contents -->
   <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
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
		#table3 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table3 tbody tr:hover{
			cursor:pointer;
		}
		#table4 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table4 tbody tr:hover{
			cursor:default;
		}
		#table5 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
    </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				General Freeday
				<small>calendar</small>
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
			<div class="col-lg-3 col-sm-12 col-xs-12">
				<form action="/Admin/Freeday/Create" method="post">
				<?php echo e(csrf_field()); ?>

                                <input type="hidden" name="sysid" id="syside">
				<div class="box box-success box-solid">
					<div class="box-header with-border">
						<label>Form Group</label>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">
						<div class="form-group">
							<label>Date</label>
							<input id="dateoff" type="date" value="<?php echo e(old('date_off')); ?>" name="date_off" class="form-control">
						</div>
						<div class="form-group">
							<label>Category</label>
							<select class="form-control" name="category" id="category">
								<option value=""></option>	
								<option value="Holiday">Holiday</option>
								<option value="Leave">Leave</option>
								<option value="Change">Change</option>
								<option value="Working">Working</option>
							</select>
						</div>
						<div class="form-group">
							<label>Description</label>
							<textarea name="description" id="description" class='form-control' rows="3"></textarea>
						</div>
					</div>
					<div class="box-footer">
						<div class="form-group pull-right">
							<button type="submit" class="btn btn-success">Simpan</button>
						</div>

					</div>
					<!-- /.box-body -->
				</div>
				</form>
			</div>
			<div class="col-lg-9 col-sm-12 col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-calendar"></i>
						<h3 class="box-title">Data Tables</h3>
					</div>
					<div class="box-body" style="overflow-x:scroll;">
						<table id="table2" class="table table-hover">
							<thead>
								<tr>
                                    <th style="width:70px;">&nbsp;</th>
									<th style="width:50px;">No</th>
                                    <th style="width:50px;">Sys-ID</th>
									<th style="width:100px;">Date</th>
									<th style="width:100px;">Category</th>
									<th>Description</th>

								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								<?php $__currentLoopData = $tb_freeday; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php $no++;?>
								<tr>
									<td>
										<button title="Edit" type="button" class="edit-modal btn btn-primary btn-xs" data-sysid="<?php echo e($dt->id); ?>" data-dateoff="<?php echo e($dt->date_off); ?>" data-category="<?php echo e($dt->category); ?>" data-description="<?php echo e($dt->description); ?>"><i class="fa fa-edit"></i></button>
										<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="<?php echo e($dt->id); ?>" data-delname="<?php echo e($dt->description); ?> on <?php echo e($dt->date_off); ?>" style="padding:1px 6px;"><i class="fa fa-trash"></i></button>
									</td>
									<td><?php echo e($no); ?></td>
									<td><?php echo e($dt->id); ?></td>
									<td><?php echo e($dt->date_off); ?></td>
									<td><?php echo e($dt->category); ?></td>
									<td><?php echo e($dt->description); ?></td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
							<tfoot>
								<tr>
									<th style="width:70px;">&nbsp;</th>
									<th style="width:50px;">No</th>
									<th style="width:50px;">Sys-ID</th>
									<th style="width:100px;">Date</th>
									<th style="width:100px;">Category</th>
									<th>Description</th>
								</tr>
							</tfoot>
						</table>
					</div>
					<!-- /.box-body -->
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
                                    Click Yes to Delete : <b id="delname1"></b> ?
                                    <input type="hidden" id="delid1">
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
				<?php if($errors->has('category')): ?>
					- Category harus dipilih<br>
				<?php endif; ?>
		</div>
	<?php endif; ?>

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
			"pageLength"  : 15,
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
	<script type="text/javascript">
		// Edit Data
		$(document).on('click', '.edit-modal', function() {
			$('#syside').val($(this).data('sysid'));
                        $('#dateoff').val($(this).data('dateoff'));
                        $('#category').val($(this).data('category'));
                        $('#description').val($(this).data('description'));
                        //alert('masuk');
		});
		// Delete Data
		$(document).on('click', '.delete-modal', function() {
			$('#delid1').val($(this).data('delid'));
			$('#delname1').text($(this).data('delname'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#delid1').val();
			window.location.href='/Admin/Freeday/Delete/'+x;
		});
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/m_calendar/freeday.blade.php ENDPATH**/ ?>