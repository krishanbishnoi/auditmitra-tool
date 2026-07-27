<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use App\Audit;
use App\User;
use App\QmSheetSubParameter;
use App\Helpers\Helper;
use Auth;

// ─────────────────────────────────────────────────────
//  MAIN EXPORT — one tab per qm_sheet_id
// ─────────────────────────────────────────────────────
class AuditsExportCategory implements WithMultipleSheets
{
    protected $startDate;
    protected $endDate;
    protected $client_id;

    public function __construct($startDate, $endDate, $client_id)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
        $this->client_id = $client_id;
    }

    /**
     * One sheet/tab per qm_sheet_id that actually has matching audits
     * in the given date range for the current client.
     */
    public function sheets(): array
    {
        $sheets = [];

        $query = Audit::query()
            ->join('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id')
            ->where('audits.status', 1)
            ->where('audits.client_id', Auth::user()->client_id);

        if (!empty($this->startDate) && !empty($this->endDate)) {
            $query->whereBetween(DB::raw('DATE(audits.created_at)'), [$this->startDate, $this->endDate]);
        }

        $qmSheets = $query
            ->select('qm_sheets.id', 'qm_sheets.name')
            ->distinct()
            ->orderBy('qm_sheets.name')
            ->get();

        foreach ($qmSheets as $qmSheet) {
            // Main tab — every parameter type for this checksheet (Detailed)
            $sheets[] = new AuditsExportCategorySheet(
                $this->startDate,
                $this->endDate,
                $this->client_id,
                $qmSheet->id,
                $qmSheet->name
            );

            // Extra tab — External parameters only, for this same checksheet
            $sheets[] = new AuditsExportCategorySheet(
                $this->startDate,
                $this->endDate,
                $this->client_id,
                $qmSheet->id,
                $qmSheet->name . ' - External',
                'External'
            );
        }

        // Guard against an empty workbook if no audits matched anything.
        if (empty($sheets)) {
            $sheets[] = new AuditsExportCategorySheet(
                $this->startDate,
                $this->endDate,
                $this->client_id,
                null,
                'No Data'
            );
        }

        return $sheets;
    }
}

// ─────────────────────────────────────────────────────
//  PER-TAB SHEET — builds one qm_sheet_id's worth of rows
// ─────────────────────────────────────────────────────
class AuditsExportCategorySheet implements FromCollection, WithHeadings, WithEvents, WithTitle
{
    protected $startDate;
    protected $endDate;
    protected $client_id;
    protected $qmSheetId;
    protected $qmSheetName;
    protected $parameterTypeFilter;

    public function __construct($startDate, $endDate, $client_id, $qmSheetId = null, $qmSheetName = 'Sheet', $parameterTypeFilter = null)
    {
        $this->startDate           = $startDate;
        $this->endDate             = $endDate;
        $this->client_id           = $client_id;
        $this->qmSheetId           = $qmSheetId;
        $this->qmSheetName         = $qmSheetName;
        $this->parameterTypeFilter = $parameterTypeFilter;
    }

    /**
     * Small helper so we don't repeat the "is this the External tab?" check
     * everywhere with slightly different casing/typos.
     */
    protected function isExternalSheet(): bool
    {
        return strtolower((string) $this->parameterTypeFilter) === 'external';
    }

    // ─────────────────────────────────────────────
    //  TAB TITLE
    // ─────────────────────────────────────────────
    public function title(): string
    {
        // Excel sheet titles: max 31 chars, no \ / ? * [ ] :
        $title = preg_replace('/[\\\\\/\?\*\[\]\:]/', '-', (string) $this->qmSheetName);
        $title = trim($title) ?: 'Sheet';

        return mb_substr($title, 0, 31);
    }

    // ─────────────────────────────────────────────
    //  HEADINGS
    // ─────────────────────────────────────────────
    public function headings(): array
    {
        return $this->isExternalSheet()
            ? $this->headingsExternal()
            : $this->headingsDetailed();
    }

