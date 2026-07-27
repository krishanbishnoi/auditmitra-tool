<?php

namespace App\Exports;

use App\Model\AuditAllocation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class AuditAllocationAssignExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting
{
    protected $authEmail;
    protected $cycle;

    public function __construct($authEmail, $cycle)
    {
        $this->authEmail = $authEmail;
        $this->cycle = $cycle;
    }

    public function collection()
    {
        
        //$startOfMonth = Carbon::now()->startOfMonth();
        //$endOfMonth = Carbon::now()->endOfMonth();

        $today = Carbon::now();
        if ($today->day < 10) {
            // If before the 10th, use the previous month's range
            $startDate = $today->copy()->subMonth()->day(10)->startOfDay()->toDateTimeString();
            $endDate = $today->copy()->day(9)->endOfDay()->toDateTimeString();
        } else {
            // If on or after the 10th, use the current month's range
            $startDate = $today->copy()->day(10)->startOfDay()->toDateTimeString();
            $endDate = $today->copy()->addMonth()->day(9)->endOfDay()->toDateTimeString();
        }

        $allocations = AuditAllocation::where('process_review_agency_email', $this->authEmail)
        ->where('process_review_period', $this->cycle)
        ->where('status',1)
        ->where('assign_status',0)
        ->get([
            'id',
            'agency_id',
            'final_agency_name',
            'agency_code',
            'type_of_agency',
            'sub_product_id', 
            'sub_product',
            'product_id',
            'product',
            'location',
            'state',
            'region',
            'process_review_agency',
            'process_review_agency_email',
            'process_review_period',
            'audit_cycle_id',
            'agency_address',
            'contact',
            'agency_email',
        ]);

        $formattedAllocations = $allocations->map(function ($allocation) {
            return [
                'id' => $allocation->id ?: '-',
                'agency_id' => $allocation->agency_id ?: '-',
                'final_agency_name' => $allocation->final_agency_name ?: '-',
                'agency_code' => $allocation->agency_code ?: '-',
                'type_of_agency' => $allocation->type_of_agency ?: '-',
                'sub_product_id' => $allocation->sub_product_id ?: '-',
                'sub_product' => $allocation->sub_product ?: '-',
                'product_id' => $allocation->product_id ?: '-',
                'product' => $allocation->product ?: '-',
                'location' => $allocation->location ?: '-',
                'state' => $allocation->state ?: '-',
                'region' => $allocation->region ?: '-',
                'process_review_agency' => $allocation->process_review_agency ?: '-',
                'process_review_agency_email' => $allocation->process_review_agency_email ?: '-',
                'process_review_period' => $allocation->process_review_period ?: '-',
                'audit_cycle_id' => $allocation->audit_cycle_id ?: '-',
                'agency_address' => $allocation->agency_address ?: '-',
                'contact' => $allocation->contact ?: '-',
                'agency_email' => $allocation->agency_email ?: '-',
                'auditor_name' => empty($allocation->auditor_name) ? '' : $allocation->auditor_name,
                'auditor_email' => empty($allocation->auditor_email) ? '' : $allocation->auditor_email,
                'audit_date' => empty($allocation->audit_date) ? 'DD-MM-YYYY' : (string) $allocation->audit_date,
            ];
        });

        return $formattedAllocations;
    }


    public function headings(): array
    {
        return [
            'id',
            'agency_id',
            'final_agency_name',
            'agency_code',
            'type_of_agency',
            'sub_product_id',
            'sub_product',
            'product_id',
            'product',
            'location',
            'state',
            'region',
            'process_review_agency',
            'process_review_agency_email',
            'process_review_period',
            'audit_cycle_id',
            'agency_address',
            'contact',
            'agency_email',
            'auditor_name',
            'auditor_email',
            'audit_date',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $headerRow = 1; 
        $highestColumn = 'V';

        $sheet->getStyle('A' . $headerRow . ':' . $highestColumn . $headerRow)->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'FFFF00'],
            ],
            'font' => [
                'bold' => true,
                'color' => ['argb' => '000000'],
            ],
        ]);

        
        foreach (range('A', $highestColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    public function columnFormats(): array
    {
        return [
            'A' => '@', 
            'V' => '@',
           
        ];
    }
}
