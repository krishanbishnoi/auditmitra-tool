<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use App\Audit;
use App\SavedAudit;
use App\User;
use App\QmSheetSubParameter;
use App\Helpers\Helper;
use Auth;

class AuditsExport implements FromQuery, WithHeadings, WithMapping, WithEvents
{
    // protected $auditAgencyId;
    // protected $agencyId;
    protected $startDate;
    protected $endDate;
    protected $dynamicHeadings = [];
    protected $client_id;
    protected $isErrorCountVisible;

    //public function __construct($auditAgencyId, $agencyId, $startDate, $endDate)
    public function __construct($startDate, $endDate, $client_id)
    {

        // $this->auditAgencyId = $auditAgencyId;
        // $this->agencyId = $agencyId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->client_id = $client_id;
    }

    public function query()
    {

        $query = Audit::query()
            ->where('audits.status', 1)
            ->join('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id')
            ->join('agencies', 'audits.agency_id', '=', 'agencies.id')
            ->join('states', 'agencies.state', '=', 'states.id')
            ->join('regions', 'agencies.region_id', '=', 'regions.id')
            ->join('products', 'audits.product_id', '=', 'products.id')
            ->join('audit_cycles', 'audits.audit_cycle_id', '=', 'audit_cycles.id')
            ->leftJoin('audit_results', 'audits.id', '=', 'audit_results.audit_id')
            ->leftJoin('audit_parameter_results', 'audits.id', '=', 'audit_parameter_results.audit_id')
            //->leftJoin('audit_allocation', 'audits.agency_id', '=', 'audit_allocation.agency_id')            
            ->leftJoin('audit_allocation', function ($join) {
                $join->on('audits.agency_id', '=', 'audit_allocation.agency_id')
                    ->whereBetween(DB::raw('DATE(audit_allocation.created_at)'), [$this->startDate, $this->endDate]);
            })
            ->join('users', 'audits.audit_agency_id', '=', 'users.id')
            ->where('audits.client_id', Auth::user()->client_id);



        if (!empty($this->startDate) && !empty($this->endDate)) {
            $query->whereBetween(DB::raw('DATE(audits.created_at)'), [$this->startDate, $this->endDate]);
        }

        return $query->select(
            'audits.id',
            'audits.overall_score',
            'audits.score_percentage',
            'audits.virtual_audit',
            'audits.present_auditor',
            'audits.grade',
            'agencies.address as agency_address',
            'agencies.location as agency_location',
            'products.name as product_name',
            'qm_sheets.name as qm_sheet_name',
            'agencies.name as agency_name',
            'agencies.agency_phone',
            'agencies.email',
            'states.name as state_name',
            'regions.name as region_name',
            'audit_cycles.name as audit_cycle_month',
            'audits.audit_sheet_type as audit_sheet_type',
            'audits.created_at as process_review_date',
            DB::raw('GROUP_CONCAT(DISTINCT CASE WHEN audit_results.option_selected = "Unsatisfactory" THEN audit_results.remark 
                        END SEPARATOR "#") as unsatisfactory_remarks'),
            DB::raw("
        CASE 
            WHEN audits.client_id = 74 THEN
                CASE
                    WHEN ROUND(CAST(REPLACE(audits.score_percentage, '%', '') AS DECIMAL(5,2))) = 100 THEN 5
                    WHEN ROUND(CAST(REPLACE(audits.score_percentage, '%', '') AS DECIMAL(5,2))) BETWEEN 91 AND 99 THEN 4
                    WHEN ROUND(CAST(REPLACE(audits.score_percentage, '%', '') AS DECIMAL(5,2))) BETWEEN 81 AND 90 THEN 3
                    WHEN ROUND(CAST(REPLACE(audits.score_percentage, '%', '') AS DECIMAL(5,2))) BETWEEN 71 AND 80 THEN 2
                    WHEN ROUND(CAST(REPLACE(audits.score_percentage, '%', '') AS DECIMAL(5,2))) BETWEEN 61 AND 70 THEN 1
                    ELSE 0
                END
            ELSE
                CASE 
                    WHEN ROUND(CAST(REPLACE(audits.score_percentage, '%', '') AS DECIMAL(5,2))) > 90 THEN 'A'
                    WHEN ROUND(CAST(REPLACE(audits.score_percentage, '%', '') AS DECIMAL(5,2))) >= 75 THEN 'B'
                    WHEN ROUND(CAST(REPLACE(audits.score_percentage, '%', '') AS DECIMAL(5,2))) >= 61 THEN 'C'
                    ELSE 'D'
                END
        END as audit_grade
    "),

            'audits.lavel_3',
            'audits.lavel_4',
            'audits.lavel_5',
            'users.name as process_review_agency',
            'audit_cycles.name as audit_cycle_month',
        )
            ->groupBy(
                'audits.id',
                'audits.overall_score',
                'audits.score_percentage',
                'audits.present_auditor',
                'audits.grade',
                'audits.agency_address',
                'agencies.location',
                'products.name',
                'agencies.name',
                'agencies.agency_phone',
                'agencies.email',
                'states.name',
                'regions.name',
                'audit_cycles.name',
                'audits.created_at',
                'audits.audit_date_by_aud',
                'audits.lavel_3',
                'audits.lavel_4',
                'audits.lavel_5',
                //'audit_allocation.process_review_period',
                'users.name'
            )
            ->orderBy('audits.id', 'asc');
    }

    public function headings(): array
    {

        $baseHeadings = [
            'AGENCY NAME',
            'TYPE OF AGENCY CHECKSHEET',
            'PRODUCT',
            'LOCATION',
            'AGENCY NAME WITH LOCATION',
            'STATE',
            'REGION',
            'PROCESS REVIEW AGENCY',
            'PROCESS REVIEW PERIOD',
            'ADDRESS',
            'CONTACT NO.',
            'EMAIL',
            'PRESENT AUDITOR',
            'LEVEL 3',
            'LEVEL 4A',
            'LEVEL 5A',
            'Level 4B',
            'Level 5B',
            'Level 4C',
            'Level 5C',
            'Level 4D',
            'Level 5D',
            'Level 4E',
            'Level 5E',
            'Level 4F',
            'Level 5F',
            'Level 4G',
            'Level 5G',
            'PROCESS REVIEW DATE',
            'Scores',
            'Grade',
            'Score_Percentage',
        ];

        if ($this->client_id == 15) {
            $baseHeadings[] = 'Nature of Sheet';
        }

        $qm_sheet_id = Audit::where('client_id', Auth::user()->client_id)->where('status' , 1)->pluck('qm_sheet_id')->unique()->toArray();
        $this->dynamicHeadings = QmSheetSubParameter::join('audits', 'audits.qm_sheet_id', '=', 'qm_sheet_sub_parameters.qm_sheet_id')
            ->whereIn('audits.qm_sheet_id', $qm_sheet_id)
            ->when($this->startDate && $this->endDate, function ($query) {
                // Apply date filter only if both start and end date are provided
                $query->whereBetween(DB::raw('DATE(audits.created_at)'), [$this->startDate, $this->endDate]);
            })
            ->orderBy('qm_sheet_sub_parameters.qm_sheet_id', 'asc')
            ->orderBy('qm_sheet_sub_parameters.id', 'asc')
            ->pluck('qm_sheet_sub_parameters.sub_parameter')
            ->unique()
            ->toArray();
        $i = 1;
        // Add "Remark" for each sub-parameter
        $dynamicHeadingsWithRemarks = [];
        foreach ($this->dynamicHeadings as $subParameter) {
            $dynamicHeadingsWithRemarks[] = $subParameter;
            if (in_array(22, Helper::allocatedmodulelist())) { // error count is allocated to client error_count id is 22
                $dynamicHeadingsWithRemarks[] = 'Error Count' . $i;
            }
            $dynamicHeadingsWithRemarks[] = 'Remark' . $i;  // Add "Remark" after each sub-parameter
            $i++;
        }

        // Merge the base headings with dynamic sub-parameters and remarks
        return array_merge($baseHeadings, $dynamicHeadingsWithRemarks, [
            'PROCESS REVIEW REMARKS',
            'Audit Month',
            'Audit Type',
        ]);
    }

    public function map($audit): array
    {
        $level3Ids = array_filter(array_map('trim', explode(',', $audit->lavel_3)));
        $level3Names = [];

        foreach ($level3Ids as $id) {
            $user = User::find($id);
            if ($user) {
                $level3Names[] = $user->name;
            }
        }
        //echo '<pre>'; print_r($audit); die;
        $level4Ids = array_filter(array_map('trim', explode(',', $audit->lavel_4)));
        $level4Names = [];

        foreach ($level4Ids as $id) {
            $user = User::find($id);
            if ($user) {
                $level4Names[] = $user->name;
            }
        }

        $level5Ids = array_filter(array_map('trim', explode(',', $audit->lavel_5)));
        $level5Names = [];

        foreach ($level5Ids as $id) {
            $user = User::find($id);
            if ($user) {
                $level5Names[] = $user->name;
            }
        }


        $row = [
            $audit->agency_name ?? '-',
            $audit->qm_sheet_name ?? '-',
            $audit->product_name ?? '-',
            ($audit->location ?? $audit->agency_location ?? '-'),

            ($audit->agency_name ?? '-') . ' - ' . ($audit->location ?? $audit->agency_location ?? '-'),
            $audit->state_name ?? '-',
            $audit->region_name ?? '-',
            $audit->process_review_agency ?? '',
            $audit->audit_cycle_month ?? '-',
            $audit->agency_address ?? '-',
            $audit->agency_phone ?? '-',
            $audit->email ?? '-',
            $audit->present_auditor ?? '-',
        ];
        $row[] = $level3Names[0] ?? '-'; // Level 3
        $row[] = $level4Names[0] ?? '-'; // Level 4A
        $row[] = $level5Names[0] ?? '-'; // Level 5A

        $row[] = $level4Names[1] ?? '-'; // Level 4B
        $row[] = $level5Names[1] ?? '-'; // Level 5B

        $row[] = $level4Names[2] ?? '-'; // Level 4C
        $row[] = $level5Names[2] ?? '-'; // Level 5C

        $row[] = $level4Names[3] ?? '-'; // Level 4D
        $row[] = $level5Names[3] ?? '-'; // Level 5D

        $row[] = $level4Names[4] ?? '-'; // Level 4E
        $row[] = $level5Names[4] ?? '-'; // Level 5E

        $row[] = $level4Names[5] ?? '-'; // Level 4F
        $row[] = $level5Names[5] ?? '-'; // Level 5F

        $row[] = $level4Names[6] ?? '-'; // Level 4G
        $row[] = $level5Names[6] ?? '-'; // Level 5G

        $row[] = $audit->process_review_date ?? '-';

        $row[] = round($audit->overall_score) ?? '-';
        $row[] = $audit->audit_grade ?? '-';
        $row[] = $audit->score_percentage ?? '-';

        if ($this->client_id == 15) {
            $row[] = $audit->audit_sheet_type ?? '-';
        }


        $dynamicDataRaw = QmSheetSubParameter::join('audit_results', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id')
            ->where('audit_results.audit_id', $audit->id)
            ->orderBy('qm_sheet_sub_parameters.qm_sheet_id', 'asc')
            ->orderBy('qm_sheet_sub_parameters.id', 'asc')
            ->select('qm_sheet_sub_parameters.sub_parameter', 'audit_results.option_selected', 'audit_results.remark', 'audit_results.error_count')
            ->get();

        // Format the dynamic data
        $dynamicData = [];
        foreach ($dynamicDataRaw as $data) {
            $dynamicData[$data->sub_parameter] = [
                'option_selected' => $data->option_selected,
                'remark' => $data->remark,
                'error_count' => $data->error_count
            ];
        }

        foreach ($this->dynamicHeadings as $subParameter) {
            if (isset($dynamicData[$subParameter])) {
                $row[] = $dynamicData[$subParameter]['option_selected'] ?? '-';

                if (in_array(22, Helper::allocatedmodulelist())) {
                    $row[] = $dynamicData[$subParameter]['error_count'] ?? '0';
                }

                $row[] = $dynamicData[$subParameter]['remark'] ?? '-';
            } else {
                $row[] = '-';  // Default if no data is found

                if (in_array(22, Helper::allocatedmodulelist())) {
                    $row[] = '0';
                }
                $row[] = '-';
            }
        }


        $unsatisfactoryRemarks = $audit->unsatisfactory_remarks ?? 'NIL';  // Default if not present
        $remarksArray = explode('#', $unsatisfactoryRemarks);

        $numberedRemarks = [];
        $remarkdupArr = [];
        $s = 1;
        foreach ($remarksArray as $index => $remark) {
            if (!in_array(trim($remark), $remarkdupArr)) {
                $remarkdupArr[] = trim($remark);
                $numberedRemarks[] = $s . ". " . $remark;  // Indexing starts from 1
                $s++;
            }
        }

        $row[] = implode(', ', $numberedRemarks);
        $row[] = $audit->audit_cycle_month ?? '-';        // Audit Month
        // echo "<pre>"; print_r($remarksArray); 
        // echo implode(', ', $numberedRemarks);
        // die;
        // echo "<pre>"; print_r($row); die;
        if ($audit->virtual_audit == 1) {
            $row[] = 'Virtual';
        } else {
            $row[] = 'Physical';
        }
        return $row;
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Get the last column and row dynamically
                $lastColumn = $sheet->getHighestColumn();
                $lastRow = $sheet->getHighestRow();

                // Set bold font for the entire header row (A1 to the last column)
                $sheet->getStyle('A1:' . $lastColumn . '1')->getFont()->setBold(true);

                // Set background color for the header row
                $sheet->getStyle('A1:' . $lastColumn . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('D6DCE4'); // Light grey background color for the header

                // Set text wrap and center alignment for all header cells
                $sheet->getStyle('A1:' . $lastColumn . '1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('A1:' . $lastColumn . '1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Center horizontally
                $sheet->getStyle('A1:' . $lastColumn . '1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER); // Center vertically

                // Set row height for header row (optional, based on your preference)
                $sheet->getRowDimension(1)->setRowHeight(30); // Adjust height as needed

                // Set column width for all columns to 50 (approximately 50px)
                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())->setWidth(50); // Set width for all columns
                }

                // Apply borders to all cells
                $sheet->getStyle('A1:' . $lastColumn . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'], // Black border color
                        ],
                    ],
                ]);

                // Optionally, auto-size columns that don't fall in the header range (if needed)
                foreach ($sheet->getColumnIterator() as $column) {
                    $colIndex = $column->getColumnIndex();
                    if ($colIndex < 'AG') {
                        $sheet->getColumnDimension($colIndex)->setAutoSize(true); // Auto-size for these columns
                    }
                }
            },
        ];
    }
}
