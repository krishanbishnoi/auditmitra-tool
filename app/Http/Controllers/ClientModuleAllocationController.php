<?php
namespace App\Http\Controllers;

use App\Model\ClientModuleAllocation;
use App\Model\ModulePermission;
use App\User;
use Illuminate\Http\Request;

class ClientModuleAllocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function module_allocation_view($clientId)
    {
        $allocations = ClientModuleAllocation::where('client_id',$clientId)->pluck('module_id')->toArray();
        $moduleList = ModulePermission::all();
        $clientDetail = user::find($clientId);
        return view('client_module_allocations.index', compact('allocations','moduleList','clientId','clientDetail'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $modules = ModulePermission::all();
        $clients = users::all();
        return view('client_module_allocations.create', compact('modules', 'clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function module_allocation_update(Request $request)
    {
        //echo "<pre>"; print_r($request->all()); die;

        $request->validate([
            'clientId' => 'required|exists:users,id',
        ]);       

        if(count($request->module_id) > 0) {
            $removeIFexist=ClientModuleAllocation::where('client_id',$request->clientId)->delete();
            foreach($request->module_id as $m) {
                $data=array();
                $data['client_id']=$request->clientId;
                $data['module_id']=$m;
                ClientModuleAllocation::create($data);
            }
        }

        return redirect()->route('client_module_allocation.module_allocation_view',$request->clientId)
                         ->with('success', 'Module allocated to client successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ClientModuleAllocation $clientModuleAllocation)
    {
        return view('client_module_allocations.show', compact('clientModuleAllocation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClientModuleAllocation $clientModuleAllocation)
    {
        $modules = ModulePermission::all();
        $clients = userss::all();
        return view('client_module_allocations.edit', compact('clientModuleAllocation', 'modules', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClientModuleAllocation $clientModuleAllocation)
    {
        $request->validate([
            'module_id' => 'required|exists:module_permissions,id',
            'client_id' => 'required|exists:userss,id',
        ]);

        $clientModuleAllocation->update($request->all());
        return redirect()->route('client_module_allocations.index')
                         ->with('success', 'Allocation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClientModuleAllocation $clientModuleAllocation)
    {
        $clientModuleAllocation->delete();

        return redirect()->route('client_module_allocations.index')
                         ->with('success', 'Allocation deleted successfully.');
    }
}
