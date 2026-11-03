<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Yard;

class YardExport implements FromArray, WithHeadings, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $data = Yard::with('User')
            ->where('client_id', auth()->user()->client_id)
            ->where('status', 0)
            ->get();

        $final = [];

        foreach ($data as $item) {

            $final[] = [


                'Yard Name' => $item->name ?? 'N\A',
                'Yard Id' => $item->agency_id ?? 'N\A',
                'Yard Manager' => $item->agency_manager ?? 'N/A',
                'Location' => $item->location ?? 'N\A',
                'Address' => $item->addresss ?? 'N\A',
                'Mobile Phone' => $item->mobile_number ?? 'N/A',
                'Email' => $item->email ?? 'N\A',
              'Created At'   => $item->created_at
                ? $item->created_at->format('Y-m-d H:i:s')
                : 'N/A',

            ];
        }
        return $final;
    }
    public function headings(): array
    {
        return [

            'Yard Name',
            'Yard Id',
            'Yard Manager',
            'Location',
            'Mobile Number',
            'Email',
            'Address',
            'Created At'
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
