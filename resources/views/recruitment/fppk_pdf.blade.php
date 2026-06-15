<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>FPPK - {{ $fppk->registration_number ?? 'Form Permintaan Pengadaan Karyawan' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 15px;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 14px;
            font-weight: normal;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10px;
        }

        /* Title Section */
        .title-section {
            background-color: #f0f0f0;
            padding: 8px;
            margin: 15px 0 10px 0;
            font-weight: bold;
            font-size: 12px;
            border-left: 4px solid #3c8dbc;
        }

        /* Table */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            vertical-align: top;
        }

        .table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: left;
            width: 35%;
        }

        .table td {
            width: 65%;
        }

        /* Approval Table */
        .approval-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .approval-table th,
        .approval-table td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: center;
        }

        .approval-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .status-baru {
            background-color: #3c8dbc;
            color: white;
        }

        .status-penggantian {
            background-color: #f39c12;
            color: white;
        }

        .status-budgeted {
            background-color: #00a65a;
            color: white;
        }

        .status-unbudgeted {
            background-color: #dd4b39;
            color: white;
        }

        /* Signature */
        .signature {
            margin-top: 30px;
            width: 100%;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            padding: 20px 10px 0 10px;
            vertical-align: top;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            color: #999;
        }

        /* Well / Box */
        .well {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 8px;
            margin-bottom: 10px;
        }

        /* Page Break */
        .page-break {
            page-break-before: always;
        }

        /* Text Alignment */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <h1>FORM PERMINTAAN PENGADAAN KARYAWAN</h1>
            <h2>(FPPK - Employee Procurement Request Form)</h2>
            <p>Dokumen ini digunakan untuk mengajukan permintaan pengadaan karyawan baru</p>
        </div>

        <!-- REGISTRATION INFO -->
        <table class="table">
            <tr>
                <th>Registration Number</th>
                <td><strong>{{ $fppk->registration_number ?? '-' }}</strong></td>
            </tr>
            <tr>
                <th>Application Status</th>
                <td>
                    @if($fppk->application_status == 'Baru')
                        <span class="status-badge status-baru">Baru (New)</span>
                    @elseif($fppk->application_status == 'Penggantian')
                        <span class="status-badge status-penggantian">Penggantian (Replacement)</span>
                    @else
                        {{ $fppk->application_status ?? '-' }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>Date Received</th>
                <td>{{ $fppk->date_received ? date('d/m/Y', strtotime($fppk->date_received)) : '-' }}</td>
            </tr>
        </table>

        <!-- SECTION 1: ORGANISASI -->
        <div class="title-section">A. DATA ORGANISASI (Organizational Data)</div>
        <table class="table">
            <tr>
                <th>a. Division</th>
                <td>{{ $fppk->division ?? '-' }}</td>
            </tr>
            <tr>
                <th>b. Department</th>
                <td>{{ $fppk->department ?? '-' }}</td>
            </tr>
            <tr>
                <th>c. Section</th>
                <td>{{ $fppk->section ?? '-' }}</td>
            </tr>
            <tr>
                <th>d. Jumlah Karyawan Pada Area Bagian</th>
                <td>{{ number_format($fppk->total_employee_section, 0) ?? '0' }} People</td>
            </tr>
            <tr>
                <th>e. Jumlah Pekerja Disetujui Dewan Direksi</th>
                <td>{{ number_format($fppk->total_employee_bod_approved, 0) ?? '0' }} People</td>
            </tr>
        </table>

        <!-- SECTION 2: DESKRIPSI POSISI -->
        <div class="title-section">B. DESKRIPSI POSISI PEKERJAAN (Job Position Description)</div>
        <table class="table">
            <tr>
                <th>1. Jabatan / Posisi (Position / Job)</th>
                <td>{{ $fppk->position_job ?? '-' }}</td>
            </tr>
            <tr>
                <th>2. Fungsi Pekerjaan & Tingkat/Golongan (Job Function & Level)</th>
                <td>{{ $fppk->job_function_level ?? '-' }}</td>
            </tr>
            <tr>
                <th>3. Status of Employment Relationship</th>
                <td>{{ $fppk->employment_status ?? '-' }} - {{ $fppk->employment_type ?? '-' }}</td>
            </tr>
            <tr>
                <th>Working Period</th>
                <td>{{ ($fppk->working_period_years ?? 0) . ' Years ' . ($fppk->working_period_months ?? 0) . ' Months' }}
                </td>
            </tr>
            <tr>
                <th>4. Tanggal Mulai Bergabung (Starting Date)</th>
                <td>{{ $fppk->starting_date ? date('d/m/Y', strtotime($fppk->starting_date)) : '-' }}</td>
            </tr>
            <tr>
                <th>5. Jumlah Pekerja yang Dibutuhkan (Number of Employees Needed)</th>
                <td><strong>{{ number_format($fppk->employee_number_needed, 0) ?? '0' }} Orang</strong></td>
            </tr>
            <tr>
                <th>7. Payment Term (Syarat Pembayaran)</th>
                <td>{{ $fppk->payment_term ?? '-' }}</td>
            </tr>
        </table>

        <!-- SECTION 3: PENAWARAN GAJI -->
        <div class="title-section">C. PENAWARAN GAJI (Salary Offer)</div>
        <table class="table">
            <tr>
                <th>Upah Minimal (Minimal Wage)</th>
                <td>Rp {{ number_format($fppk->minimal_wage, 0, ',', '.') ?? '0' }}</td>
            </tr>
            <tr>
                <th>Mengikuti Peraturan SAE's (Follow SAE Regulation)</th>
                <td>{{ $fppk->follow_sae_regulation ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <th>Catatan (Notes)</th>
                <td>{{ $fppk->salary_notes ?? '-' }}</td>
            </tr>
        </table>

        <!-- SECTION 4: TUGAS / TANGGUNG JAWAB -->
        <div class="title-section">D. TUGAS / TANGGUNG JAWAB (Tasks / Responsibilities)</div>
        <div class="well">
            {{ $fppk->task_description ?? '-' }}
        </div>

        <!-- SECTION 5: PERSYARATAN PEKERJAAN -->
        <div class="title-section">E. PERSYARATAN PEKERJAAN & PRIBADI (Job & Personal Requirements)</div>
        <table class="table">
            <tr>
                <th>Minimal Pendidikan Akhir (Min Education)</th>
                <td>{{ $fppk->min_education ?? '-' }}</td>
            </tr>
            <tr>
                <th>Kemampuan Khusus (Special Ability)</th>
                <td>{{ $fppk->special_ability ?? '-' }}</td>
            </tr>
            <tr>
                <th>Jenis Pengalaman Kerja (Work Experience Type)</th>
                <td>{{ $fppk->work_experience_type ?? '-' }}</td>
            </tr>
            <tr>
                <th>Gender</th>
                <td>{{ $fppk->gender ?? '-' }}</td>
            </tr>
            <tr>
                <th>Minimal Pengalaman Kerja (Min Work Experience)</th>
                <td>{{ ($fppk->min_work_experience_years ?? 0) . ' Years' }}</td>
            </tr>
            <tr>
                <th>Usia (Age)</th>
                <td>{{ ($fppk->min_age ?? 0) . ' - ' . ($fppk->max_age ?? 0) . ' Years' }}</td>
            </tr>
            <tr>
                <th>Kemampuan Komputer (Computer Mastery)</th>
                <td>{{ $fppk->computer_mastery ?? '-' }}</td>
            </tr>
            <tr>
                <th>Tinggi / Berat Badan (Height / Weight)</th>
                <td>{{ ($fppk->min_height_cm ?? 0) . ' cm, ' . ($fppk->min_weight_kg ?? 0) . ' - ' . ($fppk->max_weight_kg ?? 0) . ' kg' }}
                </td>
            </tr>
            <tr>
                <th>Kemampuan Bahasa Asing (Foreign Language Ability)</th>
                <td>{{ $fppk->foreign_language_ability ?? '-' }}</td>
            </tr>
            <tr>
                <th>Kepribadian (Character Personalities)</th>
                <td>{{ $fppk->character_personalities ?? '-' }}</td>
            </tr>
        </table>

        <!-- SECTION 6: ALASAN PENERIMAAN -->
        <div class="title-section">F. ALASAN PENERIMAAN (Reason for Recruiting)</div>
        <div class="well">
            {{ $fppk->reason_for_recruiting ?? '-' }}
        </div>

        <!-- SECTION 7: BUDGET -->
        <div class="title-section">G. BUDGET</div>
        <table class="table">
            <tr>
                <th>Budget Status</th>
                <td>
                    @if($fppk->budget_status == 'Budgeted')
                        <span class="status-badge status-budgeted">Budgeted</span>
                    @elseif($fppk->budget_status == 'Un-budgeted')
                        <span class="status-badge status-unbudgeted">Un-budgeted</span>
                    @else
                        {{ $fppk->budget_status ?? '-' }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>Remarks (Keterangan)</th>
                <td>{{ $fppk->remarks ?? '-' }}</td>
            </tr>
        </table>

        <!-- SECTION 8: APPROVAL -->
        <div class="title-section">H. PERSETUJUAN (Approval)</div>
        <table class="approval-table">
            <thead>
                <tr>
                    <th>Level</th>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Signature</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Submitted By</td>
                    <td>{{ $fppk->submitted_by ?? '-' }}</td>
                    <td>{{ $fppk->submitted_date ? date('d/m/Y', strtotime($fppk->submitted_date)) : '-' }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Supported By</td>
                    <td>{{ $fppk->supported_by ?? '-' }}</td>
                    <td>{{ $fppk->supported_date ? date('d/m/Y', strtotime($fppk->supported_date)) : '-' }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Checked By</td>
                    <td>{{ $fppk->checked_by ?? '-' }}</td>
                    <td>{{ $fppk->checked_date ? date('d/m/Y', strtotime($fppk->checked_date)) : '-' }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Validated By</td>
                    <td>{{ $fppk->validated_by ?? '-' }}</td>
                    <td>{{ $fppk->validated_date ? date('d/m/Y', strtotime($fppk->validated_date)) : '-' }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Approved By<br><small>(Vice President Director / Managing Director)</small></td>
                    <td>{{ $fppk->approved_by ?? '-' }}</td>
                    <td>{{ $fppk->approved_date ? date('d/m/Y', strtotime($fppk->approved_date)) : '-' }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Legalized By<br><small>(President Director)</small></td>
                    <td>{{ $fppk->legalized_by ?? '-' }}</td>
                    <td>{{ $fppk->legalized_date ? date('d/m/Y', strtotime($fppk->legalized_date)) : '-' }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <!-- SECTION 9: CANDIDATES -->
        @if($candidates && count($candidates) > 0)
            <div class="title-section">I. DAFTAR CALON YANG DITERIMA (Accepted Candidates)</div>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: center;">No</th>
                        <th style="width: 60%;">Complete Name</th>
                        <th style="width: 35%;">Working Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($candidates as $index => $candidate)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td>{{ $candidate->complete_name ?? '-' }}</td>
                            <td>{{ $candidate->working_date ? date('d/m/Y', strtotime($candidate->working_date)) : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- SIGNATURE SECTION -->
        <div class="signature">
            <table class="signature-table">
                <tr>
                    <td style="width: 25%;">
                        <div>Submitted by,</div>
                        <div style="margin-top: 50px;">(____________________)</div>
                        <div>Date: {{ $fppk->submitted_date ? date('d/m/Y', strtotime($fppk->submitted_date)) : '-' }}
                        </div>
                    </td>
                    <td style="width: 25%;">
                        <div>Supported by,</div>
                        <div style="margin-top: 50px;">(____________________)</div>
                        <div>Date: {{ $fppk->supported_date ? date('d/m/Y', strtotime($fppk->supported_date)) : '-' }}
                        </div>
                    </td>
                    <td style="width: 25%;">
                        <div>Checked by,</div>
                        <div style="margin-top: 50px;">(____________________)</div>
                        <div>Date: {{ $fppk->checked_date ? date('d/m/Y', strtotime($fppk->checked_date)) : '-' }}</div>
                    </td>
                    <td style="width: 25%;">
                        <div>Validated by,</div>
                        <div style="margin-top: 50px;">(____________________)</div>
                        <div>Date: {{ $fppk->validated_date ? date('d/m/Y', strtotime($fppk->validated_date)) : '-' }}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 33%;">
                        <div>Approved by,</div>
                        <div style="margin-top: 50px;">(____________________)</div>
                        <div>VP Director / Managing Director</div>
                        <div>Date: {{ $fppk->approved_date ? date('d/m/Y', strtotime($fppk->approved_date)) : '-' }}
                        </div>
                    </td>
                    <td style="width: 33%;">
                        <div>Legalized by,</div>
                        <div style="margin-top: 50px;">(____________________)</div>
                        <div>President Director</div>
                        <div>Date: {{ $fppk->legalized_date ? date('d/m/Y', strtotime($fppk->legalized_date)) : '-' }}
                        </div>
                    </td>
                    <td style="width: 34%;">
                        <div>HRBP,</div>
                        <div style="margin-top: 50px;">(____________________)</div>
                        <div>Date: ___________</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <p>Dokumen ini dicetak secara otomatis dari sistem. Form Permintaan Pengadaan Karyawan (FPPK)</p>
            <p>Printed on: {{ date('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>

</html>