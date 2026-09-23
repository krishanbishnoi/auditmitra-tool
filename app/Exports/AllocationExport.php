<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Model\Allocation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AllocationExport implements FromArray, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $data = Allocation::with(['user', 'sheet'])->get();
        $final = [];
        foreach ($data as $item) {
            $final[] = [
                'sheet_name' => $item->sheet->name,
                'user_name' => $item->user->name,
            ];
        }
        return $final;
    }
    public function headings(): array
    {
        return [
            'Month',
            'Audit Date',
            'Lob',
            'Agency Location',
            'State',
            'Product',
            'Sheet Type',
            'Agency Name',
            'Agency Code',
            'Collection Manager',
            'Auditor Name',
            'Visited Date & Time',
            'Status'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return
            [
                1 => ['font' => ['bold' => true]],
            ];
    }
}
