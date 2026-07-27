<?php

use App\DocumentType;
use App\Organisation;
use App\RegularDocument;
use App\Setting;
use App\Signup;
use App\SuperadminSetting;
use App\User;
use App\Workshop;
use Illuminate\Support\Facades\Auth;


function getBranchUuid()
{
    $incNumber = 001;
    $res = \App\Model\Branch::orderBy('id', 'DESC')->first();
    // dd($res);
    if ($res) {
        $incNumber = (is_string($res->uuid)?0:$res->uuid) + 1;
    }
    return $incNumber;
}

function getUser($val)
{
    $g=User::find($val);
    if($g) {
        $n=$g->name;
    } else {
        $n='';
    }
    return $n;
}
