
<?php $__env->startSection('Contents'); ?>
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<h3 class="box-title"><?php echo e($juduls); ?> - <?php echo e($periode); ?></h3>
						<div class="pull-right">
                            <button type="button" class="btn btn-primary btn-md" id="import"><i class="fa fa-upload"></i> &nbsp;Import</button>
						</div>
					</div>
					<div class="box-body">
						<table id="tables" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>NO</th>
									<th>EMPLOYEE_NAME</th>
									<th>PERIODE</th>
									<th>DAYS</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								<?php $__currentLoopData = $tb_work_entry; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr>
										<td><?php echo e(++$no); ?></td>
										<td><?php echo e($dt->nama ?? ''); ?></td>
										<td><?php echo e($dt->periode ?? ''); ?></td>
										<td>
                                            <?php echo e($dt->days ?? '0'); ?>

                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary btn-xs edit-workentry" 
                                                    data-id="<?php echo e($dt->id); ?>" 
                                                    data-nama="<?php echo e($dt->nama ?? ''); ?>" 
                                                    data-days="<?php echo e($dt->days); ?>">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
									</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Modals'); ?>
    <div class="modal fade" id="modal-edit-workentry">
        <div class="modal-dialog" style="width:400px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Work Entry</h4>
                </div>
                <div class="modal-body">
                    <form id="form-edit-workentry">
                        <input type="hidden" id="edit-we-id">
                        <div class="form-group">
                            <label>Employee Name</label>
                            <input type="text" class="form-control" id="edit-we-nama" readonly>
                        </div>
                        <div class="form-group">
                            <label>Days</label>
                            <input type="number" step="0.5" class="form-control" id="edit-we-days">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btn-save-workentry">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-import">
        <div class="modal-dialog" style="width:400px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Import Work Entry</h4>
                </div>
                <form action="<?php echo e($site); ?>/WorkEntryPKL/Import" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Download Template</label>
                            <a href="<?php echo e($site); ?>/WorkEntryPKL/Template/<?php echo e($periode); ?>" class="btn btn-success btn-xs"><i class="fa fa-download"></i> Download</a>
                        </div>
                        <div class="form-group">
                            <label>File Excel</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
    <script>
        $('#import').on('click', function() {
            $('#modal-import').modal('show');
        });

        $(document).on('click', '.edit-workentry', function() {
            $('#edit-we-id').val($(this).data('id'));
            $('#edit-we-nama').val($(this).data('nama'));
            $('#edit-we-days').val($(this).data('days'));
            $('#modal-edit-workentry').modal('show');
        });

        $('#btn-save-workentry').on('click', function() {
            var id = $('#edit-we-id').val();
            var days = $('#edit-we-days').val();
            
            $.ajax({
                url: '<?php echo e($site); ?>/WorkEntryPKL/Update',
                type: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    id: id,
                    days: days
                },
                success: function(response) {
                    if (response.status == 'Success') {
                        alert('Update Successful');
                        window.location.reload();
                    } else {
                        alert('Update Failed: ' + (response.status || response));
                    }
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/payroll_pkl', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\payroll\work_entry_pkl.blade.php ENDPATH**/ ?>