<?php

namespace App\Exports;

use App\YardRepo;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportYardRepoSheet implements FromArray, WithHeadings, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function headings(): array
    {
        return
            [
                'Yard Repo Name',
                'Yard Repo Id',
                'Yard Repo Manager',
                'Location',
                'Address',
                'Mobile Phone',
                'Email',
                'Created At'
            ];
    }

    public function array(): array
    {
        $data = YardRepo::with('User')
            ->where('client_id', auth()->user()->client_id)
            ->where('status', 0)
            ->get();

        $final = [];

        foreach ($data as $yardRepo) {
            $final[] = [
                'Yard Repo Name' => $yardRepo->name,
                'Yard Repo Id' => $yardRepo->agency_id,
                'Yard Repo Manager' => $yardRepo->agency_manager,
                'Location' => $yardRepo->location,
                'Address' => $yardRepo->address,
                'Mobile Phone' => $yardRepo->mobile_phone,
                'Email' => $yardRepo->email,
                'Created At'        => $yardRepo->created_at
                ? $yardRepo->created_at->format('Y-m-d H:i:s')
                : '',
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
