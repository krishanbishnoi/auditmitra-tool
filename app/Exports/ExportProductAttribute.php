<?php

namespace App\Exports;

use App\Model\Productattribute;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportProductAttribute implements FromArray, WithHeadings, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return
            [
                'Product Attribute Name',
                'Bucket',
                'Type',
                'Status',
                'Created At'
            ];
    }

    public function array(): array
    {
          $data = Productattribute::with('productName')
            ->join(
                'products',
                'productattributes.product_id',
                '=',
                'products.id'
            )
            ->where('products.client_id', Auth::user()->id)
            ->orderBy('products.name', 'ASC')
            ->select('productattributes.*')
            ->get();
            
        $final = [];

        foreach ($data as $productAttribute) {
            $final[] = [
                'Product Attribute Name' => $productAttribute->product_attribute_name,
                'Bucket' => $productAttribute->bucket,
                'Type' => $productAttribute->type,
                'Status' => $productAttribute->status,
                'Created At' => $productAttribute->created_at
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
