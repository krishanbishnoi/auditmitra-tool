<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
//Importing laravel-permission models
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Session,Validator;

class RoleController extends Controller {

    public function __construct() {
       // $this->middleware(['auth', 'isAdmin']);//isAdmin middleware lets only users with a //specific permission permission to access these resources
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $roles = Role::all();//Get all roles

        return view('acl.role.list')->with('roles', $roles);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $permissions = Permission::all();//Get all permissions

        return view('acl.role.create', ['permissions'=>$permissions]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //Validate name and permission fields
        $validator = Validator::make($request->all(), [
            'name'=>'required|unique:roles|max:255',
            'permissions' =>'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();
        }

        $name = $request['name'];
        $role = new Role();
        $role->name = $name;

        $permissions = $request['permissions'];

        $role->save();
        //Looping thru selected permissions
        foreach ($permissions as $permission) {
            $p = Permission::where('id', '=', $permission)->firstOrFail();
            //Fetch the newly created role and assign permission
            $role = Role::where('name', '=', $name)->first();
            $role->givePermissionTo($p);
        }

        return redirect()->route('roles.index')
            ->with('success',
                'Role'. $role->name.' added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        return redirect('roles');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $role = Role::findOrFail(Crypt::decrypt($id));
        $permissions = Permission::all();

        return view('acl.role.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $tenantConnection = Session::get('tenant_connection');
        $decryptedId = Crypt::decrypt($id);

        $role = (new Role())->setConnection($tenantConnection)->findOrFail($decryptedId);

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:roles,name,' . $decryptedId,
            'permissions' => 'required|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $role->name = $request->input('name');
        $role->save();

        // Revoke existing permissions
        $existingPermissions = (new Permission())->setConnection($tenantConnection)->get();
        foreach ($existingPermissions as $perm) {
            $role->revokePermissionTo($perm);
        }

        // Assign new permissions
        foreach ($request->input('permissions') as $permissionId) {
            $permission = (new Permission())
                ->setConnection($tenantConnection)
                ->findOrFail($permissionId);

            $role->givePermissionTo($permission);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role "' . $role->name . '" updated!');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail(Crypt::decrypt($id));
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success',
                'Role deleted!');

    }
}