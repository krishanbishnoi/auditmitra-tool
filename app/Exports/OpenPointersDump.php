<?php

namespace App\Exports;

use App\Audit;
use App\User;
use Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class OpenPointersDump implements FromQuery, WithHeadings, WithMapping, WithEvents, WithTitle
{
    protected $startDate;
    protected $endDate;
    protected $counter = 1;

    public function title(): string
    {
        return 'pointers-dump';
    }
    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
    }

    /**
     * Query: UNSATISFACTORY audit observations + closure info
     */
    public function query()
    {
        return Audit::query()
            ->join('audit_results', 'audits.id', '=', 'audit_results.audit_id')
            ->join('qm_sheet_sub_parameters', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id')
            ->join('agencies', 'audits.agency_id', '=', 'agencies.id')
            ->join('audit_cycles', 'audits.audit_cycle_id', '=', 'audit_cycles.id')
            ->join('states', 'agencies.state', '=', 'states.id')
            ->join('regions', 'agencies.region_id', '=', 'regions.id')

            // ✅ LEFT JOIN on audit_closure_artifacts (Approach 1)
            ->leftJoin('audit_closure_artifacts as aca', function ($join) {
                $join->on('aca.audit_id', '=', 'audits.id')
                    ->on('aca.sub_parameter_id', '=', 'audit_results.sub_parameter_id');
            })

            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', 1)
            ->where('audit_results.option_selected', 'Unsatisfactory')

            ->when($this->startDate && $this->endDate, function ($q) {
                $q->whereBetween(
                    DB::raw('DATE(audits.created_at)'),
                    [$this->startDate, $this->endDate]
                );
            })

            ->select(
                'audits.id',
                'audits.virtual_audit as audit_type',
                'audits.present_auditor as auditor_name',
                'audit_results.remark',
                'qm_sheet_sub_parameters.sub_parameter',
                'qm_sheet_sub_parameters.Severity',
                'audits.lavel_3',
                'audits.lavel_4',
                'audits.lavel_5',
                'audits.created_at',
                'agencies.name as agency_name',
                'agencies.location as agency_address',
                'audit_cycles.name as audit_cycle_month',
                'states.name as state_name',
                'regions.name as zone_name',

                // ✅ From audit_closure_artifacts
                'aca.approval_status',
                'aca.justification as closure_remarks'
            )
            ->orderBy('audits.id', 'asc');
    }

    /**
     * Excel Headings
     */
    public function headings(): array
    {
        return [
            'S.No.',
            'Review Month',
            'Agency Name',
            'Location',
            'State',
            'Zone',
            'Audit Type',
            'Auditor Name',
            'Collection Manager',
            'RCM',
            'ZCM Name',
            'Audit Observation',
            'Severity',
            'Category',
            'Status',
            'Closure Remarks',
            'Open Date',
            'Closure Date',
            'TAT',
        ];
    }

    /**
     * Map DB row to Excel row
     */
    public function map($row): array
    {
        $sNo = $this->counter++;

        $auditType = $row->audit_type == 1 ? 'Virtual' : 'Physical';


        // Level 3 (CM)
        $level3 = '-';
        if ($row->lavel_3) {
            $ids = explode(',', $row->lavel_3);
            $user = User::find(trim($ids[0]));
            $level3 = $user->name ?? '-';
        }

        // Level 4 (RCM)
        $level4 = '-';
        if ($row->lavel_4) {
            $ids = explode(',', $row->lavel_4);
            $user = User::find(trim($ids[0]));
            $level4 = $user->name ?? '-';
        }

        // Level 5 (ZCM)
        $level5 = '-';
        if ($row->lavel_5) {
            $ids = explode(',', $row->lavel_5);
            $user = User::find(trim($ids[0]));
            $level5 = $user->name ?? '-';
        }

        // ✅ Status derived from approval_status
        $status = $row->approval_status
            ? ucfirst($row->approval_status)
            : 'Open';



        return [
            $sNo,
            $row->audit_cycle_month,
            $row->agency_name,
            $row->agency_address,
            $row->state_name,
            $row->zone_name,
            $auditType,
            $row->auditor_name,
            $level3,
            $level4,
            $level5,
            $row->remark,
            $row->Severity,
            $row->sub_parameter,
            $status,
            $row->closure_remarks ?? null,
            $row->created_at,
            null,
            null,
        ];
    }

    /**
     * Excel Styling
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $lastColumn = $sheet->getHighestColumn();
                $lastRow    = $sheet->getHighestRow();

                // Header styling
                $sheet->getStyle("A1:{$lastColumn}1")->getFont()->setBold(true);
                $sheet->getStyle("A1:{$lastColumn}1")
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('D6DCE4');

                // Borders
                $sheet->getStyle("A1:{$lastColumn}{$lastRow}")
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' =>
                                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ],
                        ],
                    ]);

                // Column width
                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())
                        ->setWidth(40);
                }
            }
        ];
    }
}
