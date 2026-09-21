<?php

namespace App\Exports;

use App\PredefinedQuestion;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportPredefinedQuesSheet implements WithStyles, FromArray, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return [
            'Id',
            'Role',
            'Question',
            'Answer',
            'Created At'
        ];
    }

    public function array(): array
    {

        $data = PredefinedQuestion::query()
            ->orderBy('id')
            ->get();


        $final = [];

        foreach ($data as $preQuestions) {
            $final[] = [
                'Id' => $preQuestions->id,
                'Role' => $preQuestions->role,
                'Question' => $preQuestions->question,
                'Answer' => $preQuestions->answer,
                'Created At' => $preQuestions->created_at
            ];
        }
        return $final;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],

        ];
    }
}
