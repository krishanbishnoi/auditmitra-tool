<?php

namespace App\Http\Controllers;

use App\Model\AuditSeetMaster;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    public function index()
    {
        $data = AuditSeetMaster::orderBy('id', 'DESC')->get();

        return view('masters.index', compact('data'));
    }

    public function create()
    {
        return view('masters.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'name' => 'required',
        ]);

        AuditSeetMaster::create([
            'type' => $request->type,
            'name' => $request->name,
            'possible_values' => $request->possible_values,
            'show_add_button' => $request->show_add_button ?? 'N',
            'weight' => $request->weight ?? 0,
            'is_active' => $request->is_active ?? 'Y',
        ]);

        return redirect()->route('masters.index')
            ->with('success', 'Master Created Successfully');
    }

    public function edit($id)
    {
        $master = AuditSeetMaster::findOrFail($id);

        return view('masters.edit', compact('master'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required',
            'name' => 'required',
        ]);

        $master = AuditSeetMaster::findOrFail($id);

        $master->update([
            'type' => $request->type,
            'name' => $request->name,
            'possible_values' => $request->possible_values,
            'show_add_button' => $request->show_add_button ?? 'N',
            'weight' => $request->weight ?? 0,
            'is_active' => $request->is_active ?? 'Y',
        ]);

        return redirect()->route('masters.index')
            ->with('success', 'Master Updated Successfully');
    }
}