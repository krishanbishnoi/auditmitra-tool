<?php

namespace App\Exports;

use App\Model\Role;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportRolesSheet implements WithStyles, WithHeadings, FromArray
{ 
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $data = Role::query()
            ->orderBy('id')
            ->get();

        $final = [];

        foreach ($data as $roles) {
            $final[] = [
                'Id' => $roles->id,
                'Roles' => $roles->name,
                'Guard Name' => $roles->guard_name,
                'Created At' => $roles->created_at->format('Y-m-d H:i:s'),
            ];
        }
        return $final;
    }

    public function headings(): array
    {
        return [
           'Id',
            'Roles',
            'Guard Name',
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
