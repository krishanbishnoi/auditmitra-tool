<?php

namespace App\Http\Controllers\Legal;

use App\Http\Controllers\Controller;
use App\Advocate;
use Illuminate\Http\Request;

class AdvocateController extends Controller
{
    public function index()
    {
        $advocates = Advocate::where('client_id' , auth()->user()->client_id)->orderBy('id', 'desc')->get();
        return view('legal.advocates.index', compact('advocates'));
    }

    public function create()
    {
        return view('legal.advocates.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:advocates,name'
        ]);

        Advocate::create([
            'name' => $request->name,
            'is_active' => 1, 
            'client_id' =>auth()->user()->client_id
        ]);

        return redirect()->route('legal.advocates.index')->with('success', 'Advocate created');
    }

    public function edit($id)
    {
        $advocate = Advocate::findOrFail($id);
        return view('legal.advocates.form', compact('advocate'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $advocate = Advocate::findOrFail($id);
        $advocate->update([
            'name' => $request->name
        ]);

        return redirect()->route('legal.advocates.index')->with('success', 'Advocate updated');
    }

    public function toggleStatus($id)
    {
        $advocate = Advocate::findOrFail($id);
        $advocate->is_active = $advocate->is_active ? 0 : 1;
        $advocate->save();

        return redirect()->back();
    }
}
