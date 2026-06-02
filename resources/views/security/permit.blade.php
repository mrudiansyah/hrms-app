@extends('layouts/admin')
@section('Contents')
   <meta name="csrf-token" content="{{ csrf_token() }}">
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
								@foreach($data as $idx => $dt)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>{{ $dt->employee_name }}</td>
                                        <td>{{ $dt->NIK }}</td>
                                        <td>{{ $dt->department }}</td>
                                        <td align="center"><span class="label label-info">{{ $dt->category }}</span></td>
                                        <td>{{ date('d-m-Y', strtotime($dt->apply_date)) }}</td>
                                        <td align="center">
                                            <div style="display:flex; flex-direction:column; align-items:center;">
                                                <input type="checkbox" class="security-update" data-id="{{ $dt->id }}" data-type="checkout" {{ !empty($dt->mo_checkout) ? 'checked disabled' : '' }}>
                                                <span class="time-label" style="font-size:11px; color:#555;">
                                                    {{ !empty($dt->mo_checkout) ? date('H:i', strtotime($dt->mo_checkout)) : '' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td align="center">
                                            <div style="display:flex; flex-direction:column; align-items:center;">
                                                <input type="checkbox" class="security-update" data-id="{{ $dt->id }}" data-type="checkin" {{ !empty($dt->mo_checkin) ? 'checked disabled' : '' }} {{ empty($dt->mo_checkout) ? 'disabled' : '' }}>
                                                <span class="time-label" style="font-size:11px; color:#555;">
                                                    {{ !empty($dt->mo_checkin) ? date('H:i', strtotime($dt->mo_checkin)) : '' }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		</section>
  	</div>
@endsection

@section('Scripts')
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
                    url: '{{ url("/scurity/update-permit") }}',
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
@endsection
