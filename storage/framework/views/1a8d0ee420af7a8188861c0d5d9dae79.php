
<?php $__env->startSection('Contents'); ?>
<div class="content-wrapper">
	<section class="content-header">
		<h1>Checktime PKL</h1>
	</section>
	<section class="content">
		<div class="box box-primary">
			<div class="box-body">
				<form method="GET" action="/EmployeesChecktimePKL">
					<div class="row">
						<div class="col-md-4 form-group">
							<label for="employee_id">Peserta PKL</label>
							<select class="form-control select2" id="employee_id" name="employee_id" style="width: 100%;" required>
								<option value="">Pilih peserta PKL</option>
								<?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($item->id); ?>" <?php echo e((string) request('employee_id') === (string) $item->id ? 'selected' : ''); ?>>
										<?php echo e($item->nama); ?> (<?php echo e($item->PIN); ?>)
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
					<p><strong><?php echo e($employee->nama); ?></strong> | Kategori: <?php echo e($employee->kategori); ?> | Sub kategori: <?php echo e($employee->sub_kategori); ?> | Gender: <?php echo e($employee->gender); ?> | PIN: <?php echo e($employee->PIN); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<?php if($employee): ?>
			<div class="row">
				<div class="col-md-6">
					<div class="box box-info">
						<div class="box-header with-border"><h3 class="box-title">Data Checktime</h3></div>
						<div class="box-body table-responsive">
							<table id="iclockTable" class="table table-bordered table-striped table-condensed">
								<thead><tr><th>ID</th><th>Badgenumber</th><th>Checktime</th></tr></thead>
								<tbody>
									<?php $__currentLoopData = $iclockRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr><td><?php echo e($record->id); ?></td><td><?php echo e($record->badgenumber); ?></td><td><?php echo e($record->checktime); ?></td></tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="box box-success">
						<div class="box-header with-border"><h3 class="box-title">Summary Kehadiran Harian</h3></div>
						<div class="box-body table-responsive">
							<table id="attendanceSummaryTable" class="table table-bordered table-striped table-condensed">
								<thead><tr><th>Tanggal</th><th>Hari</th><th>Keterangan</th></tr></thead>
								<tbody>
									<?php $__currentLoopData = $attendanceSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr>
											<td><?php echo e($day->date); ?></td>
											<td><?php echo e($day->day_name); ?></td>
											<td>
												<?php if($day->status === 'Hadir'): ?>
													<span class="label label-success">Hadir</span>
												<?php elseif($day->status === 'Absen'): ?>
													<span class="label label-danger">Absen</span>
												<?php endif; ?>
											</td>
										</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</tbody>
							</table>
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
			placeholder: 'Pilih peserta PKL',
			allowClear: true
		});

		$('#employee_id').on('change', function () {
			var isSelected = $(this).val() !== '';
			$('#start_date, #end_date').prop('disabled', !isSelected);
		});

		if ($('#iclockTable').length) {
			$('#iclockTable').DataTable({
				pageLength: 25,
				lengthChange: false,
				order: [[2, 'asc']],
				language: { emptyTable: 'Tidak ada data checktime pada rentang tanggal ini.' }
			});
			$('#attendanceSummaryTable').DataTable({
				pageLength: 31,
				lengthChange: false,
				order: [[0, 'asc']],
				language: { emptyTable: 'Tidak ada ringkasan kehadiran.' }
			});
		}
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/user/m_employee/employees_checktime_pkl.blade.php ENDPATH**/ ?>