<?php

namespace App\Exports;

use App\Models\tb_salary_excel;
use Maatwebsite\Excel\Concerns\FromCollection;

class SalaryExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return tb_salary_excel::all();
    }
}
