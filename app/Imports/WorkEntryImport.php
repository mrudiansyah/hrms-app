<?php

namespace App\Imports;

use App\Models\tb_work_entry;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;


class WorkEntryImport implements ToModel,WithStartRow
{

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        return new tb_work_entry([
            'id' => '',
            'id_employee' => $row[1],
            'periode' => $row[5], 
            'hari_kerja' => $row[6], 
            'aktual_kerja' => $row[7], 
            'cuti' => $row[8], 
            'aktual_shift' => $row[9], 
            'ijin'=>$row[10],
            'sakit'=>$row[11],
            'magkir'=>$row[12],
            'absen'=>$row[13],
            'import_status'=>'1'
        ]);
    }

}
