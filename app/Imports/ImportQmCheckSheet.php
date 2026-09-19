<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use  App\QmSheet;

class ImportQmCheckSheet implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function model(array $row)
    {
        if (empty($row['name'])) {
            return null;
        }

        $qmSheet = QmSheet::create([
            'id' => $row['id'],
            'name' => $row['name'] ?? null,
            'code' => $row['code'] ?? null,
            'details' => $row['details'] ?? null,
            'type' => $row['type'] ?? null,
            'lob' => $row['lob'] ?? null,
            'client_id' => $row['client_id'],
            'created_at' => $row['created_at']
        ]);

        return $qmSheet;
    }
}
