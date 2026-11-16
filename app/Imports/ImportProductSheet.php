<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Model\Products;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ImportProductSheet implements ToModel, WithHeadingRow
{
   /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */


public function model(array $row)
{
  
    if (empty(trim($row['products'] ?? ''))) {
        return null;
    }

    $camelCaseText = ucwords(trim($row['products']));
    $bucketName = ucwords(trim($row['bucket'] ?? ''));

    $validator = Validator::make([
        'name' => $camelCaseText,
        'bucket' => $bucketName,
    ], [
        'name' => 'required|string|max:255',
        'bucket' => 'nullable|string|max:255',
    ]);

    if ($validator->fails()) {
        return null;
    }

    $clientId = Auth::id();

    $existingProduct = null;

    if (!empty($row['id'])) {
        $existingProduct = Products::where('id', $row['id'])
            ->where('client_id', $clientId)
            ->first();
    }

    if ($existingProduct) {

        if (
            $existingProduct->name === $camelCaseText &&
            $existingProduct->bucket === ($bucketName ?: null)
        ) {
            return null;
        }


        $existingProduct->update([
            'name' => $camelCaseText,
            'bucket' => $bucketName ?: null,
        ]);

        return $existingProduct;
    }

    
    $duplicateProduct = Products::where('name', $camelCaseText)
        ->where('bucket', $bucketName ?: null)
        ->where('client_id', $clientId)
        ->first();

    if ($duplicateProduct) {
        return null;
    }


    return Products::create([
        'name' => $camelCaseText,
        'bucket' => $bucketName ?: null,
        'type' => 0,
        'client_id' => $clientId,
    ]);
}
} 
