
<?php $__env->startSection('Contents'); ?>
<div class="content-wrapper">
	<section class="content-header">
		<h1>Employee Checktime</h1>
	</section>
	<section class="content">
		<div class="box box-primary">
			<div class="box-body">
				<form method="GET" action="/EmployeesChecktime">
					<div class="row">
						<div class="col-md-4 form-group">
							<label for="employee_id">Employee</label>
							<select class="form-control select2" id="employee_id" name="employee_id" style="width: 100%;" required>
								<option value="">Pilih employee</option>
								<?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($item->id); ?>" <?php echo e((string) request('employee_id') === (string) $item->id ? 'selected' : ''); ?>>
										<?php echo e($item->employee_name); ?> (<?php echo e($item->NIK); ?>)
									</option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</select>
						</div>
						<div class="col-md-3 form-group">
							<label for="start_date">Tanggal awal</label>
							<input class="form-control" type="date" id="start_date" name="start_date" value="<?php echo e($startDate); ?>" <?php echo e($employee ? '' : 'disabled'); ?> required>
						</div>
						<div class="col-md-3 form-group">
							<label for="end_date">Tanggal akhir</label>
							<input class="form-control" type="date" id="end_date" name="end_date" value="<?php echo e($endDate); ?>" <?php echo e($employee ? '' : 'disabled'); ?> required>
						</div>
						<div class="col-md-2 form-group">
							<label>&nbsp;</label>
							<button class="btn btn-primary btn-block" type="submit"><i class="fa fa-search"></i> Tampilkan</button>
						</div>
					</div>
				</form>
				<?php if($errors->any()): ?>
					<div class="alert alert-danger"><?php echo e($errors->first()); ?></div>
				<?php endif; ?>
				<?php if($employee): ?>
					<p><strong><?php echo e($employee->employee_name); ?></strong> | NIK: <?php echo e($employee->NIK); ?> | PIN: <?php echo e($employee->PIN); ?> | Badge: <?php echo e($employee->badgenumber); ?></p>
					<p class="text-muted">Badge number pada iClock dicocokkan dalam format 9 digit dengan penambahan nol di bagian depan.</p>
				<?php endif; ?>
			</div>
		</div>

		<?php if($employee): ?>
			<div class="row">
				<div class="col-md-5">
					<div class="box box-info">
						<div class="box-header with-border"><h3 class="box-title">Finger Print</h3></div>
						<div class="box-body table-responsive">
							<table id="iclockTable" class="table table-bordered table-striped table-condensed">
								<thead><tr><th>Badge</th><th>Nama</th><th>Checktime</th></tr></thead>
								<tbody>
									<?php $__currentLoopData = $iclockRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr><td><?php echo e($record->badgenumber); ?></td><td><?php echo e($record->name); ?></td><td><?php echo e($record->checktime); ?></td></tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</tbody>
							</table>
							<div id="iclock-download" class="text-right"></div>
						</div>
					</div>
				</div>
				<div class="col-md-7">
					<div class="box box-success">
						<div class="box-header with-border"><h3 class="box-title">Checktime Mobile</h3></div>
						<div class="box-body table-responsive">
							<table id="mobileChecktimeTable" class="table table-bordered table-striped table-condensed">
								<thead><tr><th>Nama</th><th>Badge (PIN)</th><th>Checktime</th><th>Latitude</th><th>Longitude</th><th>Lokasi</th></tr></thead>
								<tbody>
									<?php $__currentLoopData = $mobileChecktimeRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr>
											<td><?php echo e($record->employee_name); ?></td>
											<td><?php echo e($record->PIN); ?></td>
											<td><?php echo e($record->checktime); ?></td>
											<td><?php if(is_numeric($record->latitut)): ?><?php echo e(number_format((float) $record->latitut, 4, '.', '')); ?><?php endif; ?></td>
											<td><?php if(is_numeric($record->longitut)): ?><?php echo e(number_format((float) $record->longitut, 4, '.', '')); ?><?php endif; ?></td>
											<td>
												<?php if(is_numeric($record->latitut) && is_numeric($record->longitut)): ?>
													<button type="button" class="btn btn-xs btn-info preview-location" data-lat="<?php echo e($record->latitut); ?>" data-lng="<?php echo e($record->longitut); ?>" title="Preview lokasi di peta"><i class="fa fa-map-marker"></i> Peta</button>
												<?php endif; ?>
											</td>
										</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</tbody>
							</table>
							<div id="mobile-checktime-download" class="text-right"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalTitle">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="locationModalTitle">Preview Lokasi</h4>
						</div>
						<div class="modal-body">
							<p id="locationCoordinates"></p>
							<iframe id="locationMap" title="Peta lokasi checktime" width="100%" height="450" frameborder="0" loading="lazy" allowfullscreen></iframe>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</section>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
<script>
	$(function () {
		$('#employee_id').select2({
			width: '100%',
			placeholder: 'Pilih employee',
			allowClear: true
		});

		$('#employee_id').on('change', function () {
			var isSelected = $(this).val() !== '';
			$('#start_date, #end_date').prop('disabled', !isSelected);
		});

		if ($('#iclockTable').length) {
			var iclockTable = $('#iclockTable').DataTable({
				pageLength: 25,
				lengthChange: false,
				order: [[2, 'asc']],
				language: { emptyTable: 'Tidak ada data pada rentang tanggal ini.' }
			});
			new $.fn.dataTable.Buttons(iclockTable, {
				buttons: [{ extend: 'excel', text: '<i class="fa fa-download"></i> Download Excel', title: 'Checktime iClock' }]
			});
			iclockTable.buttons().container().appendTo('#iclock-download');

			var mobileChecktimeTable = $('#mobileChecktimeTable').DataTable({
				pageLength: 25,
				lengthChange: false,
				order: [[2, 'asc']],
				language: { emptyTable: 'Tidak ada data pada rentang tanggal ini.' }
			});
			new $.fn.dataTable.Buttons(mobileChecktimeTable, {
				buttons: [{
					extend: 'excel',
					text: '<i class="fa fa-download"></i> Download Excel',
					title: 'Checktime Mobile',
					exportOptions: { columns: ':not(:last-child)' }
				}]
			});
			mobileChecktimeTable.buttons().container().appendTo('#mobile-checktime-download');
		}

		$(document).on('click', '.preview-location', function () {
			var latitude = Number($(this).attr('data-lat'));
			var longitude = Number($(this).attr('data-lng'));
			if (!Number.isFinite(latitude) || !Number.isFinite(longitude) || latitude < -90 || latitude > 90 || longitude < -180 || longitude > 180) {
				return;
			}

			$('#locationCoordinates').text(latitude.toFixed(4) + ', ' + longitude.toFixed(4));
			$('#locationMap').attr('src', 'https://maps.google.com/maps?q=' + latitude + ',' + longitude + '&z=16&output=embed');
			$('#locationModal').modal('show');
		});
		$('#locationModal').on('hidden.bs.modal', function () {
			$('#locationMap').attr('src', '');
		});
	});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/user/m_employee/employees_checktime.blade.php ENDPATH**/ ?>