    /**
     * "Detailed Collection Agency Report" header — brand-new, simplified
     * layout. Entirely replaces the old long header for this sheet.
     */
    protected function headingsDetailed(): array
    {
        return [
            'Sr. No.',
            'Agency Name',
            'Agency Code',
            'Location',
            'Assessment Date',
            'Assessment Partner',
            'Assessment completed in year',
            'Groups',                 // qm_sheet_parameters.category
            'Main category',          // qm_sheet_parameters.parameter
            'Check points Category',  // SUB CATEGORY (before " - ")
            'Risk Level',             // qm_sheet_sub_parameters.severity
            'Details to be checked',  // PARAMETER (after " - ")
            'Points to be checked',   // qm_sheet_sub_parameters.details
            'Scoring Parameter',      // qm_sheet_sub_parameters.weight
            'Complied (Yes/No)',      // audit_results.option_selected
        ];
    }

    /**
     * "Assessment Collection Agency Report" header — replaces the old
     * long-form External header entirely.
     */
    protected function headingsExternal(): array
    {
        return [
            'Sr. No.',
            'Agency Name',
            'Agency Code',
            'Location',
            'Assessment Date',
            'Assessment Partner',
            'Assessment completed in year',
            'Categories',              // sub_parameter BEFORE " - "
            'Parameters',              // sub_parameter AFTER  " - "
            'Risk Level',              // qm_sheet_sub_parameters.severity
            'Scoring Parameter',       // qm_sheet_sub_parameters.weight
            'Complied (Yes/No/NA)',    // audit_results.option_selected
            'No. of Errors',           // audit_results.error_count
            'Score',                   // intentionally left blank
            'Stage 1 - Observations',  // audit_results.remark
        ];
    }

    // ─────────────────────────────────────────────
    //  DATA — one row per sub-parameter per audit, scoped to this qm_sheet_id
    // ─────────────────────────────────────────────
    public function collection()
    {
        return $this->isExternalSheet()
            ? $this->collectionExternal()
            : $this->collectionDetailed();
    }

    /**
     * "Detailed Collection Agency Report" rows — matches headingsDetailed().
     */
    protected function collectionDetailed()
    {
        $query = Audit::query()
            ->where('audits.status', 1)
            ->join('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id')
            ->join('agencies',  'audits.agency_id',    '=', 'agencies.id')
            ->join('users',     'audits.audit_agency_id', '=', 'users.id')
            ->where('audits.client_id', Auth::user()->client_id);

        if (!empty($this->qmSheetId)) {
            $query->where('audits.qm_sheet_id', $this->qmSheetId);
        }

        if (!empty($this->startDate) && !empty($this->endDate)) {
            $query->whereBetween(DB::raw('DATE(audits.created_at)'), [$this->startDate, $this->endDate]);
        }

        $audits = $query->select(
            'audits.id',
            'audits.created_at   as process_review_date',
            'agencies.name       as agency_name',
            'agencies.agency_id  as agency_code',
            'agencies.location   as agency_location',
            'users.name          as assessment_partner'
        )
            ->orderBy('audits.id', 'asc')
            ->get();

        // ── Helper: split "Sub Category - Parameter detail" ──
        $splitSubParam = function (string $value): array {
            if (strpos($value, ' - ') !== false) {
                [$sub, $param] = explode(' - ', $value, 2);
                return [trim($sub), trim($param)];
            }
            return ['-', trim($value)];
        };

        // ── Helper: format a date as "FY - 2026-27" (Apr–Mar financial year) ──
        $formatFinancialYear = function (?string $date): string {
            if (empty($date)) return '-';
            $ts    = strtotime($date);
            $month = (int) date('n', $ts);
            $year  = (int) date('Y', $ts);

            [$startYear, $endYear] = $month >= 4
                ? [$year, $year + 1]
                : [$year - 1, $year];

            return 'FY - ' . $startYear . '-' . substr((string) $endYear, -2);
        };

        $rows  = collect();
        $srNo  = 1;

        foreach ($audits as $audit) {
            $assessmentYear = $formatFinancialYear($audit->process_review_date);

            $base = [
                $audit->agency_name       ?? '-',
                $audit->agency_code       ?? '-',
                $audit->agency_location   ?? '-',
                $audit->process_review_date ?? '-',
                $audit->assessment_partner ?? '-',
                $assessmentYear,
            ];

            $subParams = QmSheetSubParameter::join(
                'audit_results',
                'audit_results.sub_parameter_id',
                '=',
                'qm_sheet_sub_parameters.id'
            )
                ->join(
                    'qm_sheet_parameters',
                    'qm_sheet_sub_parameters.qm_sheet_parameter_id',
                    '=',
                    'qm_sheet_parameters.id'
                )
                ->where('audit_results.audit_id', $audit->id)
                ->orderBy('qm_sheet_parameters.id',     'asc')
                ->orderBy('qm_sheet_sub_parameters.id', 'asc')
                ->select(
                    'qm_sheet_parameters.category           as category',
                    'qm_sheet_parameters.parameter          as main_category',
                    'qm_sheet_sub_parameters.sub_parameter',
                    'qm_sheet_sub_parameters.details        as points_to_be_checked',
                    'qm_sheet_sub_parameters.severity        as severity',
                    'audit_results.score                    as score_gained',
                    'audit_results.option_selected'
                )
                ->get();

            if ($subParams->isEmpty()) {
                $rows->push(array_merge([$srNo++], $base, ['-', '-', '-', '-', '-', '-', '-']));
                continue;
            }

            foreach ($subParams as $sp) {
                [$subCategory, $parameter] = $splitSubParam($sp->sub_parameter ?? '');

                $row = array_merge([$srNo++], $base, [
                    $sp->category            ?? '-',  // Groups
                    $sp->main_category       ?? '-',  // Main category
                    $subCategory,                     // Check points Category
                    $sp->severity            ?? '-',  // Risk Level
                    $parameter,                       // Details to be checked
                    $sp->points_to_be_checked ?? '-', // Points to be checked
                    $sp->score_gained       ?? '-',  // Scoring Parameter (score gained)
                    $sp->option_selected     ?? '-',  // Complied (Yes/No)
                ]);

                $rows->push($row);
            }
        }

        return $rows;
    }

