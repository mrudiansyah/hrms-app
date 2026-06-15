@extends('layouts/admin')
@section('Contents')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Employee Status Renewal
                <small>Form Pembaharuan Status Karyawan</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('renewal.index') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('renewal.index') }}">Renewal</a></li>
                <li class="active">Form</li>
            </ol>
        </section>

        <section class="content">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-ban"></i> Error!</h4>
                    {{ session('error') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                <i class="fa fa-plus"></i> Form Employee Status Renewal
                            </h3>
                            <div class="box-tools pull-right">
                                <a href="{{ route('renewal.index') }}" class="btn btn-default btn-sm">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <form action="{{ route('renewal.store') }}" method="POST">
                            @csrf
                            <div class="box-body">
                                <!-- Data Karyawan -->
                                <div class="box box-default collapsed-box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-user"></i> Data Karyawan</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>NIK</label>
                                                    <input type="text" name="nik" class="form-control"
                                                        value="{{ old('nik', $employeeData->nik ?? '') }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Nama Karyawan</label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="{{ old('name', $employeeData->name ?? '') }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Tanggal Bergabung</label>
                                                    <input type="date" name="date_of_join" class="form-control"
                                                        value="{{ old('date_of_join', $employeeData->date_of_join ?? '') }}"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Masa Kerja</label>
                                                    <input type="text" name="work_period" class="form-control"
                                                        value="{{ old('work_period', $employeeData->work_period ?? '') }}"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Posisi Saat Ini</label>
                                                    <input type="text" name="last_position" class="form-control"
                                                        value="{{ old('last_position', $employeeData->position ?? '') }}"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Departemen</label>
                                                    <input type="text" name="last_division" class="form-control"
                                                        value="{{ old('last_division', $employeeData->department_id ?? '') }}"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Data Perubahan -->
                                <div class="box box-success">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-exchange"></i> Data Perubahan Status</h3>
                                    </div>
                                    <div class="box-body">
                                        <!-- BARIS BARU: CATEGORY -->
                                         <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Category <span class="text-danger">*</span></label>
                                                    <select name="category" id="category" class="form-control" required>
                                                        <option value="">-- Pilih Category --</option>
                                                        <option value="Promotion" {{ old('category') == 'Promotion' ? 'selected' : '' }}>Promotion (Kenaikan Pangkat/Gaji)</option>
                                                        <option value="Mutation" {{ old('category') == 'Mutation' ? 'selected' : '' }}>Mutation (Mutasi)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Posisi Baru <span class="text-danger">*</span></label>
                                                    <select name="new_position" class="form-control" required>
                                                        <option value="">-- Pilih Posisi --</option>
                                                        @foreach($positions as $position)
                                                            <option value="{{ $position->position_name }}" {{ old('new_position') == $position->position_name ? 'selected' : '' }}>
                                                                {{ $position->position_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Grade Baru <span class="text-danger">*</span></label>
                                                    <input type="text" name="new_grade" class="form-control"
                                                        value="{{ old('new_grade') }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Divisi/Sub Divisi Baru <span class="text-danger">*</span></label>
                                                    <input type="text" name="new_division" class="form-control"
                                                        value="{{ old('new_division') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Gaji Baru <span class="text-danger" id="salary_required">*</span></label>
                                                    <input type="text" name="new_salary" id="new_salary" class="form-control currency"
                                                        value="{{ old('new_salary') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Lain-lain (Tunjangan dll)</label>
                                                    <input type="text" name="new_others" id="new_others" class="form-control"
                                                        value="{{ old('new_others') }}">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Alasan Perubahan <span class="text-danger">*</span></label>
                                                    <textarea name="reasons" class="form-control" rows="4"
                                                        required>{{ old('reasons') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Berlaku Efektif Mulai <span class="text-danger">*</span></label>
                                                    <input type="date" name="effective_from" class="form-control"
                                                        value="{{ old('effective_from', date('Y-m-d')) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Suggested By</label>
                                                    <select name="suggested_by_id" id="suggested_by_id" class="form-control select2" style="width: 100%;">
                                                        <option value="">-- Pilih Karyawan --</option>
                                                        @foreach($employees as $emp)
                                                            <option value="{{ $emp->id }}">{{ $emp->NIK }} - {{ $emp->employee_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Supported By</label>
                                                    <select name="supported_by_id" id="supported_by_id" class="form-control select2" style="width: 100%;">
                                                        <option value="">-- Pilih Karyawan --</option>
                                                        @foreach($employees as $emp)
                                                            <option value="{{ $emp->id }}">{{ $emp->NIK }} - {{ $emp->employee_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>

                                <!-- Approval Signatures -->
                                <div class="box box-default collapsed-box" style="display: none;">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-check-circle"></i> Approval Signatures</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="box box-info collapsed-box">
                                                    <div class="box-header with-border">
                                                        <h4 class="box-title">SUGGESTED BY & SUPPORTED BY</h4>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool"
                                                                data-widget="collapse"><i class="fa fa-plus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>SUGGESTED BY - NAME</label>
                                                                    <input type="text" name="suggested_by_name"
                                                                        class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>SUGGESTED BY - DATE</label>
                                                                    <input type="date" name="suggested_by_date"
                                                                        class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>SUPPORTED BY - NAME</label>
                                                                    <input type="text" name="supported_by_name"
                                                                        class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>SUPPORTED BY - DATE</label>
                                                                    <input type="date" name="supported_by_date"
                                                                        class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="box box-warning collapsed-box">
                                                    <div class="box-header with-border">
                                                        <h4 class="box-title">NOTES FROM PERSONNEL DEPT.</h4>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool"
                                                                data-widget="collapse"><i class="fa fa-plus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>NAME</label>
                                                                    <input type="text" name="personnel_name"
                                                                        class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>DATE</label>
                                                                    <input type="date" name="personnel_date"
                                                                        class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Kehadiran (Attendance)</label>
                                                                    <select name="personnel_attendance"
                                                                        class="form-control">
                                                                        <option value="">-- Pilih --</option>
                                                                        <option value="Baik">Baik</option>
                                                                        <option value="Cukup">Cukup</option>
                                                                        <option value="Kurang">Kurang</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Ketepatan Waktu (Punctuality)</label>
                                                                    <select name="personnel_punctuality"
                                                                        class="form-control">
                                                                        <option value="">-- Pilih --</option>
                                                                        <option value="Baik">Baik</option>
                                                                        <option value="Cukup">Cukup</option>
                                                                        <option value="Kurang">Kurang</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="box box-primary collapsed-box">
                                                    <div class="box-header with-border">
                                                        <h4 class="box-title">PROPOSED BY</h4>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool"
                                                                data-widget="collapse"><i class="fa fa-plus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>NAME</label>
                                                                    <input type="text" name="proposed_by_name"
                                                                        class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>DATE</label>
                                                                    <input type="date" name="proposed_by_date"
                                                                        class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="box box-success collapsed-box">
                                                    <div class="box-header with-border">
                                                        <h4 class="box-title">APPROVED BY</h4>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool"
                                                                data-widget="collapse"><i class="fa fa-plus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>NAME</label>
                                                                    <input type="text" name="approved_by_name"
                                                                        class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>DATE</label>
                                                                    <input type="date" name="approved_by_date"
                                                                        class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Simpan
                                </button>
                                <a href="{{ route('renewal.index') }}" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('Scripts')
    <!-- Select2 CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize Select2 untuk Suggested By
            $('#suggested_by_id').select2({
                placeholder: "Cari NIK atau Nama Karyawan...",
                allowClear: true,
                width: '100%'
            });
            
            // Initialize Select2 untuk Supported By
            $('#supported_by_id').select2({
                placeholder: "Cari NIK atau Nama Karyawan...",
                allowClear: true,
                width: '100%'
            });
            
            // Format currency
            $('.currency').on('keyup', function () {
                var value = $(this).val();
                value = value.replace(/[^0-9]/g, '');
                if (value) {
                    value = parseInt(value).toLocaleString('id-ID');
                    $(this).val(value);
                }
            });

            $('.currency').on('submit', function () {
                var value = $(this).val();
                value = value.replace(/\./g, '');
                $(this).val(value);
            });
        });
    </script>
    
    <script>
        $(document).ready(function() {
            // Fungsi untuk disable/enable salary dan tunjangan
            function toggleSalaryAndAllowance() {
                var category = $('#category').val();
                
                if (category === 'Mutation') {
                    // Disable gaji dan tunjangan
                    $('#new_salary').prop('disabled', true).val('');
                    $('#new_others').prop('disabled', true).val('');
                    $('#salary_required').hide();
                    // Hapus required attribute
                    $('#new_salary').removeAttr('required');
                } else if (category === 'promotion') {
                    // Enable gaji dan tunjangan
                    $('#new_salary').prop('disabled', false);
                    $('#new_others').prop('disabled', false);
                    $('#salary_required').show();
                    // Tambah required attribute
                    $('#new_salary').attr('required', true);
                } else {
                    // Default: enable
                    $('#new_salary').prop('disabled', false);
                    $('#new_others').prop('disabled', false);
                    $('#salary_required').show();
                    $('#new_salary').attr('required', true);
                }
            }
            
            // Panggil fungsi saat halaman load
            toggleSalaryAndAllowance();
            
            // Panggil fungsi saat category berubah
            $('#category').on('change', function() {
                toggleSalaryAndAllowance();
            });
            
            // Format currency
            $('.currency').on('keyup', function() {
                var value = $(this).val();
                value = value.replace(/[^0-9]/g, '');
                if (value && !$(this).prop('disabled')) {
                    value = parseInt(value).toLocaleString('id-ID');
                    $(this).val(value);
                }
            });
            
            // Format sebelum submit
            $('form').on('submit', function() {
                $('.currency').each(function() {
                    if (!$(this).prop('disabled')) {
                        var value = $(this).val();
                        value = value.replace(/\./g, '');
                        $(this).val(value);
                    }
                });
            });
        });
    </script>
@endsection