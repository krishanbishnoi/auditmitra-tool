<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GovernanceDashboard extends Controller
{
    // public $activeTab = 'clients';

    // public function mount()
    // {
    //     $this->activeTab = 'clients';
    // }

    // public function changeTab($tab)
    // {
    //     $this->activeTab = $tab;
    // }

    public function gDashboard(Request $request)
    {
        $activeTab = $request->query('tab', 'clients'); 
        return view('governanceDashboard', ['activeTab' => $activeTab]);
    }
}
