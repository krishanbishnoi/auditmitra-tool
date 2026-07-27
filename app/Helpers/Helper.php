<?php
namespace App\Helpers;
use App\User;
use Auth;
use App\Model\ClientModuleAllocation; 

class Helper 
{

    public static function getUser($val)
    {
        $g=User::find($val);
        if($g) {
            $n=$g->name;
        } else {
            $n='';
        }
        return $n;
    }
    
    public static function allocatedmodulelist() {
        $getList = [];
        // Get the currently authenticated user
        $user = Auth::user();
        // Check if the user has a client_id associated
        if ($user && isset($user->client_id)) {
            // Fetch the list of allocated modules based on the client's ID
            $getList = ClientModuleAllocation::on('mysql')
                ->where('client_id', $user->client_id)
                ->pluck('module_id')
                ->toArray();
        }
        return $getList;
    }

    public static function generateRandomCode($length = 6) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $randomCode = '';
        $maxIndex = strlen($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $randomCode .= $characters[random_int(0, $maxIndex)];
        }
        return $randomCode;
    }

}

?>