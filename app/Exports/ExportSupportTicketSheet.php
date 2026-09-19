<?php

namespace App\Exports;

use App\SupportTicket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class ExportSupportTicketSheet implements WithHeadings, WithStyles, FromArray
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $data = SupportTicket::query()
            ->orderBy('id')
            ->get();

        $final = [];

        foreach ($data as $supTickets) {
            $final[] = [
                'ID' => $supTickets->id,
                'Help Topic' => $supTickets->help_topic,
                'Issue Type' => $supTickets->issue_type,
                'Subject' => $supTickets->subject,
                'Priority' => $supTickets->priority,
                'Description' => $supTickets->description,
                'Status' => $supTickets->status,
                'Closure Feedback' => $supTickets->closure_feedback,
                'Support Id' => $supTickets->support_id,
                'Created AT' => $supTickets->created_at

            ];
        }

        return $final;
    }

    public function headings(): array
    {
        return
            [
                'Id',
                'Help Topic',
                'Issue Type',
                'Subject',
                'Priority',
                'Description',
                'Status',
                'Closure Feedback',
                'Support Id',
                'Created AT'
            ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
