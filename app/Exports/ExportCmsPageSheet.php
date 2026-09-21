<?php

namespace App\Exports;

use App\Model\CmsPage;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportCmsPageSheet implements FromArray, WithHeadings, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return [
            
            'Name',
            'Title',
            'File Path',
            'Status',
            'Created At'

        ];
    }

    public function array(): array
    {
        $data = CmsPage::query()
            ->orderBy('id')
            ->get();

        $final = [];

        foreach ($data as $cmsPage) {
            $final[] = [
            
                'Name' => $cmsPage->name,
                'Title' => $cmsPage->title,
                'File Path' => $cmsPage->file_path,
                'Status' => $cmsPage->status,
                'Created At' => $cmsPage->created_at
            ];
        }
        return $final;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
