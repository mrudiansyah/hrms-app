@extends('layouts/admin')
@section('Contents')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                FPPK
                <small>Form Permintaan Pengadaan Karyawan - Tambah Data</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ url('/FPPK') }}">FPPK</a></li>
                <li class="active">Tambah Data</li>
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
                            <h3 class="box-title"><i class="fa fa-plus"></i> Form Permintaan Pengadaan Karyawan</h3>
                        </div>
                        <form action="{{ url('/FPPK/store') }}" method="POST" role="form">
                            @csrf
                            <div class="box-body">
                                <!-- HEADER -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Registration Number</label>
                                            <input type="text" name="registration_number" class="form-control"
                                                placeholder="Auto generated atau diisi manual"
                                                value="FPPK-{{ date('Y') }}-{{ rand(100, 999) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Application Status</label>
                                            <select name="application_status" class="form-control" required>
                                                <option value="">-- Pilih Status --</option>
                                                <option value="Baru">Baru</option>
                                                <option value="Penggantian">Penggantian (Replacement)</option>
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
                                                        placeholder="Division">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Department</label>
                                                    <input type="text" name="department" class="form-control"
                                                        placeholder="Department">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Section</label>
                                                    <input type="text" name="section" class="form-control"
                                                        placeholder="Section">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Jumlah Karyawan Pada Area Bagian (People)</label>
                                                    <input type="number" name="total_employee_section" class="form-control"
                                                        placeholder="0">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Jumlah Pekerja Disetujui Dewan Direksi (People)</label>
                                                    <input type="number" name="total_employee_bod_approved"
                                                        class="form-control" placeholder="0">
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
                                                        placeholder="Jabatan / Posisi">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Fungsi Pekerjaan & Tingkat/Golongan</label>
                                                    <input type="text" name="job_function_level" class="form-control"
                                                        placeholder="Fungsi Pekerjaan & Tingkat/Golongan">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Employment Status</label>
                                                    <select name="employment_status" class="form-control">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="Permanent">Permanent</option>
                                                        <option value="Temporary">Temporary</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Employment Type</label>
                                                    <select name="employment_type" class="form-control">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="With Probation">With Probation</option>
                                                        <option value="Without Probation">Without Probation</option>
                                                        <option value="Contract">Contract</option>
                                                        <option value="Job-Work">Job-Work</option>
                                                        <option value="Daily">Daily</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Working Period</label>
                                                    <div class="row">
                                                        <div class="col-xs-6">
                                                            <input type="number" name="working_period_years"
                                                                class="form-control" placeholder="Years">
                                                        </div>
                                                        <div class="col-xs-6">
                                                            <input type="number" name="working_period_months"
                                                                class="form-control" placeholder="Months">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Tanggal Mulai Bergabung</label>
                                                    <input type="date" name="starting_date" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Jumlah Pekerja yang Dibutuhkan</label>
                                                    <input type="number" name="employee_number_needed" class="form-control"
                                                        placeholder="0">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Tanggal Penerimaan Formulir</label>
                                                    <input type="date" name="date_received" class="form-control"
                                                        value="{{ date('Y-m-d') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Payment Term</label>
                                                    <select name="payment_term" class="form-control">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="Monthly">Monthly</option>
                                                        <option value="Weekly">Weekly</option>
                                                        <option value="Daily">Daily</option>
                                                        <option value="Piece Rate">Piece Rate</option>
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
                                                        class="form-control" placeholder="0">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>
                                                        <input type="checkbox" name="follow_sae_regulation" value="1">
                                                        Mengikuti Peraturan SAE's
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Catatan Penawaran Gaji</label>
                                                    <textarea name="salary_notes" class="form-control" rows="2"
                                                        placeholder="Catatan tambahan tentang penawaran gaji..."></textarea>
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
                                            <textarea name="task_description" class="form-control" rows="4"
                                                placeholder="Tugas/Tanggung Jawab & Deskripsi Pekerjaan..."></textarea>
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
                                                        placeholder="SMA/SMK/D3/S1/S2">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Gender</label>
                                                    <select name="gender" class="form-control">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                        <option value="Both">Both</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Budget Status</label>
                                                    <select name="budget_status" class="form-control">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="Budgeted">Budgeted</option>
                                                        <option value="Un-budgeted">Un-budgeted</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Kemampuan Khusus</label>
                                                    <textarea name="special_ability" class="form-control" rows="2"
                                                        placeholder="Kemampuan Khusus..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Jenis Pengalaman Kerja</label>
                                                    <textarea name="work_experience_type" class="form-control" rows="2"
                                                        placeholder="Jenis Pengalaman Kerja yang dibutuhkan..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Minimal Pengalaman (Years)</label>
                                                    <input type="number" name="min_work_experience_years"
                                                        class="form-control" placeholder="0">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Usia Minimal</label>
                                                    <input type="number" name="min_age" class="form-control"
                                                        placeholder="0">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Usia Maksimal</label>
                                                    <input type="number" name="max_age" class="form-control"
                                                        placeholder="0">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Kemampuan Komputer</label>
                                                    <input type="text" name="computer_mastery" class="form-control"
                                                        placeholder="Ms Office, dll">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Tinggi Minimal (cm)</label>
                                                    <input type="number" name="min_height_cm" class="form-control"
                                                        placeholder="0">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Berat Minimal (kg)</label>
                                                    <input type="number" name="min_weight_kg" class="form-control"
                                                        placeholder="0">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Berat Maksimal (kg)</label>
                                                    <input type="number" name="max_weight_kg" class="form-control"
                                                        placeholder="0">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Kemampuan Bahasa Asing</label>
                                                    <input type="text" name="foreign_language_ability" class="form-control"
                                                        placeholder="English, Mandarin, dll">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Kepribadian / Character Personalities</label>
                                                    <textarea name="character_personalities" class="form-control" rows="2"
                                                        placeholder="Karakter yang diinginkan..."></textarea>
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
                                            <textarea name="reason_for_recruiting" class="form-control" rows="3"
                                                placeholder="Alasan Penerimaan (Reason for Recruiting)..."></textarea>
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
                                            <textarea name="remarks" class="form-control" rows="2"
                                                placeholder="Keterangan tambahan..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
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
            // Auto generate registration number based on date
            var date = new Date();
            var year = date.getFullYear();
            var random = Math.floor(Math.random() * 900) + 100;
            if ($('input[name="registration_number"]').val() == '') {
                $('input[name="registration_number"]').val('FPPK-' + year + '-' + random);
            }
        });
    </script>
@endsection