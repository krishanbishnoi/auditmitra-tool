<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Client;

class ImportClientSheet implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function model(array $row)
    {
        try {
            if (!isset($row['client_name']) || empty($row['client_name'])) {
                return null;
            }

            $client = Client::create([
                'client_id' => $row['client_id'] ?? null,
                'client_code' => $row['client_code'] ?? null,
                'client_name' => $row['client_name'] ?? null,
                'client_email' => $row['client_email'] ?? null,
                'created_at' => $row['created_at'] ?? now(),
            ]);
            return $client;
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
