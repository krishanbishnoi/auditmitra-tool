<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;
use App\Model\Branch;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use App\Model\Branchable;
// use Override;

class BranchExport implements FromArray, WithHeadings, WithStyles
{
    // public function getReportingManager($branch_id,$product){
    //     // $ids=Branchable::where(['branch_id'=>$branch_id,'product_id'=>$product])->get()->pluck('manager_id');
    //     $ids=Branchable::with('user')->where(['branch_id'=>$branch_id,'product_id'=>$product])->get();
    //     $email='';
    //     $degination='';
    //     if($ids->where('type','Area_Collection_Manager')->count()>0){
    //         $user=$ids->where('type','Area_Collection_Manager')->first();
    //         $email=$user->user->name ?? '';
    //         $degination='Area Collection Manager';
    //     }
    //     elseif($ids->where('type','Regional_Collection_Manager')->count()>0){
    //         $user=$ids->where('type','Regional_Collection_Manager')->first();
    //         $email=$user->user->name ?? '';
    //         $degination='Regional Collection Manager';
    //     }
    //     elseif($ids->where('type','Zonal_Collection_Manager')->count()>0){
    //         $user=$ids->where('type','Zonal_Collection_Manager')->first();
    //         $email=$user->user->name ?? '';
    //         $degination='Zonal Collection Manager';
    //     }
    //     elseif($ids->where('type','National_Collection_Manager')->count()>0){
    //         $user=$ids->where('type','National_Collection_Manager')->first();
    //         $email=$user->user->name ?? '';
    //         $degination='National Collection Manager';
    //     }
    //     elseif($ids->where('type','Group_Product_Head')->count()>0){
    //         $user=$ids->where('type','Group_Product_Head')->first();
    //         $email=$user->user->name ?? '';
    //         $degination='Group Product Head';
    //     }
    //     else{
    //         $user=$ids->where('type','Head_of_the_Collections')->first();
    //         $email=$user->user->name ?? '';
    //         $degination='Head of the Collections';
    //     }
    //     // dd($degination,$email);
    //     return ['name'=>$email,'degination'=>$degination];
    // }
    // public function array(): array
    // {
    //     //
    //     $data=Branch::with(['branchable'=>function($q){
    //         $q->with('product')->where('type','Collection_Manager');
    //     },'city.state.region'])->get();
    //     // dd($data->first());
    //     $final=[];
    //     foreach($data as $item){
    //         foreach($item->branchable as $val){
    //             $report=$this->getReportingManager($item->id,$val->product_id);
    //             $final[]=[
    //                 'LOB'=>$item->lob,
    //                 'Zone'=>$item->city->state->region->name ?? '',
    //                 'Branch'=>$item->name,
    //                 'Product'=>$val->product->name ?? '',
    //                 'Bucket'=>$val->bucket,
    //                 'Collection_Manager_Name'=>$val->user->name ?? '',
    //                 'Branch_Location'=>$item->location,
    //                 'Branch_Address'=>$item->location,
    //                 'Reporting_manager'=>$report['name'],
    //                 'Designation'=>$report['degination']
    //             ];
    //         }
    //     }
    //     return $final;
    // }
    // public function headings(): array
    // {
    //     return [
    //         'LOB',
    //         'Zone',
    //         'Branch',
    //         'Product',
    //         'Bucket',
    //         'Collection Manager Name',
    //         'Branch Location',
    //         'Branch Address',
    //         'Reporting Manager',
    //          'Designation',
    //     ];
    // }


    public function headings(): array
    {
        return
            [
                'Branch Name',
                'Branch ID',
                'Branch Manager',
                'Location',
                'Address',
                'Created At'
            ];
    }

    public function array(): array
{

    $data = Branch::where('client_id', auth()->user()->client_id)
        ->where('status', 0)
        ->orderBy('id')
        ->get();


        //    dd($data->toArray());

    $final = [];

    foreach ($data as $branchData) {
        $final[] = [
            'Branch Name'    => $branchData->name,
            'Branch ID'      => $branchData->agency_id,
            'Branch Manager' => $branchData->agency_manager ?? 'N/A',
            'Location'       => $branchData->location ?? 'N/A',
            'Address'        => $branchData->address ?? 'N/A',
            'Created At'     => $branchData->created_at
                                ? $branchData->created_at->format('Y-m-d H:i:s')
                                : 'N/A',
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
