<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Model\Products;
use App\Model\Productattribute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImportProductAttributeSheet implements ToCollection, WithHeadingRow
{
    protected $newProduct = [];
    protected $existingProducts = [];
    protected $hasImportedAttributes = false;

public function collection(Collection $rows)
{
    $clientId = Auth::user()->client_id;

    // Get unique product names from Excel
    $productNames = $rows->pluck('product_name')
        ->filter()
        ->map(fn($name) => trim($name))
        ->unique()
        ->values();

    // Fetch existing products
     $products = Products::all()
    ->keyBy(fn($product) => strtolower(trim($product->name)));

    // First check whether all products exist
    foreach ($productNames as $productName) {

        if (!$products->has(strtolower($productName))) {
            $this->newProduct[] = $productName;
        }
    }

    // If any product is missing, stop the import
    if (!empty($this->newProduct)) {
        return;
    } 

    // All products exist, now import attributes
    DB::transaction(function () use ($rows, $products)  {

        $processedAttributes = [];

        foreach ($rows as $row) {

            $productName = trim($row['product_name'] ?? '');
            $attributeName = trim($row['product_attribute_name'] ?? '');

            if ($productName === '' || $attributeName === '') {
                continue;
            }

            $product = $products->get(strtolower($productName));

            $normalizedAttribute = strtolower($attributeName);

            $uniqueKey = $product->id . '_' . $normalizedAttribute;

            if (isset($processedAttributes[$uniqueKey])) {
                continue;
            }

            $processedAttributes[$uniqueKey] = true;

           $exists = Productattribute::where('product_id', $product->id)
                ->where('product_id', $product->id)
                ->whereRaw(
                    'LOWER(TRIM(product_attribute_name)) = ?',
                    [$normalizedAttribute]
                )
                ->exists();

            if ($exists) {
                continue;
            }

            Productattribute::create([
                'product_id' => $product->id,
                'product_attribute_name' => ucwords($attributeName),
                'type' => 0,
            ]);

            $this->hasImportedAttributes = true;
        }
    });
}

public function getNewProducts()
{
    return array_values(array_unique($this->newProduct));
}

public function getExistingProducts()
{
    return array_values(array_unique($this->existingProducts));
}

public function getHasImportedAttributes()
{
    return $this->hasImportedAttributes;
}

}