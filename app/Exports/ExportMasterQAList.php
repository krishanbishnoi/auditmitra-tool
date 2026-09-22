<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportMasterQAList implements WithHeadings, WithStyles, FromArray
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
                'Created At'
            ];
    }

    public function array(): array
    {
        $data = User::where('active_status', 0)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Master QA');
            })
            ->with('roles')
            ->get();

        $final = [];

        foreach ($data as $user) {
            $final[] = [
                'Name' => $user->name,
                'Email' => $user->email,
                'Phone' => $user->mobile,
                'Created At' => $user->created_at
                    ? $user->created_at->format('Y-m-d H:i:s')
                    : '',
            ];
        }
        return $final;
    }
    public function styles(Worksheet $style)
    {
        return [1 => ['font' => ['bold' => 'true']],];
    }
}
