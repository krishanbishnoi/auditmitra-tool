<?php

namespace App\Imports;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;
//use App\Model\Agency;
use App\Model\AuditorAssign;
use App\Model\AuditAllocation;
use App\User;
use App\Agency;
use App\Branch;
use App\AgencyRepo;
use App\BranchRepo;
use App\Yard;
use App\YardRepo;
use App\Audit;
use App\AuditCycle;
use App\Model\Products;
use App\Model\Productattribute;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use auth;

class AuditorAssignImport implements ToModel, WithHeadingRow, WithUpserts
{
    
    protected $userId;
    protected $client_id;
    protected $duplicates = [];

   
    public function __construct($userId)
    {
        $this->userId = $userId;
         $this->client_id = Auth::user()->client_id;
    }

    public function model(array $row)
    {
        
        // V Validate that process_review_agency_email is n  blank
        $requiredFields = [
            'agency_id', 'final_agency_name', 'agency_code', 'sub_product_id',
            'sub_product', 'product_id', 'product', 'location', 'state',
            'region', 'process_review_agency', 'process_review_agency_email',
            'process_review_period', 'agency_address', 'contact', 'auditor_email', 'agency_email',
            'audit_date'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($row[$field]) || trim($row[$field]) === '') {
                throw new \Exception("The field '$field' is missing or empty.");
            }
        }


        //V Update AuditAllocation.assign_status if id in the Excel row matches AuditAllocation.id
        if (!empty($row['id'])) {
            $allocation = AuditAllocation::find($row['id']);
            if ($allocation) {
                $allocation->assign_status = 1;
                $allocation->save();
            }
        }


        // Check audit cycle match
        $processReviewAgencyEmail = User::where('email', $row['process_review_agency_email'] ?? null)
        ->first();
        if (!$processReviewAgencyEmail) {
            throw new \Exception("No matching process review agency email found: {$row['process_review_agency_email']}");
        }


        // Check audit cycle match
        $auditorEmail = User::where('client_id',  $this->client_id)->where('email', $row['auditor_email'] ?? null)
        ->first();
        if (!$auditorEmail) {
            throw new \Exception("No matching auditor email found: {$row['auditor_email']}");
        }



        // Check if process_review_agency_email exists in the users table and get user ID
        $processReviewAgencyEmail = $row['process_review_agency_email'];
        $user = User::where('email', $processReviewAgencyEmail)->first();
        $processReviewAgencyId = $user ? $user->id : null;


        $currentMonth = Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');

        $auditorAssign = AuditorAssign::where('agency_code', $row['agency_code'])
        ->whereYear('created_at', $currentYear)
        ->whereMonth('created_at', $currentMonth)
        ->where('status', 1)
        ->first();

        $auditDate = isset($row['audit_date']) ? $this->transformDate($row['audit_date']) : null;



        // V Check record exists in the audits table with the same details
        $product = Products::where('name', $row['product'] ?? null)
        ->first();

        if($row['type_of_agency'] === 'agency'){
            $agency = Agency::where('agency_id', $row['agency_code'])->first();
            $auditExists = Audit::where('agency_id',$agency->id)
        // ->where('agency_code', $agencyCode)
        //->where('location', $location)
        ->where('product_id', $product->id)
        ->where('audit_cycle_id', $row['process_review_period'])
        // ->where('audit_cycle_id', $process_review_period)
        ->exists();
        }elseif($row['type_of_agency'] === 'branch'){
            $branch = Branch::where('agency_id', $row['agency_code'])->first();
            $auditExists = Audit::where('branch_id',$branch->id)
        // ->where('agency_code', $agencyCode)
        //->where('location', $location)
        ->where('product_id', $product->id)
        ->where('audit_cycle_id', $row['process_review_period'])
        // ->where('audit_cycle_id', $process_review_period)
        ->exists();
        }elseif($row['type_of_agency'] === 'agencyrepo'){
            $agencyrepo = AgencyRepo::where('agency_id', $row['agency_code'])->first();
            $auditExists = Audit::where('agency_repo_id',$agencyrepo->id)
        // ->where('agency_code', $agencyCode)
        //->where('location', $location)
        ->where('product_id', $product->id)
        // ->where('audit_cycle_id', $process_review_period)
        ->exists();
        }elseif($row['type_of_agency'] === 'branchrepo'){
            $branchrepo = BranchRepo::where('agency_id', $row['agency_code'])->first();
            $auditExists = Audit::where('branch_repo_id',$branchrepo->id)
        // ->where('agency_code', $agencyCode)
        //->where('location', $location)
        ->where('product_id', $product->id)
        // ->where('audit_cycle_id', $process_review_period)
        ->exists();
        }elseif($row['type_of_agency'] === 'yard'){
            $yard = Yard::where('agency_id', $row['agency_code'])->first();
            $auditExists = Audit::where('yard_id',$yard->id)
        // ->where('agency_code', $agencyCode)
        //->where('location', $location)
        ->where('product_id', $product->id)
        // ->where('audit_cycle_id', $process_review_period)
        ->exists();
        }elseif($row['type_of_agency'] === 'yardrepo'){
            $yardrepo = YardRepo::where('agency_id', $row['agency_code'])->first();
            $auditExists = Audit::where('yard_repo_id',$yardrepo->id)
        // ->where('agency_code', $agencyCode)
        //->where('location', $location)
        ->where('product_id', $product->id)
        // ->where('audit_cycle_id', $process_review_period)
        ->exists();
        }
        // $agency = Agency::where('agency_id', $row['agency_code'])->first();
        // $auditExists = Audit::where('agency_id',$agency->id)
        // // ->where('agency_code', $agencyCode)
        // //->where('location', $location)
        // ->where('product_id', $product->id)
        // // ->where('audit_cycle_id', $process_review_period)
        // ->exists();

