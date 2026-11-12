<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Services\AuditLogger;
use DateTime;
use Auth;
use PDF;

class ess_controller extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    #region Slip & index
    function index()
    {
        $today = date('Y-m-d');
        $tb_utility = DB::table('tb_utilities')->where('atribut', 'expired_password')->where('status', '1')->count();
        if ($tb_utility > 0) {
            $locked = Auth::user()->locked_status;
            $expired_date = Auth::user()->expired_date;
            if ($locked == 1) {
                return abort(403, 'Forbidden');
            }
            if ($expired_date < $today) {
                return redirect('/ChangePassword');
            }
        }
        $email = Auth::user()->email;
        $tb_employee = DB::table('tb_emails')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_emails.id_employee')
            ->where('email_address', $email)->get();
        foreach ($tb_employee as $dt) {
            $id = $dt->id_employee;
            $pin = $dt->PIN;
            $badgenumber = $dt->badgenumber;
        }
        //return $email;
        date_default_timezone_set("Asia/Jakarta");
        $Tgl = date('Y-m-d');

        $tb_employee = DB::table('tb_employees')
            ->leftjoin('tb_departments', 'tb_departments.id', '=', 'tb_employees.dept_id')
            ->leftjoin('tb_positions', 'tb_positions.id', '=', 'tb_employees.position_id')
            ->leftjoin('tb_work_contract', 'tb_work_contract.id_employee', '=', 'tb_employees.id')
            ->leftjoin('tb_work_shift', 'tb_work_shift.id', '=', 'tb_work_contract.id_work_shift')
            ->leftjoin('tb_work_group', 'tb_work_group.id', '=', 'tb_work_shift.id_work_group')
            ->leftJoin('tb_employee_detail', 'tb_employee_detail.id_employee', '=', 'tb_employees.id')
            ->where([['tb_employees.id', $id]])->limit(1)->get(['tb_employees.*', 'tb_departments.dept_code', 'tb_departments.dept_name', 'tb_departments.divisi', 'tb_positions.position_name', 'tb_work_contract.id_work_shift as id_shift', 'tb_employee_detail.agama', 'tb_employee_detail.tanggal_lahir', 'tb_employee_detail.tempat_lahir', 'tb_employee_detail.nomor_telepon', 'tb_employee_detail.golongan_darah', 'tb_work_shift.shift_code as shift_code', 'tb_work_group.id as id_work_group', 'tb_work_shift.start_implement', 'tb_work_group.cycle_day']);
        $shift_code = '';
        foreach ($tb_employee as $dt) {
            $shift_code = $dt->shift_code;
            $tgl1 = new DateTime($dt->start_implement);
            $tgl2 = new DateTime($Tgl);
            $diffdays = $tgl2->diff($tgl1)->days;
            $cycle = $dt->cycle_day;
            $diffcycle = Floor($diffdays / $cycle);
            $modcycle = $diffdays % $cycle;
            $modcycle++;

            $tabel = DB::table('tb_work_cycle')
                ->leftjoin('tb_work_time', 'tb_work_time.id', '=', 'tb_work_cycle.id_work_time')
                ->where('tb_work_cycle.id_work_group', $dt->id_work_group)
                ->where('tb_work_cycle.days', $modcycle)
                ->get();
            foreach ($tabel as $dt1) {
                $cshift = date('H:i', strtotime($dt1->check_in)) . ' - ' . date('H:i', strtotime($dt1->check_out));
                if ($dt1->is_advance == 0)
                    $tanggal = $Tgl;
                else
                    $tanggal = date('Y-m-d', strtotime('-1 days', strtotime($Tgl)));
                if ($dt1->id_work_time > 0) {
                    $check_in = $tanggal . ' ' . $dt1->check_in;
                    $check_out = $Tgl . ' ' . $dt1->check_out;
                } else {
                    $check_in = '';
                    $check_out = '';
                }

            }
            //Return Checkin Range
            if (isset($check_in) && $check_in != '' && $check_in != $check_out) {
                $ncdatein = $check_in;
                $ncdateindown = date('Y-m-d H:i:s', strtotime('-3 hours', strtotime($ncdatein)));
                $ncdateinup = date('Y-m-d H:i:s', strtotime('+5 hours', strtotime($ncdatein)));
            } else {
                $ncdateindown = '';
                $ncdateinup = '';
            }
            //Return Checkout Range
            if (isset($check_out) && $check_out != '' && $check_in != $check_out) {
                $ncdateout = $check_out;
                $ncdateoutdown = date('Y-m-d H:i:s', strtotime('-2 hours', strtotime($ncdateout)));
                $ncdateoutup = date('Y-m-d H:i:s', strtotime('+5 hours', strtotime($ncdateout)));
            } else {
                $ncdateoutdown = '';
                $ncdateoutup = '';
            }
            $checkin_act = '-';
            $checkout_act = '-';
            $badge = str_pad($dt->badgenumber, 9, '0', STR_PAD_LEFT);
            $checkin_acts = DB::table('tb_iclock')->where('badgenumber', $badge)->where('checktime', '>=', $ncdateindown)->where('checktime', '<=', $ncdateinup)->value('checktime');
            $checkout_acts = DB::table('tb_iclock')->where('badgenumber', $badge)->where('checktime', '>=', $ncdateoutdown)->where('checktime', '<=', $ncdateoutup)->value('checktime');
            if ($checkin_acts != '')
                $checkin_act = date('H:i', strtotime($checkin_acts));
            if ($checkout_acts != '')
                $checkout_act = date('H:i', strtotime($checkout_acts));
        }

        $id_shift = '';
        foreach ($tb_employee as $dt) {
            $id_shift = $dt->id_shift;
            $leader_id = $dt->leader_id;
        }
        $tb_photo = DB::table('tb_photos')->where('id_employee', '0')->get();
        foreach ($tb_photo as $dt_photo) {
            $photo = $dt_photo->nama_photo;
        }
        $tb_photo = DB::table('tb_photos')->where('id_employee', $id)->orderby('id', 'desc')->limit('1')->get();
        foreach ($tb_photo as $dt_photo) {
            $photo = $dt_photo->nama_photo;
        }
        $tb_employee_family = DB::table('tb_employee_family')->where([['id_employee', $id]])->where('status', '1')->get();
        $tb_domicile = DB::table('tb_domiciles')->where([['id_employee', $id], ['status', '1']])->get();
        $tb_address_darurat = DB::table('tb_address_darurat')->where([['id_employee', $id]])->where('status', '1')->get();
        $tb_bagian = DB::table('tb_bagians')->where([['id_employee', $id]])->orderby('implement', 'desc')->limit(1)->get();
        $tb_status = DB::table('tb_statuses')->where('id_employee', $id)->orderby('id', 'desc')->get();
        $tb_address = DB::table('tb_addresses')->where([['id_employee', $id], ['status', '1']])->get();
        $tb_education = DB::table('tb_educations')->where('id_employee', $id)->orderby('graduate_year', 'desc')->get();
        $tb_experience = DB::table('tb_experiences')->where('id_employee', $id)->orderby('finish_year', 'desc')->get();
        $tb_skill = DB::table('tb_skills')->where('id_employee', $id)->get();

        $leader_name = '';
        $tb_leader = DB::table('tb_employees')->where('id', $leader_id)->get();
        foreach ($tb_leader as $dt3) {
            $leader_name = $dt3->employee_name;
        }
        $tb_employee_leave = DB::table('tb_employee_leaves')->where([['id_employee', $id], ['extend', '>=', $Tgl], ['status', '1'], ['start', '<=', $today], ['end', '>=', $today]])->get();
        $tb_leaves = '';
        foreach ($tb_employee_leave as $dt) {
            $start = $dt->start;
            $end = $dt->end;
            $extend = $dt->extend;
            $tb_leaves = DB::table('tb_leaves')->where('id_leave', $dt->id)->orderby('start_leave', 'desc')->get();
        }
        $tb_utility = DB::table('tb_utilities')->where('atribut', 'ess_leave')->get(['status']);
        foreach ($tb_utility as $dt) {
            $ess_leave = $dt->status;
        }

        return view('page_ess/profile', ['photo' => $photo, 'tb_employee_family' => $tb_employee_family, 'tb_domicile' => $tb_domicile, 'tb_address_darurat' => $tb_address_darurat, 'tb_bagian' => $tb_bagian, 'tb_status' => $tb_status, 'id_employee' => $id, 'tb_employee' => $tb_employee, 'tb_employee_leave' => $tb_employee_leave, 'checkin_act' => $checkin_act, 'checkout_act' => $checkout_act, 'cshift' => $cshift, 'leader_id' => $leader_id, 'leader_name' => $leader_name, 'shift_code' => $shift_code, 'id_employee' => $id, 'PIN' => $pin, 'tb_address' => $tb_address, 'tb_education' => $tb_education, 'tb_experience' => $tb_experience, 'tb_skill' => $tb_skill, 'tb_leaves' => $tb_leaves, 'email' => $email, 'juduls' => 'Profile', 'menu' => 'employees', 'ess_leave' => $ess_leave]);
    }
    function employee_slip()
    {
        $email = Auth::user()->email;
        $tb_employee = DB::table('tb_emails')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_emails.id_employee')
            ->where('email_address', $email)->get();
        foreach ($tb_employee as $dt) {
            $id_employee = $dt->id_employee;
            $NIK = $dt->NIK;
            $employee_name = $dt->employee_name;
            $tb_salary_excel = DB::table('tb_salary_excel')->where('NIK', $NIK)->where('send_mail', '1')->orderby('periode', 'desc')->limit(6)->get();
        }

        $tb_utility = DB::table('tb_utilities')->where('atribut', 'ess_leave')->get(['status']);
        foreach ($tb_utility as $dt) {
            $ess_leave = $dt->status;
        }
        $tb1 = DB::table('tb_salary_summary')->where('id_employee', $id_employee)->where('mail_status', '1')->orderby('periode', 'desc')->get();
        if (request()->user()->hasRole('root') || request()->user()->hasRole('ess')) {
            return view('page_ess/slip_gaji', ['tb_salary_excel' => $tb_salary_excel, 'tb1' => $tb1, 'NIK' => $NIK, 'employee_name' => $employee_name, 'menu' => 'slip_gaji', 'juduls' => 'Slip Gaji', 'ess_leave' => $ess_leave]);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function slip($periode, $id_employee)
    {
        $email = Auth::user()->email;
        $tb_employee = DB::table('tb_emails')
            ->where('email_address', $email)->get();
        foreach ($tb_employee as $dt) {
            $id_employee_Login = $dt->id_employee;
        }
        if ($id_employee_Login != $id_employee) {
            return "Gagal Download, Apakah ini NIK Anda....? Silahkan hubungi HR.";
        } else {
            $kategori = DB::table('tb_salary_contract')->where('id_employee', $id_employee)->value('tipe_kontrak');
            $thn = date('Y', strtotime($periode . '-01'));
            $bln = date('F', strtotime($periode . '-01'));
            $periode_text = $thn . ' ' . $bln;

            $tb_salary_summary_employee = $this->tb_salary_summary_employee($periode, $id_employee);
            $view = '';
            if ($kategori == 'SAI')
                $view = "page_ess/salary_slip_sai";
            if ($kategori == 'MAGANG')
                $view = "page_ess/salary_slip_magang";
            if ($view == '')
                return abort(403, 'Under Maintenance');
            $FileName = 'SLIP.PDF';
            if ($kategori == 'PSAB' || $kategori == 'PKL')
                $pdf = PDF::loadview($view, ['tb_salary_summary_employee' => $tb_salary_summary_employee, 'periode_text' => $periode_text, 'thn' => $thn, 'menu' => 'salary_slip'])->setPaper(array(0, 0, 280, 355));
            else
                $pdf = PDF::loadview($view, ['tb_salary_summary_employee' => $tb_salary_summary_employee, 'periode_text' => $periode_text, 'thn' => $thn, 'menu' => 'salary_slip'])->setPaper(array(0, 0, 280, 630));
            return $pdf->stream($FileName);

        }
    }
    public function tb_salary_summary_employee($periode, $id_employee)
    {
        $tabel = DB::table('tb_salary_summary')
            ->leftjoin('tb_salary_contract', 'tb_salary_contract.id_employee', '=', 'tb_salary_summary.id_employee');
        if ($periode != '0')
            $tabel = $tabel->where('periode', $periode);
        $tabel = $tabel->where('tb_salary_summary.id_employee', $id_employee)
            ->orderby('tb_salary_summary.periode', 'desc')
            ->get(['tb_salary_summary.*', 'tb_salary_contract.NIK', 'tb_salary_contract.nama_karyawan', 'tb_salary_contract.department as dept_code', 'tb_salary_contract.jabatan as nama_jabatan', 'tb_salary_contract.status_pajak', 'tb_salary_contract.total_salary', 'tb_salary_contract.tipe_kontrak', 'tb_salary_contract.upah_harian as upah_psab', 'tb_salary_contract.tunjangan_harian as tunjangan_pkl']);
        return $tabel;
    }
    function employee_slip_temp_download($periode, $NIK)
    {
        $email = Auth::user()->email;
        $tb_employee = DB::table('tb_emails')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_emails.id_employee')
            ->where('email_address', $email)->get();
        foreach ($tb_employee as $dt) {
            $NIK_Login = $dt->NIK;
        }
        if ($NIK_Login != $NIK) {
            return "Gagal Download, Apakah ini NIK Anda....? Silahkan hubungi HR.";
        } else {
            $thn = date('Y', strtotime($periode . '-01'));
            $bln = date('m', strtotime($periode . '-01'));
            $kalendar = CAL_GREGORIAN;
            $hariawal = date('Y-m-d', strtotime($thn . '-' . $bln . '-01'));
            $hariakhir = cal_days_in_month($kalendar, $bln, $thn);
            $bulan = date('F', strtotime($thn . '-' . $bln . '-01'));
            $akhirbulan = date('Y-m-d', strtotime($thn . '-' . $bln . '-' . $hariakhir));
            $periode_text = date('F Y', strtotime($thn . '-' . $bln . '-01'));

            $tb_slip = DB::table('tb_salary_excel')->where('periode', $periode)
                ->where('NIK', $NIK)
                ->get();

            $is_magang = 0;
            $tb_employee = DB::table('tb_employees')->where('NIK', $NIK)->where('position_id', 19)->where('status', '1')->get();
            foreach ($tb_employee as $dt) {
                $is_magang = 1;
            }

            $FileName = 'SLIP_GAJI ' . $periode . ' ' . $NIK . '.PDF';
            if ($is_magang == 0)
                $pdf = PDF::loadview('page_ess/slip_gaji_mail', ['tb_slip' => $tb_slip, 'thn' => $thn, 'bln' => $bln, 'periode_text' => $periode_text, 'akhirbulan' => $akhirbulan, 'periode' => $periode, 'hariawal' => $hariawal, 'NIK' => $NIK, 'menu' => 'overtimes'])->setPaper(array(0, 0, 280, 620));
            else
                $pdf = PDF::loadview('page_ess/slip_gaji_magang', ['tb_slip' => $tb_slip, 'thn' => $thn, 'bln' => $bln, 'periode_text' => $periode_text, 'akhirbulan' => $akhirbulan, 'periode' => $periode, 'hariawal' => $hariawal, 'NIK' => $NIK, 'menu' => 'overtimes'])->setPaper(array(0, 0, 280, 620));
            return $pdf->stream($FileName);
        }

        //return view('page/user/m_report/slip_overtime_mail',['tb_sum'=>$tb_sum,'tb_slip'=>$tb_slip,'thn'=>$thn,'bln'=>$bln,'judul'=>$judul,'akhirbulan'=>$akhirbulan,'periode'=>$periode,'hariawal'=>$hariawal,'id_employee'=>$id_employee,'menu'=>'overtimes']);
    }
    function employee_slip_ot()
    {
        $email = Auth::user()->email;
        $tb_employee = DB::table('tb_emails')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_emails.id_employee')
            ->leftjoin('tb_positions', 'tb_positions.id', '=', 'tb_employees.position_id')
            ->where('email_address', $email)->get();
        foreach ($tb_employee as $dt) {
            $id_employee = $dt->id_employee;
            $NIK = $dt->NIK;
            $employee_name = $dt->employee_name;
            $position_index = $dt->position_index;
        }
        $tb_summary_overtime = DB::table('tb_summary_overtime')
            ->leftjoin('tb_rapel_khusus', 'tb_rapel_khusus.id_summary', '=', 'tb_summary_overtime.id')
            ->where('send_mail', '1')
            ->where('tb_summary_overtime.id_employee', $id_employee)
            ->orderby('periode', 'desc')
            ->limit(6)
            ->get(['tb_summary_overtime.*', 'tb_rapel_khusus.rapel_gaji', 'tb_rapel_khusus.rapel_ot', 'tb_rapel_khusus.pph21_rapel', 'tb_rapel_khusus.rapel_total']);

        $tb_utility = DB::table('tb_utilities')->where('atribut', 'ess_leave')->get(['status']);
        foreach ($tb_utility as $dt) {
            $ess_leave = $dt->status;
        }
        $tb1 = DB::table('tb_ot_summary')->where('id_employee', $id_employee)->where('distribute', '1')->orderby('periode', 'desc')->get();
        if ($position_index < 3) {
            $status = '1';
        } else {
            $status = '2';
        }
        if (request()->user()->hasRole('root') || request()->user()->hasRole('ess')) {
            return view('page_ess/slip_overtime', ['tb_summary_overtime' => $tb_summary_overtime, 'tb1' => $tb1, 'NIK' => $NIK, 'employee_name' => $employee_name, 'status' => $status, 'menu' => 'slip_gaji', 'juduls' => 'Slip Overtime', 'ess_leave' => $ess_leave]);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function document($id_doc)
    {
        $tb_training_document = DB::table('tb_training_document')->where('doc_level', '0')->orderby('id', 'desc')->get();
        $related_file = DB::table('tb_training_document')->where('id', $id_doc)->get();
        $file_name = '';
        $document_name = '';
        $related_link = '';
        foreach ($related_file as $dt) {
            $file_name = $dt->file_name;
            $document_name = $dt->document_name;
            $related_link = $dt->related_link;
        }
        if (request()->user()->hasRole('root') || request()->user()->hasRole('training')) {
            return view('page_ess/training_document', ['tb_training_document' => $tb_training_document, 'id_doc' => $id_doc, 'file_name' => $file_name, 'related_link' => $related_link, 'document_name' => $document_name, 'menu' => 'training_tools', 'juduls' => 'Training Documents']);
            //}elseif (request()->user()->hasRole('ess')){
            //return view('page/training/document',['tb_training_document'=>$tb_training_document,'site'=>$this->site,'menu'=>'training_tools','juduls'=>'Training Documents']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function document_upload(Request $data)
    {
        $admin = Auth::user()->name;
        $documentId = trim((string) $data->input('id_document'));
        $isNewDocument = $documentId === '';
        $contentType = $data->input('content_type');
        if ($contentType === null) {
            $contentType = $isNewDocument
                ? ($data->hasFile('training_doc') ? 'file' : '')
                : 'keep';
        }

        if (trim((string) $data->document_name) === '')
            return redirect()->back()->with(['success' => 'Document Name can not be null']);

        if ($isNewDocument && (trim((string) $data->skill) === '' || trim((string) $data->department) === ''))
            return redirect()->back()->with(['success' => 'Skill and Department can not be null']);

        if (!in_array($contentType, ['file', 'link', 'keep'], true) || ($isNewDocument && $contentType === 'keep'))
            return redirect()->back()->with(['success' => 'Pilih upload file atau masukkan link dokumen']);

        if ($contentType === 'file' && !$data->hasFile('training_doc'))
            return redirect()->back()->with(['success' => 'Silakan pilih file untuk diupload']);

        $relatedLink = trim((string) $data->input('related_link'));
        if ($contentType === 'link' && (
            !filter_var($relatedLink, FILTER_VALIDATE_URL)
            || !preg_match('/^https?:\\/\\//i', $relatedLink)
            || strlen($relatedLink) > 2048
        )) {
            return redirect()->back()->with(['success' => 'Masukkan link dokumen yang valid dengan awalan http:// atau https://']);
        }

        $file = $contentType === 'file' ? $data->file('training_doc') : null;
        $namaFile = $file ? $file->getClientOriginalName() : '';
        if ($file) {
            $existingFileQuery = DB::table('tb_training_document')
                ->where('file_name', $namaFile)
                ->where('doc_level', '0');
            if (!$isNewDocument) {
                $existingFileQuery->where('id', '<>', $documentId);
            }
            if ($existingFileQuery->exists())
                return redirect()->back()->with(['success' => 'File already exists']);
        }

        if ($isNewDocument) {
            if ($file && !$file->move(storage_path('app/public'), $namaFile))
                return redirect()->back()->with(['success' => 'File gagal diupload']);

            $add = DB::transaction(function () use ($data, $namaFile, $relatedLink, $contentType, $admin) {
                $category = 'TRN';
                $skill = trim($data->skill);
                $selectedNomor = trim((string) $data->nomor);
                $documentQuery = DB::table('tb_training_document')
                    ->where('category', $category)
                    ->where('skill', $skill)
                    ->where('doc_level', '0')
                    ->lockForUpdate();

                if ($selectedNomor !== '') {
                    $nomor = (int) $selectedNomor;
                    if (!$documentQuery->where('nomor', $nomor)->exists()) {
                        return false;
                    }
                    $revision = ((int) DB::table('tb_training_document')
                        ->where('category', $category)
                        ->where('skill', $skill)
                        ->where('nomor', $nomor)
                        ->where('doc_level', '0')
                        ->lockForUpdate()
                        ->max('revision')) + 1;
                } else {
                    $nomor = ((int) $documentQuery->max('nomor')) + 1;
                    $revision = 1;
                }
                $codeDocument = $category . '-' . $skill . '-' . str_pad($nomor, 3, '0', STR_PAD_LEFT) . '-' . str_pad($revision, 2, '0', STR_PAD_LEFT);

                return DB::table('tb_training_document')->insert([
                    'document_name' => $data->document_name,
                    'file_name' => $namaFile,
                    'related_link' => $contentType === 'link' ? $relatedLink : null,
                    'doc_level' => '0',
                    'category' => $category,
                    'skill' => $skill,
                    'revision' => $revision,
                    'nomor' => $nomor,
                    'code_document' => $codeDocument,
                    'training_name' => $data->training_name,
                    'department' => $data->department,
                    'author' => $admin,
                    'status' => 0,
                    'information' => $data->information,
                    'admin' => $admin,
                ]);
            });
            if (!$add) {
                if ($file && file_exists(storage_path('app/public/' . $namaFile))) {
                    unlink(storage_path('app/public/' . $namaFile));
                }
                return redirect()->back()->with(['success' => 'Nomor tidak tersedia untuk skill yang dipilih']);
            }
            return redirect()->back()->with(['success' => 'Dokumen berhasil disimpan']);
        } else {
            $updateData = [
                'document_name' => $data->document_name,
                'training_name' => $data->training_name,
                'department' => $data->department,
                'information' => $data->information,
                'admin' => $admin,
            ];
            if (request()->user()->hasRole('training')) {
                $updateData['status'] = $data->status;
            }
            $existingDocument = DB::table('tb_training_document')
                ->where('id', $documentId)
                ->first(['file_name']);
            if (!$existingDocument)
                return redirect()->back()->with(['success' => 'Dokumen tidak ditemukan']);

            if ($contentType === 'link') {
                $updateData['file_name'] = '';
                $updateData['related_link'] = $relatedLink;
            } elseif ($contentType === 'file') {
                if (!$file->move(storage_path('app/public'), $namaFile))
                    return redirect()->back()->with(['success' => 'File gagal diupload']);
                $updateData['file_name'] = $namaFile;
                $updateData['related_link'] = null;
            }

            $update = DB::table('tb_training_document')->where('id', $documentId)->update($updateData);
            if ($update && $contentType !== 'keep' && $existingDocument->file_name && $existingDocument->file_name !== $namaFile) {
                $oldFilePath = storage_path('app/public/' . $existingDocument->file_name);
                $oldFileStillUsed = DB::table('tb_training_document')
                    ->where('file_name', $existingDocument->file_name)
                    ->exists();
                if (!$oldFileStillUsed && is_file($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }
            if ($update)
                return redirect()->back()->with(['success' => 'Update Sukses']);
        }
        return redirect()->back()->with(['success' => 'There is no change']);
    }
    function delete_document(Request $data)
    {
        $tb_training_document = DB::table('tb_training_document')->where('id', $data->id)->get();
        foreach ($tb_training_document as $dt) {
            if (empty($dt->file_name)) {
                continue;
            }
            $file_path = storage_path('app/public/' . $dt->file_name);
            if (is_file($file_path)) {
                unlink($file_path);
            }
        }
        $delete = DB::table('tb_training_document')->where('id', $data->id)->delete();
        if ($delete) {
            $hasil = 'Sukses';
        } else
            $hasil = 'Gagal';
        return $hasil;
    }
    function document_keyword($id)
    {
        if (!request()->user()->hasRole('root') && !request()->user()->hasRole('training')) {
            return response()->json(['message' => 'Anda tidak punya akses'], 403);
        }

        $document = DB::table('tb_training_document')->where('id', $id)->first(['id', 'document_name']);
        if (!$document) {
            return response()->json(['message' => 'Document tidak ditemukan'], 404);
        }

        $keywords = DB::table('tb_training_dockeyword')
            ->where('id_training_document', $id)
            ->orderBy('keyword', 'asc')
            ->get(['id', 'keyword']);

        return response()->json(['document' => $document, 'keywords' => $keywords]);
    }
    function document_keyword_save(Request $data)
    {
        if (!request()->user()->hasRole('root') && !request()->user()->hasRole('training')) {
            return response()->json(['message' => 'Anda tidak punya akses'], 403);
        }

        $documentId = $data->input('id_training_document');
        if (!DB::table('tb_training_document')->where('id', $documentId)->exists()) {
            return response()->json(['message' => 'Document tidak ditemukan'], 404);
        }

        DB::transaction(function () use ($data, $documentId) {
            $deleteIds = $data->input('delete_ids', []);
            if (is_array($deleteIds) && count($deleteIds) > 0) {
                DB::table('tb_training_dockeyword')
                    ->where('id_training_document', $documentId)
                    ->whereIn('id', $deleteIds)
                    ->delete();
            }

            $keyword = trim((string) $data->input('keyword'));
            if ($keyword !== '') {
                $exists = DB::table('tb_training_dockeyword')
                    ->where('id_training_document', $documentId)
                    ->where('keyword', $keyword)
                    ->exists();
                if (!$exists) {
                    DB::table('tb_training_dockeyword')->insert([
                        'id_training_document' => $documentId,
                        'keyword' => $keyword,
                    ]);
                }
            }
        });

        return response()->json(['message' => 'Keyword berhasil diperbarui']);
    }
    function document_download($id)
    {
        if (request()->user()->hasRole('ess') || request()->user()->hasRole('root') || request()->user()->hasRole('training')) {
            $tb_training_document = DB::table('tb_training_document')->where('id', $id)->get();
            foreach ($tb_training_document as $dt) {
                $file_name = $dt->file_name;
                if (empty($file_name)) {
                    return redirect()->back()->with(['error' => 'Dokumen ini tersedia melalui link, bukan file yang dapat diunduh.']);
                }
                if (Storage::disk('public')->exists($file_name)) {
                    return Storage::disk('public')->download($file_name);
                }
                if (Storage::disk('local')->exists('public/' . $file_name)) {
                    return Storage::disk('local')->download('public/' . $file_name);
                }
                if (Storage::disk('local')->exists($file_name)) {
                    return Storage::disk('local')->download($file_name);
                }
                $file_path = storage_path('app/public/' . $file_name);
                if (file_exists($file_path)) {
                    return response()->download($file_path);
                }
                $pathLocal = storage_path('app/' . $file_name);
                if (file_exists($pathLocal)) {
                    return response()->download($pathLocal);
                }
                return redirect()->back()->with(['error' => 'File tidak tersedia di server storage.']);
            }
            return redirect()->back()->with(['error' => 'Dokumen tidak ditemukan.']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function training_invitation()
    {
        if (request()->user()->hasRole('ess')) {
            $email = Auth::user()->email;
            $tb_employee = DB::table('tb_emails')
                ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_emails.id_employee')
                ->where('email_address', $email)->get();
            foreach ($tb_employee as $dt) {
                $id_employee = $dt->id_employee;
            }
            $tb_training_invitation = DB::table('tb_training_invitation')
                ->leftjoin('tb_training_schedule', 'tb_training_schedule.id', '=', 'tb_training_invitation.id_training_schedule')
                ->leftjoin('tb_training_list', 'tb_training_list.id', '=', 'tb_training_schedule.id_training')
                ->leftjoin('tb_skill_type', 'tb_skill_type.id', '=', 'tb_training_list.id_type')
                ->leftjoin('tb_training_participant', 'tb_training_participant.id_training_invitation', '=', 'tb_training_invitation.id')
                ->leftjoin('tb_related_test', 'tb_related_test.id_training_schedule', '=', 'tb_training_schedule.id')
                ->leftjoin('tb_events', 'tb_events.id_training_schedule', '=', 'tb_training_schedule.id')
                ->leftjoin('tb_event_attendances', function ($join) {
                    $join->on('tb_event_attendances.id_event', '=', 'tb_events.id')
                        ->on('tb_event_attendances.id_entrant', '=', 'tb_training_invitation.id_employee');
                })
                ->where('tb_training_invitation.id_employee', $id_employee)
                //->orderby('tb_training_schedule.id','desc')->get(['tb_training_schedule.*','tb_training_list.training_name','tb_skill_type.skill_type']);
                ->orderby('tb_training_schedule.id', 'desc')
                ->get([
                    'tb_training_invitation.*',
                    'tb_training_schedule.id as id_training_schedule',
                    'tb_training_schedule.id_training',
                    'tb_training_schedule.nara_sumber',
                    'tb_training_schedule.tanggal',
                    'tb_training_schedule.start',
                    'tb_training_schedule.finish',
                    'tb_training_list.training_name',
                    'tb_skill_type.skill_type',
                    'tb_training_participant.free_test',
                    'tb_training_participant.post_test',
                    'tb_training_participant.passing_grade',
                    'tb_training_participant.progress',
                    'tb_training_participant.grade_status',
                    'tb_related_test.id_test',
                    'tb_training_participant.id as idparticipant',
                    'tb_events.id as id_event',
                    'tb_event_attendances.id as id_event_attendance',
                    'tb_event_attendances.entrant_code',
                    'tb_event_attendances.checkin as event_checkin',
                    'tb_event_attendances.checkout as event_checkout',
                    'tb_event_attendances.status as event_attendance_status',
                ]);
            return view('page/training/training_invitation', ['tb_training_invitation' => $tb_training_invitation, 'page_status' => '', 'menu' => 'training_activity', 'juduls' => 'Training Schedule']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function training_schedule($id_training_invitation)
    {
        $email = Auth::user()->email;
        $admin = Auth::user()->name;
        $time = date('Y-m-d h:i:s');
        $tanggal = date('Y-m-d');
        $start = $time;
        $finish = $time;

        $id_employee = DB::table('tb_emails')->where('email_address', $email)->value('id_employee');
        $id_training_schedule = DB::table('tb_training_invitation')->where('id_employee', $id_employee)->where('id', $id_training_invitation)->value('id_training_schedule');
        $cek_aktual = DB::table('tb_training_actual')
            ->where('id_training_schedule', $id_training_schedule)
            ->where('is_delete', '0')
            ->count();
           
        if ($cek_aktual == 0) {
            $tb_training_schedule = DB::table('tb_training_schedule')->where('id', $id_training_schedule)->get();
            foreach ($tb_training_schedule as $dt) {
                $add_actual = DB::table('tb_training_actual')->insert([
                    'id_training_schedule' => $dt->id,
                    'nara_sumber' => $dt->nara_sumber,
                    'tanggal_aktual' => $tanggal,
                    'start_aktual' => $start,
                    'finish_aktual' => $finish,
                    'in_class' => 0,
                    'admin' => $admin,
                    'created_at' => $time,
                ]);
            }
        }
        $id_training_actual = DB::table('tb_training_actual')->where('id_training_schedule', $id_training_schedule)->where('is_delete', '0')->value('id');
        $cek_participant = DB::table('tb_training_participant')
            ->where('id_training_invitation', $id_training_invitation)
            ->where('is_delete', '0')
            ->where('id_employee', $id_employee)
            ->where('id_training_actual', $id_training_actual)
            ->count();
        if ($cek_participant == 0) {
            $tb_training_invitation = DB::table('tb_training_invitation')
                ->where('tb_training_invitation.id', $id_training_invitation)
                ->get();
            foreach ($tb_training_invitation as $dt) {
                $id_test = DB::table('tb_related_test')->where('id_training_schedule', $id_training_schedule)->value('id_test');
                $passing_grade = DB::table('tb_training_test')->where('id', $id_test)->value('passing_grade');
                $add_participant = DB::table('tb_training_participant')->insert([
                    'id_training_actual' => $id_training_actual,
                    'id_training_invitation' => $dt->id,
                    'id_employee' => $dt->id_employee,
                    'NIK' => $dt->NIK,
                    'nama_karyawan' => $dt->nama_karyawan,
                    'department' => $dt->department,
                    'jabatan' => $dt->jabatan,
                    'passing_grade' => $passing_grade,
                    'admin' => $admin,
                    'created_at' => $time
                ]);
                if ($add_participant) {
                    $this->update_tb_schedule($id_training_schedule);
                }
            }
        }
        $id_training_participant = DB::table('tb_training_participant')->where('id_training_invitation', $id_training_invitation)->where('id_training_actual',$id_training_actual)->value('id');
        $tb_questions = DB::table('tb_question as tq')
            ->leftjoin('tb_related_test as rt', 'rt.id_test', '=', 'tq.id_training_test')
            ->leftjoin('tb_training_schedule as ts', 'ts.id', '=', 'rt.id_training_schedule')
            ->where('ts.id', $id_training_schedule)->get(['tq.id', 'tq.answer_code']);

        // Batch insert: cek existing questions sekaligus, hindari N+1 query
        if ($tb_questions->isNotEmpty()) {
            $existing_free = DB::table('tb_free_test')
                ->where('id_participant', $id_training_participant)
                ->whereIn('id_question', $tb_questions->pluck('id'))
                ->pluck('id_question')->toArray();

            $existing_post = DB::table('tb_post_test')
                ->where('id_participant', $id_training_participant)
                ->whereIn('id_question', $tb_questions->pluck('id'))
                ->pluck('id_question')->toArray();

            $free_inserts = [];
            $post_inserts = [];
            foreach ($tb_questions as $dt2) {
                if (!in_array($dt2->id, $existing_free)) {
                    $free_inserts[] = [
                        'id_participant' => $id_training_participant,
                        'id_question'    => $dt2->id,
                        'answer_code'    => $dt2->answer_code,
                        'admin'          => $admin,
                    ];
                }
                if (!in_array($dt2->id, $existing_post)) {
                    $post_inserts[] = [
                        'id_participant' => $id_training_participant,
                        'id_question'    => $dt2->id,
                        'answer_code'    => $dt2->answer_code,
                        'admin'          => $admin,
                    ];
                }
            }
            if (!empty($free_inserts))
                DB::table('tb_free_test')->insert($free_inserts);
            if (!empty($post_inserts))
                DB::table('tb_post_test')->insert($post_inserts);
        }
        return redirect('/Training/Actual/' . $id_training_actual . '/0');
        // return redirect()->back();

    }
    function training_actual($id, $id_doc)
    {
        $tb_training_actual = DB::table('tb_training_actual')->where('id', $id)->get();
        foreach ($tb_training_actual as $dt) {
            $id_training_schedule = $dt->id_training_schedule;
        }
        $email = Auth::user()->email;
        $tb_employee = DB::table('tb_emails')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_emails.id_employee')
            ->where('email_address', $email)->get();
        foreach ($tb_employee as $dt) {
            $id_employee = $dt->id_employee;
            $pin = $dt->PIN;
        }

        if (request()->user()->hasRole('ess')) {
            $tb_employee = DB::table('tb_training_invitation')->where('id_employee', $id_employee)->orderby('nama_karyawan', 'asc')->get();
            $tb_training_actual = DB::table('tb_training_actual')
                ->leftjoin('tb_training_schedule', 'tb_training_schedule.id', '=', 'tb_training_actual.id_training_schedule')
                ->leftjoin('tb_training_list', 'tb_training_list.id', '=', 'tb_training_schedule.id_training')
                ->leftjoin('tb_skill_type', 'tb_skill_type.id', '=', 'tb_training_list.id_type')
                ->where('tb_training_actual.id', $id)
                ->orderby('tb_training_actual.id', 'desc')->get(['tb_training_actual.*', 'tb_training_list.training_name', 'tb_skill_type.skill_type', 'tb_training_schedule.nara_sumber']);
            foreach ($tb_training_actual as $dt) {
                if ($dt->nara_sumber == '')
                    $type_training = 'Virtual';
                else
                    $type_training = 'InClass';
            }
            $tb_training_participant = DB::table('tb_training_participant')
                ->where('id_training_actual', $id)
                ->where('id_employee', $id_employee)
                ->orderby('id', 'desc')->get();
            foreach ($tb_training_participant as $dt) {
                $id_participant = $dt->id;
                $data['pre_test'] = $dt->free_test;
                $data['post_test'] = $dt->post_test;
                $data['grade_status'] = $dt->grade_status;
            }

            $tb_related_document = DB::table('tb_related_document')
                ->leftjoin('tb_training_document', 'tb_training_document.id', '=', 'tb_related_document.id_document')
                ->leftjoin('tb_training_schedule', 'tb_training_schedule.id', '=', 'tb_related_document.id_training_schedule')
                ->leftjoin('tb_training_actual', 'tb_training_actual.id_training_schedule', '=', 'tb_training_schedule.id')
                ->where('tb_training_actual.id', $id)->orderby('id', 'desc')->get(['tb_related_document.*', 'tb_training_document.document_name', 'tb_training_document.file_name', 'tb_training_document.id as id_doc', 'tb_training_document.related_link']);

            $related_file = DB::table('tb_related_document')
                ->leftjoin('tb_training_document', 'tb_training_document.id', '=', 'tb_related_document.id_document')
                ->where('id_document', $id_doc)->get();
            $file_name = '';
            foreach ($related_file as $dt) {
                $file_name = $dt->file_name;
            }
            //return $id_doc;
            $tb_training_test = DB::table('tb_training_test')->orderby('test_name', 'asc')->get();
            $tb_related_test = DB::table('tb_related_test')
                ->leftjoin('tb_training_test', 'tb_training_test.id', '=', 'tb_related_test.id_test')
                ->where('id_training_schedule', $id_training_schedule)->get(['tb_related_test.*', 'tb_training_test.test_name', 'tb_training_test.minutes', 'tb_training_test.passing_grade']);

            $tb_assignment=DB::table('tb_training_assignment as a')
                ->leftjoin('tb_training_participant as b','b.id_training_actual','=','a.id_training_actual')
                ->where('a.id_training_actual',$id)->where('b.id_employee',$id_employee)
                ->get(['a.*','b.id as participant_id','b.assignment_status','b.collected_date','b.posted_file']);

            return view('page/training/training_participant', ['tb_training_participant' => $tb_training_participant, 'data' => $data, 'tb_training_actual' => $tb_training_actual, 'tb_employee' => $tb_employee, 'tb_training_test' => $tb_training_test, 'tb_related_document' => $tb_related_document, 'tb_related_test' => $tb_related_test, 'id_training' => $id, 'id_participant' => $id_participant, 'id_doc' => $id_doc, 'file_name' => $file_name, 'type_training' => $type_training,'tb_assignment'=>$tb_assignment, 'menu' => 'training_activity', 'juduls' => 'Training Schedule']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function submit_training_assignment(Request $data)
    {
        abort_unless(Auth::user()->hasRole('ess'), 403, 'Anda tidak punya akses');

        $validated = $data->validate([
            'id_training_actual' => 'required|integer',
            'id_assignment' => 'required|integer|min:1',
            'posted_file' => 'required|file|max:10240',
        ]);

        $employeeId = DB::table('tb_emails')
            ->where('email_address', Auth::user()->email)
            ->value('id_employee');
        abort_if(!$employeeId, 403, 'Data karyawan tidak ditemukan.');

        $assignment = DB::table('tb_training_assignment')
            ->where('id', $validated['id_assignment'])
            ->where('id_training_actual', $validated['id_training_actual'])
            ->first(['id', 'id_training_actual']);
        abort_if(!$assignment, 404, 'Assignment tidak ditemukan.');

        $participant = DB::table('tb_training_participant')
            ->where('id_training_actual', $assignment->id_training_actual)
            ->where('id_employee', $employeeId)
            ->first(['id', 'posted_file']);
        abort_if(!$participant, 403, 'Anda tidak terdaftar sebagai peserta training ini.');

        $uploadedFile = $data->file('posted_file');
        $fileName = basename(str_replace('\\', '/', $uploadedFile->getClientOriginalName()));
        if ($fileName === '' || \Illuminate\Support\Str::length($fileName) > 255) {
            return response()->json(['message' => 'Nama file tidak valid atau melebihi 255 karakter.'], 422);
        }

        $disk = Storage::disk('local');
        $directory = 'training/assignment_submissions/' . $assignment->id . '/' . $participant->id;
        $newFilePath = $disk->putFileAs($directory, $uploadedFile, $fileName);
        if ($newFilePath === false) {
            throw new \RuntimeException('File assignment gagal disimpan.');
        }

        $oldFilePath = $participant->posted_file
            ? $directory . '/' . basename(str_replace('\\', '/', $participant->posted_file))
            : null;

        try {
            DB::table('tb_training_participant')
                ->where('id', $participant->id)
                ->update([
                    'posted_file' => $fileName,
                    'assignment_status' => 1,
                    'collected_date' => now(),
                ]);
        } catch (\Throwable $exception) {
            if ($newFilePath !== $oldFilePath && !$disk->delete($newFilePath)) {
                Log::warning('Failed to remove a training assignment submission after a database error.', [
                    'file' => $newFilePath,
                ]);
            }

            throw $exception;
        }

        if ($oldFilePath && $oldFilePath !== $newFilePath && !$disk->delete($oldFilePath)) {
            Log::warning('Failed to remove the replaced training assignment submission.', [
                'file' => $oldFilePath,
            ]);
        }

        return response()->json(['message' => 'File assignment berhasil dikirim.']);
    }
    function download_training_assignment_submission($id)
    {
        abort_unless(Auth::user()->hasRole('ess'), 403, 'Anda tidak punya akses');

        $employeeId = DB::table('tb_emails')
            ->where('email_address', Auth::user()->email)
            ->value('id_employee');
        abort_if(!$employeeId, 403, 'Data karyawan tidak ditemukan.');

        $submission = DB::table('tb_training_assignment as a')
            ->join('tb_training_participant as p', 'p.id_training_actual', '=', 'a.id_training_actual')
            ->where('a.id', $id)
            ->where('p.id_employee', $employeeId)
            ->first(['a.id as assignment_id', 'p.id as participant_id', 'p.posted_file']);
        abort_if(!$submission || !$submission->posted_file, 404, 'File assignment tidak ditemukan.');

        $fileName = basename(str_replace('\\', '/', $submission->posted_file));
        $filePath = 'training/assignment_submissions/' . $submission->assignment_id . '/' . $submission->participant_id . '/' . $fileName;
        $disk = Storage::disk('local');
        abort_unless($disk->exists($filePath), 404, 'File assignment tidak ditemukan.');

        return response()->download($disk->path($filePath), $fileName);
    }
    function free_test($id_participant)
    {
        $email = Auth::user()->email;
        $tb_employee = DB::table('tb_emails')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_emails.id_employee')
            ->where('email_address', $email)->get();
        foreach ($tb_employee as $dt) {
            $id_employee = $dt->id_employee;
        }
        $tb_training_participant = DB::table('tb_training_participant')->where('id', $id_participant)->get();
        foreach ($tb_training_participant as $dt2) {
            $id_employee2 = $dt2->id_employee;
        }
        if ($id_employee == $id_employee2) {
            $tb_training_test = DB::table('tb_training_test')
                ->leftjoin('tb_related_test', 'tb_training_test.id', '=', 'tb_related_test.id_test')
                ->leftjoin('tb_training_schedule', 'tb_training_schedule.id', '=', 'tb_related_test.id_training_schedule')
                ->leftjoin('tb_training_actual', 'tb_training_actual.id_training_schedule', '=', 'tb_training_schedule.id')
                ->leftjoin('tb_training_participant', 'tb_training_actual.id', '=', 'tb_training_participant.id_training_actual')
                ->where('tb_training_participant.id', $id_participant)
                ->get(['tb_training_test.*', 'tb_training_actual.tanggal_aktual', 'tb_training_participant.free_test', 'tb_training_participant.id as id_participant', 'tb_training_participant.id_training_actual']);
            foreach ($tb_training_test as $dt) {
                $id_test = $dt->id;
                $id_training_actual = $dt->id_training_actual;
            }
            $tb_question = DB::table('tb_question')
                ->leftjoin('tb_free_test', 'tb_free_test.id_question', '=', 'tb_question.id')
                ->where('id_training_test', $id_test)
                ->where('tb_free_test.id_participant', $id_participant)
                ->orderby('index_question', 'asc')->get(['tb_question.*', 'tb_free_test.answer_actual', 'tb_free_test.answer_status']);
            foreach ($tb_training_test as $dt) {
                $test_name = $dt->test_name;
                $id_participant = $dt->id_participant;
                $passing_grade = $dt->passing_grade;
            }
            //return $id_participant;
            return view('page/training/training_free_test', ['tb_training_test' => $tb_training_test, 'test_name' => $test_name, 'tb_question' => $tb_question, 'id_test' => $id_test, 'id_participant' => $id_participant, 'passing_grade' => $passing_grade, 'id_training_actual' => $id_training_actual, 'menu' => 'training_tools', 'juduls' => 'Training Documents']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function simpan_free_test(request $data)
    {
        $hasil = "There is no change";
        date_default_timezone_set("Asia/Jakarta");
        $now = date('Y-m-d H:i:s');
        $tb_test = DB::table('tb_free_test')->where('id_participant', $data->idparticipant)->where('id_question', $data->idquestion)->get();
        #region NEW

        $id_participant = $data->idparticipant;
        $check_telat = DB::table('tb_training_actual as a')
            ->leftJoin('tb_training_participant as b', 'b.id_training_actual', 'a.id')
            ->where('b.id', $id_participant)->select('a.tanggal_aktual', 'a.finish_aktual')->get();
        foreach ($check_telat as $r) {
            $finish_date = $r->tanggal_aktual;
            $finish_time = $r->finish_aktual;
        }
        $finish = date($finish_date . ' ' . $finish_time);
        #endregion
        // dd($now.'     '.$finish);
        foreach ($tb_test as $dt) {
            $answer_code = $dt->answer_code;
            if ($answer_code == $data->isi)
                $status = '1';
            else
                $status = '0';
            $now > $finish ? $telat = '1' : $telat = '0';
            $update = DB::table('tb_free_test')->where('id_participant', $data->idparticipant)->where('id_question', $data->idquestion)->update([
                'answer_actual' => $data->isi,
                'answer_status' => $status,
                'answer_late' => $telat,
                'created_at' => $now
            ]);
            if ($update)
                $hasil = "Sukses";
        }

        $id_test = $data->idtest;
        $passing_grade = $data->passinggrade;
        $id_participant = $data->idparticipant;

        $email = Auth::user()->email;
        $id_employee = DB::table('tb_emails')
            ->where('email_address', $email)
            ->value('id_employee');
        $tb_question = DB::table('tb_question')
            ->leftjoin('tb_free_test', 'tb_free_test.id_question', '=', 'tb_question.id')
            ->leftjoin('tb_training_participant', 'tb_training_participant.id', '=', 'tb_free_test.id_participant')
            ->where('id_training_test', $id_test)
            ->where('tb_training_participant.id', $id_participant)
            ->orderby('index_question', 'asc')
            ->get([
                'tb_question.id', 'tb_question.index_question', 'tb_question.question',
                'tb_question.option_a', 'tb_question.option_b', 'tb_question.option_c', 'tb_question.option_d',
                'tb_question.answer_code',
                'tb_free_test.answer_actual', 'tb_free_test.answer_status', 'tb_free_test.answer_late'
            ]);
        $no = 1;
        $total = 0;
        $benar = 0;
        $percent = '';
        $status_grade = 0;
        $konten = '';
        // $tokens = Sesion
        foreach ($tb_question as $dt) {
            $no++;
            $total++;
            $konten .= "<div class='col-md-12 col-lg-12 col-xs-12'>";
            $konten .= "<div class='box box-default'>";
            $konten .= "<div class='box-header with-border'>";
            $konten .= "<h3 class='box-title'>";
            $konten .= "<b class='label label-info'>" . $dt->index_question . "</b>";
            $konten .= "</h3>";
            $konten .= "<div class='pull-right'>";

            if ($dt->answer_code == $dt->answer_actual && $dt->answer_late == 0) {
                $benar++;
                //$konten.="<i class='fa fa-thumbs-o-up'></i> ";
                // $konten.="<b class='badge bg-green'>".$dt->answer_actual."</b>";
            } elseif ($dt->answer_code != $dt->answer_actual && $dt->answer_actual != '') {
                // $konten.="<b class='badge bg-red'>".$dt->answer_actual."</b>";
            }
            $percent = $benar * 100 / $total;
            if ($percent >= $passing_grade)
                $status_grade = 1;
            else
                $status_grade = 0;
            $konten .= "</div>";
            $konten .= "</div>";
            $konten .= "<div class='box-body row'>";
            $konten .= $dt->question;
            $konten .= "<div class='form-group'>";
            $konten .= "<div class='radio pilihan' data-idquestion='" . $dt->id . "' data-isi='A'><label><input type='radio' name='pilihan' id='" . $dt->index_question . "#" . $id_participant . "' value='A'";
            if ($dt->answer_actual == 'A')
                $konten .= ' checked';
            $konten .= ">" . $dt->option_a . "</label></div>";
            $konten .= "<div class='radio pilihan' data-idquestion='" . $dt->id . "' data-isi='B'><label><input type='radio' name='pilihan' id='" . $dt->index_question . "#" . $id_participant . "' value='B'";
            if ($dt->answer_actual == 'B')
                $konten .= ' checked';
            $konten .= ">" . $dt->option_b . "</label></div>";
            $konten .= "<div class='radio pilihan' data-idquestion='" . $dt->id . "' data-isi='C'><label><input type='radio' name='pilihan' id='" . $dt->index_question . "#" . $id_participant . "' value='C'";
            if ($dt->answer_actual == 'C')
                $konten .= ' checked';
            $konten .= ">" . $dt->option_c . "</label></div>";
            $konten .= "<div class='radio pilihan' data-idquestion='" . $dt->id . "' data-isi='D'><label><input type='radio' name='pilihan' id='" . $dt->index_question . "#" . $id_participant . "' value='D'";
            if ($dt->answer_actual == 'D')
                $konten .= ' checked';
            $konten .= ">" . $dt->option_d . "</label></div>";
            $konten .= "</div>";
            $konten .= "</div>";
            $konten .= "</div>";
            $konten .= "</div>";
        }
        $konten .= "<input type='hidden' value='" . $percent . "' id='percent'>";
        $konten .= "<input type='hidden' value='" . $status_grade . "' id='statusgrade'>";
        $progress = 2;

        DB::table('tb_training_participant')->where('id', $data->idparticipant)->update([
            'free_test' => $percent,
            'progress' => $progress,
            'passing_grade' => $passing_grade,
            'grade_status' => $status_grade
        ]);
        return $konten;
    }
    function post_test($id_participant)
    {
        $email = Auth::user()->email;
        $tb_employee = DB::table('tb_emails')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_emails.id_employee')
            ->where('email_address', $email)->get();
        foreach ($tb_employee as $dt) {
            $id_employee = $dt->id_employee;
        }
        $tb_training_participant = DB::table('tb_training_participant')->where('id', $id_participant)->get();
        // return $tb_training_participant;
        foreach ($tb_training_participant as $dt2) {
            $id_employee2 = $dt2->id_employee;
        }
        if ($id_employee == $id_employee2) {
            $tb_training_test = DB::table('tb_training_test')
                ->leftjoin('tb_related_test', 'tb_training_test.id', '=', 'tb_related_test.id_test')
                ->leftjoin('tb_training_schedule', 'tb_training_schedule.id', '=', 'tb_related_test.id_training_schedule')
                ->leftjoin('tb_training_actual', 'tb_training_actual.id_training_schedule', '=', 'tb_training_schedule.id')
                ->leftjoin('tb_training_participant', 'tb_training_actual.id', '=', 'tb_training_participant.id_training_actual')
                ->where('tb_training_participant.id', $id_participant)
                ->get(['tb_training_test.*', 'tb_training_actual.tanggal_aktual', 'tb_training_participant.post_test', 'tb_training_participant.id as id_participant', 'tb_training_participant.id_training_actual']);
            foreach ($tb_training_test as $dt) {
                $id_test = $dt->id;
                $id_training_actual = $dt->id_training_actual;
            }
            $tb_question = DB::table('tb_question')
                ->leftjoin('tb_post_test', 'tb_post_test.id_question', '=', 'tb_question.id')
                ->leftjoin('tb_training_participant', 'tb_training_participant.id', '=', 'tb_post_test.id_participant')
                ->where('id_training_test', $id_test)
                ->where('tb_training_participant.id', $id_participant)
                ->orderby('index_question', 'asc')->get(['tb_question.*', 'tb_post_test.answer_actual', 'tb_post_test.answer_status']);
            foreach ($tb_training_test as $dt) {
                $test_name = $dt->test_name;
                $id_participant = $dt->id_participant;
                $passing_grade = $dt->passing_grade;
            }
            //return $tb_question;
            return view('page/training/training_post_test', ['tb_training_test' => $tb_training_test, 'test_name' => $test_name, 'tb_question' => $tb_question, 'id_test' => $id_test, 'id_participant' => $id_participant, 'passing_grade' => $passing_grade, 'id_training_actual' => $id_training_actual, 'menu' => 'training_tools', 'juduls' => 'Training Documents']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function simpan_post_test(request $data)
    {
        $hasil = "There is no change";
        date_default_timezone_set("Asia/Jakarta");
        $now = date('Y-m-d H:i:s');
        #region NEW
        $id_participant = $data->idparticipant;
        $check_telat = DB::table('tb_training_actual as a')
            ->leftJoin('tb_training_participant as b', 'b.id_training_actual', 'a.id')
            ->where('b.id', $id_participant)->select('a.tanggal_aktual', 'a.finish_aktual')->get();
        foreach ($check_telat as $r) {
            $finish_date = $r->tanggal_aktual;
            $finish_time = $r->finish_aktual;
        }
        $finish = date($finish_date . ' ' . $finish_time);
        #endregion
        $tb_test = DB::table('tb_post_test')->where('id_participant', $data->idparticipant)->where('id_question', $data->idquestion)->get();
        foreach ($tb_test as $dt) {
            $answer_code = $dt->answer_code;
            if ($answer_code == $data->isi)
                $status = '1';
            else
                $status = '0';
            $now > $finish ? $telat = '1' : $telat = '0';

            $update = DB::table('tb_post_test')->where('id_participant', $data->idparticipant)->where('id_question', $data->idquestion)->update([
                'answer_actual' => $data->isi,
                'answer_status' => $status,
                'answer_late' => $telat,
                'created_at' => $now
            ]);
            if ($update)
                $hasil = "Sukses";
        }

        $id_test = $data->idtest;
        $passing_grade = $data->passinggrade;
        $id_participant = $data->idparticipant;

        $email = Auth::user()->email;
        $tb_employee = DB::table('tb_emails')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_emails.id_employee')
            ->where('email_address', $email)->get();
        foreach ($tb_employee as $dt) {
            $id_employee = $dt->id_employee;
        }
        $tb_question = DB::table('tb_question')
            ->leftjoin('tb_post_test', 'tb_post_test.id_question', '=', 'tb_question.id')
            ->leftjoin('tb_training_participant', 'tb_training_participant.id', '=', 'tb_post_test.id_participant')
            ->where('id_training_test', $id_test)
            ->where('tb_training_participant.id', $id_participant)
            ->orderby('index_question', 'asc')->get(['tb_question.*', 'tb_post_test.answer_actual', 'tb_post_test.answer_status']);
        $no = 1;
        $total = 0;
        $benar = 0;
        $percent = '';
        $status_grade = 0;
        $konten = '';
        foreach ($tb_question as $dt) {
            $no++;
            $total++;
            $konten .= "<div class='col-md-12 col-lg-12 col-xs-12'>";
            $konten .= "<div class='box box-default'>";
            $konten .= "<div class='box-header with-border'>";
            $konten .= "<h3 class='box-title'>";
            $konten .= "<b class='label label-info'>" . $dt->index_question . "</b>";
            $konten .= "</h3>";
            $konten .= $dt->question;
            $konten .= "<div class='pull-right'>";

            if ($dt->answer_code == $dt->answer_actual) {
                $benar++;
                //$konten.="<i class='fa fa-thumbs-o-up'></i> ";
                // $konten.="<b class='badge bg-green'>".$dt->answer_actual."</b>";
            } elseif ($dt->answer_code != $dt->answer_actual && $dt->answer_actual != '') {
                // $konten.="<b class='badge bg-red'>".$dt->answer_actual."</b>";
            }
            $percent = $benar * 100 / $total;
            if ($percent >= $passing_grade)
                $status_grade = 1;
            else
                $status_grade = 0;
            $konten .= "</div>";
            $konten .= "</div>";
            $konten .= "<div class='box-body'>";
            $konten .= "<div class='form-group col-lg-6'>";
            $konten .= "<b>A</b> <div class='radio pilihan' data-idquestion='" . $dt->id . "' data-isi='A'><label><input type='radio' name='pilihan" . $dt->index_question . "' id='" . $dt->index_question . "#" . $id_participant . "' value='A'";
            if ($dt->answer_actual == 'A')
                $konten .= ' checked';
            $konten .= ">" . $dt->option_a . "</label></div></div>";
            $konten .= " <div class='form-group col-lg-6'><b>B</b><div class='radio pilihan' data-idquestion='" . $dt->id . "' data-isi='B'><label class=''><input type='radio' name='pilihan" . $dt->index_question . "' id='" . $dt->index_question . "#" . $id_participant . "' value='B'";
            if ($dt->answer_actual == 'B')
                $konten .= ' checked';
            $konten .= ">" . $dt->option_b . "</label></div></div>";
            $konten .= "<div class='form-group col-lg-6'><b>C</b><div class='radio pilihan' data-idquestion='" . $dt->id . "' data-isi='C'><label><input type='radio' name='pilihan" . $dt->index_question . "' id='" . $dt->index_question . "#" . $id_participant . "' value='C'";
            if ($dt->answer_actual == 'C')
                $konten .= ' checked';
            $konten .= ">" . $dt->option_c . "</label></div></div>";
            $konten .= "<div class='form-group col-lg-6'><b>D</b><div class='radio pilihan' data-idquestion='" . $dt->id . "' data-isi='D'><label><input type='radio' name='pilihan" . $dt->index_question . "' id='" . $dt->index_question . "#" . $id_participant . "' value='D'";
            if ($dt->answer_actual == 'D')
                $konten .= ' checked';
            $konten .= ">" . $dt->option_d . "</label></div></div>";
            $konten .= "</div>";
            $konten .= "</div>";
            $konten .= "</div>";
        }
        $konten .= "<input type='hidden' value='" . $percent . "' id='percent'>";
        $konten .= "<input type='hidden' value='" . $status_grade . "' id='statusgrade'>";

        $tb_participant = DB::table('tb_training_participant')->where('id', $data->idparticipant)->get();
        foreach ($tb_participant as $dt) {
            if ($percent < $dt->free_test) {
                $progress = '-1';
            } elseif ($dt->free_test == $percent) {
                $progress = '0';
            } else {
                $progress = '1';
            }
        }
        $update_participant = DB::table('tb_training_participant')->where('id', $data->idparticipant)->update([
            'post_test' => $percent,
            'passing_grade' => $passing_grade,
            'progress' => $progress,
            'grade_status' => $status_grade
        ]);

        return $konten;
    }
    public function send_progress(Request $dt)
    {
        $tb = DB::table('tb_free_test')->where('id_participant', $dt->id_participant)->get();
        $total = 0;
        $benar = 0;
        foreach ($tb as $dt2) {
            $total++;
            if ($dt2->answer_status == 1) {
                $benar++;
            }
        }
        $percent = $benar * 100 / $total;
        $update = DB::table('tb_training_participant')
            ->where('id', $dt->id_participant)
            ->update(['progress' => 3, 'free_test' => $percent]);
        if ($update)
            return 'OK';
        else
            return 'NO';
    }
    public function DocPreview(Request $request)
    {
        $id_doc = $request->id_doc;
        $related_file = DB::table('tb_related_document')
            ->leftjoin('tb_training_document', 'tb_training_document.id', '=', 'tb_related_document.id_document')
            ->where('id_document', $id_doc)->get();
        $file_name = '';
        foreach ($related_file as $dt) {
            $file_name = $dt->file_name;
        }
        $data['file_name'] = $file_name;

        return view('page/training/doc_file', $data);
    }
    public function send_progress_post(Request $dt)
    {
        $tb = DB::table('tb_post_test')->where('id_participant', $dt->id_participant)->get();
        $total = 0;
        $benar = 0;
        foreach ($tb as $dt2) {
            $total++;
            if ($dt2->answer_status == 1) {
                $benar++;
            }
        }
        $percent = $benar * 100 / $total;

        $id_participant = $dt->id_participant;
        $tb_participant = DB::table('tb_training_participant')->where('id', $dt->id_participant)->get();
        foreach ($tb_participant as $dt) {
            if ($dt->post_test < $dt->free_test)
                $progress = '5'; //Progress 5 = Nilai Post Test < dari niali pre test
            elseif ($dt->free_test == $dt->post_test)
                $progress = '0';
            else
                $progress = '6'; //Progress 6 = Nilai Post Test > dari nilai pre test
        }
        $update = DB::table('tb_training_participant')->where('id', $id_participant)->update([
            'progress' => $progress,
            'post_test' => $percent
        ]);
        if ($update)
            return 'OK';
        else
            return 'NO';

    }
    function update_tb_schedule($id)
    {
        $tb1=DB::table('tb_training_invitation')->where('id_training_schedule', $id)->count();
        $tb2=DB::table('tb_training_participant')->where('id_training_actual', function($query) use ($id){
            $query->select('id')->from('tb_training_actual')->where('id_training_schedule', $id);
        })->count();
        $update = DB::table('tb_training_schedule')->where('id', $id)->update([
            'plan_qty' => $tb1,
            'actual_qty' => $tb2
        ]);
    }
    function profileEdit(){
        // if(request()->user()->hasRole('hr_access')){

        $email=Auth::user()->email;
        $tb_employee=DB::table('tb_emails')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_emails.id_employee')
        ->where('email_address',$email)->get();
        foreach($tb_employee as $dt){
            $id = $dt->id_employee;
        }

        $this->ensureTopEducation((int) $id);

        $leader_name='';
        $tb_employee=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->where('tb_employees.id',$id)->orderby('id','desc')->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name']);
        //return $id;
        foreach($tb_employee as $dt){
            $PIN=$dt->PIN;
            $leader=DB::table('tb_employees')->where('id',$dt->leader_id)->get();
            foreach($leader as $dt2){
                $leader_name=$dt2->employee_name;
            }
            $nama_karyawan=$dt->employee_name;
            $NIK=$dt->NIK;
        }
        $tb_address=DB::table('tb_addresses')->where([['id_employee',$id]])->get();
        $tb_domiciles=DB::table('tb_domiciles')->where([['id_employee',$id]])->get();
        $tb_education=DB::table('tb_educations')->where('id_employee',$id)->orderby('graduate_year','desc')->get();
        $tb_experience=DB::table('tb_experiences')->where('id_employee',$id)->orderby('finish_year','desc')->get();
        $tb_skill=DB::table('tb_skills')->where('id_employee',$id)->get();
        $tb_email=DB::table('tb_emails')->where([['id_employee',$id]])->get();
        $tb_salary=DB::table('tb_salaries')->where([['id_employee',$id],['status','1']])->get();
        $tb_admin=DB::table('tb_admins')->leftjoin('tb_departments','tb_departments.id','=','tb_admins.dept_id')->where('tb_admins.id_employee',$id)->get(['tb_admins.*','tb_departments.dept_code','tb_departments.dept_name']);
        $tb_bagian=DB::table('tb_bagians')->where([['id_employee',$id]])->orderby('implement','desc')->limit(1)->get();
        $tb_detail=DB::table('tb_employee_detail')->where([['id_employee',$id]])->get();
        $tb_address_darurat=DB::table('tb_address_darurat')->where([['id_employee',$id]])->where('status','1')->get();
        $tb_employee_family=DB::table('tb_employee_family')->where([['id_employee',$id]])->where('status','1')->get();
        //return $tb_employee_family;
        $confirmationPeriod = now()->format('Y');
        $tb_uniform = DB::table('tb_uniforms')
            ->where('id_employee', $id)
            ->where('periode', $confirmationPeriod)
            ->first();
        $confirmationIssues = $this->profileConfirmationIssues((int) $id);
        $alreadyConfirmed = DB::table('tb_refresh_data')
            ->where('id_employee', $id)
            ->where('periode', $confirmationPeriod)
            ->exists();
        
        $koneksi='OK';

        $tb_utility=DB::table('tb_utilities')->where('atribut','ess_leave')->get(['status']);
        foreach($tb_utility as $dt){
            $ess_leave=$dt->status;
        }
        $tb_provinsi = DB::table('wilayah_2020')
            ->select('kode', 'nama')
            ->whereRaw('LEN(kode) = 2')
            ->orderBy('nama')
            ->get();
        

    return view('page_ess/profile_update',['tb_employee_family'=>$tb_employee_family,'tb_domiciles'=>$tb_domiciles,'tb_address_darurat'=>$tb_address_darurat,'tb_detail'=>$tb_detail,'tb_bagian'=>$tb_bagian,'PIN'=>$PIN,'koneksi'=>$koneksi,'tb_admin'=>$tb_admin,'tb_salary'=>$tb_salary,'tb_employee'=>$tb_employee,'tb_address'=>$tb_address,'leader_name'=>$leader_name,'tb_education'=>$tb_education,'tb_experience'=>$tb_experience,'tb_skill'=>$tb_skill,'tb_email'=>$tb_email,'tb_uniform'=>$tb_uniform,'id_employee'=>$id,'nama_karyawan'=>$nama_karyawan,'NIK'=>$NIK,'menu'=>'employee','ess_leave'=>$ess_leave,'tb_provinsi'=>$tb_provinsi,'confirmationIssues'=>$confirmationIssues,'confirmationPeriod'=>$confirmationPeriod,'alreadyConfirmed'=>$alreadyConfirmed,'juduls'=>'Profile Update']);
    }

    public function updateProfileEmail(Request $request)
    {
        $user = Auth::user();
        $employeeId = $this->profileEmployeeId();
        $emailRecord = DB::table('tb_emails')
            ->where('id_employee', $employeeId)
            ->where('email_address', $user->email)
            ->first();

        abort_if(!$emailRecord, 404, 'Alamat email akun ini tidak ditemukan pada data karyawan.');

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
                Rule::unique('tb_emails', 'email_address')->ignore($emailRecord->id),
            ],
        ]);

        DB::transaction(function () use ($user, $emailRecord, $validated) {
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'email' => $validated['email'],
                    'updated_at' => now(),
                ]);

            DB::table('tb_emails')
                ->where('id', $emailRecord->id)
                ->update([
                    'email_address' => $validated['email'],
                    'updated_at' => now(),
                ]);
        });

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with(
            'status',
            'Email berhasil diperbarui. Silakan masuk kembali menggunakan email baru.'
        );
    }

    private function profileConfirmationIssues(int $employeeId): array
    {
        $requiredTables = [
            'tb_addresses' => 'Alamat',
            'tb_domiciles' => 'Domisili',
            'tb_address_darurat' => 'Kontak darurat',
            'tb_educations' => 'Pendidikan',
        ];
        $issues = [];

        foreach ($requiredTables as $table => $label) {
            if (!DB::table($table)->where('id_employee', $employeeId)->exists()) {
                $issues[] = 'Lengkapi data '.$label;
            }
        }

        $uniform = DB::table('tb_uniforms')
            ->where('id_employee', $employeeId)
            ->where('periode', now()->format('Y'))
            ->first();
        if (!$uniform || !$uniform->ukuran_baju || !$uniform->ukuran_celana || !$uniform->ukuran_sepatu) {
            $issues[] = 'Lengkapi ukuran seragam (baju, celana, dan sepatu)';
        }

        $detail = DB::table('tb_employee_detail')
            ->where('id_employee', $employeeId)
            ->first();
        if (!$detail) {
            $issues[] = 'Lengkapi data Detail Karyawan';

            return $issues;
        }

        foreach ([
            'tanggal_lahir' => 'Tanggal lahir',
            'kode_status' => 'Status perkawinan',
            'nomor_telepon' => 'Nomor telepon',
        ] as $field => $label) {
            if ($detail->{$field} === null || trim((string) $detail->{$field}) === '') {
                $issues[] = 'Isi '.$label.' pada Detail Karyawan';
            }
        }

        return $issues;
    }

    public function saveProfileUniform(Request $request)
    {
        $validated = $request->validate([
            'ukuran_baju' => ['required', 'in:XS,S,M,L,XL,XXL,XXXL'],
            'ukuran_celana' => ['required', 'in:28,30,32,34,36,38,40,42,44'],
            'ukuran_sepatu' => ['required', 'in:36,37,38,39,40,41,42,43,44,45,46'],
        ]);
        $employeeId = $this->profileEmployeeId();
        $period = now()->format('Y');
        $updatedBy = mb_substr((string) Auth::user()->name, 0, 50);

        DB::transaction(function () use ($validated, $employeeId, $period, $updatedBy) {
            $uniform = DB::table('tb_uniforms')
                ->where('id_employee', $employeeId)
                ->where('periode', $period)
                ->first();
            $values = [
                'ukuran_baju' => $validated['ukuran_baju'],
                'ukuran_celana' => $validated['ukuran_celana'],
                'ukuran_sepatu' => $validated['ukuran_sepatu'],
                'updated_at' => now(),
                'updated_by' => $updatedBy,
            ];

            if ($uniform) {
                DB::table('tb_uniforms')
                    ->where('id', $uniform->id)
                    ->update($values);
            } else {
                DB::table('tb_uniforms')->insert(array_merge($values, [
                    'periode' => $period,
                    'id_employee' => $employeeId,
                    'created_at' => now(),
                ]));
            }
        });

        return redirect('/ProfileEdit')->with('success', 'Ukuran seragam berhasil disimpan.');
    }

    public function confirmProfileData()
    {
        $employeeId = $this->profileEmployeeId();
        $period = now()->format('Y');
        $userName = mb_substr((string) Auth::user()->name, 0, 50);

        $issues = DB::transaction(function () use ($employeeId, $period, $userName) {
            DB::table('tb_employees')->where('id', $employeeId)->lockForUpdate()->first();

            $issues = $this->profileConfirmationIssues($employeeId);
            if ($issues) {
                return $issues;
            }

            $confirmation = DB::table('tb_refresh_data')
                ->where('id_employee', $employeeId)
                ->where('periode', $period)
                ->lockForUpdate()
                ->first();

            if ($confirmation) {
                DB::table('tb_refresh_data')
                    ->where('id_employee', $employeeId)
                    ->where('periode', $period)
                    ->update(['updated_at' => now()]);
            } else {
                DB::table('tb_refresh_data')->insert([
                    'periode' => $period,
                    'id_employee' => $employeeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'updated_by' => $userName,
                ]);
            }

            return [];
        });

        if ($issues) {
            return redirect()->back()
                ->with('confirmationError', 'Konfirmasi belum dapat disimpan. Lengkapi data berikut:')
                ->with('confirmationIssues', $issues);
        }

        return redirect()->back()->with('success', 'Data profil berhasil dikonfirmasi untuk periode '.$period.'.');
    }

    public function saveProfileAddress(Request $request)
    {
        $request->validate([
            'provinsi' => ['required', 'string'],
            'kabupaten' => ['required', 'string'],
            'kecamatan' => ['required', 'string'],
            'kelurahan' => ['required', 'string'],
            'detail' => ['required', 'string', 'max:1000'],
        ]);
        $wilayah = $this->resolveProfileWilayah($request, [
            'province' => 'provinsi',
            'regency' => 'kabupaten',
            'district' => 'kecamatan',
            'village' => 'kelurahan',
        ]);
        $employeeId = $this->profileEmployeeId();

        DB::transaction(function () use ($request, $wilayah, $employeeId) {
            $this->deactivateProfileRows('tb_addresses', $employeeId, 'Menonaktifkan alamat aktif');
            $addressId = DB::table('tb_addresses')->insertGetId([
                'id_employee' => $employeeId,
                'provinsi' => $wilayah['province'],
                'kabupaten' => $wilayah['regency'],
                'kecamatan' => $wilayah['district'],
                'kelurahan' => $wilayah['village'],
                'detail' => $request->input('detail'),
                'map_address' => '',
                'status' => '1',
            ]);
            $this->auditProfileChange(
                'created',
                'tb_addresses',
                (int) $addressId,
                null,
                $this->profileAuditSnapshot('tb_addresses', $addressId),
                'Menambahkan alamat karyawan'
            );
        });

        return redirect('/ProfileEdit')->with('success', 'Data Address berhasil disimpan.');
    }

    public function saveProfileDomicile(Request $request)
    {
        $request->validate([
            'provinsi_domicile' => ['required', 'string'],
            'kabupaten_domicile' => ['required', 'string'],
            'kecamatan_domicile' => ['required', 'string'],
            'kelurahan_domicile' => ['required', 'string'],
            'detail_domicile' => ['required', 'string', 'max:1000'],
        ]);
        $wilayah = $this->resolveProfileWilayah($request, [
            'province' => 'provinsi_domicile',
            'regency' => 'kabupaten_domicile',
            'district' => 'kecamatan_domicile',
            'village' => 'kelurahan_domicile',
        ]);
        $employeeId = $this->profileEmployeeId();

        DB::transaction(function () use ($request, $wilayah, $employeeId) {
            $this->deactivateProfileRows('tb_domiciles', $employeeId, 'Menonaktifkan domisili aktif');
            $domicileId = DB::table('tb_domiciles')->insertGetId([
                'id_employee' => $employeeId,
                'provinsi' => $wilayah['province'],
                'kabupaten' => $wilayah['regency'],
                'kecamatan' => $wilayah['district'],
                'kelurahan' => $wilayah['village'],
                'detail' => $request->input('detail_domicile'),
                'map_address' => '',
                'status' => '1',
            ]);
            $this->auditProfileChange(
                'created',
                'tb_domiciles',
                (int) $domicileId,
                null,
                $this->profileAuditSnapshot('tb_domiciles', $domicileId),
                'Menambahkan domisili karyawan'
            );
        });

        return redirect('/ProfileEdit')->with('success', 'Data domisili berhasil disimpan.');
    }

    public function copyAddressToDomicile()
    {
        $employeeId = $this->profileEmployeeId();
        $copied = DB::transaction(function () use ($employeeId) {
            DB::table('tb_employees')->where('id', $employeeId)->lockForUpdate()->first();

            if (DB::table('tb_domiciles')->where('id_employee', $employeeId)->exists()) {
                return false;
            }

            $address = DB::table('tb_addresses')
                ->where('id_employee', $employeeId)
                ->where('status', '1')
                ->orderByDesc('id')
                ->first();

            if (!$address) {
                $address = DB::table('tb_addresses')
                    ->where('id_employee', $employeeId)
                    ->orderByDesc('id')
                    ->first();
            }

            if (!$address) {
                return false;
            }

            $domicileId = DB::table('tb_domiciles')->insertGetId([
                'id_employee' => $employeeId,
                'provinsi' => $address->provinsi,
                'kabupaten' => $address->kabupaten,
                'kecamatan' => $address->kecamatan,
                'kelurahan' => $address->kelurahan,
                'detail' => $address->detail,
                'map_address' => $address->map_address ?? '',
                'status' => '1',
            ]);

            $this->auditProfileChange(
                'created',
                'tb_domiciles',
                (int) $domicileId,
                null,
                $this->profileAuditSnapshot('tb_domiciles', $domicileId),
                'Menyalin alamat menjadi domisili'
            );

            return true;
        });

        if (!$copied) {
            return redirect('/ProfileEdit')->with(
                'error',
                'Alamat tidak dapat disalin. Data alamat mungkin belum tersedia atau domisili sudah tercatat.'
            );
        }

        return redirect('/ProfileEdit')->with('success', 'Alamat berhasil disalin menjadi domisili.');
    }

    public function saveProfileEmergencyContact(Request $request)
    {
        $request->validate([
            'nama_keluarga' => ['required', 'string', 'max:255'],
            'hubungan' => ['required', 'string', 'max:100'],
            'nomor_kontak' => ['required', 'string', 'max:50'],
            'provinsi_kontak' => ['required', 'string'],
            'kabupaten_kontak' => ['required', 'string'],
            'kecamatan_kontak' => ['required', 'string'],
            'kelurahan_kontak' => ['required', 'string'],
            'detail_kontak' => ['required', 'string', 'max:1000'],
        ]);
        $wilayah = $this->resolveProfileWilayah($request, [
            'province' => 'provinsi_kontak',
            'regency' => 'kabupaten_kontak',
            'district' => 'kecamatan_kontak',
            'village' => 'kelurahan_kontak',
        ]);
        $employeeId = $this->profileEmployeeId();

        DB::transaction(function () use ($request, $wilayah, $employeeId) {
            $contactId = DB::table('tb_address_darurat')->insertGetId([
                'id_employee' => $employeeId,
                'nama_keluarga' => $request->input('nama_keluarga'),
                'hubungan' => $request->input('hubungan'),
                'nomor_kontak' => $request->input('nomor_kontak'),
                'provinsi_kontak' => $wilayah['province'],
                'kabupaten_kontak' => $wilayah['regency'],
                'kecamatan_kontak' => $wilayah['district'],
                'kelurahan_kontak' => $wilayah['village'],
                'detail_kontak' => $request->input('detail_kontak'),
            ]);
            $this->auditProfileChange(
                'created',
                'tb_address_darurat',
                (int) $contactId,
                null,
                $this->profileAuditSnapshot('tb_address_darurat', $contactId),
                'Menambahkan kontak darurat'
            );
        });

        return redirect('/ProfileEdit')->with('success', 'Data kontak darurat berhasil disimpan.');
    }

    private function profileEmployeeId(): int
    {
        $employeeId = DB::table('tb_emails')
            ->where('email_address', Auth::user()->email)
            ->value('id_employee');

        abort_if(!$employeeId, 404, 'Data karyawan untuk akun ini tidak ditemukan.');

        return (int) $employeeId;
    }

    private function resolveProfileWilayah(Request $request, array $fields): array
    {
        $levels = [
            'province' => 2,
            'regency' => 5,
            'district' => 8,
            'village' => 13,
        ];
        $names = [];
        $parentCode = null;

        foreach ($levels as $level => $length) {
            $field = $fields[$level];
            $code = $request->input($field);
            $query = DB::table('wilayah_2020')
                ->select('kode', 'nama')
                ->where('kode', $code)
                ->whereRaw('LEN(kode) = ?', [$length]);

            if ($parentCode !== null) {
                $query->whereRaw('LEFT(kode, ?) = ?', [strlen($parentCode), $parentCode])
                    ->whereRaw('SUBSTRING(kode, ?, 1) = ?', [strlen($parentCode) + 1, '.']);
            }

            $wilayah = $query->first();
            if (!$wilayah) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    $field => 'Pilihan wilayah tidak valid atau tidak sesuai urutannya.',
                ]);
            }

            $names[$level] = $wilayah->nama;
            $parentCode = $wilayah->kode;
        }

        return $names;
    }

    private function profileAuditSnapshot(string $table, int|string $id): ?array
    {
        $row = DB::table($table)->where('id', $id)->first();

        return $row ? (array) $row : null;
    }

    private function ensureTopEducation(int $employeeId): void
    {
        DB::transaction(function () use ($employeeId) {
            DB::table('tb_employees')->where('id', $employeeId)->lockForUpdate()->first();

            $rows = DB::table('tb_educations')
                ->where('id_employee', $employeeId)
                ->get()
                ->keyBy('id');
            if ($rows->isEmpty()) {
                return;
            }

            $topId = DB::table('tb_educations')
                ->where('id_employee', $employeeId)
                ->orderByDesc('graduate_year')
                ->orderByDesc('id')
                ->value('id');
            $currentTopRows = $rows->filter(fn ($row) => (int) $row->is_top === 1);
            if ($currentTopRows->count() === 1) {
                return;
            }

            DB::table('tb_educations')
                ->where('id_employee', $employeeId)
                ->update(['is_top' => '0']);
            DB::table('tb_educations')
                ->where('id', $topId)
                ->where('id_employee', $employeeId)
                ->update(['is_top' => '1']);

            $updatedRows = DB::table('tb_educations')
                ->where('id_employee', $employeeId)
                ->get()
                ->keyBy('id');
            foreach ($updatedRows as $id => $updatedRow) {
                $oldRow = $rows->get($id);
                if ($oldRow && (int) $oldRow->is_top !== (int) $updatedRow->is_top) {
                    $this->auditProfileChange(
                        'updated',
                        'tb_educations',
                        (int) $id,
                        (array) $oldRow,
                        (array) $updatedRow,
                        'Menyesuaikan pendidikan tertinggi berdasarkan tahun lulus'
                    );
                }
            }
        });
    }

    private function auditProfileChange(
        string $event,
        string $table,
        int $id,
        ?array $oldValues,
        ?array $newValues,
        string $description
    ): void {
        app(AuditLogger::class)->record(
            $event,
            $table,
            $id,
            $oldValues,
            $newValues,
            $description
        );
    }

    private function deactivateProfileRows(string $table, int $employeeId, string $description): void
    {
        $activeRows = DB::table($table)
            ->where('id_employee', $employeeId)
            ->where(function ($query) {
                $query->where('status', '<>', '0')->orWhereNull('status');
            })
            ->get();

        DB::table($table)
            ->where('id_employee', $employeeId)
            ->update(['status' => '0']);

        foreach ($activeRows as $row) {
            $oldValues = (array) $row;
            $newValues = array_replace($oldValues, ['status' => '0']);
            $this->auditProfileChange(
                'updated',
                $table,
                (int) $row->id,
                $oldValues,
                $newValues,
                $description
            );
        }
    }

    function selectDelete($id,$tabel){
        $auditTables = [
            'Address' => 'tb_addresses',
            'Domicile' => 'tb_domiciles',
            'Education' => 'tb_educations',
            'Kontak' => 'tb_address_darurat',
            'Family' => 'tb_employee_family',
        ];
        if (isset($auditTables[$tabel])) {
            $table = $auditTables[$tabel];
            $delete = DB::transaction(function () use ($table, $id) {
                $oldValues = $this->profileAuditSnapshot($table, (int) $id);
                abort_unless(
                    $oldValues && (int) ($oldValues['id_employee'] ?? 0) === $this->profileEmployeeId(),
                    404
                );
                $deleted = DB::table($table)->where('id',$id)->delete();
                if ($deleted && $oldValues) {
                    $this->auditProfileChange(
                        'deleted',
                        $table,
                        (int) $id,
                        $oldValues,
                        null,
                        'Menghapus data '.$table
                    );
                }

                return $deleted;
            });
        }elseif($tabel=='Experience'){
            $delete=DB::table('tb_experiences')->where('id',$id)->delete();
        }elseif($tabel=='Skill'){
            $delete=DB::table('tb_skills')->where('id',$id)->delete();
        } else {
            abort(404);
        }

        if($delete)
        $teks="Data Tabel ".$tabel." berhasil dihapus";
        return redirect()->back()->with(['success' => $teks]);    
    }
    function saveFamily(Request $data){
        //return "Masuk";
        $this->validate($data,['nama_keluargas'=>'required','hubungans'=>'required','tanggal_lahir_keluarga'=>'required']);
        $employeeId = $this->profileEmployeeId();
        $familyId=DB::transaction(function () use ($data, $employeeId) {
            $familyId = DB::table('tb_employee_family')->insertGetId([
                'id_employee'=>$employeeId,
                'nama_keluarga'=>$data->nama_keluargas,
                'hubungan'=>$data->hubungans,
                'tanggal_lahir'=>$data->tanggal_lahir_keluarga,
                'status'=>'1'
            ]);
            $this->auditProfileChange(
                'created',
                'tb_employee_family',
                (int) $familyId,
                null,
                $this->profileAuditSnapshot('tb_employee_family', $familyId),
                'Menambahkan anggota keluarga'
            );

            return $familyId;
        });
        $simpan = $familyId;
        if($simpan)
        $teks='Data Family berhasil disimpan';
        //return $simpan;
        return redirect()->back()->with(['success' => $teks]);    
    }
    function saveEducation(Request $data){
        $this->validate($data,['level_education'=>'required']);
        $employeeId = $this->profileEmployeeId();
        $educationId=DB::transaction(function () use ($data, $employeeId) {
            DB::table('tb_employees')->where('id', $employeeId)->lockForUpdate()->first();
            $oldRows = DB::table('tb_educations')
                ->where('id_employee', $employeeId)
                ->get()
                ->keyBy('id');
            $educationId = DB::table('tb_educations')->insertGetId([
                'id_employee'=>$employeeId,
                'level_education'=>$data->level_education,
                'institute'=>$data->institute,
                'prodi'=>$data->prodi,
                'year'=>$data->year,
                'graduate_year'=>$data->graduate_year,
                'remark'=>$data->remark,
                'is_top'=>'0',
            ]);
            $topEducation = DB::table('tb_educations')
                ->where('id_employee', $employeeId)
                ->orderByDesc('graduate_year')
                ->orderByDesc('id')
                ->first();

            DB::table('tb_educations')
                ->where('id_employee', $employeeId)
                ->update(['is_top' => '0']);
            if ($topEducation) {
                DB::table('tb_educations')->where('id', $topEducation->id)->update(['is_top' => '1']);
            }

            $newRows = DB::table('tb_educations')
                ->where('id_employee', $employeeId)
                ->get()
                ->keyBy('id');
            foreach ($newRows as $id => $newRow) {
                if ((int) $id === (int) $educationId) {
                    $this->auditProfileChange(
                        'created',
                        'tb_educations',
                        (int) $educationId,
                        null,
                        (array) $newRow,
                        'Menambahkan data pendidikan'
                    );
                    continue;
                }

                $oldRow = $oldRows->get($id);
                if ($oldRow && (string) $oldRow->is_top !== (string) $newRow->is_top) {
                    $this->auditProfileChange(
                        'updated',
                        'tb_educations',
                        (int) $id,
                        (array) $oldRow,
                        (array) $newRow,
                        'Memperbarui pendidikan tertinggi'
                    );
                }
            }

            return $educationId;
        });
        $simpan = $educationId;
        if($simpan)
        $teks='Data Education berhasil disimpan';
        //return $simpan;
        return redirect()->back()->with(['success' => $teks]);    
    }
    public function setTopEducation($id)
    {
        $employeeId = $this->profileEmployeeId();
        $changed = DB::transaction(function () use ($id, $employeeId) {
            DB::table('tb_employees')->where('id', $employeeId)->lockForUpdate()->first();

            $education = DB::table('tb_educations')
                ->where('id', $id)
                ->where('id_employee', $employeeId)
                ->first();
            abort_unless($education, 404);

            $oldRows = DB::table('tb_educations')
                ->where('id_employee', $employeeId)
                ->get()
                ->keyBy('id');

            DB::table('tb_educations')
                ->where('id_employee', $employeeId)
                ->update(['is_top' => '0']);
            DB::table('tb_educations')
                ->where('id', $id)
                ->where('id_employee', $employeeId)
                ->update(['is_top' => '1']);

            $newRows = DB::table('tb_educations')
                ->where('id_employee', $employeeId)
                ->get()
                ->keyBy('id');
            $changed = false;
            foreach ($newRows as $rowId => $newRow) {
                $oldRow = $oldRows->get($rowId);
                if ($oldRow && (string) $oldRow->is_top !== (string) $newRow->is_top) {
                    $changed = true;
                    $this->auditProfileChange(
                        'updated',
                        'tb_educations',
                        (int) $rowId,
                        (array) $oldRow,
                        (array) $newRow,
                        'Memperbarui pendidikan tertinggi'
                    );
                }
            }

            return $changed;
        });

        $message = $changed
            ? 'Pendidikan tertinggi berhasil diperbarui'
            : 'Pendidikan ini sudah ditandai sebagai pendidikan tertinggi';

        return redirect()->back()->with(['success' => $message]);
    }
    function saveExperience(Request $data){
        $this->validate($data,['factory'=>'required','section'=>'required','year'=>'required','finish_year'=>'required']);
        $simpan=DB::table('tb_experiences')->insert([
            'id_employee'=>$data->id_employee,
            'factory'=>$data->factory,
            'section'=>$data->section,
            'year'=>$data->year,
            'finish_year'=>$data->finish_year,
            'remark'=>$data->remark
        ]);
        if($simpan)
        $teks='Data Experience berhasil disimpan';
        //return $simpan;
        return redirect()->back()->with(['success' => $teks]);    
    }
    function saveSkill(Request $data){
        $this->validate($data,['skill_name'=>'required']);
        $simpan=DB::table('tb_skills')->insert([
            'id_employee'=>$data->id_employee,
            'skill_type'=>$data->skill_type,
            'skill_name'=>$data->skill_name
        ]);
        if($simpan)
        $teks='Data Skill berhasil disimpan';
        //return $simpan;
        return redirect()->back()->with(['success' => $teks]);    
    }
    function saveDetail(Request $data){
        //$this->validate($data,['tanggal_lahir'=>'required']);
        $employeeId = $this->profileEmployeeId();
        $simpan = DB::transaction(function () use ($data, $employeeId) {
            $oldValues = DB::table('tb_employee_detail')->where('id_employee',$employeeId)->first();
            if(!$oldValues){
                $writeSucceeded = DB::table('tb_employee_detail')->insert([
                    'id_employee'=>$employeeId,
                    'tempat_lahir'=>$data->tempat_lahir,
                    'tanggal_lahir'=>$data->tanggal_lahir,
                    'agama'=>$data->agama,
                    'golongan_darah'=>$data->golongan_darah,
                    'ibu_kandung'=>$data->ibu_kandung,
                    'nomor_ktp'=>$data->nomor_ktp,
                    'nomor_kk'=>$data->nomor_kk,
                    'nomor_npwp'=>$data->nomor_npwp,
                    'kode_status'=>$data->kode_status,
                    'nomor_bpjs_kes'=>$data->nomor_bpjs_kes,
                    'nomor_bpjs_ket'=>$data->nomor_bpjs_ket,
                    'nomor_rekening'=>$data->nomor_rekening,
                    'nama_bank'=>$data->nama_bank,
                    'nomor_telepon'=>$data->nomor_telepon,
                ]);
                $event = 'created';
            }else{
                $writeSucceeded = DB::table('tb_employee_detail')->where('id_employee',$employeeId)->update([
                    'tempat_lahir'=>$data->tempat_lahir,
                    'tanggal_lahir'=>$data->tanggal_lahir,
                    'agama'=>$data->agama,
                    'golongan_darah'=>$data->golongan_darah,
                    'ibu_kandung'=>$data->ibu_kandung,
                    'nomor_ktp'=>$data->nomor_ktp,
                    'nomor_kk'=>$data->nomor_kk,
                    'nomor_npwp'=>$data->nomor_npwp,
                    'nomor_bpjs_kes'=>$data->nomor_bpjs_kes,
                    'nomor_bpjs_ket'=>$data->nomor_bpjs_ket,
                    'kode_status'=>$data->kode_status,
                    'nomor_rekening'=>$data->nomor_rekening,
                    'nama_bank'=>$data->nama_bank,
                    'nomor_telepon'=>$data->nomor_telepon,
                ]);
                $event = 'updated';
            }

            $newValues = DB::table('tb_employee_detail')->where('id_employee',$employeeId)->first();
            if ($writeSucceeded && $newValues && (!$oldValues || (array) $oldValues !== (array) $newValues)) {
                $this->auditProfileChange(
                    $event,
                    'tb_employee_detail',
                    (int) $newValues->id,
                    $oldValues ? (array) $oldValues : null,
                    $newValues ? (array) $newValues : null,
                    $event === 'created' ? 'Menambahkan detail karyawan' : 'Memperbarui detail karyawan'
                );
            }

            return $writeSucceeded;
        });
        //return "Tiga";
        if($simpan)
        $teks='General Data berhasil disimpan';
        else $teks='Anda tidak merubah data apapun';
        //return $simpan;
        return redirect()->back()->with(['success' => $teks]);    
    }
}
#endregion
