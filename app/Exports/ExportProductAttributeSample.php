<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\FromArray;

class ExportProductAttributeSample implements WithStyles, WithHeadings, FromArray
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return
            [
                'Product Name',
                'Product Attribute Name',
            ];
    }

    public function array(): array
    {
        return [
            [
                'Dummy Product Name',
                'Dummy Product Attribute Name',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
{
    return [
        1 => [
            'font' => [
                'bold' => true,
              
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'ED7D31',
                ],
            ],
        ],
    ];
}
}
