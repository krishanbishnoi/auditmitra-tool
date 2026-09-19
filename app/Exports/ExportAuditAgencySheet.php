<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
Use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportAuditAgencySheet implements WithHeadings, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return
        [
                   'Name',
                   'Email',
                   'Phone',
                   'Audit Agency Name'
        ];
    }

    public function styles(Worksheet $sheet)
    {
 return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
