<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportQmCheckSheetSample implements WithHeadings, WithStyles, FromArray
{
    /**
    * @return \Illuminate\Support\Collection
    */
     public function headings(): array
    {
        return
            [
                'Id',
                'Name',
                'Code',
                'Details',
                'Type',
                'Lob',
                'Client Id',
                'Created At'
            ];
    }

public function array(): array
{
    return[
        [
              '1',
              'Test',
              '#0yhq1',
              'this is test Agency',
              'branch',
              'collection',
              '76',
              '2024-07-24 10:05:30'

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
