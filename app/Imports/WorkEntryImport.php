<?php

namespace App\Imports;

use Illuminate\Database\Eloquent\Model;
use App\Models\tb_work_entry;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;


class WorkEntryImport implements ToModel, WithStartRow
{

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row): Model
    {
        return new tb_work_entry([
            'id' => '',
            'id_employee' => $row[1],
            'tipe_kontrak' => $row[2],
            'periode' => $row[6],
            'hari_kerja' => '0',
            'aktual_kerja' => (int) (float) ($row[7] ?? 0),
            'ot_weekend' => '0',
            'cuti' => '0',
            'aktual_shift' => (int) (float) ($row[8] ?? 0),
            'ijin' => '0',
            'sakit' => '0',
            'mangkir' => '0',
            'absen' => (int) (float) ($row[9] ?? 0),
            'import_status' => '1'
        ]);
    }

}
