<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Worksheet\WorkSheet;

class ExportRolesSample implements WithHeadings, WithStyles, FromArray
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return
            [
                'Roles',
                'Created At'
            ];
    }

    public function array(): array
    {
        return 
        [
           [
            'Admin',
           '2019-12-03 16:18:25'
           ],
        ];
    }

    public function styles(WorkSheet $sheet)
    {
        return [1 => ['font' => ['bold' => 'true']],];
    }
}
