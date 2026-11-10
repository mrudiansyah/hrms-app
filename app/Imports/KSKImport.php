<?php

namespace App\Imports;

use App\Models\tb_ksk_detail_performance;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class KSKImport implements ToModel, WithStartRow, WithChunkReading, WithBatchInserts
{
    public function __construct(private string $periode)
    {
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row): Model
    {
        return new tb_ksk_detail_performance([
            'periode' => $this->periode,
            'id_ksk_detail' => $row[2],
            'warning_letter'=> $row[6],
            'sick' => $row[7], 
            'permit' => $row[8], 
            'alpa' => $row[9], 
            'late' => $row[10], 
            'minutes' => $row[11], 
            'performance' => $row[12], 
            'skill_matriks' => '0', 
        ]);
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
