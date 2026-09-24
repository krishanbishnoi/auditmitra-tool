<?php

namespace App\Exports;

use DB;
Use Auth;
use Carbon\Carbon;
Use App\Audit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportSendClosureAudit implements FromCollection, WithHeadings, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array 
    {
         return 
      [
        'Audit ID',
        'Month',
        'Audit Date',
        'Lob',
        'State',
        'Location',
        'Product',
        'Audit Type',
        'Location Name',
        'Location Code',
        'Collection Manager',
        'Collection Manager Email',
        'Auditor Name',
        'Last Modified Date',
        'Unsatisfactory Parameter Count',
        'Closure Status'
      ];
    }

   public function collection()
    {
        $user = Auth::user();
        $userRole = $user->roles()->first()->name;

        $query = DB::table('closure_audits')
            ->join('audits', 'closure_audits.audit_id', '=', 'audits.id')
            ->select(
                'closure_audits.id as closure_id',
                'closure_audits.status as closure_status',
                'audits.id'
            )
            ->where('closure_audits.status', 0);


        if ($userRole == 'Super Admin') {
            // All clients
            $query->where('closure_audits.audit_id', '!=', null);
        }
        else {
            $query->where(
                'closure_audits.client_id',
                $user->client_id
            );
        }
        $rows = $query
            ->orderBy('audits.audit_date_by_aud', 'desc')
            ->get();

         return $rows->map(function ($row) {

            $audit = Audit::with([
                'qmsheet',
                'product',
                'agency',
                'agency.city.state',
                'user',
                'qa_qtl_detail'
            ])->find($row->id);

            if (!$audit) {
                return [];
            }

            $state = '';

            if ($audit->agency && $audit->agency->state) {
                $state = DB::table('states')
                    ->where('id', $audit->agency->state)
                    ->value('name');
            }

            $unsatisfactoryCount = DB::table('audit_results')
                ->where('audit_id', $audit->id)
                ->where('option_selected', 'Unsatisfactory')
                ->count();

            $closureStatus = 'Pending';

            if ($row->closure_status == 1) {
                $closureStatus = 'Approved';
            } elseif ($row->closure_status == 2) {
                $closureStatus = 'Rejected';
            }

            return [
                'Audit ID' => '00' . $audit->id,

                'Month' => $audit->created_at
                    ? Carbon::parse($audit->created_at)->format("M'y")
                    : '',

                'Audit Date' => $audit->created_at ?? '',

                'Lob' => $audit->qmsheet->lob ?? '',

                'State' => $state ?? '',

                'Location' => $audit->agency_location ?? '',

                'Product' => $audit->product->name ?? '',

                'Audit Type' => $audit->qmsheet->type ?? '',

                'Agency Name' => $audit->agency->name ?? '',

                'Agency Code' => $audit->agency->agency_id ?? '',

                'Collection Manager' => $audit->user->name ?? '',

                'Collection Manager Email' => $audit->user->email ?? '',

                'Auditor Name' => $audit->qa_qtl_detail->name ?? '',

                'Last Modified Date' => $audit->updated_at ?? '',

                'Unsatisfactory Parameters Count' => $unsatisfactoryCount,

                'Closure Status' => $closureStatus,
            ];
        });
    }

    public function styles(Worksheet $sheet)
    {
        return
            [
                1 => ['font' => ['bold' => true]],
            ];
    }
}
