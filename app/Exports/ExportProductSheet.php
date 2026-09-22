<?php

namespace App\Exports;

use App\Model\Products;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportProductSheet implements FromArray, WithHeadings, WithStyles
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
        $data = Products::query()
         ->where('status', 0)
            ->where('client_id', Auth::user()->id)
            ->orderBy('id')
            ->get();

        $final = [];

        foreach ($data as $products) {
            $final[] = [
                'Product Name' => $products->name,
                'Type' => $products->type,
                'Bucket' => $products->bucket,
                'Capacity' => $products->capacity,
                'Status' =>  $products->status,
                'Created At' => $products->created_at
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
