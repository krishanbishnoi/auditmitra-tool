<?php

namespace App\Exports;

use DB;
use Auth;
use Carbon\Carbon;
use App\Audit;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
// use App\Model\Allocation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AllocationExport implements FromArray, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
   public function array(): array
    {
        $user = Auth::user();

        $query = Audit::with([
            'qmsheet',
            'product',
            'branch.city.state',
            'branch.branchable',
            'yard.branch.city.state',
            'agency.branch.city.state',
            'qa_qtl_detail',
            'user',
        ])
            ->where('status', '<=', 1);

        /*
         * Super Admin
         * ----------------
         * Export all clients
         */
        if (!$user->hasRole('Super Admin')) {

            // Client-wise restriction
            $query->where('client_id', $user->client_id);

            // Quality Auditor restriction
            if ($user->hasRole('Quality Auditor')) {
                $query->where('audited_by_id', $user->id);
            }
        }

        $data = $query
            ->orderBy('id', 'desc')
            ->limit(500)
            ->get();

        return $data->map(function ($row) {

            $name = '';
            $agencyCode = '';
            $state = '';

            /*
             * Get Name, Agency Code and State
             * based on Sheet Type
             */
            switch ($row->qmsheet->type ?? '') {

                case 'agency':
                    $name = $row->agency->name ?? '';
                    $agencyCode = $row->agency->agency_id ?? '';
                    $state = $row->agency->stateName->name ?? '';
                    break;

                case 'branch':
                    $state = $row->branch->stateName->name ?? '';
                    break;

                case 'yard':
                    $name = $row->yard->name ?? '';
                    $state = $row->yard->stateName->name ?? '';
                    break;

                case 'branch_repo':
                    $name = $row->branchRepo->name ?? '';
                    $state = $row->branchRepo->stateName->name ?? '';
                    break;

                case 'agency_repo':
                    $name = $row->agencyRepo->name ?? '';
                    $state = $row->agencyRepo->stateName->name ?? '';
                    break;

                case 'yard_repo':
                    $name = $row->yardRepo->name ?? '';
                    $state = $row->yardRepo->stateName->name ?? '';
                    break;
            }

            return [
                '00' . $row->id,

                // // Audit Type
                // $row->virtual_audit == 1
                //     ? 'Virtual'
                //     : 'Physical',

                // Month
                $row->created_at
                    ? Carbon::parse($row->created_at)->format("M'y")
                    : '',

                // Audit Date
                $row->created_at ?? '',

                // Lob
                ucfirst($row->qmsheet->lob ?? ''),

                // Location
                ucfirst($row->agency_location ?? ''),

                // State
                ucfirst(strtolower($state)),

                // Product
                $row->product->name ?? '',

                // Sheet Type
                ucfirst($row->qmsheet->type ?? ''),

                // Agency Name
                ucwords(strtolower($name)),

                // Agency Code
                $agencyCode,

                // Collection Manager
                $row->user->name ?? '',

                // Collection Manager Email
                $row->user->email ?? '',

                // Auditor Name
                $row->qa_qtl_detail->name ?? '',

                // Visited Date & Time
                $row->created_at ?? '',

                // Status
              $row->status == 1 ? 'Submited' : 'Saved',
];
        
        })
        ->values()
        ->toArray();
    }
    public function headings(): array
    {
        return [
            'Audit Id',
            'Month',
            'Audit Date',
            'Lob',
            'Agency Location',
            'State',
            'Product',
            'Sheet Type',
            'Agency Name',
            'Agency Code',
            'Collection Manager',
            'Collection Manager Email',
            'Auditor Name',
            'Visited Date & Time',
            'Status'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return
            [
                1 => ['font' => ['bold' => true]],
            ];
    }
}