    /**
     * "Assessment Collection Agency Report" rows — matches headingsExternal().
     * Score column is intentionally left blank per instructions.
     */
    protected function collectionExternal()
    {
        $query = Audit::query()
            ->where('audits.status', 1)
            ->join('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id')
            ->join('agencies',  'audits.agency_id',    '=', 'agencies.id')
            ->join('users',     'audits.audit_agency_id', '=', 'users.id')
            ->where('audits.client_id', Auth::user()->client_id);

        if (!empty($this->qmSheetId)) {
            $query->where('audits.qm_sheet_id', $this->qmSheetId);
        }

        if (!empty($this->startDate) && !empty($this->endDate)) {
            $query->whereBetween(DB::raw('DATE(audits.created_at)'), [$this->startDate, $this->endDate]);
        }

        $audits = $query->select(
            'audits.id',
            'audits.created_at   as process_review_date',
            'agencies.name       as agency_name',
            'agencies.agency_id  as agency_code',
            'agencies.location   as agency_location',
            'users.name          as assessment_partner'
        )
            ->orderBy('audits.id', 'asc')
            ->get();

        // ── Helper: split "Category - Parameter detail" ──
        $splitSubParam = function (string $value): array {
            if (strpos($value, ' - ') !== false) {
                [$category, $param] = explode(' - ', $value, 2);
                return [trim($category), trim($param)];
            }
            return ['-', trim($value)];
        };

        // ── Helper: format a date as "FY - 2026-27" (Apr–Mar financial year) ──
        $formatFinancialYear = function (?string $date): string {
            if (empty($date)) return '-';
            $ts    = strtotime($date);
            $month = (int) date('n', $ts);
            $year  = (int) date('Y', $ts);

            [$startYear, $endYear] = $month >= 4
                ? [$year, $year + 1]
                : [$year - 1, $year];

            return 'FY - ' . $startYear . '-' . substr((string) $endYear, -2);
        };

        $rows = collect();
        $srNo = 1;

        foreach ($audits as $audit) {
            $assessmentYear = $formatFinancialYear($audit->process_review_date);

            $base = [
                $audit->agency_name         ?? '-',
                $audit->agency_code         ?? '-',
                $audit->agency_location     ?? '-',
                $audit->process_review_date ?? '-',
                $audit->assessment_partner  ?? '-',
                $assessmentYear,
            ];

            $subParams = QmSheetSubParameter::join(
                'audit_results',
                'audit_results.sub_parameter_id',
                '=',
                'qm_sheet_sub_parameters.id'
            )
                ->join(
                    'qm_sheet_parameters',
                    'qm_sheet_sub_parameters.qm_sheet_parameter_id',
                    '=',
                    'qm_sheet_parameters.id'
                )
                ->where('audit_results.audit_id', $audit->id)
                ->whereRaw('LOWER(qm_sheet_sub_parameters.parameter_type) = ?', ['external'])
                ->orderBy('qm_sheet_parameters.id',     'asc')
                ->orderBy('qm_sheet_sub_parameters.id', 'asc')
                ->select(
                    'qm_sheet_parameters.parameter          as main_category',
                    'qm_sheet_sub_parameters.sub_parameter',
                    'qm_sheet_sub_parameters.severity        as severity',
                    'audit_results.score                    as score_gained',
                    'audit_results.option_selected',
                    'audit_results.error_count',
                    'audit_results.remark'
                )
                ->get();

            // No External-type rows for this audit — skip it, as before.
            if ($subParams->isEmpty()) {
                continue;
            }

            foreach ($subParams as $sp) {
                [$beforeDash, $afterDash] = $splitSubParam($sp->sub_parameter ?? '');

                $row = array_merge([$srNo++], $base, [
                    $sp->main_category   ?? '-',   // Categories  (qm_sheet_parameters.parameter)
                    $beforeDash,                    // Parameters  (BEFORE " - ")
                    $sp->severity        ?? '-',   // Risk Level
                    $sp->score_gained   ?? '-',   // Scoring Parameter (score gained)
                    $sp->option_selected ?? '-',   // Complied (Yes/No/NA)
                    $sp->error_count     ?? '0',   // No. of Errors
                    '',                             // Score — intentionally left blank
                    $sp->remark          ?? '-',   // Stage 1 - Observations
                ]);

                $rows->push($row);
            }
        }

        return $rows;
    }

