<?php

namespace App\Imports;

use App\Model\Role;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportRolesSheet implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function model(array $row)
    {
            // dd($row);

        if (empty($row['roles'])) {
            return null;
        }

        $roles = Role::create([
            'id' => $row['id'], 
            'name' => $row['roles'] ?? null,
            'guard_name' => $row['guard_name'] ?? null,
        ]);
        return $roles;
    }
}
