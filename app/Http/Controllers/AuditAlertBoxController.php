<?php

namespace App\Http\Controllers;

use App\AuditAlertBox;
use Illuminate\Http\Request;
use Validator;
use Auth;
use Crypt;

class AuditAlertBoxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = AuditAlertBox::all();
        return view('audit_alert_box.list',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('audit_alert_box.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'name' => 'required',
            'details' => 'required'
        ]);

        if($validator->fails())
        {
            return redirect('audit_alert_box/create')
                        ->withErrors($validator)
                        ->withInput();
        }else
        {
            $new_rc = new AuditAlertBox;
            $new_rc->fill($request->all());
            $new_rc->save();
        }
        return redirect('audit_alert_box')->with('success', 'Alert box created successfully');    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = AuditAlertBox::find(Crypt::decrypt($id));
        return view('audit_alert_box.edit',compact('data'));

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
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'name' => 'required',
            'details' => 'required'
        ]);

        if($validator->fails())
        {
            return redirect('audit_alert_box/create')
                        ->withErrors($validator)
                        ->withInput();
        }else
        {
            $new_rc =  AuditAlertBox::find(Crypt::decrypt($id));
            $new_rc->fill($request->all());
            $new_rc->save();
        }
        return redirect('audit_alert_box')->with('success', 'Alert box edited successfully');  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        AuditAlertBox::find(Crypt::decrypt($id))->delete();
        return redirect('audit_alert_box')->with('success', 'Alert box deleted successfully.');    
    }
}
