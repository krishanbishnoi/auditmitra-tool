<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportAuditReportSheet implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        $audit_reports = DB::table('audit_reports')
            ->where(
                'audit_reports.client_id',
                auth()->user()->client_id
            )
            ->whereNotNull('audit_reports.audit_id')
            ->join(
                'agencies',
                'audit_reports.agency_id',
                '=',
                'agencies.id'
            )
            ->select(
                'audit_reports.audit_id',
                'audit_reports.process_review_period',
                'agencies.name as campus_name',
                'agencies.agency_id as campus_code',
                'agencies.location',
                'audit_reports.audit_date'
                
            )
            ->orderBy('audit_reports.audit_date', 'desc')
            ->get();

        $final = [];

        foreach ($audit_reports as $report) {

            $final[] = [
                'Audit ID' => $report->audit_id ?? '',
                'Process Review Period' => $report->process_review_period ?? '',
                'Campus Name' => $report->campus_name ?? '',
                'Campus Code' => $report->campus_code ?? '',
                'Location' => $report->location ?? '',
                'Audit Date' => $report->audit_date ?? ''
                
            ];
        }

        return $final;
    }

    public function headings(): array
    {
        return [
            'Audit ID',
            'Process Review Period',
            'Campus Name',
            'Campus Code',
            'Location',
            'Audit Date'
         
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true
                ]
            ],
        ];
    }
}