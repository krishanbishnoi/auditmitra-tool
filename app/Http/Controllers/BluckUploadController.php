<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Validator;
use Response;
use App\Imports\BranchImport;
use Maatwebsite\Excel\Facades\Excel;
class BluckUploadController extends Controller
{
   
    public function index()
    {
        return view('bulkUpload.branch');
    }
    function checkExcelFile($file_ext){
        $valid=array(
            // 'csv',
            'xls','xlsx' // add your extensions here.
        );        
        return in_array($file_ext,$valid) ? true : false;
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required',
           
         ]);
         $validator->after(function ($validator) use ($request){
            if($request->hasFile('file') && $this->checkExcelFile($request->file('file')->getClientOriginalExtension()) == false) {
                //return validator with error by file input name
                $validator->errors()->add('file', 'The file must be a file of type: xlsx, xls');
            }
        });
        if ($validator->fails()) {

            return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();
        }
        if($request->hasFile('file')){
            $path1 = $request->file('file')->store('temp'); 
            $dacpath=storage_path('app').'/'.$path1;
            
            $branchImport = new BranchImport();

            Excel::import($branchImport, $dacpath);

            try{
                
            }catch ( \Maatwebsite\Excel\Validators\ValidationException $e){
                $failures = $e->failures();
                // dd($failures,$e->getMessage());
                return redirect()->back()->withErrors($failures)->withInput();
            }
        }
        return redirect()->back()->with('success', ['file imported successfully.']);
    }
    public function downloadBranchNew(){
        // dd('hello');
        $file= public_path(). "/download/bulk_upload_rbl.xlsx";
        $headers = array(
                  'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                );
        return Response::download($file, 'Bulk Upload.xlsx',$headers);
       }
}
