@extends('layouts/admin')
@section('Contents')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Payroll
                <small>Overtime Summary Detail</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li>Overtime Summary</li>
                <li class="active">Summary Detail</li>
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
                            <div class="pull-right">
    							<a href="{{ url('/payroll/tax_overtime/'.$start.'/'.$end) }}" class="btn btn-default btn-md"><i class="fa fa-angle-double-left"></i> &nbsp;Back</a>
                                <button class="btn btn-success btn-md" onclick=""><i class="fa fa-file-excel-o"></i> &nbsp;Save as Excel</button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Import -->
                    <div class="modal fade" id="modal-import" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-aqua">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Import Rapel</h4>
                                </div>
                                <form action="{{ url('/payroll/import_rapel') }}" method="POST" enctype="multipart/form-data">
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

                    <!-- Data Box -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-list"></i> Overtime Summary ({{ $start }} - {{ $end }})</h3>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="payroll-table" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Dept</th>
                                            <th>CC_Code</th>
                                            <th>Employee Name</th>
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
                                                <td>{{ number_format($item->rapel_amount, 0) }}</td>
                                                <td>{{ number_format($item->gross_amount, 0) }}</td>
                                                <td>{{ number_format($item->pph21_amount, 0) }}</td>
                                                <td>{{ number_format($item->net_amount, 0) }}</td>
                                                <td>{{ $item->nomor_rekening }}</td>
                                                <td>{{ number_format($item->net_amount, 0) }}</td>
                                                <td>{{ $item->employee_name }}</td>
                                                <td>
                                                    <a href="{{ url('/payroll/slip/overtime/'.$start.'/'.$end.'/'.$item->id_employee) }}" class="btn btn-xs btn-primary"><i class="fa fa-print"></i></a>
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
        $(document).ready(function() {
            $('#payroll-table').DataTable({
                'paging'      : true,
                'lengthChange': true,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : false,
                "pageLength"  : 10,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'print'
                ]
            });
        });
    </script>
@endsection
