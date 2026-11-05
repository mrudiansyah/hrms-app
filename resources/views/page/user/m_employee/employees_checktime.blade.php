@extends('layouts/admin')
@section('Contents')
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
								@foreach($employees as $item)
									<option value="{{ $item->id }}" {{ (string) request('employee_id') === (string) $item->id ? 'selected' : '' }}>
										{{ $item->employee_name }} ({{ $item->NIK }})
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
					<p><strong>{{ $employee->employee_name }}</strong> | NIK: {{ $employee->NIK }} | PIN: {{ $employee->PIN }} | Badge: {{ $employee->badgenumber }}</p>
					<p class="text-muted">Badge number pada iClock dicocokkan dalam format 9 digit dengan penambahan nol di bagian depan.</p>
				@endif
			</div>
		</div>

		@if($employee)
			<div class="row">
				<div class="col-md-5">
					<div class="box box-info">
						<div class="box-header with-border"><h3 class="box-title">iClock (tb_iclock)</h3></div>
						<div class="box-body table-responsive">
							<table id="iclockTable" class="table table-bordered table-striped table-condensed">
								<thead><tr><th>Badge</th><th>Nama</th><th>Checktime</th></tr></thead>
								<tbody>
									@foreach($iclockRecords as $record)
										<tr><td>{{ $record->badgenumber }}</td><td>{{ $record->name }}</td><td>{{ $record->checktime }}</td></tr>
									@endforeach
								</tbody>
							</table>
							<div id="iclock-download" class="text-right"></div>
						</div>
					</div>
				</div>
				<div class="col-md-7">
					<div class="box box-success">
						<div class="box-header with-border"><h3 class="box-title">Checktime Mobile (tb_checktimes)</h3></div>
						<div class="box-body table-responsive">
							<table id="mobileChecktimeTable" class="table table-bordered table-striped table-condensed">
								<thead><tr><th>Nama</th><th>Badge (PIN)</th><th>Checktime</th><th>Latitude</th><th>Longitude</th><th>Lokasi</th></tr></thead>
								<tbody>
									@foreach($mobileChecktimeRecords as $record)
										<tr>
											<td>{{ $record->employee_name }}</td>
											<td>{{ $record->PIN }}</td>
											<td>{{ $record->checktime }}</td>
											<td>@if(is_numeric($record->latitut)){{ number_format((float) $record->latitut, 4, '.', '') }}@endif</td>
											<td>@if(is_numeric($record->longitut)){{ number_format((float) $record->longitut, 4, '.', '') }}@endif</td>
											<td>
												@if(is_numeric($record->latitut) && is_numeric($record->longitut))
													<button type="button" class="btn btn-xs btn-info preview-location" data-lat="{{ $record->latitut }}" data-lng="{{ $record->longitut }}" title="Preview lokasi di peta"><i class="fa fa-map-marker"></i> Peta</button>
												@endif
											</td>
										</tr>
									@endforeach
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
		@endif
	</section>
</div>
@endsection
@section('Scripts')
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
@endsection