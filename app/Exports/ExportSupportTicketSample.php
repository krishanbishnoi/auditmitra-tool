<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportSupportTicketSample implements FromArray, WithStyles, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
public function headings(): array
    {
        return
            [
                'Help Topic',
                'Issue Type',
                'Subject',
                'Priority',
                'Description',
                'Status',
                'Closure Feedback',
                'Support Id',
                'Created AT'
            ];
    }

public function array(): array
{
    return 
    [
        [
            'how to audit',
            'audit',
            'OTP based issue',
            'Medium',
            'Not receiving OTP',
            'open',
            'not done',
            '24',
            '2025-05-27 20:38:11'
        ],
    ];
}

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
