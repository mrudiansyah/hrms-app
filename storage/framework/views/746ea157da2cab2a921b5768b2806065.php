
<?php $__env->startSection('Contents'); ?>
   <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
	<div class="content-wrapper">
		<section class="content-header">
			<h1>Security Check <small>Form Ijin (Employee Permit)</small></h1>
		</section>

		<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-file-text-o"></i>
						<h3 class="box-title">Daftar Form Ijin (Hari Ini & Kemarin)</h3>
					</div>
					<div class="box-body">
						<table id="tables" class="table table-hover table-bordered">
							<thead>
								<tr style="background:#d3d8d8ff">
									<th style="width:30px;">No</th>
									<th>Employee</th>
									<th style="width:100px;">NIK</th>
                                    <th>Dept</th>
                                    <th>Kategori</th>
									<th>Tanggal</th>
                                    <th style="text-align:center; width:100px;">Checkout</th>
                                    <th style="text-align:center; width:100px;">Checkin</th>
								</tr>
							</thead>
							<tbody>
								<?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($idx + 1); ?></td>
                                        <td><?php echo e($dt->employee_name); ?></td>
                                        <td><?php echo e($dt->NIK); ?></td>
                                        <td><?php echo e($dt->department); ?></td>
                                        <td align="center"><span class="label label-info"><?php echo e($dt->category); ?></span></td>
                                        <td><?php echo e(date('d-m-Y', strtotime($dt->apply_date))); ?></td>
                                        <td align="center">
                                            <div style="display:flex; flex-direction:column; align-items:center;">
                                                <input type="checkbox" class="security-update" data-id="<?php echo e($dt->id); ?>" data-type="checkout" <?php echo e(!empty($dt->mo_checkout) ? 'checked disabled' : ''); ?>>
                                                <span class="time-label" style="font-size:11px; color:#555;">
                                                    <?php echo e(!empty($dt->mo_checkout) ? date('H:i', strtotime($dt->mo_checkout)) : ''); ?>

                                                </span>
                                            </div>
                                        </td>
                                        <td align="center">
                                            <div style="display:flex; flex-direction:column; align-items:center;">
                                                <input type="checkbox" class="security-update" data-id="<?php echo e($dt->id); ?>" data-type="checkin" <?php echo e(!empty($dt->mo_checkin) ? 'checked disabled' : ''); ?> <?php echo e(empty($dt->mo_checkout) ? 'disabled' : ''); ?>>
                                                <span class="time-label" style="font-size:11px; color:#555;">
                                                    <?php echo e(!empty($dt->mo_checkin) ? date('H:i', strtotime($dt->mo_checkin)) : ''); ?>

                                                </span>
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
  	</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('Scripts'); ?>
	<script>
		(function($) {
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var table = $('#tables').DataTable({
                    'paging'      : false,
                    'lengthChange': false,
                    'searching'   : true,
                    'ordering'    : true,
                    'info'        : true,
                    'autoWidth'   : false
                });

            });
        })(jQuery);
	</script>
    <script>
        $(document).on('change', '.security-update', function() {
            var cb = $(this);
            var id = cb.data('id');
            var type = cb.data('type');
            var timeLabel = cb.siblings('.time-label');

            if (cb.is(':checked')) {
                if (!confirm('Apakah Anda yakin ingin melakukan ' + type + ' saat ini?')) {
                    cb.prop('checked', false);
                    return;
                }

                cb.prop('disabled', true);

                $.ajax({
                    url: '<?php echo e(url("/scurity/update-permit")); ?>',
                    type: 'POST',
                    data: {
                        id: id,
                        type: type
                    },
                    success: function(response) {
                        console.log('Update success:', response);
                        if (response.success) {
                            timeLabel.text(response.time);
                            if (type == 'checkout') {
                                cb.closest('tr').find('input[data-type="checkin"]').prop('disabled', false);
                            }
                        } else {
                            alert('Error: ' + response.message);
                            cb.prop('checked', false);
                            cb.prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        console.error('Update error:', xhr.responseText);
                        alert('Something went wrong!');
                        cb.prop('checked', false);
                        cb.prop('disabled', false);
                    }
                });
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\security\permit.blade.php ENDPATH**/ ?>