<?php



namespace App\Http\Controllers\Legal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Storage;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Auth;
use App\QmSheetParameter;
use Illuminate\Support\Facades\Validator;
use App\Mail\LegalAuditIntimationMail;
class IntimationController extends Controller
{


    public function create()
{
    $audits = DB::table('legal_audit_assignments as laa')->where('laa.client_id', auth()->user()->client_id)
    ->join('advocates as a', 'a.id', '=', 'laa.advocate_id')
    ->select(
        'laa.id as legal_audit_assign_id',
        'laa.legal_cycle',
        'a.id as advocate_id',
        'a.name as advocate_name'
    )
    ->get();

    return view('legal.intimation.create', compact('audits'));
}
public function store(Request $request)
{
    $request->validate([
        'legal_audit_assign_id' => 'required',
        'advocate_id'           => 'required',
        'advocate_email'        => 'required|email',
        'auditor_name'          => 'required',
        'audit_date'            => 'required|date',
    ]);

    // Save data
    DB::table('legal_intimations')->insert([
        'legal_audit_assign_id' => $request->legal_audit_assign_id,
        'advocate_id'           => $request->advocate_id,
        'advocate_email'        => $request->advocate_email,
        'auditor_name'          => $request->auditor_name,
        'audit_date'            => $request->audit_date,
        'created_at'            => now(),
        'updated_at'            => now(),
    ]);

    // Send mail
    Mail::to($request->advocate_email)
        ->send(new LegalAuditIntimationMail( $request->auditor_name,
        $request->audit_date,
        $request->legal_audit_assign_id));

    return back()->with('success', 'Legal Audit Intimation sent successfully');
}


    public function index()
{
    $intimations = DB::table('legal_intimations as li')
        ->join('legal_audit_assignments as laa', 'laa.id', '=', 'li.legal_audit_assign_id')
        ->join('advocates as a', 'a.id', '=', 'li.advocate_id')
        // ->leftJoin('legal_cycles as lc', 'lc.id', '=', 'li.legal_cycle_id')
        ->select(
            'li.id',
            'li.legal_audit_assign_id',
            'li.advocate_email',
            'li.auditor_name',
            'li.audit_date',
            'li.created_at as sent_on',
            'a.name as advocate_name',
            // 'lc.name as legal_cycle_name'
        )
        ->orderBy('li.created_at', 'desc')
        ->get();

    return view('legal.intimation.index', compact('intimations'));
}

}