        if ($auditExists) {
           // Redirect back with an error if an audit has been performed
           throw new \Exception("Error: An audit has been performed on this allocation assign. You cannot Update it.");
        }
        

        if ($auditorAssign) {
        $auditorAssign->update([
            'user_id' => $this->userId,
            'client_id' => $this->client_id,
            'allocation_id'=>$row['id'] ?? null,
            'agency_id' => $row['agency_id'] ?? null,
            'final_agency_name' => $row['final_agency_name'] ?? null,
            'agency_code' => $row['agency_code'] ?? null,
            'type_of_agency' => $row['type_of_agency'] ?? null,
            'sub_product_id' => $row['sub_product_id'] ?? null,
            'sub_product' => $row['sub_product'] ?? null,
            'product_id' => $row['product_id'] ?? null,
            'product' => $row['product'] ?? null,
            'location' => $row['location'] ?? null,
            'state' => $row['state'] ?? null,
            'region' => $row['region'] ?? null,
            'process_review_agency' => $row['process_review_agency'] ?? null,
            'process_review_agency_id' => $processReviewAgencyId,
            'process_review_agency_email' => $row['process_review_agency_email'] ?? null,
            'process_review_period' => $row['process_review_period'] ?? null,
            'audit_cycle_id' => $row['audit_cycle_id'] ?? null,
            'agency_address' => $row['agency_address'] ?? null,
            'contact' => $row['contact'] ?? null,
            'agency_email' => $row['agency_email'] ?? null,
            'auditor_name' => $row['auditor_name'] ?? null,
            'auditor_email' => $row['auditor_email'] ?? null,
            //'audit_date' => $row['audit_date'] ?? null,
            'audit_date' => $auditDate,
        ]);
        return $auditorAssign;
        } else {
            // If no existing record, create a new one
            return AuditorAssign::create([
                'user_id' => $this->userId,
                'client_id' => $this->client_id,
                'allocation_id'=>$row['id'] ?? null,
                'agency_id' => $row['agency_id'] ?? null,
                'final_agency_name' => $row['final_agency_name'] ?? null,
                'agency_code' => $row['agency_code'] ?? null,
                'type_of_agency' => $row['type_of_agency'] ?? null,
                'sub_product_id' => $row['sub_product_id'] ?? null,
                'sub_product' => $row['sub_product'] ?? null,
                'product_id' => $row['product_id'] ?? null,
                'product' => $row['product'] ?? null,
                'location' => $row['location'] ?? null,
                'state' => $row['state'] ?? null,
                'region' => $row['region'] ?? null,
                'process_review_agency' => $row['process_review_agency'] ?? null,
                'process_review_agency_id' => $processReviewAgencyId,
                'process_review_agency_email' => $row['process_review_agency_email'] ?? null,
                'process_review_period' => $row['process_review_period'] ?? null,
                'audit_cycle_id' => $row['audit_cycle_id'] ?? null,
                'agency_address' => $row['agency_address'] ?? null,
                'contact' => $row['contact'] ?? null,
                'agency_email' => $row['agency_email'] ?? null,
                'auditor_name' => $row['auditor_name'] ?? null,
                'auditor_email' => $row['auditor_email'] ?? null,
                //'audit_date' => $row['audit_date'] ?? null,
                'audit_date' => $auditDate,
            ]);
        }
    }

    private function transformDate($value)
{
    if (empty($value)) {
        throw new \Exception("Audit date is required.");
    }

    // Ensure the input matches dd-mm-yyyy strictly
    if (!preg_match('/^(0[1-9]|[12][0-9]|3[01])-(0[1-9]|1[0-2])-(\d{4})$/', $value)) {
        throw new \Exception("Invalid date format: '{$value}'. Required format is dd-mm-yyyy.");
    }

    try {
        // Convert to Y-m-d format
        return \Carbon\Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
    } catch (\Exception $e) {
        throw new \Exception("Invalid date value: '{$value}'.");
    }
}

    public function uniqueBy()
    {
        return 'agency_code';
    }

}

