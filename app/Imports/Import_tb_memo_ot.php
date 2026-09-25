<?php

namespace App\Imports;

use App\Models\import_tb_memo_ot_model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;


class Import_tb_memo_ot implements ToModel,WithStartRow
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
        return new import_tb_memo_ot_model([
            'id_memo' => $row[1],
            'part_no' => $row[2],
            'part_name'=> $row[3],
            'lines' => $row[4], 
            'jph_gsph' => $row[5], 
            'process' => $row[6], 
            'date_ot' => $row[7], 
            'start_ot' => $row[8], 
            'finish_ot' => $row[9], 
            'plan_qty' => $row[10], 
            'reason_ot' => $row[11],
            'remark'=>$row[12] 
        ]);
    }
}
