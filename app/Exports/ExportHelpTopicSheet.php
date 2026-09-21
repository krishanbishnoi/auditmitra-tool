<?php

namespace App\Exports;

use App\HelpTopic;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportHelpTopicSheet implements FromArray, WithHeadings, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function array(): array
    {
        $data = HelpTopic::query()
            ->orderBy('id')
            ->get();

        $final = [];

        foreach ($data as $helpTopic) {
            $final[] = [
                'Id' => $helpTopic->id,
                'Name' => $helpTopic->name,
                'created At' => $helpTopic->created_at
            ];
        }
        return $final;
    }

    public function headings(): array
    {
        return
            [
                'Id',
                'Name',
                'Created At'
            ];
    }

    public function styles(Worksheet $sheet)
    {
        return
            [
                1 => ['font' => ['bold' => true]],
            ];
    }
}
