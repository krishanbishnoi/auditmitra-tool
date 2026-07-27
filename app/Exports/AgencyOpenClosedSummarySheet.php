<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class AgencyOpenClosedSummarySheet implements FromCollection, WithHeadings, WithEvents, WithTitle
{
    protected $startDate;
    protected $endDate;
    protected $clientId;
    protected $counter = 1;

    public function title(): string
    {
        return 'pointers-count';
    }

    public function __construct($startDate, $endDate, $clientId)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
        $this->clientId  = $clientId;
    }

    public function collection()
    {
        $query = DB::table('audits')
            ->join('agencies', 'audits.agency_id', '=', 'agencies.id')
            ->join('audit_results', 'audits.id', '=', 'audit_results.audit_id')
            ->join('states', 'agencies.state', '=', 'states.id')
            ->join('regions', 'agencies.region_id', '=', 'regions.id')
            ->leftJoin('audit_closure_artifacts as aca', function ($join) {
                $join->on('aca.audit_id', '=', 'audits.id')
                    ->on('aca.sub_parameter_id', '=', 'audit_results.sub_parameter_id');
            })
            ->where('audits.client_id', $this->clientId)
            ->where('audits.status', 1)
            ->where('audit_results.option_selected', 'Unsatisfactory');

        if ($this->startDate && $this->endDate) {
            $query->whereBetween(
                DB::raw('DATE(audits.created_at)'),
                [$this->startDate, $this->endDate]
            );
        }

        return $query
            ->select(
                'agencies.name as agency_name',
                'agencies.location',
                'states.name as state_name',
                'regions.name as zone_name',
                'audits.virtual_audit as audit_type',

                // Total Unsatisfactory
                DB::raw("COUNT(*) as total_count"),

                // Closed = Approved
                DB::raw("
                    SUM(
                        CASE 
                            WHEN aca.approval_status = 'Approved'
                            THEN 1 ELSE 0
                        END
                    ) as closed_count
                "),

                // Open = Total - Closed
                DB::raw("
                    COUNT(*) -
                    SUM(
                        CASE 
                            WHEN aca.approval_status = 'Approved'
                            THEN 1 ELSE 0
                        END
                    ) as open_count
                ")
            )
            ->groupBy(
                'agencies.id',
                'agencies.name',
                'agencies.location',
                'states.name',
                'regions.name',
                'audits.virtual_audit'
            )
            ->get()
            ->map(function ($row) {

                $closedPercentage = $row->total_count > 0
                    ? round(($row->closed_count / $row->total_count) * 100, 2)
                    : 0;

                $openPercentage = $row->total_count > 0
                    ? round(($row->open_count / $row->total_count) * 100, 2)
                    : 0;

                return [
                    $this->counter++,
                    $row->agency_name,
                    $row->location,
                    $row->state_name,
                    $row->zone_name,
                    $row->audit_type == 1 ? 'Virtual' : 'Physical',
                    $row->open_count,
                    $openPercentage . '%',
                    $row->closed_count,
                    $closedPercentage . '%',
                    $row->total_count,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Agency Name',
            'Location',
            'State',
            'Zone',
            'Audit Type',
            'Open Count',
            'Open Percentage',
            'Closed Count',
            'Closed Percentage',
            'Total Count',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                $lastColumn = $sheet->getHighestColumn();
                $lastRow = $sheet->getHighestRow();

                $sheet->getStyle("A1:{$lastColumn}1")->getFont()->setBold(true);
                $sheet->getStyle("A1:{$lastColumn}1")
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('E2EFDA');

                $sheet->getStyle("A1:{$lastColumn}{$lastRow}")
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' =>
                                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ],
                        ],
                    ]);

                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())
                        ->setWidth(30);
                }
            }
        ];
    }
}
