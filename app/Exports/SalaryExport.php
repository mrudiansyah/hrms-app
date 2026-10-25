<?php

namespace App\Exports;

use App\Models\tb_salary_excel;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;

class SalaryExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(): Enumerable
    {
        return tb_salary_excel::all();
    }
}
