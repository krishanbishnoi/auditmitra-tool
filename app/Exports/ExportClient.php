<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use App\Client;

class ExportClient implements FromArray, WithHeadings, WithStyles
{
    /**
     * Fetch client records directly from the clients table.
     */
    public function array(): array
    {
        $record = Client::query()
            ->orderBy('client_id')
            ->get();

        $final = [];

        foreach ($record as $client) {
            $final[] = [
                'Client ID' => $client->client_id,
                'Client Code' => $client->client_code,
                'Client Name' => $client->client_name,
                'Client Email' => $client->client_email,
                'Created At' => ($client->created_at)->format('Y-m-d H:i:s'),
            ];
        }
        return $final;
    }

    public function headings(): array
    {
        return [
            'Client ID',
            'Client Code',
            'Client Name',
            'Client Email',
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
