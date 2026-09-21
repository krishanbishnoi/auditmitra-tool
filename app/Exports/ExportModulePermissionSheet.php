<?php

namespace App\Exports;

use App\Model\ModulePermission; 
use Maatwebsite\Excel\Concerns\FromArray;
Use Maatwebsite\Excel\Concerns\WithHeadings;
Use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportModulePermissionSheet implements FromArray, WithHeadings, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return 
        [
            'Name',
            'Created At'
        ];
    }

    public function array(): array
    {
        $data =  ModulePermission::query()
        ->orderBy('id')
        ->get();

        $final = [];

        foreach($data as $modulePermission)
            {
                $final[] =[
'Module Name' => $modulePermission->module_name,
'Created At' => $modulePermission->created_at
    ];
        }
        return $final;
    }

    public function styles(Worksheet $sheet)
    {
        return
        [
            1 => ['font'=> ['bold' => true]], 
        ];
    }
}
