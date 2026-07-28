<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GovernanceDashboard extends Controller
{
    public function gDashboard(){
        return view('/governanceDashboard');
    }
}
