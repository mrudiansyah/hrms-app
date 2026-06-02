<?php

namespace App\Imports;

use App\Models\tb_salary_excel;
use Maatwebsite\Excel\Concerns\ToModel;

class SalaryImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new tb_salary_excel([
            'id' => $row[0],
            'Nama' => $row[1],
            'NIK' => $row[2], 
            'Jabatan' => $row[3], 
            'Bagian' => $row[4], 
            'Upah_Pokok' => $row[5], 
            'Tunjangan_Jabatan' => $row[6], 
            'Tunjangan_Skill' => $row[7], 
            'Tunjangan_Prestasi' => $row[8], 
            'Standby_OnCall' => $row[9], 
            'Rapel' => $row[10], 
            'PPH21' => $row[11], 
            'PPH21_Lebih_Bayar' => $row[12], 
            'Ketidakhadiran' => $row[13], 
            'Ketidakhadiran_Ammount' => $row[14], 
            'Serikat' => $row[15], 
            'Lain_Lain' => $row[16], 
            'PPH21_Kurang_Bayar' => $row[17], 
            'Koperasi' => $row[18], 
            'BPJS_Kesehatan' => $row[19], 
            'BPJS_JHT' => $row[20], 
            'BPJS_Pensiun' => $row[21], 
            'PPH21_Insentif' => $row[22],
            'Periode'=>$row[23],
            'Doc_Date'=>$row[24]
        ]);
    }}
