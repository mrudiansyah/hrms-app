@extends('layouts/payroll_recap')
@section('Contents')
    <section class="content">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sx-12">
                <div class="box box-primary" style="background:#FFF;">
                    <div class="box-header with-border">
                        <h3 class="box-title">Create New Loan</h3>
                        <div class="pull-right">
                            <a href="{{ route('loans.index') }}" class="btn btn-default btn-sm">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('loans.store') }}" method="POST">
                            {{ csrf_field() }}
                            
                            <div class="form-group">
                                <label>Employee <span class="text-danger">*</span></label>
                                <select id="id_employee" name="id_employee" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Select Employee --</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id_employee }}" {{ old('id_employee') == $emp->id_employee ? 'selected' : '' }}>
                                            {{ $emp->NIK }} - {{ $emp->nama_karyawan }} ({{ $emp->jabatan }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Loan Amount <span class="text-danger">*</span></label>
                                <input type="text" id="amount_loan" name="amount_loan" class="form-control" 
                                       value="{{ old('amount_loan') }}" 
                                       placeholder="Enter loan amount" required>
                            </div>

                            <div class="form-group">
                                <label>Amount Per Month <span class="text-danger">*</span></label>
                                <input type="text" id="amount_per_month" name="amount_per_month" class="form-control" 
                                       value="{{ old('amount_per_month') }}" 
                                       placeholder="Enter installment amount per month" required>
                            </div>

                            <div class="form-group">
                                <label>Start Period <span class="text-danger">*</span></label>
                                <input type="month" id="start_periode" name="start_periode" class="form-control" 
                                       value="{{ old('start_periode', date('Y-m')) }}" required>
                            </div>

                            <div class="form-group">
                                <label>Total Months (Installments) <span class="text-danger">*</span></label>
                                <input type="number" id="total_months" name="total_months" class="form-control" 
                                       value="{{ old('total_months', 12) }}" 
                                       placeholder="Enter number of months" required min="1">
                                <input type="hidden" id="admin" name="admin" class="form-control" 
                                value="{{ old('admin', $admin) }}">
                            </div>

                            <div class="form-group">
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> 
                                    Loan details will be generated automatically based on the start period and total months.
                                    Payment date will be set to the 25th of each month.
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-md">
                                    <i class="fa fa-save"></i> Create Loan
                                </button>
                                <a href="{{ route('loans.index') }}" class="btn btn-default btn-md">
                                    <i class="fa fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
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
        // Format currency input
        $(document).on('keyup', '#amount_loan, #amount_per_month', function() {
            var value = this.value.replace(/[^,\d]/g, '');
            if (value) {
                var split = value.split(',');
                var sisa = split[0].length % 3;
                var rupiah = split[0].substr(0, sisa);
                var ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                
                if (ribuan) {
                    var separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                
                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                this.value = rupiah;
            }
        });

        // Auto-calculate total months based on loan amount and per month
        $('#amount_loan, #amount_per_month').on('change keyup', function() {
            var loan = parseFloat($('#amount_loan').val().replace(/\./g, '').replace(/,/g, '.')) || 0;
            var perMonth = parseFloat($('#amount_per_month').val().replace(/\./g, '').replace(/,/g, '.')) || 0;
            
            if (loan > 0 && perMonth > 0) {
                var months = Math.ceil(loan / perMonth);
                $('#total_months').val(months);
            }
        });

        // Initialize selectpicker
        $('.selectpicker').selectpicker();
    </script>
@endsection