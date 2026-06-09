@extends('layouts/admin')
@section('Contents')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Payroll
                <small>Overtime Summary</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Overtime Summary</li>
            </ol>
        </section>

        <section class="content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Success!</h4>
                    {{ session('success') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <!-- Filter Box -->
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-filter"></i> Filter Periode</h3>
                        </div>
                        <div class="box-body">
                            <form action="{{ url('/payroll/tax_overtime') }}" method="GET" class="form-inline"
                                style="display: inline-block;">
                                <div class="form-group">
                                    <label for="start">Start Date: </label>
                                    <input type="date" name="start" id="start" class="form-control" value="{{ $start }}">
                                </div>
                                <div class="form-group" style="margin-left: 10px;">
                                    <label for="end">End Date: </label>
                                    <input type="date" name="end" id="end" class="form-control" value="{{ $end }}">
                                </div>
                                <button type="submit" class="btn btn-primary" style="margin-left: 10px;">
                                    <i class="fa fa-search"></i> Filter
                                </button>
                                <button type="button" class="btn btn-info" style="margin-left: 5px;" data-toggle="modal"
                                    data-target="#modal-import">
                                    <i class="fa fa-file-excel-o"></i> Rapel
                                </button>
                            </form>
                            <form action="{{ url('/payroll/collect_meals') }}" method="POST" style="display: inline-block;">
                                @csrf
                                <input type="hidden" name="start" value="{{ $start }}">
                                <input type="hidden" name="end" value="{{ $end }}">
                                <button type="submit" class="btn btn-info" style="margin-left: 5px;">
                                    <i class="fa fa-cutlery"></i> Meals
                                </button>
                            </form>&nbsp;&nbsp;
                            <form action="{{ url('/payroll/tax_overtime/calculation') }}" method="POST"
                                style="display: inline-block;">
                                @csrf
                                <input type="hidden" name="start" value="{{ $start }}">
                                <input type="hidden" name="end" value="{{ $end }}">
                                <button type="submit" class="btn btn-warning" style="margin-left: 5px;">
                                    <i class="fa fa-calculator"></i> Tax Calculation
                                </button>
                            </form>&nbsp;&nbsp;
                            <a href="{{ url('/payroll/tax_overtime_excel/' . $start . '/' . $end) }}"
                                class="btn btn-success btn-md" style="margin-left: 5px;"><i class="fa fa-file-excel-o"></i>
                                &nbsp;Excel</a>
                            <a href="{{ url('/payroll/distribute_spl_slip/' . $start . '/' . $end) }}"
                                class="btn btn-primary btn-md" style="margin-left: 5px;"><i class="fa fa-envelope"></i>
                                &nbsp;Distribute</a>
                        </div>
                    </div>

                    <!-- Modal Import -->
                    <div class="modal fade" id="modal-import" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-aqua">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Import Rapel</h4>
                                </div>
                                <form action="{{ url('/payroll/import_rapel') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="start" value="{{ $start }}">
                                    <input type="hidden" name="end" value="{{ $end }}">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="file">Excel File</label>
                                            <input type="file" name="file" id="file" class="form-control" required>
                                            <p class="help-block">
                                                Format: Kolom A (NIK), Kolom B (Periode YYYY-MM), Kolom C (Amount).
                                                <br>
                                                <a href="{{ url('/payroll/download_format_rapel') }}" class="text-primary">
                                                    <i class="fa fa-download"></i> Download Format Excel
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Import Now</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Detail Overtime -->
                    <div class="modal fade" id="modal-detail-overtime" tabindex="-1" role="dialog"
                        aria-labelledby="modalOvertimeLabel">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-yellow">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="modalOvertimeLabel">
                                        <i class="fa fa-edit"></i> Detail Overtime &mdash; <span
                                            id="modal-employee-name"></span>
                                    </h4>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table id="table-detail-overtime"
                                            class="table table-bordered table-striped table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Date</th>
                                                    <th>Start</th>
                                                    <th>Finish</th>
                                                    <th>Hours</th>
                                                    <th>SLPJ</th>
                                                    <th>Amount</th>
                                                    <th>Capture</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-detail-overtime">
                                                <tr>
                                                    <td colspan="8" class="text-center">Loading...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">
                                        <i class="fa fa-times"></i> Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Box -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-list"></i> Overtime Summary ({{ $start }} - {{ $end }})
                            </h3>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="payroll-table" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Dept</th>
                                            <th>CC_Code</th>
                                            <th>Name</th>
                                            <th>NIK</th>
                                            <th>Position</th>
                                            <th>SLPJ</th>
                                            <th>Hours</th>
                                            <th>Convetion</th>
                                            <th>Amount</th>
                                            <th>Meal</th>
                                            <th>Rapel</th>
                                            <th>Final_Amount</th>
                                            <th>PPh21</th>
                                            <th>Amount</th>
                                            <th>Norek</th>
                                            <th>Total_Paid</th>
                                            <th>Nama</th>
                                            <th>Slip</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tb1 as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->dept_code }}</td>
                                                <td>{{ $item->cc_code }}</td>
                                                <td>{{ $item->employee_name }}</td>
                                                <td>{{ $item->nik }}</td>
                                                <td>{{ $item->position_name }}</td>
                                                <td>{{ number_format($item->slpj, 0) }}</td>
                                                <td>{{ number_format($item->hours_act, 2) }}</td>
                                                <td>{{ number_format($item->hour_convertion, 2) }}</td>
                                                <td>{{ number_format($item->ot_amount, 0) }}</td>
                                                <td>{{ number_format($item->meal_amount, 0) }}</td>
                                                <td>
                                                    {{ number_format($item->rapel_amount, 0) }}
                                                    <button type="button"
                                                        class="btn btn-xs btn-warning btn-detail-overtime pull-right"
                                                        data-id="{{ $item->id }}" data-id-employee="{{ $item->id_employee }}"
                                                        data-employee-name="{{ $item->employee_name }}" title="Detail Overtime">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </td>
                                                <td>{{ number_format($item->gross_amount, 0) }}</td>
                                                <td>{{ number_format($item->pph21_amount, 0) }}</td>
                                                <td>{{ number_format($item->net_amount, 0) }}</td>
                                                <td>{{ $item->nomor_rekening }}</td>
                                                <td>{{ number_format($item->net_amount, 0) }}</td>
                                                <td>{{ $item->employee_name }}</td>
                                                <td>
                                                    <a href="{{ url('/payroll/slip/overtime/' . $start . '/' . $end . '/' . $item->id_employee) }}"
                                                        class="btn btn-xs btn-primary"><i class="fa fa-print"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('Scripts')
    <script>
        $(document).ready(function () {
            $('#payroll-table').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': false,
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'print'
                ]
            });

            // Handle tombol Detail Overtime
            $(document).on('click', '.btn-detail-overtime', function () {
                var id = $(this).data('id');
                var idEmployee = $(this).data('id-employee');
                var employeeName = $(this).data('employee-name');

                // Set judul modal
                $('#modal-employee-name').text(employeeName);

                // Simpan id summary (untuk update rapel_amount)
                $('#modal-detail-overtime').data('summary-id', id);

                // Reset tbody
                $('#tbody-detail-overtime').html('<tr><td colspan="8" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');

                // Tampilkan modal
                $('#modal-detail-overtime').modal('show');

                // AJAX: ambil data overtime
                $.ajax({
                    url: '{{ url("/payroll/overtime_detail") }}',
                    type: 'GET',
                    data: {
                        id: id,
                        id_employee: idEmployee
                    },
                    success: function (response) {
                        var rows = '';
                        if (response.length === 0) {
                            rows = '<tr><td colspan="8" class="text-center text-muted">Tidak ada data</td></tr>';
                        } else {
                            $.each(response, function (i, row) {
                                var ammount = row.ammount ?? 0;
                                rows += '<tr>';
                                rows += '<td>' + (i + 1) + '</td>';
                                rows += '<td>' + (row.date_on ?? '-') + '</td>';
                                rows += '<td>' + (row.start_act ?? '-') + '</td>';
                                rows += '<td>' + (row.finish_act ?? '-') + '</td>';
                                rows += '<td>' + (row.hours_act ?? '-') + '</td>';
                                rows += '<td>' + numberFormat(row.SLPJ ?? 0) + '</td>';
                                rows += '<td>' + numberFormat(ammount) + '</td>';
                                rows += '<td><button type="button" class="btn btn-xs btn-success btn-capture-rapel"'
                                    + ' data-ammount="' + ammount + '"'
                                    + ' data-idtrans="' + row.idtrans + '"'
                                    + ' title="Capture ke Rapel">'
                                    + '<i class="fa fa-check-circle"></i> Capture</button></td>';
                                rows += '</tr>';
                            });
                        }
                        $('#tbody-detail-overtime').html(rows);
                    },
                    error: function () {
                        $('#tbody-detail-overtime').html('<tr><td colspan="8" class="text-center text-danger">Gagal memuat data. Silakan coba lagi.</td></tr>');
                    }
                });
            });

            // Handle tombol Capture
            $(document).on('click', '.btn-capture-rapel', function () {
                var ammount = $(this).data('ammount');
                var summaryId = $('#modal-detail-overtime').data('summary-id');
                var idtrans = $(this).data('idtrans');
                var $btn = $(this);

                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

                $.ajax({
                    url: '{{ url("/payroll/capture_rapel") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: summaryId,
                        rapel_amount: ammount,
                        idtrans: idtrans,
                    },
                    success: function (response) {
                        if (response.success) {
                            $btn.removeClass('btn-success').addClass('btn-default')
                                .html('<i class="fa fa-check"></i> Done');
                            setTimeout(function () { location.reload(); }, 800);

                        } else {
                            $btn.prop('disabled', false)
                                .html('<i class="fa fa-check-circle"></i> Capture');
                            alert('Gagal: ' + (response.message ?? 'Unknown error'));
                        }
                    },
                    error: function () {
                        $btn.prop('disabled', false)
                            .html('<i class="fa fa-check-circle"></i> Capture');
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    }
                });
            });

            function numberFormat(num) {
                return Number(num).toLocaleString('id-ID');
            }
        });
    </script>
@endsection