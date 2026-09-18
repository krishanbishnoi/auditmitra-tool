<?php

namespace App\Exports;

use App\QmSheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportQmCheckSheet implements WithStyles, WithHeadings, FromArray
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $data = QmSheet::query()
            ->orderBy('id')
            ->get();

        $final = [];

        foreach ($data as $qmSheet) {
            $final[] = [
                'ID' => $qmSheet->id,
                'Name' => $qmSheet->name,
                'Code' => $qmSheet->code,
                'Details' => $qmSheet->details,
                'Type' => $qmSheet->type,
                'Lob' => $qmSheet->lob,
                'Client ID' => $qmSheet->client_id,
                'Created At' => $qmSheet->created_at->format('Y-m-d H:i:s'),
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
                'Code',
                'Details',
                'Type',
                'Lob',
                'Client Id',
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
