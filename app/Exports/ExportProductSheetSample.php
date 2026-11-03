<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportProductSheetSample implements WithHeadings, WithStyles, FromArray
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return
            [
                'Product Name',
                'Type',
                'Bucket',
                'Capacity',
                'Status',
                'Created At'
            ];
    }

    public function array(): array
    {
        return [
            [

                'Two-Wheeler Loan',
                '',
                'Test Bucket',
                '',
                '',
                '2025-07-08 11:39:47'

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
