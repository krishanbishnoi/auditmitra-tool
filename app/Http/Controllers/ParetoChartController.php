<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\User;
use App\Agency;
use App\Model\Branch;
use Validator;
use Maatwebsite\Excel\Facades\Excel;

use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class ParetoChartController extends Controller
{   

    public function index(Request $request)
    {

        $agencies = DB::table('agencies')
        ->join('audits', 'agencies.id', '=', 'audits.agency_id')
        ->select('agencies.id', 'agencies.name')
        ->distinct()
        ->get();

        return view('pareto_chart.index', compact('agencies'));
    }


    public function paretoChart(Request $request)
    {
        $agencyId = $request->agency_id;
        if(auth()->user()->client_id==13){
            $data = DB::table('qm_sheet_sub_parameters')
            ->leftJoin('audit_results', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id')
            ->leftJoin('audits', 'audits.id', '=', 'audit_results.audit_id')
            ->when($agencyId && $agencyId !== 'all', function ($query) use ($agencyId) {
                $query->where('audits.agency_id', $agencyId);
            })
            ->select(
                DB::raw("CONCAT_WS(' ', 
                            SUBSTRING_INDEX(qm_sheet_sub_parameters.sub_parameter, ' ', 1),
                            SUBSTRING_INDEX(SUBSTRING_INDEX(qm_sheet_sub_parameters.sub_parameter, ' ', 2), ' ', -1),
                            SUBSTRING_INDEX(SUBSTRING_INDEX(qm_sheet_sub_parameters.sub_parameter, ' ', 3), ' ', -1)) as label"),
                DB::raw('COUNT(audit_results.option_selected) as y')
            )
            ->where('audit_results.option_selected', 'Unsatisfactory')
            ->groupBy('label') 
            ->orderBy('y', 'desc')
            ->get();
        }
        else{
        $data = DB::table('qm_sheet_sub_parameters')->where('audits.client_id', auth()->user()->client_id)
            ->leftJoin('audit_results', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id')
            ->leftJoin('audits', 'audits.id', '=', 'audit_results.audit_id')
            ->when($agencyId && $agencyId !== 'all', function ($query) use ($agencyId) {
                $query->where('audits.agency_id', $agencyId);
            })
            ->select(
                DB::raw("CONCAT_WS(' ', 
                            SUBSTRING_INDEX(qm_sheet_sub_parameters.sub_parameter, ' ', 1),
                            SUBSTRING_INDEX(SUBSTRING_INDEX(qm_sheet_sub_parameters.sub_parameter, ' ', 2), ' ', -1),
                            SUBSTRING_INDEX(SUBSTRING_INDEX(qm_sheet_sub_parameters.sub_parameter, ' ', 3), ' ', -1)) as label"),
                DB::raw('COUNT(audit_results.option_selected) as y')
            )
            ->where('audit_results.option_selected', 'Unsatisfactory')
            ->groupBy('label') 
            ->orderBy('y', 'desc')
            ->get();

        // Check if data is empty
        if ($data->isEmpty()) {
            return response()->json([
                'categories' => [],
                'data' => [],
                'cumulative' => []
            ]);
        }
    }

        // Extract categories (labels) and values (counts)
        $categories = $data->pluck('label');
        $data_values = $data->pluck('y');
        $total = $data_values->sum(); 

        // Prepare cumulative data
        $cumulative_data = [];
        $cumulative = 0;

        foreach ($data_values as $value) {
            $cumulative += $value;
            $cumulative_data[] = ($cumulative / $total) * 100;  // Calculate cumulative percentage
        }

        // Return the data in the expected format for the Highcharts JS frontend
        return response()->json([
            'categories' => $categories,  // Categories for the x-axis
            'data' => $data_values,       // Data for the bars (y-axis)
            'cumulative' => $cumulative_data  // Cumulative percentage for the line
        ]);
    }


    public function repeat_issues(Request $request)
    {
        $agencyId = $request->agency_id;
        // $data = DB::select(DB::raw("select au.agency_id,aus.parameter_id,aus.sub_parameter_id,aus.selected_option from audits as au inner join audit_results as aus on au.id=aus.audit_id where aus.selected_option=0 and au.agency_id=".$agencyId." order by au.id desc"));
        if($agencyId == 'all') {
            return response()->json([
                'categories' => ['Lock And Key Storage Facility Available At The Agency','Windows10 & Winzip-9 Or Above Versions Are Installed','Dialers & Call Recording Facilities Are Installed And Operational (Tele-Calling Agency)'],
                'data' => [2,1,3],
                'cumulative' => [35,25,40]
        ]);  
        }
        
        elseif($agencyId == '1277') {
        return response()->json([
                'categories' => ['Lock And Key Storage Facility Available At The Agency','NDC Printed On Agency Letter Head','Fire Extinguisher With Active Validity is Available in The Premises'],
                'data' => [2,4,3],
                'cumulative' => [10,60,30]
        ]);  
       }

       else {
        return response()->json([
                'categories' => ['Agency Location Address Matches With The CVMU Tracker','Windows10 & Winzip-9 Or Above Versions Are Installed','Fire Extinguisher With Active Validity is Available in The Premises'],
                'data' => [4,2,3],
                'cumulative' => [45,25,30]
        ]); 
        
        }
    }













    //1 working fine but i want agency wise
    // public function paretoChart(Request $request)
    // {
    //     // Fetch 'Unsatisfactory' data from the database
    //     $data = DB::table('qm_sheet_sub_parameters')
    //         ->leftJoin('audit_results', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id')
    //         ->when($request->agency_id && $request->agency_id !== 'all', function ($query) use ($request) {
    //             $query->where('audit_results.agency_id', $request->agency_id);
    //         })
    //         ->when($request->start_date && $request->end_date, function ($query) use ($request) {
    //             $query->whereBetween(DB::raw('DATE(audit_results.created_at)'), [$request->start_date, $request->end_date]);
    //         })
    //         ->select(
    //             DB::raw("CONCAT_WS(' ', 
    //                         SUBSTRING_INDEX(qm_sheet_sub_parameters.sub_parameter, ' ', 1),
    //                         SUBSTRING_INDEX(SUBSTRING_INDEX(qm_sheet_sub_parameters.sub_parameter, ' ', 2), ' ', -1),
    //                         SUBSTRING_INDEX(SUBSTRING_INDEX(qm_sheet_sub_parameters.sub_parameter, ' ', 3), ' ', -1)) as label"),
    //             DB::raw('COUNT(audit_results.option_selected) as y') // Count the 'Unsatisfactory' occurrences
    //         )
    //         ->where('audit_results.option_selected', 'Unsatisfactory') // Only consider 'Unsatisfactory' option
    //         ->groupBy('label')
    //         ->orderBy('y', 'desc')
    //         ->get();

    //     // Prepare the response data for Highcharts
    //     $categories = [];
    //     $data_values = [];
    //     $cumulative_data = [];
    //     $total = 0;
    //     $cumulative = 0;

    //     foreach ($data as $item) {
    //         $categories[] = $item->label;  // Label for the x-axis
    //         $data_values[] = $item->y;     // Values for the bars (y-axis)
    //         $total += $item->y;            // Calculate total for cumulative percentage
    //     }

    //     // Calculate cumulative percentages for Pareto line
    //     foreach ($data_values as $value) {
    //         $cumulative += $value;
    //         $cumulative_percentage = ($cumulative / $total) * 100;
    //         $cumulative_data[] = $cumulative_percentage;  // Store cumulative data
    //     }

    //     // Return the formatted data as JSON for Highcharts
    //     return response()->json([
    //         'categories' => $categories,     // Categories for the x-axis
    //         'data' => $data_values,          // Values for the bars
    //         'cumulative' => $cumulative_data // Cumulative percentages for the line
    //     ]);
    // }

   

}
