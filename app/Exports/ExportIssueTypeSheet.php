<?php

namespace App\Exports;

use App\IssueType;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;



class ExportIssueTypeSheet implements FromArray, WithHeadings, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $data = IssueType::query()
            ->orderBy('id')
            ->get();

        $final = [];

        foreach ($data as $issueType) {
            $final[] = [
                'Id' => $issueType->id,
                'Name' => $issueType->name,
                'Help Topic Id' => $issueType->help_topic_id,
                'Created At' => $issueType->created_at
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
                'Help Topic Id',
                'Created At'
            ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
