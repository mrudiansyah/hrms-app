<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Employee Status Renewal - {{ $renewal->nik }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        @page {
            margin: 20px;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 9px;
            line-height: 1.15;
            color: #000;
            margin: 20px;
            padding: 0;
        }

        .main-container {
            width: 100%;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            border: 3px solid #000;
        }

        .main-table td {
            padding: 0;
            margin: 0;
            vertical-align: top;
        }

        .section-divider {
            border-bottom: 2px solid #000;
        }

        .thin-divider {
            border-bottom: 1.5px solid #000;
        }

        .sub-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin: 0;
        }

        .sub-table td {
            border: none;
            padding: 0;
        }

        .label {
            font-size: 8.5px;
            font-family: Arial, sans-serif;
        }

        .value {
            font-size: 9px;
            font-family: Arial, sans-serif;
        }

        .bold-val {
            font-weight: bold;
        }

        .italic-lbl {
            font-style: italic;
        }

        .center-text {
            text-align: center;
        }

        .right-text {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <table class="main-table">
            <!-- Row 1: Header -->
            <tr>
                <td class="section-divider">
                    <table class="sub-table" style="width: 100%;">
                        <tr>
                            <!-- Logo and Company -->
                            <td style="width: 60%; padding: 0px; vertical-align: middle;">
                                <table class="sub-table" style="width: 100%;">
                                    <tr>
                                        <td style="padding: 0px; vertical-align: middle;">
                                            <img src="{{ public_path('images/logosai2.png') }}"
                                                style="max-width: 200px; max-height: 70px;" alt="Logo">
                                        </td>
                                        <td
                                            style="padding-left: 8px; vertical-align: middle; font-family: 'Arial Black', Arial, sans-serif; font-size: 16px; font-weight: bold; letter-spacing: -0.5px;">
                                            PT SUMMIT ADYAWINSA INDONESIA
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- Nomor Formulir -->
                            <td
                                style="border-left: 3px solid #000; border-right: 0px solid #000; width: 18%; text-align: center; vertical-align: middle; font-weight: bold; font-size: 10px; padding: 4px;">
                                Nomor Formulir
                            </td>
                            <!-- SOP and digit boxes -->
                            <td style=" text-align: center; vertical-align: middle; padding: 4px;">
                                <table class="sub-table" style="width: auto; margin: 0 auto;">
                                    <tr>
                                        <!-- SOP Box -->
                                        <td
                                            style="border: 2px solid #000; padding: 2px 6px; font-weight: bold; font-size: 9.5px; vertical-align: middle;">
                                            SOP HRGA
                                        </td>
                                        <!-- Bullet spacer -->
                                        <td style="padding: 0 6px; vertical-align: middle; text-align: center;">
                                            <div
                                                style="width: 6px; height: 6px; background-color: #000; margin: 0 auto; display: block;">
                                            </div>
                                        </td>
                                        <!-- Digit 1 -->
                                        <td
                                            style="border: 2px solid #000; padding: 2px 5px; font-weight: bold; font-size: 9.5px; vertical-align: middle; text-align: center;">
                                            0
                                        </td>
                                        <!-- Digit 2 -->
                                        <td
                                            style="border: 2px solid #000; border-left: none; padding: 2px 5px; font-weight: bold; font-size: 9.5px; vertical-align: middle; text-align: center;">
                                            0
                                        </td>
                                        <!-- Digit 3 -->
                                        <td
                                            style="border: 2px solid #000; border-left: none; padding: 2px 5px; font-weight: bold; font-size: 9.5px; vertical-align: middle; text-align: center;">
                                            01
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Row 2: Title -->
            <tr>
                <td class="section-divider" style="padding: 8px 0; text-align: center;">
                    <div style="font-size: 13px; font-weight: bold; letter-spacing: 0.5px;">
                        EMPLOYEE'S STATUS RENEWAL
                    </div>
                    <div style="font-family: 'Georgia', serif; font-size: 11px; font-style: italic; margin-top: 2px;">
                        Pembaharuan Status Karyawan
                    </div>
                </td>
            </tr>

            <!-- Row 3: Employee Info -->
            <tr>
                <td class="section-divider">
                    <table class="sub-table" style="width: 100%;">
                        <tr>
                            <!-- Left: Name & NIK -->
                            <td style="width: 50%; padding: 5px 8px; vertical-align: top;">
                                <table class="sub-table">
                                    <tr>
                                        <td class="label" style="width: 110px; padding: 2px 0;"><strong>NAME</strong> /
                                            <span class="italic-lbl">Nama</span>
                                        </td>
                                        <td style="width: 10px; padding: 2px 0; text-align: center;">:</td>
                                        <td class="value bold-val" style="padding: 2px 0 2px 3px;">
                                            {{ $renewal->name ?? '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="height: 14px;"></td>
                                    </tr>
                                    <tr>
                                        <td class="label" style="padding: 2px 0;"><strong>EMPLOYEE'S NO.</strong> /
                                            <span class="italic-lbl">NIK</span>
                                        </td>
                                        <td style="padding: 2px 0; text-align: center;">:</td>
                                        <td class="value bold-val" style="padding: 2px 0 2px 3px;">
                                            {{ $renewal->nik ?? '' }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- Right: Join Date, Work Period, Last Renewal -->
                            <td style="width: 50%; padding: 5px 8px; vertical-align: top; border-left: 2px solid #000;">
                                <table class="sub-table">
                                    <tr>
                                        <td class="label" style="width: 170px; padding: 2px 0;"><strong>DATE OF
                                                JOIN</strong> / <span class="italic-lbl">Tgl masuk kerja</span></td>
                                        <td style="width: 10px; padding: 2px 0; text-align: center;">:</td>
                                        <td class="value" style="padding: 2px 0 2px 3px;">
                                            {{ $renewal->date_of_join ? date('d/m/Y', strtotime($renewal->date_of_join)) : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label" style="padding: 2px 0;"><strong>WORK PERIOD</strong> / <span
                                                class="italic-lbl">Masa Kerja</span></td>
                                        <td style="padding: 2px 0; text-align: center;">:</td>
                                        <td class="value" style="padding: 2px 0 2px 3px;">
                                            {{ $renewal->work_period ?? '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label" style="padding: 2px 0;"><strong>LAST RENEWAL</strong> / <span
                                                class="italic-lbl">Tgl perubahan akhir</span></td>
                                        <td style="padding: 2px 0; text-align: center;">:</td>
                                        <td class="value" style="padding: 2px 0 2px 3px;">
                                            {{ $renewal->last_renewal ? date('d/m/Y', strtotime($renewal->last_renewal)) : '' }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Row 4: Condition Section Header -->
            <tr>
                <td class="section-divider">
                    <table class="sub-table" style="width: 100%;">
                        <tr>
                            <td class="center-text"
                                style="width: 50%; padding: 4px;height:20px; font-size: 9px; font-weight: bold;">
                                LAST CONDITION / <span class="italic-lbl" style="font-weight: normal;">Kondisi
                                    Terakhir/Sekarang</span>
                            </td>
                            <td class="center-text"
                                style="width: 50%; padding: 4px; font-size: 9px; font-weight: bold; border-left: 2px solid #000;">
                                NEW CONDITION / <span class="italic-lbl" style="font-weight: normal;">Kondisi
                                    Baru</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Row 5: Position, Grade, Division -->
            <tr>
                <td class="thin-divider">
                    <table class="sub-table" style="width: 100%;">
                        <tr>
                            <!-- Last Condition -->
                            <td style="width: 50%; padding: 4px 8px; vertical-align: top;">
                                <table class="sub-table">
                                    <tr>
                                        <td class="label" style="width: 170px; padding: 2px 0;">
                                            <strong>POSITION</strong> / <span class="italic-lbl">Jabatan</span>
                                        </td>
                                        <td style="width: 10px; padding: 2px 0; text-align: center;">:</td>
                                        <td class="value" style="padding: 2px 0 2px 3px;">
                                            {{ $renewal->last_position ?? '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label" style="padding: 2px 0;"><strong>GRADE</strong> / <span
                                                class="italic-lbl">Golongan</span></td>
                                        <td style="padding: 2px 0; text-align: center;">:</td>
                                        <td class="value" style="padding: 2px 0 2px 3px;">
                                            {{ $renewal->last_grade ?? '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label" style="padding: 2px 0;"><strong>DIV./SUB DIV.</strong> / <span
                                                class="italic-lbl">Divisi/Sub.Divisi</span></td>
                                        <td style="padding: 2px 0; text-align: center;">:</td>
                                        <td class="value" style="padding: 2px 0 2px 3px;">
                                            {{ $renewal->last_division ?? '' }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- New Condition -->
                            <td style="width: 50%; padding: 4px 8px; vertical-align: top; border-left: 2px solid #000;">
                                <table class="sub-table">
                                    <tr>
                                        <td class="label" style="width: 170px; padding: 2px 0;">
                                            <strong>POSITION</strong> / <span class="italic-lbl">Jabatan</span>
                                        </td>
                                        <td style="width: 10px; padding: 2px 0; text-align: center;">:</td>
                                        <td class="value bold-val" style="padding: 2px 0 2px 3px;">
                                            {{ $renewal->new_position ?? '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label" style="padding: 2px 0;"><strong>GRADE</strong> / <span
                                                class="italic-lbl">Golongan</span></td>
                                        <td style="padding: 2px 0; text-align: center;">:</td>
                                        <td class="value bold-val" style="padding: 2px 0 2px 3px;">
                                            {{ $renewal->new_grade ?? '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label" style="padding: 2px 0;"><strong>DIV./SUB DIV.</strong> / <span
                                                class="italic-lbl">Divisi/Sub.Divisi</span></td>
                                        <td style="padding: 2px 0; text-align: center;">:</td>
                                        <td class="value bold-val" style="padding: 2px 0 2px 3px;">
                                            {{ $renewal->new_division ?? '' }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Row 6: Salary -->
            <tr>
                <td class="thin-divider">
                    <table class="sub-table" style="width: 100%;">
                        <tr>
                            <!-- Last Condition Salary -->
                            <td style="width: 50%; padding: 4px 8px; vertical-align: top; height: 65px;">
                                <table class="sub-table">
                                    <tr>
                                        <td class="label" style="width: 170px; padding: 2px 0;"><strong>SALARY</strong>
                                            / <span class="italic-lbl">Gaji</span></td>
                                        <td style="width: 10px; padding: 2px 0; text-align: center;">:</td>
                                        <td class="value" style="padding: 2px 0 2px 3px;">
                                            {{ $renewal->last_salary ? 'Rp ' . number_format($renewal->last_salary, 0, ',', '.') : '' }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- New Condition Salary -->
                            <td
                                style="width: 50%; padding: 4px 8px; vertical-align: top; border-left: 2px solid #000; height: 65px;">
                                <table class="sub-table">
                                    <tr>
                                        <td class="label" style="width: 170px; padding: 2px 0;"><strong>SALARY</strong>
                                            / <span class="italic-lbl">Gaji</span></td>
                                        <td style="width: 10px; padding: 2px 0; text-align: center;">:</td>
                                        <td class="value bold-val" style="padding: 2px 0 2px 3px;">
                                            {{ $renewal->new_salary ? 'Rp ' . number_format($renewal->new_salary, 0, ',', '.') : '' }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Row 7: Others -->
            <tr>
                <td class="section-divider">
                    <table class="sub-table" style="width: 100%;">
                        <tr>
                            <!-- Last Condition Others -->
                            <td style="width: 50%; padding: 4px 8px; vertical-align: top; height: 65px;">
                                <table class="sub-table">
                                    <tr>
                                        <td class="label" style="width: 170px; padding: 2px 0;"><strong>OTHERS</strong>
                                            / <span class="italic-lbl">Lain-lain</span></td>
                                        <td style="width: 10px; padding: 2px 0; text-align: center;">:</td>
                                        <td class="value" style="padding: 2px 0 2px 3px;">
                                            {{ $renewal->last_others ?? '' }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- New Condition Others -->
                            <td
                                style="width: 50%; padding: 4px 8px; vertical-align: top; border-left: 2px solid #000; height: 65px;">
                                <table class="sub-table">
                                    <tr>
                                        <td class="label" style="width: 170px; padding: 2px 0;"><strong>OTHERS</strong>
                                            / <span class="italic-lbl">Lain-lain</span></td>
                                        <td style="width: 10px; padding: 2px 0; text-align: center;">:</td>
                                        <td class="value bold-val" style="padding: 2px 0 2px 3px;">
                                            {{ $renewal->new_others ?? '' }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Row 8: Reasons -->
            <tr>
                <td class="section-divider" style="padding: 5px 8px; height: 90px; vertical-align: top;">
                    <div class="label" style="margin-bottom: 4px;">
                        <strong>REASONS</strong> / <span class="italic-lbl">Alasan-alasan perubahan</span>
                    </div>
                    <div style="font-size: 9px; line-height: 1.3; padding-left: 4px;">
                        {{ $renewal->reasons ?? '' }}
                    </div>
                </td>
            </tr>

            <!-- Row 9: Effective From -->
            <tr>
                <td class="section-divider" style="padding: 5px 8px;height:20px; vertical-align: middle;">
                    <span class="label">
                        <strong>EFFECTIVE FROM</strong> / <span class="italic-lbl">Berlaku efektif mulai tanggal
                            :</span>
                    </span>
                    <span class="value bold-val" style="margin-left: 4px;">
                        {{ $renewal->effective_from ? date('d/m/Y', strtotime($renewal->effective_from)) : '' }}
                    </span>
                </td>
            </tr>

            <!-- Row 10: Suggested By & Supported By -->
            <tr>
                <td class="section-divider">
                    <table class="sub-table" style="width: 100%;">
                        <tr>
                            <!-- Suggested By -->
                            <td style="width: 50%; padding: 5px 8px; vertical-align: top;">
                                <div class="center-text" style="font-size: 9px; font-weight: bold; margin-bottom: 6px;">
                                    SUGGESTED BY / <span class="italic-lbl" style="font-weight: normal;">Diusulkan
                                        oleh,</span>
                                </div>
                                <div class="label" style="margin-bottom: 35px;height:60px;">
                                    SIGNATURE / <span class="italic-lbl">Tanda Tangan</span><br>
                                    <div style="text-align: center; width: 100%;">
                                        <?php
                                        $default = 'approval/confirm.png';
                                        $ttd = 'approval/'.$user_id.'.png';

                                        // Cek apakah file TTD ada
                                        if (file_exists(public_path($ttd))) {
                                            $imagePath = $ttd;
                                        } else {
                                            $imagePath = $default;
                                        }
                                        ?>
                                        <img src="{{ public_path($imagePath) }}" style="max-width: 200px; max-height: 70px; display: inline-block;" alt="Logo">
                                    </div>
                                </div>
                                <div class="label" style="padding-top: 4px;">
                                    <strong>NAME / Nama :</strong> <span
                                        class="value">{{ $renewal->suggested_by_name ?? '' }}</span>
                                </div>
                                <div class="label">
                                    <strong>DATE / Tanggal :</strong> <span
                                        class="value">{{ $renewal->suggested_by_date ? date('d/m/Y', strtotime($renewal->suggested_by_date)) : '' }}</span>
                                </div>
                            </td>
                            <!-- Supported By -->
                            <td style="width: 50%; padding: 5px 8px; vertical-align: top; border-left: 2px solid #000;">
                                <div class="center-text" style="font-size: 9px; font-weight: bold; margin-bottom: 6px;">
                                    SUPPORTED BY / <span class="italic-lbl" style="font-weight: normal;">Didukung
                                        oleh,</span>
                                </div>
                                <div class="label" style="margin-bottom: 35px;height:60px;">
                                    SIGNATURE / <span class="italic-lbl">Tanda Tangan</span>
                                </div>
                                <div class="label" style="padding-top: 4px;">
                                    <strong>NAME / Nama :</strong> <span
                                        class="value">{{ $renewal->supported_by_name ?? '' }}</span>
                                </div>
                                <div class="label">
                                    <strong>DATE / Tanggal :</strong> <span
                                        class="value">{{ $renewal->supported_by_date ? date('d/m/Y', strtotime($renewal->supported_by_date)) : '' }}</span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Row 11: Notes from Personnel Dept -->
            <tr>
                <td class="section-divider">
                    <div class="center-text"
                        style="font-size: 9px; font-weight: bold;height:20px; border-bottom: 1px solid #000; padding: 3px;">
                        NOTES FROM PERSONNEL DEPT. / <span class="italic-lbl" style="font-weight: normal;">Catatan dari
                            Bagian Personalia,</span>
                    </div>
                    <table class="sub-table" style="width: 100%;">
                        <tr>
                            <!-- Left: Name & Date -->
                            <td style="width: 25%; padding: 4px 8px; vertical-align: bottom; height: 55px;">
                                <div class="label" style="margin-bottom: 3px;">
                                    <strong>NAME / Nama :</strong> <span class="value"
                                        style="font-size: 8.5px;">{{ $renewal->personnel_name ?? '' }}</span>
                                </div>
                                <div class="label">
                                    <strong>DATE / Tanggal :</strong> <span class="value"
                                        style="font-size: 8.5px;">{{ $renewal->personnel_date ? date('d/m/Y', strtotime($renewal->personnel_date)) : '' }}</span>
                                </div>
                            </td>
                            <!-- Right: Description -->
                            <td
                                style="width: 75%; padding: 4px 8px; vertical-align: top; border-left: 2px solid #000; height: 55px;">
                                <div class="label" style="font-weight: bold; margin-bottom: 4px;">
                                    DESCRIPTION / <span class="italic-lbl"
                                        style="font-weight: normal;">Keterangan,</span>
                                </div>

                                <!-- Evaluation rows -->
                                <table class="sub-table" style="margin: 1px 0;">
                                    <tr>
                                        <td style="width: 20px;"></td>
                                        <td class="label" style="width: 75px;">: Kehadiran :</td>
                                        <td class="value" style="width: 80px;">
                                            {{ $renewal->personnel_attendance ?? '' }}
                                        </td>
                                        <td class="label" style="width: 100px;">Ketepatan Waktu :</td>
                                        <td class="value">{{ $renewal->personnel_punctuality ?? '' }}</td>
                                    </tr>
                                </table>
                                <table class="sub-table" style="margin: 1px 0;">
                                    <tr>
                                        <td style="width: 20px;"></td>
                                        <td class="label" style="width: 75px; color: #666;">: Kehadiran :</td>
                                        <td style="width: 80px;"></td>
                                        <td class="label" style="width: 100px; color: #666;">Ketepatan Waktu :</td>
                                        <td></td>
                                    </tr>
                                </table>
                                <table class="sub-table" style="margin: 1px 0;">
                                    <tr>
                                        <td style="width: 20px;"></td>
                                        <td class="label" style="width: 75px; color: #666;">: Kehadiran :</td>
                                        <td style="width: 80px;"></td>
                                        <td class="label" style="width: 100px; color: #666;">Ketepatan Waktu :</td>
                                        <td></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Row 12: Proposed By -->
            <tr>
                <td class="section-divider" style="padding: 5px 8px; vertical-align: top; height: 55px;">
                    <div class="label" style="font-weight: bold; margin-bottom: 3px;">
                        PROPOSED BY / <span class="italic-lbl" style="font-weight: normal;">Diajukan oleh,</span>
                    </div>
                    <table class="sub-table" style="margin-top: 10px;">
                        <tr>
                            <td class="label" style="width: 50%; vertical-align: bottom; padding-bottom: 2px;">
                                <strong>NAME / Nama :</strong> <span class="value"
                                    style="font-size: 9px;">{{ $renewal->proposed_by_name ?? '' }}</span>
                            </td>
                            <td style="width: 50%; vertical-align: bottom;">
                                <table class="sub-table">
                                    <tr>
                                        <td class="label" style="width: 100px;height:80px; padding: 1px 0;">
                                            <strong>SIGNATURE / Ttd
                                                :</strong>
                                        </td>
                                        <td style="border-bottom: 1px solid #000; padding: 1px 0;"></td>
                                    </tr>
                                    <tr>
                                        <td class="label" style="padding: 1px 0;"><strong>DATE / Tanggal :</strong></td>
                                        <td class="value" style="border-bottom: 1px solid #000; padding: 1px 0;">
                                            {{ $renewal->proposed_by_date ? date('d/m/Y', strtotime($renewal->proposed_by_date)) : '' }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Row 13: Approved By -->
            <tr>
                <td style="padding: 5px 8px; vertical-align: top; height: 55px;">
                    <div class="label" style="font-weight: bold; margin-bottom: 3px;">
                        APPROVED BY / <span class="italic-lbl" style="font-weight: normal;">Disetujui oleh,</span>
                    </div>
                    <table class="sub-table" style="margin-top: 10px;">
                        <tr>
                            <td class="label" style="width: 50%; vertical-align: bottom; padding-bottom: 2px;">
                                <strong>NAME / Nama :</strong> <span class="value"
                                    style="font-size: 9px;">{{ $renewal->approved_by_name ?? '' }}</span>
                            </td>
                            <td style="width: 50%; vertical-align: bottom;">
                                <table class="sub-table">
                                    <tr>
                                        <td class="label" style="width: 100px;height:80px; padding: 1px 0;">
                                            <strong>SIGNATURE / Ttd
                                                :</strong>
                                        </td>
                                        <td style="border-bottom: 1px solid #000; padding: 1px 0;"></td>
                                    </tr>
                                    <tr>
                                        <td class="label" style="padding: 1px 0;"><strong>DATE / Tanggal :</strong></td>
                                        <td class="value" style="border-bottom: 1px solid #000; padding: 1px 0;">
                                            {{ $renewal->approved_by_date ? date('d/m/Y', strtotime($renewal->approved_by_date)) : '' }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>