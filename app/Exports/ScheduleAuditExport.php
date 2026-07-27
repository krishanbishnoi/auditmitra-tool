<?php


namespace App\Exports;

use App\Model\AuditorAssign;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class ScheduleAuditExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $auditAgencyId;
    protected $startDate;
    protected $endDate;
    protected $client_id;

    public function __construct($auditAgencyId, $startDate, $endDate, $client_id)
    {
        $this->auditAgencyId = $auditAgencyId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->client_id = $client_id;
    }

    /**
     * Fetch data for the report.
     */
    public function collection()
    {
        $query = AuditorAssign::query()->where('client_id','=', $this->client_id);

        // Apply filters if provided
        if ($this->auditAgencyId) {
            $query->where('user_id', $this->auditAgencyId);
        }
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('audit_date', [$this->startDate, $this->endDate]);
        }

        // Fetch records with required fields
        return $query->where('status', 1)->get([
         
            'final_agency_name',
            'agency_code',
            'type_of_agency',
          
            'sub_product',
          
            'product',
            'location',
            'state',
            'region',
            'process_review_agency',
            'process_review_agency_email',
            'process_review_period',
            'agency_address',
            'contact',
            'agency_email',
            'auditor_name',
            'auditor_email',
            'audit_date',
        ]);
    }

    /**
     * Define the headings for the exported file.
     */
    public function headings(): array
    {
        return [
           
            'Final Agency Name',
            'Agency Code',
            'Type of Agency',
        
            'Sub Product',
           
            'Product',
            'Location',
            'State',
            'Region',
            'Process Review Agency', 
            'Process Review Agency Email',
            'Process Review Period',
            'Agency Address',
            'Contact',
            'Agency Email',
            'Auditor Name',
            'Auditor Email',
            'Audit Date',
        ];
    }

    /**
     * Map each row of data to the desired format.
     */
    public function map($allocation): array
    {
        return [
           
            $allocation->final_agency_name ?? '-',
            $allocation->agency_code ?? '-',
            $allocation->type_of_agency ?? '-',
           
            $allocation->sub_product ?? '-',

            $allocation->product ?? '-',
            $allocation->location ?? '-',
            $allocation->state ?? '-',
            $allocation->region ?? '-',
            $allocation->process_review_agency ?? '-',
            $allocation->process_review_agency_email ?? '-',
            $allocation->process_review_period ?? '-',
            $allocation->agency_address ?? '-',
            $allocation->contact ?? '-',
            $allocation->agency_email ?? '-',
            $allocation->auditor_name ?? '-',
            $allocation->auditor_email ?? '-',
            $allocation->audit_date ?? '-',
        ];
    }

    /**
     * Apply styling to the Excel sheet.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Apply styling to the header row
                $sheet->getStyle('A1:T1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => '000000'],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFFF00'],
                    ],
                ]);

                // Auto-size columns
                foreach (range('A', 'T') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}

