<?php

namespace App\Exports;

use App\QmSheet;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportQmCheckSheet implements WithStyles, WithHeadings, FromArray
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {

            // Super Admin: Export all sheets
            $data = QmSheet::query()
                ->orderBy('id')
                ->get();
        } else {

            // Client: Export only their own sheets
            $data = QmSheet::query()
                ->where('client_id', $user->id)
                ->orderBy('id')
                ->get();
        }
        $final = [];

        foreach ($data as $qmSheet) {
            $final[] = [

                'Name' => $qmSheet->name,
                'Code' => $qmSheet->code,
                'Details' => $qmSheet->details,
                'Type' => $qmSheet->type,
                'Lob' => $qmSheet->lob,

                'Created At' => $qmSheet->created_at->format('Y-m-d H:i:s'),
            ];
        }
        return $final;
    }

    public function headings(): array
    {
        return
            [

                'Name',
                'Code',
                'Details',
                'Type',
                'Lob',

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
