<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LegalAuditsExport implements FromCollection, WithEvents
{
    protected $startDate;
    protected $endDate;
    protected $clientId;

    protected $structure = [];
    protected $baseColumns = [
        'Advocate Name',
        'Address',
        'Location',
        'State',
        'Audit Date',
        'Empanelled From',
        'Legal Manager',
        'Legal Cycle',
        'Auditor Name',
    ];

    public function __construct($startDate, $endDate, $clientId)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
        $this->clientId  = $clientId;
    }

    public function collection()
    {
        // Single optimized query with proper indexing hints
        $rows = DB::table('legal_audits as la')
            ->leftJoin('advocates as adv', 'adv.id', '=', 'la.advocate_id')
            ->leftJoin('legal_parameter_results as lpr', 'lpr.legal_audit_id', '=', 'la.id')
            ->leftJoin('qm_sheet_sub_parameters as qsps', 'qsps.id', '=', 'lpr.sub_parameter_id')
            ->leftJoin('qm_sheet_parameters as qsp', 'qsp.id', '=', 'lpr.parameter_id')
            ->where('la.client_id', $this->clientId)
            ->whereBetween('la.audit_date', [$this->startDate, $this->endDate])
            ->select(
                'la.id as audit_id',
                'adv.name as advocate_name',
                'la.address',
                'la.location',
                'la.state',
                'la.audit_date',
                'la.empanelled_from',
                'la.legal_manager',
                'la.legal_cycle',
                'la.auditor_name',
                'la.recommendations',
                'qsp.id as parameter_id',
                'qsp.parameter',
                'qsps.sub_parameter',
                'lpr.remark',
                'lpr.compliance_status'
            )
            ->orderBy('la.id')
            ->orderBy('qsp.id')
            ->orderBy('qsps.id')
            ->get();

        // Early return if no data
        if ($rows->isEmpty()) {
            return collect([]);
        }

        // Fetch parameter summaries with single query
        $auditIds = $rows->pluck('audit_id')->unique()->values()->all();
        
        $summaries = DB::table('legal_parameter_summaries as lps')
            ->leftJoin('qm_sheet_parameters as qsp', 'qsp.id', '=', 'lps.parameter_id')
            ->whereIn('lps.legal_audit_id', $auditIds)
            ->select(
                'lps.legal_audit_id',
                'lps.parameter_id',
                'qsp.parameter',
                'lps.summary'
            )
            ->get();

        // Build summary lookup map for O(1) access
        $summaryMap = [];
        foreach ($summaries as $summary) {
            $summaryMap[$summary->legal_audit_id][$summary->parameter] = $summary->summary;
        }

        // Group rows by audit_id
        $grouped = $rows->groupBy('audit_id');

        // Build structure: track ALL unique parameters across all audits
        // Use associative array for faster lookups
        $allParameters = [];
        $parameterOrder = [];
        
        foreach ($grouped as $auditItems) {
            foreach ($auditItems as $item) {
                if (!isset($allParameters[$item->parameter])) {
                    $allParameters[$item->parameter] = [];
                    $parameterOrder[] = $item->parameter;
                }
                
                // Use isset instead of in_array for O(1) lookup
                $subParamKey = $item->sub_parameter;
                if (!isset($allParameters[$item->parameter][$subParamKey])) {
                    $allParameters[$item->parameter][$subParamKey] = true;
                }
            }
        }
        
        // Convert to array format for structure
        foreach ($allParameters as $param => &$subs) {
            $subs = array_keys($subs);
        }
        unset($subs);
        
        $this->structure = $allParameters;

        // Pre-calculate total columns needed
        $totalSummaryCols = count($this->structure);
        $totalDetailCols = 0;
        foreach ($this->structure as $subParams) {
            $totalDetailCols += count($subParams) * 2;
        }

        // Build data rows with optimized lookups
        return $grouped->map(function ($items) use ($summaryMap) {
            $auditId = $items->first()->audit_id;
            $firstItem = $items->first();
            
            // Base columns
            $row = [
                $firstItem->advocate_name,
                $firstItem->address,
                $firstItem->location,
                $firstItem->state,
                $firstItem->audit_date,
                $firstItem->empanelled_from,
                $firstItem->legal_manager,
                $firstItem->legal_cycle,
                $firstItem->auditor_name,
            ];

            // Add parameter summaries FIRST using direct map lookup
            foreach ($this->structure as $parameter => $subParameters) {
                $row[] = $summaryMap[$auditId][$parameter] ?? '';
            }

            // Build data map with single pass
            $dataMap = [];
            foreach ($items as $item) {
                $key = $item->parameter . '|' . $item->sub_parameter;
                $dataMap[$key] = [
                    $item->remark,
                    $item->compliance_status
                ];
            }

            // Add cells for each parameter's sub-parameters AFTER summaries
            foreach ($this->structure as $parameter => $subParameters) {
                foreach ($subParameters as $subParameter) {
                    $key = $parameter . '|' . $subParameter;
                    
                    if (isset($dataMap[$key])) {
                        $row[] = $dataMap[$key][0]; // remark
                        $row[] = $dataMap[$key][1]; // compliance_status
                    } else {
                        $row[] = '';
                        $row[] = '';
                    }
                }
            }

            // Add recommendations at the end
            $row[] = $firstItem->recommendations ?? '';

            return $row;
        })->values();
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Early return if no structure
                if (empty($this->structure)) {
                    return;
                }

                // Insert 3 header rows at the top
                $sheet->insertNewRowBefore(1, 3);

                $baseColCount = count($this->baseColumns);
                
                // Merge base columns vertically (rows 1-3) and set values
                for ($i = 1; $i <= $baseColCount; $i++) {
                    $col = Coordinate::stringFromColumnIndex($i);
                    $sheet->mergeCells("{$col}1:{$col}3");
                    $sheet->setCellValue("{$col}1", $this->baseColumns[$i - 1]);
                }

                $currentCol = $baseColCount + 1;
                $summaryStartCol = $currentCol;

                // SECTION 1: PARAMETER SUMMARIES
                // ROW 1: "Summaries" header spanning all summary columns
                $totalSummaryColumns = count($this->structure);
                
                $startCol = Coordinate::stringFromColumnIndex($currentCol);
                $endCol = Coordinate::stringFromColumnIndex($currentCol + $totalSummaryColumns - 1);
                
                $sheet->mergeCells("{$startCol}1:{$endCol}1");
                $sheet->setCellValue("{$startCol}1", "Summaries");
                
                // ROW 2 & 3: Individual parameter names for summaries
                foreach ($this->structure as $parameter => $subParameters) {
                    $col = Coordinate::stringFromColumnIndex($currentCol);
                    $sheet->mergeCells("{$col}2:{$col}3");
                    $sheet->setCellValue("{$col}2", $parameter);
                    $currentCol++;
                }

                // SECTION 2: PARAMETER DETAILS
                // ROW 1: Parameter names (merged across sub-parameters)
                foreach ($this->structure as $parameter => $subParameters) {
                    $colSpan = count($subParameters) * 2; // Each sub-param has 2 columns (remark, compliance)
                    $startCol = Coordinate::stringFromColumnIndex($currentCol);
                    $endCol = Coordinate::stringFromColumnIndex($currentCol + $colSpan - 1);
                    
                    $sheet->mergeCells("{$startCol}1:{$endCol}1");
                    $sheet->setCellValue("{$startCol}1", $parameter);
                    
                    $currentCol += $colSpan;
                }

                // ROW 2: Sub-parameter names (merged across remark + compliance columns)
                $currentCol = $summaryStartCol + $totalSummaryColumns; // Skip summary columns
                foreach ($this->structure as $parameter => $subParameters) {
                    foreach ($subParameters as $subParameter) {
                        $startCol = Coordinate::stringFromColumnIndex($currentCol);
                        $endCol = Coordinate::stringFromColumnIndex($currentCol + 1);
                        
                        $sheet->mergeCells("{$startCol}2:{$endCol}2");
                        $sheet->setCellValue("{$startCol}2", $subParameter);
                        
                        $currentCol += 2;
                    }
                }

                // ROW 3: Detail headers (remark, compliance status)
                $currentCol = $summaryStartCol + $totalSummaryColumns; // Skip summary columns
                foreach ($this->structure as $parameter => $subParameters) {
                    foreach ($subParameters as $subParameter) {
                        $remarkCol = Coordinate::stringFromColumnIndex($currentCol);
                        $complianceCol = Coordinate::stringFromColumnIndex($currentCol + 1);
                        
                        $sheet->setCellValue("{$remarkCol}3", 'remark');
                        $sheet->setCellValue("{$complianceCol}3", 'compliance status');
                        
                        $currentCol += 2;
                    }
                }

                // Add Recommendations column at the end
                $recommendCol = Coordinate::stringFromColumnIndex($currentCol);
                $sheet->mergeCells("{$recommendCol}1:{$recommendCol}3");
                $sheet->setCellValue("{$recommendCol}1", "Recommendations");

                // Get dimensions once
                $totalColumns = $sheet->getHighestColumn();
                $totalRows = $sheet->getHighestRow();

                // Prepare style arrays once (reuse instead of recreating)
                $headerStyle = [
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D9E1F2'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ];

                $dataStyle = [
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_TOP,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ];

                // Apply styling in bulk
                $sheet->getStyle("A1:{$totalColumns}3")->applyFromArray($headerStyle);

                if ($totalRows > 3) {
                    $sheet->getStyle("A4:{$totalColumns}{$totalRows}")->applyFromArray($dataStyle);
                }

                // Set column widths efficiently
                // Base columns
                for ($i = 1; $i <= $baseColCount; $i++) {
                    $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setWidth(18);
                }

                // Summary columns
                $currentCol = $baseColCount + 1;
                $summaryEndCol = $currentCol + $totalSummaryColumns - 1;
                for ($i = $currentCol; $i <= $summaryEndCol; $i++) {
                    $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setWidth(40);
                }

                // Detail columns
                $currentCol = $summaryEndCol + 1;
                foreach ($this->structure as $parameter => $subParameters) {
                    foreach ($subParameters as $subParameter) {
                        $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($currentCol))->setWidth(35);
                        $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($currentCol + 1))->setWidth(20);
                        $currentCol += 2;
                    }
                }

                // Recommendations column
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($currentCol))->setWidth(40);

                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(25);
                $sheet->getRowDimension(3)->setRowHeight(25);

                // Freeze header rows (freeze panes at row 4)
                $sheet->freezePane('A4');
            }
        ];
    }
}
