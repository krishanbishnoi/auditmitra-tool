<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class ExportClientSample implements FromArray, WithHeadings, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return
            [
                'Client ID',
                'Client Code',
                'Client Name',
                'Client Email',
                'Created At'
            ];
    }

    public function array(): array
    {
        return [[
            '1',
            'xJAjSg',
            'Tata Capital',
            'tata@gamil.com',
            '2025-12-15 13:40:41'
        ]];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => 'true']],];
    }
}