    // ─────────────────────────────────────────────
    //  SHEET STYLING
    // ─────────────────────────────────────────────
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet      = $event->sheet->getDelegate();
                $lastColumn = $sheet->getHighestColumn();
                $lastRow    = $sheet->getHighestRow();

                // ── Header row ──────────────────────────────────
                $headerRange = 'A1:' . $lastColumn . '1';
                $sheet->getStyle($headerRange)->getFont()->setBold(true);
                $sheet->getStyle($headerRange)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('D6DCE4');
                $sheet->getStyle($headerRange)->getAlignment()
                    ->setWrapText(true)
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // ── Borders ─────────────────────────────────────
                $sheet->getStyle('A1:' . $lastColumn . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color'       => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                // ── Column widths ───────────────────────────────
                foreach ($sheet->getColumnIterator() as $column) {
                    $colIndex = $column->getColumnIndex();
                    if ($colIndex < 'AG') {
                        $sheet->getColumnDimension($colIndex)->setAutoSize(true);
                    } else {
                        $sheet->getColumnDimension($colIndex)->setWidth(50);
                    }
                }

                // ── Locate the "Complied" column (Detailed: "Complied (Yes/No)",
                //    External: "Complied (Yes/No/NA)") ────────
                $optionCol = null;
                foreach ($sheet->getColumnIterator() as $col) {
                    $heading = strtoupper(trim(
                        $sheet->getCell($col->getColumnIndex() . '1')->getValue() ?? ''
                    ));
                    if (strpos($heading, 'COMPLIED') === 0) {
                        $optionCol = $col->getColumnIndex();
                        break;
                    }
                }

                // ── Colour ONLY the Complied cell ───────────────
                if ($optionCol) {
                    $colorMap = [
                        'satisfactory'   => 'C6EFCE',  // green
                        'unsatisfactory' => 'FFC7CE',  // red
                        'na'             => 'FFEB9C',  // yellow
                        'n/a'            => 'FFEB9C',
                    ];

                    for ($r = 2; $r <= $lastRow; $r++) {
                        $cellRef = $optionCol . $r;
                        $val     = strtolower(trim(
                            $sheet->getCell($cellRef)->getValue() ?? ''
                        ));
                        $bgColor = $colorMap[$val] ?? null;

                        if ($bgColor) {
                            $sheet->getStyle($cellRef)
                                ->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()->setARGB($bgColor);

                            $sheet->getStyle($cellRef)->getFont()->setBold(true);
                        }
                    }
                }
            },
        ];
    }
}