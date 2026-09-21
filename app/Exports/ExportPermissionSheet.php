<?php

namespace App\Exports;

use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportPermissionSheet implements WithHeadings, WithStyles, FromArray
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return [
            'Name',
            'Guard Name',
            'Created At'
        ];
    }

    public function array(): array
    {
        $data = Permission::query()
            ->orderBy('id')
            ->get();

        $final = [];

        foreach ($data as $permission) {
            $final[] = [
                'Name' =>  $permission->name,
                'Guard Name' => $permission->guard_name,
                'Created At' => $permission->created_at
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
