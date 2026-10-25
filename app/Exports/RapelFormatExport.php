<?php

namespace App\Exports;

use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RapelFormatExport implements FromCollection, WithHeadings
{
    public function collection(): Enumerable
    {
        return collect([
            [
                'nik' => '12345678',
                'periode' => date('Y-m'),
                'amount' => '500000',
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'NIK',
            'Periode',
            'Rapel Amount',
        ];
    }
}
