<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TrainingQuestionTemplateExport implements FromCollection, WithHeadings
{
    private $idTest;

    public function __construct($idTest)
    {
        $this->idTest = $idTest;
    }

    public function collection()
    {
        return collect([
            [
                $this->idTest,
                1,
                'Contoh pertanyaan',
                'Pilihan A',
                'Pilihan B',
                'Pilihan C',
                'Pilihan D',
                'A',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'id_test',
            'index_question',
            'question',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'answer_code',
        ];
    }
}
