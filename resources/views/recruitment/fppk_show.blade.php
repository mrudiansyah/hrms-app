@extends('layouts/admin')
@section('Contents')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                FPPK
                <small>Form Permintaan Pengadaan Karyawan - Detail Data</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ url('/FPPK') }}">FPPK</a></li>
                <li class="active">Detail Data</li>
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
                    <!-- Tombol Aksi -->
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-navicon"></i> Aksi</h3>
                        </div>
                        <div class="box-body">
                            <a href="{{ url('/FPPK') }}" class="btn btn-default"><i class="fa fa-arrow-left"></i> Kembali</a>
                            <a href="{{ url('/FPPK/edit/' . $fppk->id) }}" class="btn btn-warning"><i class="fa fa-edit"></i> Edit</a>
                            <a href="{{ url('/FPPK/print/' . $fppk->id) }}" class="btn btn-info" target="_blank"><i class="fa fa-print"></i> Print PDF</a>
                            <button type="button" class="btn btn-primary" onclick="showApprovalModal({{ $fppk->id }})"><i class="fa fa-check-circle"></i> Approval</button>
                            @if($fppk->legalized_by && $fppk->legalized_date)
                                <span class="label label-success pull-right" style="font-size: 14px; padding: 8px 12px;">
                                    <i class="fa fa-check-circle"></i> STATUS: LEGALIZED
                                </span>
                            @elseif($fppk->approved_by && $fppk->approved_date)
                                <span class="label label-primary pull-right" style="font-size: 14px; padding: 8px 12px;">
                                    <i class="fa fa-check"></i> STATUS: APPROVED
                                </span>
                            @elseif($fppk->validated_by && $fppk->validated_date)
                                <span class="label label-info pull-right" style="font-size: 14px; padding: 8px 12px;">
                                    <i class="fa fa-check"></i> STATUS: VALIDATED
                                </span>
                            @elseif($fppk->submitted_by && $fppk->submitted_date)
                                <span class="label label-warning pull-right" style="font-size: 14px; padding: 8px 12px;">
                                    <i class="fa fa-clock-o"></i> STATUS: SUBMITTED
                                </span>
                            @else
                                <span class="label label-default pull-right" style="font-size: 14px; padding: 8px 12px;">
                                    <i class="fa fa-pencil"></i> STATUS: DRAFT
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- DATA FPPK -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-file-text"></i> Form Permintaan Pengadaan Karyawan</h3>
                        </div>
                        <div class="box-body">
                            <!-- HEADER -->
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <th width="40%">Registration Number</th>
                                            <td width="60%">{{ $fppk->registration_number ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Application Status</th>
                                            <td>
                                                @if($fppk->application_status == 'Baru')
                                                    <span class="label label-primary">Baru</span>
                                                @elseif($fppk->application_status == 'Penggantian')
                                                    <span class="label label-warning">Penggantian (Replacement)</span>
                                                @else
                                                    <span class="label label-default">{{ $fppk->application_status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <th width="40%">Created By</th>
                                            <td width="60%">{{ $fppk->created_by ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th>
                                            <td>{{ $fppk->created_at ? date('d/m/Y H:i:s', strtotime($fppk->created_at)) : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Last Updated</th>
                                            <td>{{ $fppk->updated_at ? date('d/m/Y H:i:s', strtotime($fppk->updated_at)) : '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- ORGANISASI -->
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-building"></i> Data Organisasi</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Division</th>
                                            <td width="70%">{{ $fppk->division ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Department</th>
                                            <td>{{ $fppk->department ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Section</th>
                                            <td>{{ $fppk->section ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jumlah Karyawan Pada Area Bagian</th>
                                            <td>{{ number_format($fppk->total_employee_section, 0) ?? '-' }} People</td>
                                        </tr>
                                        <tr>
                                            <th>Jumlah Pekerja Disetujui Dewan Direksi</th>
                                            <td>{{ number_format($fppk->total_employee_bod_approved, 0) ?? '-' }} People</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- DESKRIPSI POSISI -->
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-briefcase"></i> Deskripsi Posisi Pekerjaan</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Jabatan / Posisi</th>
                                            <td width="70%">{{ $fppk->position_job ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Fungsi Pekerjaan & Tingkat/Golongan</th>
                                            <td>{{ $fppk->job_function_level ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Employment Status</th>
                                            <td>{{ $fppk->employment_status ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Employment Type</th>
                                            <td>{{ $fppk->employment_type ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Working Period</th>
                                            <td>{{ ($fppk->working_period_years ?? 0) . ' Years ' . ($fppk->working_period_months ?? 0) . ' Months' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Mulai Bergabung</th>
                                            <td>{{ $fppk->starting_date ? date('d/m/Y', strtotime($fppk->starting_date)) : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jumlah Pekerja yang Dibutuhkan</th>
                                            <td>{{ number_format($fppk->employee_number_needed, 0) ?? '-' }} Orang</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Penerimaan Formulir</th>
                                            <td>{{ $fppk->date_received ? date('d/m/Y', strtotime($fppk->date_received)) : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Payment Term</th>
                                            <td>{{ $fppk->payment_term ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- PENAWARAN GAJI -->
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-money"></i> Penawaran Gaji</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Upah Minimal</th>
                                            <td width="70%">Rp {{ number_format($fppk->minimal_wage, 0, ',', '.') ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Mengikuti Peraturan SAE's</th>
                                            <td>{{ $fppk->follow_sae_regulation ? 'Ya' : 'Tidak' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Catatan Penawaran Gaji</th>
                                            <td>{{ $fppk->salary_notes ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- TUGAS / TANGGUNG JAWAB -->
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-tasks"></i> Tugas / Tanggung Jawab</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="well well-sm">
                                        {{ $fppk->task_description ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <!-- PERSYARATAN PEKERJAAN -->
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-check-circle"></i> Persyaratan Pekerjaan & Pribadi</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Minimal Pendidikan Akhir</th>
                                            <td width="70%">{{ $fppk->min_education ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kemampuan Khusus</th>
                                            <td>{{ $fppk->special_ability ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jenis Pengalaman Kerja</th>
                                            <td>{{ $fppk->work_experience_type ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Gender</th>
                                            <td>
                                                @if($fppk->gender == 'Male')
                                                    <span class="label label-info"><i class="fa fa-mars"></i> Male</span>
                                                @elseif($fppk->gender == 'Female')
                                                    <span class="label label-danger"><i class="fa fa-venus"></i> Female</span>
                                                @else
                                                    {{ $fppk->gender ?? '-' }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Minimal Pengalaman Kerja</th>
                                            <td>{{ ($fppk->min_work_experience_years ?? 0) . ' Years' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Usia</th>
                                            <td>{{ ($fppk->min_age ?? 0) . ' - ' . ($fppk->max_age ?? 0) . ' Years' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kemampuan Komputer</th>
                                            <td>{{ $fppk->computer_mastery ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tinggi / Berat Badan</th>
                                            <td>{{ ($fppk->min_height_cm ?? 0) . ' cm, ' . ($fppk->min_weight_kg ?? 0) . ' - ' . ($fppk->max_weight_kg ?? 0) . ' kg' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kemampuan Bahasa Asing</th>
                                            <td>{{ $fppk->foreign_language_ability ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kepribadian / Character Personalities</th>
                                            <td>{{ $fppk->character_personalities ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- ALASAN PENERIMAAN -->
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-question-circle"></i> Alasan Penerimaan</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="well well-sm">
                                        {{ $fppk->reason_for_recruiting ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <!-- KETERANGAN TAMBAHAN -->
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-comment"></i> Keterangan Tambahan</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="well well-sm">
                                        {{ $fppk->remarks ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <!-- BUDGET STATUS -->
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-line-chart"></i> Budget</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Budget Status</th>
                                            <td width="70%">
                                                @if($fppk->budget_status == 'Budgeted')
                                                    <span class="label label-success">Budgeted</span>
                                                @elseif($fppk->budget_status == 'Un-budgeted')
                                                    <span class="label label-danger">Un-budgeted</span>
                                                @else
                                                    {{ $fppk->budget_status ?? '-' }}
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- INFORMASI APPROVAL -->
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-check-square-o"></i> Informasi Approval</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-striped">
                                                <tr>
                                                    <th width="40%">Submitted By</th>
                                                    <td width="60%">{{ $fppk->submitted_by ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Submitted Date</th>
                                                    <td>{{ $fppk->submitted_date ? date('d/m/Y', strtotime($fppk->submitted_date)) : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Supported By</th>
                                                    <td>{{ $fppk->supported_by ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Supported Date</th>
                                                    <td>{{ $fppk->supported_date ? date('d/m/Y', strtotime($fppk->supported_date)) : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Checked By</th>
                                                    <td>{{ $fppk->checked_by ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Checked Date</th>
                                                    <td>{{ $fppk->checked_date ? date('d/m/Y', strtotime($fppk->checked_date)) : '-' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-striped">
                                                <tr>
                                                    <th width="40%">Validated By</th>
                                                    <td width="60%">{{ $fppk->validated_by ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Validated Date</th>
                                                    <td>{{ $fppk->validated_date ? date('d/m/Y', strtotime($fppk->validated_date)) : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Approved By</th>
                                                    <td>{{ $fppk->approved_by ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Approved Date</th>
                                                    <td>{{ $fppk->approved_date ? date('d/m/Y', strtotime($fppk->approved_date)) : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Legalized By</th>
                                                    <td>{{ $fppk->legalized_by ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Legalized Date</th>
                                                    <td>{{ $fppk->legalized_date ? date('d/m/Y', strtotime($fppk->legalized_date)) : '-' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- KANDIDAT YANG DITERIMA -->
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-users"></i> Daftar Calon Yang Diterima</h3>
                                </div>
                                <div class="box-body">
                                    @if($candidates && count($candidates) > 0)
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Complete Name</th>
                                                    <th>Working Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($candidates as $index => $candidate)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $candidate->complete_name ?? '-' }}</td>
                                                        <td>{{ $candidate->working_date ? date('d/m/Y', strtotime($candidate->working_date)) : '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i> Belum ada data calon yang diterima.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal Approval -->
    <div class="modal fade" id="modal-approval" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Approval FPPK</h4>
                </div>
                <form id="approval-form" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="fppk_id" id="approval_fppk_id">
                        <div class="form-group">
                            <label>Approval Stage</label>
                            <select name="approval_stage" id="approval_stage" class="form-control" required>
                                <option value="">-- Select Stage --</option>
                                <option value="submitted">Submitted</option>
                                <option value="supported">Supported</option>
                                <option value="checked">Checked</option>
                                <option value="validated">Validated</option>
                                <option value="approved">Approved</option>
                                <option value="legalized">Legalized</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Approver Name</label>
                            <input type="text" name="approver_name" id="approver_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Approval Date</label>
                            <input type="date" name="approval_date" id="approval_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit Approval</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('Scripts')
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable untuk tabel kandidat
            $('#candidates-table').DataTable({
                'paging': true,
                'lengthChange': false,
                'searching': true,
                'ordering': true,
                'info': true,
                'pageLength': 10
            });
        });

        function showApprovalModal(id) {
            $('#approval_fppk_id').val(id);
            $('#modal-approval').modal('show');
        }

        $('#approval-form').on('submit', function(e) {
            e.preventDefault();
            var id = $('#approval_fppk_id').val();
            var formData = $(this).serialize();

            $.ajax({
                url: '/FPPK/approve/' + id,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#modal-approval').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan, silakan coba lagi.');
                }
            });
        });
    </script>
@endsection