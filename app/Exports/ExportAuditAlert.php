<?php

namespace App\Exports;

use App\AuditAlertBox;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportAuditAlert implements FromArray, WithStyles, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function headings(): array
    {
        return
            [
                'Name',
                'Details',
                'Created At'
            ];
    }

    public function array(): array
    {
        $data = AuditAlertBox::query()
            ->orderBy('id')
            ->get();

        $final = [];

        foreach ($data as $auditAlertBox) {
            $final[] = [
                'Name' => $auditAlertBox->name,
                'Details' => $auditAlertBox->details,
                'Created At' => $auditAlertBox->created_at
            ];
        }
        return $final;
    }

    public function styles(Worksheet $sheet)
    {
        return
            [
                1 => ['font' => ['bold' => true]],
            ];
    }
}
