@extends('layouts/admin')
@section('Contents')
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
								@foreach($employees as $item)
									<option value="{{ $item->id }}" {{ (string) request('employee_id') === (string) $item->id ? 'selected' : '' }}>
										{{ $item->nama }} ({{ $item->PIN }})
									</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-3 form-group">
							<label for="start_date">Tanggal awal</label>
							<input class="form-control" type="date" id="start_date" name="start_date" value="{{ $startDate }}" {{ $employee ? '' : 'disabled' }} required>
						</div>
						<div class="col-md-3 form-group">
							<label for="end_date">Tanggal akhir</label>
							<input class="form-control" type="date" id="end_date" name="end_date" value="{{ $endDate }}" {{ $employee ? '' : 'disabled' }} required>
						</div>
						<div class="col-md-2 form-group">
							<label>&nbsp;</label>
							<button class="btn btn-primary btn-block" type="submit"><i class="fa fa-search"></i> Tampilkan</button>
						</div>
					</div>
				</form>
				@if($errors->any())
					<div class="alert alert-danger">{{ $errors->first() }}</div>
				@endif
				@if($employee)
					<p><strong>{{ $employee->nama }}</strong> | Kategori: {{ $employee->kategori }} | Sub kategori: {{ $employee->sub_kategori }} | Gender: {{ $employee->gender }} | PIN: {{ $employee->PIN }}</p>
				@endif
			</div>
		</div>

		@if($employee)
			<div class="row">
				<div class="col-md-6">
					<div class="box box-info">
						<div class="box-header with-border"><h3 class="box-title">Data Checktime</h3></div>
						<div class="box-body table-responsive">
							<table id="iclockTable" class="table table-bordered table-striped table-condensed">
								<thead><tr><th>ID</th><th>Badgenumber</th><th>Checktime</th></tr></thead>
								<tbody>
									@foreach($iclockRecords as $record)
										<tr><td>{{ $record->id }}</td><td>{{ $record->badgenumber }}</td><td>{{ $record->checktime }}</td></tr>
									@endforeach
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
									@foreach($attendanceSummary as $day)
										<tr>
											<td>{{ $day->date }}</td>
											<td>{{ $day->day_name }}</td>
											<td>
												@if($day->status === 'Hadir')
													<span class="label label-success">Hadir</span>
												@elseif($day->status === 'Absen')
													<span class="label label-danger">Absen</span>
												@endif
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		@endif
	</section>
</div>
@endsection
@section('Scripts')
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
@endsection
