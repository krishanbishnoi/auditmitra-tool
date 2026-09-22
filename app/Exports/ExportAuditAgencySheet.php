<?php

namespace App\Exports;

use App\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;
Use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportAuditAgencySheet implements WithHeadings, WithStyles, FromArray
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
 $user = Auth::user();

        if ($user->hasRole('Super Admin')) {

            // Super Admin: export all Audit Agencies
            $data = User::role('Admin')
                ->orderBy('id')
                ->get();

        } elseif ($user->hasRole('Client')) {

            // Client: export only their Audit Agencies
            $data = User::role('Admin')
                ->where('client_id', $user->id)
                ->orderBy('id')
                ->get();

        } else {

            $data = collect();
        }

        $final = [];

        foreach ($data as $agency) {
            $final[] = [
                
                'Name' => $agency->name,
                'Email' => $agency->email,
                'Phone' => $agency->mobile,         
                'Created At' => $agency->created_at
                    ? $agency->created_at->format('Y-m-d H:i:s')
                    : '',
            ];
        }

        return $final;
    }

    public function styles(Worksheet $sheet)
    {
 return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
