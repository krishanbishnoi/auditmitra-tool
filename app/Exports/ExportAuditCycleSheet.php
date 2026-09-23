<?php

namespace App\Exports;

use App\AuditCycle;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportAuditCycleSheet implements FromArray, WithStyles, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return
            [
                'Name',
                'Status',
                'Created At'
            ];
    }

    public function array(): array
    {
        $user = Auth::user();
        if ($user->hasRole('Super Admin')) {
            $data = AuditCycle::query()
                ->orderBy('id')
                ->get();
        } else {
            $data = AuditCycle::query()
                ->where('client_id', $user->id)
                ->orderBy('id')
                ->get();
        }

        $final = [];


        foreach ($data as $auditCycle) {
            $final[] = [
                'Name' => $auditCycle->name,
                'Status' => $auditCycle->status,
                'Created At' => $auditCycle->created_at
            ];
        }
        return $final;
    }

    public function styles(Worksheet $sheet)
    {
        return
            [
                1 => ['font' => ['font' => true]],
            ];
    }
}
