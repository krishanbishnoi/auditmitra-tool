<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Model\Products;
use Illuminate\Support\Facades\Auth;

class ImportProductSheet implements ToModel, WithHeadingRow
{
   /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */


    public function model(array $row)
    {
                 

      if (empty($row['products'])) {
            return null;
        }

        return Products::create([
            'name'      => ucwords(trim($row['products'])),
            'type'      => $row['type'] ?? 0,
            'bucket'    => $row['bucket'] ?? null,
            'capacity'  => $row['capacity'] ?? null,
            'status'    => 0,
            'client_id' => Auth::id(),
        ]);

       }
} 
