<?php

namespace App\Http\Controllers;

use App\Model\ModulePermission;
use Illuminate\Http\Request;

class ModulePermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = ModulePermission::all();
        return view('module_permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('module_permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'module_name' => 'required|string|max:255',
        ]);

        ModulePermission::create($request->all());

        return redirect()->route('module_permissions.index')
                         ->with('success', 'Module permission created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ModulePermission $modulePermission)
    {
        return view('module_permissions.show', compact('modulePermission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ModulePermission $modulePermission)
    {
        return view('module_permissions.edit', compact('modulePermission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModulePermission $modulePermission)
    {
        $request->validate([
            'module_name' => 'required|string|max:255',
        ]);

        $modulePermission->update($request->all());

        return redirect()->route('module_permissions.index')
                         ->with('success', 'Module permission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModulePermission $modulePermission)
    {
        $modulePermission->delete();

        return redirect()->route('module_permissions.index')
                         ->with('success', 'Module permission deleted successfully.');
    }
}
