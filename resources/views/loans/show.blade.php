@extends('layouts/payroll_recap')
@section('Contents')
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary" style="background:#FFF;">
                    <div class="box-header with-border">
                        <h3 class="box-title">Loan Details</h3>
                        <div class="pull-right">
                            <a href="{{ route('loans.index') }}" class="btn btn-default btn-sm">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 col-xs-12">
                                <h4>Employee Installment</h4>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th style="width:150px;">Employee</th>
                                        <td>{{ $loan->nama_karyawan }} ({{ $loan->NIK }})</td>
                                    </tr>
                                    <tr>
                                        <th>Position</th>
                                        <td>{{ $loan->jabatan }}</td>
                                    </tr>
                                    <tr>
                                        <th>Department</th>
                                        <td>{{ $loan->department }}</td>
                                    </tr>
                                    <tr>
                                        <th>Amount Loan</th>
                                        <td align="right"><strong>{{ number_format($loan->amount_loan, 0, ',', '.') }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Amount Per Month</th>
                                        <td align="right">{{ number_format($loan->amount_per_month, 0, ',', '.') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-lg-8 col-md-8 col-xs-12">
                                <h4>Installment Schedule</h4>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width:50px;">No</th>
                                            <th style="width:120px;">Periode</th>
                                            <th style="width:150px;">Payment Date</th>
                                            <th style="width:100px;">Amount</th>
                                            <th style="width:100px;">Status</th>
                                            <th style="width:150px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($loanDetails as $detail)
                                            <tr>
                                                <td align="center">{{ $detail->sequance }}</td>
                                                <td align="center">{{ $detail->periode }}</td>
                                                <td align="center">{{ $detail->payment_date }}</td>
                                                <td align="center">{{ number_format($detail->amount_per_month,0) }}</td>
                                                <td align="center">
                                                    @if($detail->status == 0)
                                                        <span class="label label-success">PAID</span>
                                                    @else
                                                        <span class="label label-danger">UNPAID</span>
                                                    @endif
                                                </td>
                                                <td align="center">
                                                    @if($detail->status == 1)
                                                        <button class="btn btn-success btn-xs mark-paid" data-id="{{ $detail->id }}" data-seq="{{ $detail->sequance }}">
                                                            <i class="fa fa-check"></i> Mark as Paid
                                                        </button>
                                                    @else
                                                        <button class="btn btn-warning btn-xs mark-unpaid" data-id="{{ $detail->id }}" data-seq="{{ $detail->sequance }}">
                                                            <i class="fa fa-undo"></i> Mark as Unpaid
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    </section>
@endsection

@section('Scripts')
    <script>
        // Mark as paid
        $(document).on('click', '.mark-paid', function() {
            var id = $(this).data('id');
            var seq = $(this).data('seq');
            
            if (confirm('Mark installment #' + seq + ' as PAID?')) {
                $.ajax({
                    url: "{{ route('loans.detail.status.update', ':id') }}".replace(':id', id),
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        status: 0,
                        _method: 'PUT'
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Failed to update status. Please try again.');
                    }
                });
            }
        });

        // Mark as unpaid
        $(document).on('click', '.mark-unpaid', function() {
            var id = $(this).data('id');
            var seq = $(this).data('seq');
            
            if (confirm('Mark installment #' + seq + ' as UNPAID?')) {
                $.ajax({
                    url: "{{ route('loans.detail.status.update', ':id') }}".replace(':id', id),
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        status: 1,
                        _method: 'PUT'
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Failed to update status. Please try again.');
                    }
                });
            }
        });
    </script>
@endsection