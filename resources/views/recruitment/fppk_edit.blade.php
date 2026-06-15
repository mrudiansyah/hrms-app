@extends('layouts/admin')
@section('Contents')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                FPPK
                <small>Form Permintaan Pengadaan Karyawan - Edit Data</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ url('/FPPK') }}">FPPK</a></li>
                <li class="active">Edit Data</li>
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
                            <h3 class="box-title"><i class="fa fa-edit"></i> Edit Form Permintaan Pengadaan Karyawan</h3>
                        </div>
                        <form action="{{ url('/FPPK/update/' . $fppk->id) }}" method="POST" role="form">
                            @csrf
                            @method('PUT')
                            <div class="box-body">
                                <!-- HEADER -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Registration Number</label>
                                            <input type="text" name="registration_number" class="form-control"
                                                value="{{ old('registration_number', $fppk->registration_number) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Application Status</label>
                                            <select name="application_status" class="form-control" required>
                                                <option value="">-- Pilih Status --</option>
                                                <option value="Baru" {{ old('application_status', $fppk->application_status) == 'Baru' ? 'selected' : '' }}>Baru</option>
                                                <option value="Penggantian" {{ old('application_status', $fppk->application_status) == 'Penggantian' ? 'selected' : '' }}>
                                                    Penggantian (Replacement)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- ORGANISASI -->
                                <div class="box box-default collapsed-box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-building"></i> Data Organisasi</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Division</label>
                                                    <input type="text" name="division" class="form-control"
                                                        value="{{ old('division', $fppk->division) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Department</label>
                                                    <input type="text" name="department" class="form-control"
                                                        value="{{ old('department', $fppk->department) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Section</label>
                                                    <input type="text" name="section" class="form-control"
                                                        value="{{ old('section', $fppk->section) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Jumlah Karyawan Pada Area Bagian (People)</label>
                                                    <input type="number" name="total_employee_section" class="form-control"
                                                        value="{{ old('total_employee_section', $fppk->total_employee_section) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Jumlah Pekerja Disetujui Dewan Direksi (People)</label>
                                                    <input type="number" name="total_employee_bod_approved"
                                                        class="form-control"
                                                        value="{{ old('total_employee_bod_approved', $fppk->total_employee_bod_approved) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- DESKRIPSI POSISI -->
                                <div class="box box-default collapsed-box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-briefcase"></i> Deskripsi Posisi Pekerjaan
                                        </h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Jabatan / Posisi</label>
                                                    <input type="text" name="position_job" class="form-control"
                                                        value="{{ old('position_job', $fppk->position_job) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Fungsi Pekerjaan & Tingkat/Golongan</label>
                                                    <input type="text" name="job_function_level" class="form-control"
                                                        value="{{ old('job_function_level', $fppk->job_function_level) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Employment Status</label>
                                                    <select name="employment_status" class="form-control">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="Permanent" {{ old('employment_status', $fppk->employment_status) == 'Permanent' ? 'selected' : '' }}>
                                                            Permanent</option>
                                                        <option value="Temporary" {{ old('employment_status', $fppk->employment_status) == 'Temporary' ? 'selected' : '' }}>
                                                            Temporary</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Employment Type</label>
                                                    <select name="employment_type" class="form-control">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="With Probation" {{ old('employment_type', $fppk->employment_type) == 'With Probation' ? 'selected' : '' }}>
                                                            With Probation</option>
                                                        <option value="Without Probation" {{ old('employment_type', $fppk->employment_type) == 'Without Probation' ? 'selected' : '' }}>Without Probation</option>
                                                        <option value="Contract" {{ old('employment_type', $fppk->employment_type) == 'Contract' ? 'selected' : '' }}>
                                                            Contract</option>
                                                        <option value="Job-Work" {{ old('employment_type', $fppk->employment_type) == 'Job-Work' ? 'selected' : '' }}>
                                                            Job-Work</option>
                                                        <option value="Daily" {{ old('employment_type', $fppk->employment_type) == 'Daily' ? 'selected' : '' }}>Daily
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Working Period</label>
                                                    <div class="row">
                                                        <div class="col-xs-6">
                                                            <input type="number" name="working_period_years"
                                                                class="form-control" placeholder="Years"
                                                                value="{{ old('working_period_years', $fppk->working_period_years) }}">
                                                        </div>
                                                        <div class="col-xs-6">
                                                            <input type="number" name="working_period_months"
                                                                class="form-control" placeholder="Months"
                                                                value="{{ old('working_period_months', $fppk->working_period_months) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Tanggal Mulai Bergabung</label>
                                                    <input type="date" name="starting_date" class="form-control"
                                                        value="{{ old('starting_date', $fppk->starting_date) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Jumlah Pekerja yang Dibutuhkan</label>
                                                    <input type="number" name="employee_number_needed" class="form-control"
                                                        value="{{ old('employee_number_needed', $fppk->employee_number_needed) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Tanggal Penerimaan Formulir</label>
                                                    <input type="date" name="date_received" class="form-control"
                                                        value="{{ old('date_received', $fppk->date_received) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Payment Term</label>
                                                    <select name="payment_term" class="form-control">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="Monthly" {{ old('payment_term', $fppk->payment_term) == 'Monthly' ? 'selected' : '' }}>Monthly
                                                        </option>
                                                        <option value="Weekly" {{ old('payment_term', $fppk->payment_term) == 'Weekly' ? 'selected' : '' }}>Weekly
                                                        </option>
                                                        <option value="Daily" {{ old('payment_term', $fppk->payment_term) == 'Daily' ? 'selected' : '' }}>Daily
                                                        </option>
                                                        <option value="Piece Rate" {{ old('payment_term', $fppk->payment_term) == 'Piece Rate' ? 'selected' : '' }}>Piece
                                                            Rate</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- PENAWARAN GAJI -->
                                <div class="box box-default collapsed-box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-money"></i> Penawaran Gaji</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Upah Minimal</label>
                                                    <input type="number" step="0.01" name="minimal_wage"
                                                        class="form-control"
                                                        value="{{ old('minimal_wage', $fppk->minimal_wage) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>
                                                        <input type="checkbox" name="follow_sae_regulation" value="1" {{ old('follow_sae_regulation', $fppk->follow_sae_regulation) ? 'checked' : '' }}>
                                                        Mengikuti Peraturan SAE's
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Catatan Penawaran Gaji</label>
                                                    <textarea name="salary_notes" class="form-control"
                                                        rows="2">{{ old('salary_notes', $fppk->salary_notes) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- TUGAS / TANGGUNG JAWAB -->
                                <div class="box box-default collapsed-box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-tasks"></i> Tugas / Tanggung Jawab</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="form-group">
                                            <textarea name="task_description" class="form-control"
                                                rows="4">{{ old('task_description', $fppk->task_description) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- PERSYARATAN PEKERJAAN -->
                                <div class="box box-default collapsed-box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-check-circle"></i> Persyaratan Pekerjaan &
                                            Pribadi</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Minimal Pendidikan Akhir</label>
                                                    <input type="text" name="min_education" class="form-control"
                                                        value="{{ old('min_education', $fppk->min_education) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Gender</label>
                                                    <select name="gender" class="form-control">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="Male" {{ old('gender', $fppk->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                                        <option value="Female" {{ old('gender', $fppk->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                                        <option value="Both" {{ old('gender', $fppk->gender) == 'Both' ? 'selected' : '' }}>Both</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Budget Status</label>
                                                    <select name="budget_status" class="form-control">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="Budgeted" {{ old('budget_status', $fppk->budget_status) == 'Budgeted' ? 'selected' : '' }}>Budgeted
                                                        </option>
                                                        <option value="Un-budgeted" {{ old('budget_status', $fppk->budget_status) == 'Un-budgeted' ? 'selected' : '' }}>
                                                            Un-budgeted</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Kemampuan Khusus</label>
                                                    <textarea name="special_ability" class="form-control"
                                                        rows="2">{{ old('special_ability', $fppk->special_ability) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Jenis Pengalaman Kerja</label>
                                                    <textarea name="work_experience_type" class="form-control"
                                                        rows="2">{{ old('work_experience_type', $fppk->work_experience_type) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Minimal Pengalaman (Years)</label>
                                                    <input type="number" name="min_work_experience_years"
                                                        class="form-control"
                                                        value="{{ old('min_work_experience_years', $fppk->min_work_experience_years) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Usia Minimal</label>
                                                    <input type="number" name="min_age" class="form-control"
                                                        value="{{ old('min_age', $fppk->min_age) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Usia Maksimal</label>
                                                    <input type="number" name="max_age" class="form-control"
                                                        value="{{ old('max_age', $fppk->max_age) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Kemampuan Komputer</label>
                                                    <input type="text" name="computer_mastery" class="form-control"
                                                        value="{{ old('computer_mastery', $fppk->computer_mastery) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Tinggi Minimal (cm)</label>
                                                    <input type="number" name="min_height_cm" class="form-control"
                                                        value="{{ old('min_height_cm', $fppk->min_height_cm) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Berat Minimal (kg)</label>
                                                    <input type="number" name="min_weight_kg" class="form-control"
                                                        value="{{ old('min_weight_kg', $fppk->min_weight_kg) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Berat Maksimal (kg)</label>
                                                    <input type="number" name="max_weight_kg" class="form-control"
                                                        value="{{ old('max_weight_kg', $fppk->max_weight_kg) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Kemampuan Bahasa Asing</label>
                                                    <input type="text" name="foreign_language_ability" class="form-control"
                                                        value="{{ old('foreign_language_ability', $fppk->foreign_language_ability) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Kepribadian / Character Personalities</label>
                                                    <textarea name="character_personalities" class="form-control"
                                                        rows="2">{{ old('character_personalities', $fppk->character_personalities) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ALASAN PENERIMAAN -->
                                <div class="box box-default collapsed-box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-question-circle"></i> Alasan Penerimaan</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="form-group">
                                            <textarea name="reason_for_recruiting" class="form-control"
                                                rows="3">{{ old('reason_for_recruiting', $fppk->reason_for_recruiting) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- KETERANGAN TAMBAHAN -->
                                <div class="box box-default collapsed-box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-comment"></i> Keterangan Tambahan</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="form-group">
                                            <textarea name="remarks" class="form-control"
                                                rows="2">{{ old('remarks', $fppk->remarks) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- APPROVAL INFO (Readonly) -->
                                <div class="box box-default collapsed-box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-check-square-o"></i> Informasi Approval</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Submitted By</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ $fppk->submitted_by ?? '-' }}" readonly disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Submitted Date</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ $fppk->submitted_date ?? '-' }}" readonly disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Approved By</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ $fppk->approved_by ?? '-' }}" readonly disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Approved Date</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ $fppk->approved_date ?? '-' }}" readonly disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Legalized By</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ $fppk->legalized_by ?? '-' }}" readonly disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Legalized Date</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ $fppk->legalized_date ?? '-' }}" readonly disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
                                <a href="{{ url('/FPPK') }}" class="btn btn-default"><i class="fa fa-arrow-left"></i>
                                    Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('Scripts')
    <script>
        $(document).ready(function () {
            // Format rupiah display jika diperlukan
            $('input[type="number"]').on('change', function () {
                // Optional: Add number formatting
            });
        });
    </script>
@endsection