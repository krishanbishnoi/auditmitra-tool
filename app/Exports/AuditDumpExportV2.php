<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AuditDumpExportV2 implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithEvents
{
    protected $auditIds;

    protected $data;

    public function __construct($auditIds)
    {
        $this->auditIds = $auditIds;
    }

    /*
    |--------------------------------------------------------------------------
    | Collection
    |--------------------------------------------------------------------------
    */

    public function collection()
    {

        $this->data = DB::table('audit_result_v2 as ar')

            /*
            |--------------------------------------------------------------------------
            | Audit Join
            |--------------------------------------------------------------------------
            */

            ->join('audits', 'audits.id', '=', 'ar.audit_id')


            /*
            |--------------------------------------------------------------------------
            | Agency Details
            |--------------------------------------------------------------------------
            */

            ->join('agencies', 'audits.agency_id', '=', 'agencies.id')


            /*
            |--------------------------------------------------------------------------
            | Parameter
            |--------------------------------------------------------------------------
            */

            ->leftJoin(
                'qm_sheet_parameters as qp',
                'qp.id',
                '=',
                'ar.parameter_id'
            )


            /*
            |--------------------------------------------------------------------------
            | Sub Parameter
            |--------------------------------------------------------------------------
            */

            ->leftJoin(
                'qm_sheet_sub_parameters as qsp',
                'qsp.id',
                '=',
                'ar.sub_parameter_id'
            )


            /*
            |--------------------------------------------------------------------------
            | Select
            |--------------------------------------------------------------------------
            */

            ->select(

                'ar.audit_id',

                'ar.parameter_id',

                'ar.parameter_index',

                'agencies.name as agency_name',

                'agencies.location as agency_location',

                'agencies.address as agency_address',

                'ar.location',

                'ar.campus_type',

                // 'ar.brand',

                'ar.pillar',

                'ar.touch_point',

                'qp.parameter as parameter_name',

                'qsp.sub_parameter as sub_parameter_name',

                'qsp.details as details',

                'ar.option_selected',

                'ar.remark',

                'ar.score',

                'ar.scorable',

                'ar.compliance_experience'
            )


            /*
            |--------------------------------------------------------------------------
            | Filters
            |--------------------------------------------------------------------------
            */

            ->whereIn('ar.audit_id', $this->auditIds)

            ->where('audits.status', 1)


            /*
            |--------------------------------------------------------------------------
            | Order
            |--------------------------------------------------------------------------
            */

            ->orderBy('ar.audit_id')

            ->orderBy('ar.location')

            ->orderBy('ar.campus_type')

            ->orderBy('ar.brand')

            ->orderBy('ar.pillar')

            ->orderBy('ar.parameter_id')

            ->orderBy('ar.parameter_index')

            ->get();

        return $this->data;
    }


    /*
    |--------------------------------------------------------------------------
    | Mapping
    |--------------------------------------------------------------------------
    */

    public function map($row): array
    {

        $parameterName = $row->parameter_name;


        /*
        |--------------------------------------------------------------------------
        | Add Index Only If Multiple Parameter Index Exists
        |--------------------------------------------------------------------------
        */

        $parameterCount = DB::table('audit_result_v2')

            ->where('audit_id', $row->audit_id)

            ->where('parameter_id', $row->parameter_id)

            ->distinct('parameter_index')

            ->count('parameter_index');


        if ($parameterCount > 1) {

            $parameterName =
                $row->parameter_name . ' ' . $row->parameter_index;
        }


        return [

            $row->audit_id,

            $row->agency_name,

            $row->agency_location,

            $row->agency_address,

            $row->location,

            $row->campus_type,

            // $row->brand,

            $row->pillar,

            $row->touch_point,

            $parameterName,

            $row->sub_parameter_name,

            $row->details,

            $row->option_selected,

            $row->remark,

            $row->scorable,

            $row->score,

            $row->compliance_experience
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Excel Headings
    |--------------------------------------------------------------------------
    */

    public function headings(): array
    {
        return [

            'Audit ID',

            'Agency Name',

            'Agency Location',

            'Agency Address',

            'Location',

            'Campus Type',

            // 'Brand',

            'Pillar',

            'Touch Point',

            'Parameter',

            'Sub Parameter',

            'Points to be Checked',

            'Observation',

            'Remark',

            'Scorable',

            'Score',

            'Compliance/Experience'
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Excel Styling
    |--------------------------------------------------------------------------
    */

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $lastColumn = $sheet->getHighestColumn();

                $lastRow = $sheet->getHighestRow();


                /*
                |--------------------------------------------------------------------------
                | Header Style
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A1:' . $lastColumn . '1')

                    ->getFont()

                    ->setBold(true);


                /*
                |--------------------------------------------------------------------------
                | Header Background
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A1:' . $lastColumn . '1')

                    ->getFill()

                    ->setFillType(
                        \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID
                    )

                    ->getStartColor()

                    ->setARGB('FFF2CC');


                /*
                |--------------------------------------------------------------------------
                | Alignment
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A1:' . $lastColumn . $lastRow)

                    ->getAlignment()

                    ->setWrapText(true);


                // Horizontal Center

                $sheet->getStyle('A1:' . $lastColumn . $lastRow)

                    ->getAlignment()

                    ->setHorizontal(
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                    );


                // Vertical Center

                $sheet->getStyle('A1:' . $lastColumn . $lastRow)

                    ->getAlignment()

                    ->setVertical(
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                    );


                /*
                |--------------------------------------------------------------------------
                | Column Width
                |--------------------------------------------------------------------------
                */

                foreach ($sheet->getColumnIterator() as $column) {

                    $sheet->getColumnDimension(
                        $column->getColumnIndex()
                    )->setWidth(40);
                }


                /*
                |--------------------------------------------------------------------------
                | Borders
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A1:' . $lastColumn . $lastRow)

                    ->applyFromArray([

                        'borders' => [
 
                            'allBorders' => [

                                'borderStyle' =>
                                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,

                                'color' => [
                                    'argb' => '000000'
                                ],
                            ],
                        ],
                    ]);
            },
        ];
    }
}