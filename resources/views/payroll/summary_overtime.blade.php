@extends('layouts/admin')
@section('Contents')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Payroll
                <small>Summary Overtime</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Summary Overtime</li>
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

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-ban"></i> Error!</h4>
                    {{ session('error') }}
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
                            <form action="{{ url('/payroll/summary_overtime') }}" method="GET" class="form-inline" style="display: inline-block;">
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
                            </form>

                            <form action="{{ url('/payroll/save_summary') }}" method="POST" style="display: inline-block; margin-left: 10px;">
                                @csrf
                                <input type="hidden" name="start" value="{{ $start }}">
                                <input type="hidden" name="end" value="{{ $end }}">
                                <button type="submit" class="btn btn-success" onclick="return confirm('Apakah anda yakin ingin menyimpan data ke table summary?')">
                                    <i class="fa fa-save"></i> Capture
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Data Box -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-list"></i> Summary Overtime ({{ $start }} - {{ $end }})</h3>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="payroll-table" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Dept</th>
                                            <th>NIK</th>
                                            <th>Employee Name</th>
                                            <th>SLPJ</th>
                                            <th>Hours Convertion</th>
                                            <th>Total Overtime</th>
                                            <th>Total Meal</th>
                                            <th>Total Bayar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tb1 as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->dept_code }}</td>
                                                <td>{{ $item->NIK }}</td>
                                                <td>{{ $item->employee_name }}</td>
                                                <td>{{number_format($item->slpj,0)}}</td>
                                                <td>{{ number_format($item->hours_convertion, 2) }}</td>
                                                <td>{{ number_format($item->total_overtime, 0, ',', '.') }}</td>
                                                <td>{{ number_format($item->total_meal, 0, ',', '.') }}</td>
                                                <td>{{ number_format($item->total_bayar, 0, ',', '.') }}</td>
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
