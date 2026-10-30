
<?php $__env->startSection('Contents'); ?>
   <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
	<div class="content-wrapper">
		<section class="content-header">
			<h1>Security Check<small>Tugas Luar (Outside Assignment)</small></h1>
		</section>

		<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-file-text-o"></i>
						<h3 class="box-title">Daftar Tugas Luar (Hari Ini & Kemarin)</h3>
					</div>
					<div class="box-body">
						<table id="tables" class="table table-hover table-bordered">
							<thead>
								<tr style="background:#d3d8d8ff">
									<th style="width:30px;">No</th>
									<th>Employee</th>
									<th style="width:100px;">NIK</th>
                                    <th>Dept</th>
                                    <th>Status</th>
									<th>Tanggal</th>
                                    <th style="text-align:center; width:100px;">Checkout</th>
                                    <th style="text-align:center; width:100px;">Checkin</th>
								</tr>
							</thead>
							<tbody>
								<?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupId => $groupItems): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $first = $groupItems->first(); ?>
                                    <tr style="background:#f4f4f4; font-weight:bold;">
                                        <td><i class="fa fa-car text-blue"></i></td>
                                        <td colspan="5">
                                            <span style="font-size:14px;">N0.POL: <?php echo e($first->nopol); ?></span> &nbsp;
                                        </td>
                                        <td align="center">
                                            <button class="btn btn-xs btn-primary bulk-update" data-group="<?php echo e($groupId); ?>" data-type="checkout" title="Checkout All in this vehicle">
                                                <i class="fa fa-arrow-circle-right"></i> All
                                            </button>
                                        </td>
                                        <td align="center">
                                            <button class="btn btn-xs btn-success bulk-update" data-group="<?php echo e($groupId); ?>" data-type="checkin" title="Checkin All in this vehicle">
                                                <i class="fa fa-arrow-circle-left"></i> All
                                            </button>
                                        </td>
                                    </tr>
                                    <?php $__currentLoopData = $groupItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="group-<?php echo e($groupId); ?>">
                                        <td><?php echo e($idx + 1); ?></td>
                                        <td><?php echo e($dt->nama_karyawan); ?></td>
                                        <td><?php echo e($dt->NIK); ?></td>
                                        <td><?php echo e($dt->department); ?></td>
                                        <td>
                                            <?php
                                                $statusClass = 'label-default';
                                                $statusText = 'Pending';
                                                if ($dt->outside_status == 1) {
                                                    $statusClass = 'label-warning';
                                                    $statusText = 'Out';
                                                } elseif ($dt->outside_status == 2) {
                                                    $statusClass = 'label-success';
                                                    $statusText = 'Kembali';
                                                }
                                            ?>
                                            <span class="label <?php echo e($statusClass); ?> status-label"><?php echo e($statusText); ?></span>
                                        </td>
                                        <td><?php echo e(date('d-m-Y', strtotime($dt->tanggal))); ?></td>
                                        <td align="center">
                                            <div style="display:flex; flex-direction:column; align-items:center;">
                                                <input type="checkbox" class="security-update" data-id="<?php echo e($dt->id); ?>" data-type="checkout" <?php echo e(!empty($dt->checkout_time) ? 'checked disabled' : ''); ?>>
                                                <span class="time-label" style="font-size:11px; color:#555;">
                                                    <?php echo e(!empty($dt->checkout_time) ? date('H:i', (int)$dt->checkout_time) : ''); ?>

                                                </span>
                                            </div>
                                        </td>
                                        <td align="center">
                                            <div style="display:flex; flex-direction:column; align-items:center;">
                                                <input type="checkbox" class="security-update" data-id="<?php echo e($dt->id); ?>" data-type="checkin" <?php echo e(!empty($dt->checkin_time) ? 'checked disabled' : ''); ?> <?php echo e(empty($dt->checkout_time) ? 'disabled' : ''); ?>>
                                                <span class="time-label" style="font-size:11px; color:#555;">
                                                    <?php echo e(!empty($dt->checkin_time) ? date('H:i', (int)$dt->checkin_time) : ''); ?>

                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    'ordering'    : false,
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
                    url: '<?php echo e(url("/scurity/update-assignment")); ?>',
                    type: 'POST',
                    data: {
                        id: id,
                        type: type
                    },
                    success: function(response) {
                        console.log('Update success:', response);
                        if (response.success) {
                            timeLabel.text(response.time);
                            var statusLabel = cb.closest('tr').find('.status-label');
                            var status = parseInt(response.status, 10);
                            if (status === 1) {
                                statusLabel.removeClass('label-default label-success').addClass('label-warning').text(response.statusText || 'Out');
                            } else if (status === 2) {
                                statusLabel.removeClass('label-default label-warning').addClass('label-success').text(response.statusText || 'Kembali');
                            }

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
                        alert('Something went wrong! Check console for details.');
                        cb.prop('checked', false);
                        cb.prop('disabled', false);
                    }
                });
            }
        });

        $(document).on('click', '.bulk-update', function() {
            var btn = $(this);
            var groupId = btn.data('group');
            var type = btn.data('type');

            if (!confirm('Apakah Anda yakin ingin melakukan ' + type + ' UNTUK SEMUA karyawan di kendaraan ini?')) {
                return;
            }

            btn.prop('disabled', true);

            $.ajax({
                url: '<?php echo e(url("/scurity/bulk-update-assignment")); ?>',
                type: 'POST',
                data: {
                    id_tugasluar: groupId,
                    type: type
                },
                success: function(response) {
                    if (response.success) {
                        var status = parseInt(response.status, 10);
                        var statusLabelText = response.statusText || (status === 1 ? 'Out' : 'Kembali');
                        var statusClass = status === 1 ? 'label-warning' : 'label-success';
                        var statusRemove = status === 1 ? 'label-default label-success' : 'label-default label-warning';

                        $('.group-' + groupId).each(function() {
                            $(this).find('.status-label')
                                .removeClass(statusRemove)
                                .addClass(statusClass)
                                .text(statusLabelText);
                        });
                        btn.prop('disabled', false);
                    } else {
                        alert('Error: ' + response.message);
                        btn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    console.error('Bulk update error:', xhr.responseText);
                    alert('Something went wrong!');
                    btn.prop('disabled', false);
                }
            });
        });

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\security\assignment.blade.php ENDPATH**/ ?>