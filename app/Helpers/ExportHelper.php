<?php

namespace App\Helpers;

class ExportHelper
{
    public static function getExportQuery($query)
    {
        $user = auth()->user();
        $userRole = $user->roles->first();

        if ($userRole && $userRole->name == 'Client') {
            return $query->where('client_id', $user->client_id);
        } else {
            return $query->where('created_by', $user->email);
        }
    }
}
