<?php

namespace App\Imports;

use App\Model\AuditAllocation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use App\Agency;
use App\Branch;
use App\AgencyRepo;
use App\BranchRepo;
use App\Yard;
use App\YardRepo;
use App\Model\Products;
use App\Model\Productattribute;
use App\User;
use App\Model\AgencyMobileEmail;
use App\Model\BranchMobileEmail;
use App\Model\AgencyRepoMobileEmail;
use App\Model\BranchRepoMobileEmail;
use App\Model\YardMobileEmail;
use App\Model\YardRepoMobileEmail;
use App\Model\Region;
use App\Model\State;
use App\Model\City;
use App\AuditCycle;
use App\Audit;

class AuditAllocationImport implements ToModel, WithHeadingRow
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function model(array $row)
    {
              

    // Check required fields
    if (
        !isset($row['final_agency_name']) || trim($row['final_agency_name']) === '' ||
        !isset($row['agency_code']) || trim($row['agency_code']) === '' ||
        !isset($row['type']) || trim($row['type']) === '' ||
        !isset($row['sub_product']) || trim($row['sub_product']) === '' ||
        !isset($row['product']) || trim($row['product']) === '' ||
        !isset($row['location']) || trim($row['location']) === '' ||
        !isset($row['state']) || trim($row['state']) === '' ||
        !isset($row['region']) || trim($row['region']) === '' ||
        !isset($row['process_review_agency']) || trim($row['process_review_agency']) === '' ||
        !isset($row['process_review_agency_email']) || trim($row['process_review_agency_email']) === '' ||
        !isset($row['process_review_period']) || trim($row['process_review_period']) === '' ||
        !isset($row['agency_address']) || trim($row['agency_address']) === '' ||
        !isset($row['contact']) || trim($row['contact']) === '' ||
        !isset($row['agency_email']) || trim($row['agency_email']) === ''
    ) {
        throw new \Exception('One or more required fields are missing or empty.');
    }

        $loggedInUserClientId = auth()->user()->client_id;

    // Check audit cycle match
        $processReviewAgencyEmail = User::where('client_id', $this->userId)->where('email', $row['process_review_agency_email'] ?? null)
        ->first();
        if (!$processReviewAgencyEmail) {
            throw new \Exception("No matching process review agency email found: {$row['process_review_agency_email']}");
        }

        // Check audit cycle match
        $product = Products::where('client_id', $this->userId)->where('name', $row['product'] ?? null)
        ->first();
        if (!$product) {
            throw new \Exception("No matching product found: {$row['product']}");
        }


        // Validate process_review_period
        if (isset($row['process_review_period']) && !preg_match("/^[A-Za-z]{3}'\d{2}$/", $row['process_review_period'])) {
            throw new \Exception("Error: Invalid process review period format: {$row['process_review_period']}. Expected format: Mon'YY (e.g., Oct'24).");
        }

        // Step 1: Check if the product exists. If not, create it.
        // $product = Products::firstOrCreate(
        //     ['name' => $row['product']],
        //     [
        //         'type' => 1,
        //         'bucket' => 'default',
        //         'is_recovery' => 0,
        //         'capacity' => 'standard',
        //         'status' => 0,
        //     ]
        // );

        // Step 2: Handle sub-products, linking them to the product
        $subProductNames = explode(',', $row['sub_product']); // Split sub-products by comma if multiple
        $subProductIds = [];

        foreach ($subProductNames as $subProductName) {
            $subProduct = Productattribute::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'product_attribute_name' => trim($subProductName),
                ],
                [
                    'bucket' => 'default',
                    'type' => 1,
                    'is_recovery' => 0,
                    'status' => 0,
                ]
            );
            $subProductIds[] = $subProduct->id;
        }

        $subProductIdsString = implode(',', $subProductIds); // Join IDs as a comma-separated string
        $data =[
                'name' => $row['final_agency_name'],
                'product_id' => $product->id, // Link the product ID
                'sub_product_id' => $subProductIdsString, // Save sub-product IDs as comma-separated values
                'location' => $row['location'] ?? null,
                'region_id' => $this->getRegionId($row['region']),
                'state' => $this->getStateId($row['state']),
                'city_id' => $this->getCityId($row['location']),
                'address' => $row['agency_address'] ?? null,
                'status' => 0,
                'client_id' => $this->userId,
        ];
        $agency = Agency::where('agency_id', $row['agency_code'])->first();
        $branch = Branch::where('agency_id', $row['agency_code'])->first();
        $agencyrepo = BranchRepo::where('agency_id', $row['agency_code'])->first();
        $branchrepo = AgencyRepo::where('agency_id', $row['agency_code'])->first();
        $yard = Yard::where('agency_id', $row['agency_code'])->first();
        $yardrepo = YardRepo::where('agency_id', $row['agency_code'])->first();

        // Step 3: Check if the agency exists, and create it if it doesn't, associating it with the product and sub-product IDs
        if ($row['type'] === 'agency') {
            // $agency = Agency::where('agency_id', $row['agency_code'])->first();
            $type = 'agency';
            if ($agency) {
                // Update agency status if it already exists
                if ($agency->status == 1) {
                    $agency->status = 0;
                }
                $agency->sub_product_id = $subProductIdsString;
                $agency->save();
            } else {
                $data['agency_id'] = $row['agency_code'];
                $agency = Agency::create($data);
                // Create new agency if not found, associating it with the product and sub-product IDs
            }
            $this->handleContacts($agency->id, $row['contact'] ?? null, $row['agency_email'] ?? null , $type);

        } elseif ($row['type'] === 'branch') {
            // $branch = Branch::where('agency_id', $row['agency_code'])->first();
            $type = 'branch';
            if ($branch) {
                // Update agency status if it already exists
                if ($branch->status == 1) {
                    $branch->status = 0;
                }
                $branch->sub_product_id = $subProductIdsString;
                $branch->save();
            } else {
                $data['agency_id'] = $row['agency_code'];
                $branch = Branch::create($data);
                // Create new agency if not found, associating it with the product and sub-product IDs
            }
            $this->handleContacts($branch->id, $row['contact'] ?? null, $row['agency_email'] ?? null , $type);
             
        }
        elseif ($row['type'] === 'branchrepo') {
            // $branch = Branch::where('agency_id', $row['agency_code'])->first();
            $type = 'branchrepo';
            if ($branchrepo) {
                // Update agency status if it already exists
                if ($branchrepo->status == 1) {
                    $branchrepo->status = 0;
                }
                $branchrepo->sub_product_id = $subProductIdsString;
                $branchrepo->save();
            } else {
                $data['agency_id'] = $row['agency_code'];
                $branchrepo = BranchRepo::create($data);
                // Create new agency if not found, associating it with the product and sub-product IDs
            }
            $this->handleContacts($branchrepo->id, $row['contact'] ?? null, $row['agency_email'] ?? null , $type);
             
        }
        elseif ($row['type'] === 'agencyrepo') {
            // $branch = Branch::where('agency_id', $row['agency_code'])->first();
            $type = 'agencyrepo';
            if ($agencyrepo) {
                // Update agency status if it already exists
                if ($agencyrepo->status == 1) {
                    $agencyrepo->status = 0;
                }
                $agencyrepo->sub_product_id = $subProductIdsString;
                $agencyrepo->save();
            } else {
                $data['agency_id'] = $row['agency_code'];
                $agencyrepo = AgencyRepo::create($data);
                // Create new agency if not found, associating it with the product and sub-product IDs
            }
            $this->handleContacts($agencyrepo->id, $row['contact'] ?? null, $row['agency_email'] ?? null , $type);
             
        }
        elseif ($row['type'] === 'yard') {
            // $branch = Branch::where('agency_id', $row['agency_code'])->first();
            $type = 'yard';
            if ($yard) {
                // Update agency status if it already exists
                if ($yard->status == 1) {
                    $yard->status = 0;
                }
                $yard->sub_product_id = $subProductIdsString;
                $yard->save();
            } else {
                $data['agency_id'] = $row['agency_code'];
                $yard = Yard::create($data);
                // Create new agency if not found, associating it with the product and sub-product IDs
            }
            $this->handleContacts($yard->id, $row['contact'] ?? null, $row['agency_email'] ?? null , $type);
             
        }
        elseif ($row['type'] === 'yardrepo') {
            // $branch = Branch::where('agency_id', $row['agency_code'])->first();
            $type = 'yardrepo';
            if ($yardrepo) {
                // Update agency status if it already exists
                if ($yardrepo->status == 1) {
                    $yardrepo->status = 0;
                }
                $yardrepo->sub_product_id = $subProductIdsString;
                $yardrepo->save();
            } else {
                $data['agency_id'] = $row['agency_code'];
                $yardrepo = YardRepo::create($data);
                // Create new agency if not found, associating it with the product and sub-product IDs
            }
            $this->handleContacts($yardrepo->id, $row['contact'] ?? null, $row['agency_email'] ?? null , $type);
             
        }

        // Step 4: Handle agency contacts
        // $this->handleContacts($agency->id, $row['contact'] ?? null, $row['agency_email'] ?? null);

        // Step 5: Get user ID for process review
        $user_id = User::where('email', 
        $row['process_review_agency_email'])->first();

        // Current month and year
        $currentMonth = Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');

        
        //Check audit cycle match
        $auditCycle = AuditCycle::where('client_id', $this->userId)->where('name', $row['process_review_period'] ?? null)
        ->where('status', 1)
        ->first();
        if (!$auditCycle) {
            throw new \Exception("Invalid audit cycle found or Deactivated for process review period: {$row['process_review_period']}");
        }


        //Check State Name Match
        $stateName = State::where('name', $row['state'] ?? null)
        ->first();
        if (!$stateName) {
            throw new \Exception("No matching State Name found: {$row['state']}");
        }


        //Check Location as City Name Match
        $locationName = City::where('name', $row['location'] ?? null)
        ->first();
        if (!$locationName) {
            throw new \Exception("No matching Location Name found: {$row['location']}");
        }



        // Step 6: Check if audit allocation exists for the current month and process_review_period -- okay
        // $allocation = AuditAllocation::where('agency_code', $branch->agency_id)
        // ->where('process_review_period', $row['process_review_period'] ?? null)
        // ->first();


        if ($row['type'] === 'agency') {
             $allocation = AuditAllocation::where('agency_code', $agency->agency_id)
        ->where('process_review_period', $row['process_review_period'] ?? null)
        ->first();

         $auditExists = Audit::where('agency_id',$agency->id)
        // ->where('agency_code', $agencyCode)
        //->where('location', $location)
        ->where('product_id', $product->id)
        ->where('audit_cycle_id', $row['process_review_period'])
        ->exists();

        }elseif ($row['type'] === 'branch') {
            $allocation = AuditAllocation::where('agency_code', $branch->agency_id)
        ->where('process_review_period', $row['process_review_period'] ?? null)
        ->first();

         $auditExists = Audit::where('branch_id',$branch->id)
        // ->where('agency_code', $agencyCode)
        //->where('location', $location)
        ->where('product_id', $product->id)
        // ->where('audit_cycle_id', $process_review_period)
        ->exists();
        }elseif ($row['type'] === 'branchrepo') {
            $allocation = AuditAllocation::where('agency_code', $branchrepo->agency_id)
        ->where('process_review_period', $row['process_review_period'] ?? null)
        ->first();

         $auditExists = Audit::where('branch_repo_id',$branchrepo->id)
        // ->where('agency_code', $agencyCode)
        //->where('location', $location)
        ->where('product_id', $product->id)
        // ->where('audit_cycle_id', $process_review_period)
        ->exists();
        }elseif ($row['type'] === 'agencyrepo') {
            $allocation = AuditAllocation::where('agency_code', $agencyrepo->agency_id)
        ->where('process_review_period', $row['process_review_period'] ?? null)
        ->first();

         $auditExists = Audit::where('agency_repo_id',$agencyrepo->id)
        // ->where('agency_code', $agencyCode)
        //->where('location', $location)
        ->where('product_id', $product->id)
        // ->where('audit_cycle_id', $process_review_period)
        ->exists();
        }elseif ($row['type'] === 'yard') {
            $allocation = AuditAllocation::where('agency_code', $yard->agency_id)
        ->where('process_review_period', $row['process_review_period'] ?? null)
        ->first();

         $auditExists = Audit::where('yard_id',$yard->id)
        // ->where('agency_code', $agencyCode)
        //->where('location', $location)
        ->where('product_id', $product->id)
        // ->where('audit_cycle_id', $process_review_period)
        ->exists();
        }elseif ($row['type'] === 'yardrepo') {
            $allocation = AuditAllocation::where('agency_code', $yardrepo->agency_id)
        ->where('process_review_period', $row['process_review_period'] ?? null)
        ->first();

         $auditExists = Audit::where('yard_repo_id',$yardrepo->id)
        // ->where('agency_code', $agencyCode)
        //->where('location', $location)
        ->where('product_id', $product->id)
        // ->where('audit_cycle_id', $process_review_period)
        ->exists();
        }


        // R Check if any record exists in the audits table with the same details
        // $auditExists = Audit::where('agency_id',$branch->id)
        // // ->where('agency_code', $agencyCode)
        // //->where('location', $location)
        // ->where('product_id', $product->id)
        // // ->where('audit_cycle_id', $process_review_period)
        // ->exists();

        if ($auditExists) {
           // Redirect back with an error if an audit has been performed
           throw new \Exception("Error: An audit has been performed on this allocation. You cannot Update it.");
        }
        $regionId = $this->getRegionId($row['region']);
        $stateId = $this->getStateId($row['state'], $regionId);
        $cityId  = $this->getCityId($row['location'], $stateId, $row['state']);
        //  return new AuditAllocation([
        //         'user_id' => $this->userId,
        //         'client_id' => $this->userId,
        //         'agency_id' => $branch->id,
        //         'final_agency_name' => $row['final_agency_name'] ?? null,
        //         'agency_code' => $branch->branch_id,
        //         'type_of_agency' => $row['type'] ?? null,
        //         'sub_product_id' => $subProductIdsString,
        //         'sub_product' => $row['sub_product'] ?? null,
        //         'product_id' => $product->id,
        //         'product' => $row['product'] ?? null,
        //         'location' => $row['location'] ?? null,
        //         'city_id' => $cityId,
        //         'state' => $row['state'] ?? null,
        //         'state_id' => $stateId,
        //         'region' => $row['region'] ?? null,
        //         'region_id' => $regionId,
        //         'process_review_agency' => $row['process_review_agency'] ?? null,
        //         'process_review_agency_email' => $row['process_review_agency_email'] ?? null,
        //         'process_review_agency_id' => $user_id->id ?? null,
        //         'process_review_period' => $row['process_review_period'] ?? null,
        //         'audit_cycle_id' => $auditCycle->id,
        //         'agency_address' => $row['agency_address'] ?? null,
        //         'contact' => $row['contact'] ?? null,
        //         'agency_email' => $row['agency_email'] ?? null,
        //     ]);




        $allocateddata = [
                'user_id' => $this->userId,
                'client_id' => $this->userId,
                // 'agency_id' => $agency->id,
                'final_agency_name' => $row['final_agency_name'] ?? null,
                // 'agency_code' => $agency->agency_id,
                'type_of_agency' => $row['type'] ?? null,
                'sub_product_id' => $subProductIdsString,
                'sub_product' => $row['sub_product'] ?? null,
                'product_id' => $product->id,
                'product' => $row['product'] ?? null,
                'location' => $row['location'] ?? null,
                'city_id' => $cityId,
                'state' => $row['state'] ?? null,
                'state_id' => $stateId,
                'region' => $row['region'] ?? null,
                'region_id' => $regionId,
                'process_review_agency' => $row['process_review_agency'] ?? null,
                'process_review_agency_email' => $row['process_review_agency_email'] ?? null,
                'process_review_agency_id' => $user_id->id ?? null,
                'process_review_period' => $row['process_review_period'] ?? null,
                'audit_cycle_id' => $auditCycle->id,
                'agency_address' => $row['agency_address'] ?? null,
                'contact' => $row['contact'] ?? null,
                'agency_email' => $row['agency_email'] ?? null,
        ];

        // Step 7: Update or create audit allocation record

        if ($row['type'] === 'agency'){
        if ($allocation) {
            // Update existing allocation
            $allocateddata['agency_id'] =$agency->id;
             $allocateddata['agency_code'] = $agency->agency_id;
            $allocation->update($allocateddata);
        } 
             else {

                $allocateddata['agency_id'] =$agency->id;
             $allocateddata['agency_code'] = $agency->agency_id;
            return new AuditAllocation($allocateddata);
            // Create new audit allocation
            
        }}
        elseif ($row['type'] === 'branch'){
            if ($allocation) {
            // Update existing allocation
            $allocateddata['agency_id'] =$branch->id;
             $allocateddata['agency_code'] = $branch->agency_id;
            $allocation->update($allocateddata);
        } 
             else {

                $allocateddata['agency_id'] =$branch->id;
             $allocateddata['agency_code'] = $branch->agency_id;
            return new AuditAllocation($allocateddata);
            // Create new audit allocation
            
        }
        }elseif ($row['type'] === 'branchrepo'){
            if ($allocation) {
            // Update existing allocation
            $allocateddata['agency_id'] =$branchrepo->id;
             $allocateddata['agency_code'] = $branchrepo->agency_id;
            $allocation->update($allocateddata);
        } 
             else {

                $allocateddata['agency_id'] =$branchrepo->id;
             $allocateddata['agency_code'] = $branchrepo->agency_id;
            return new AuditAllocation($allocateddata);
            // Create new audit allocation
            
        }
        }elseif ($row['type'] === 'agencyrepo'){
            if ($allocation) {
            // Update existing allocation
            $allocateddata['agency_id'] =$agencyrepo->id;
             $allocateddata['agency_code'] = $agencyrepo->agency_id;
            $allocation->update($allocateddata);
        } 
             else {

                $allocateddata['agency_id'] =$agencyrepo->id;
             $allocateddata['agency_code'] = $agencyrepo->agency_id;
            return new AuditAllocation($allocateddata);
            // Create new audit allocation
            
        }
        }elseif ($row['type'] === 'yard'){
            if ($allocation) {
            // Update existing allocation
            $allocateddata['agency_id'] =$yard->id;
             $allocateddata['agency_code'] = $yard->agency_id;
            $allocation->update($allocateddata);
        } 
             else {

                $allocateddata['agency_id'] =$yard->id;
             $allocateddata['agency_code'] = $yard->agency_id;
            return new AuditAllocation($allocateddata);
            // Create new audit allocation
            
        }
        }elseif ($row['type'] === 'yardrepo'){
            if ($allocation) {
            // Update existing allocation
            $allocateddata['agency_id'] =$yardrepo->id;
             $allocateddata['agency_code'] = $yardrepo->agency_id;
            $allocation->update($allocateddata);
        } 
             else {

                $allocateddata['agency_id'] =$yardrepo->id;
             $allocateddata['agency_code'] = $yardrepo->agency_id;
            return new AuditAllocation($allocateddata);
            // Create new audit allocation
            
        }
        }



    }
    protected function handleContacts($agencyId, $contact, $agencyEmail, $type)
{
    // Map the type to the corresponding model class
    $modelClass = null;

    if ($type === 'agency') {
        $modelClass = AgencyMobileEmail::class;
    } elseif ($type === 'branch') {
        $modelClass = BranchMobileEmail::class;
    }elseif ($type === 'yardrepo') {
        $modelClass = YardRepoMobileEmail::class;
    } elseif ($type === 'branchrepo') {
        $modelClass = BranchRepoMobileEmail::class;
    } elseif ($type === 'agencyrepo') {
        $modelClass = AgencyRepoMobileEmail::class;
    } elseif ($type === 'yard') {
        $modelClass = YardMobileEmail::class;
    }  else {
        // Unknown type, optionally throw exception or just return
        return;
    }

    if (!empty($contact)) {
        $mobileNumbers = explode(',', $contact);
        $modelClass::where('agency_id', $agencyId)
            ->whereNotIn('mobile_number', $mobileNumbers)
            ->delete();

        foreach ($mobileNumbers as $mobileNumber) {
            $modelClass::updateOrCreate(
                ['agency_id' => $agencyId, 'mobile_number' => trim($mobileNumber)]
            );
        }
    }

    if (!empty($agencyEmail)) {
        $emails = explode(',', $agencyEmail);
        $modelClass::where('agency_id', $agencyId)
            ->whereNotIn('email', $emails)
            ->delete();

        foreach ($emails as $email) {
            $modelClass::updateOrCreate(
                ['agency_id' => $agencyId, 'email' => trim($email)]
            );
        }
    }
}





    protected function getRegionId($regionName)
{
    if (!empty($regionName)) {
        $region = Region::where('name', $regionName)->first();
        if (!$region) {
            throw new \Exception("Invalid Region: {$regionName}");
        }
        return $region->id;
    }
    return null;
}

protected function getStateId($stateName, $regionId = null)
{
    if (!empty($stateName)) {
        $query = State::where('name', $stateName);

        if ($regionId) {
            $query->where('region_id', $regionId);
        }

        $state = $query->first();
        if (!$state) {
            throw new \Exception("Invalid State: {$stateName} for Region ID: {$regionId}");
        }
        return $state->id;
    }
    return null;
}

protected function getCityId($cityName, $stateId = null, $stateName = null)
{
    if (!empty($cityName)) {
        $query = City::where('name', $cityName);

        if ($stateId) {
            $query->where('state_id', $stateId);
        }

        $city = $query->first();
        if (!$city) {
            throw new \Exception("Invalid City: {$cityName} for State: {$stateName}");
        }
        return $city->id;
    }
    return null;
}
}
