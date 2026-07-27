<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MappingMaster;
use App\QmSheetParameter;
use App\QmSheetSubParameter;

class MappingController extends Controller
{
    public function index()
    {
        $locations = MappingMaster::select('location')
            ->distinct()
            ->pluck('location');

        $campusTypes = MappingMaster::select('campus_type')
            ->distinct()
            ->pluck('campus_type');
        $brands = MappingMaster::whereNotNull('brand')
        ->distinct()
        ->pluck('brand');

        return view('mapping.index', compact(
            'locations',
            'campusTypes',
            'brands'
        ));
    }


    // Get Pillars
    public function getPillars(Request $request)
{
    $query = MappingMaster::where(
            'location',
            $request->location
        )
        ->where(
            'campus_type',
            $request->campus_type
        );

    // Brand Filter
    if($request->campus_type == 'Brand')
    {
        $query->where(
            'brand',
            $request->brand
        );
    }

    $pillars = $query
        ->select('pillar')
        ->distinct()
        ->pluck('pillar');

    return response()->json($pillars);
}


    // Get Touch Points
    public function getTouchPoints(Request $request)
{
    $query = MappingMaster::where(
            'location',
            $request->location
        )
        ->where(
            'campus_type',
            $request->campus_type
        );

    // Brand Filter
    if($request->campus_type == 'Brand')
    {
        $query->where(
            'brand',
            $request->brand
        );
    }

    $touchPoints = $query
        ->where(
            'pillar',
            $request->pillar
        )
        ->select('touch_points')
        ->distinct()
        ->pluck('touch_points');

    return response()->json($touchPoints);
}
public function listing()
{
    $mappings = MappingMaster::orderBy('id', 'asc')->get();

    return view('mapping.list', compact('mappings'));
}

public function parameterMapping($id)
{
    $mapping = MappingMaster::findOrFail($id);

    $parameters = \DB::table('qm_sheet_parameters')->where('client_id', auth()->user()->client_id)
        ->select('id', 'parameter')
        ->get();

    // Already Assigned Parameter IDs
    $assignedParameters = \DB::table('mapping_master_parameters')
        ->where('mapping_master_id', $id)
        ->pluck('parameter_id')
        ->toArray();

    return view(
        'mapping.parameters',
        compact(
            'mapping',
            'parameters',
            'assignedParameters'
        )
    );
}
public function storeParameters(Request $request, $id)
{
    foreach($request->parameters as $parameterId)
    {
        \DB::table('mapping_master_parameters')->insert([

            'mapping_master_id' => $id,
            'parameter_id' => $parameterId,

            'created_at' => now(),
            'updated_at' => now()

        ]);
    }

    return redirect('/mapping-list')
        ->with('success', 'Parameters Assigned');
}

    public function parameterSelection()
{
    $locations = MappingMaster::select('location')
        ->distinct()
        ->pluck('location');

    $campusTypes = MappingMaster::select('campus_type')
        ->distinct()
        ->pluck('campus_type');

        $brands = MappingMaster::whereNotNull('brand')
        ->distinct()
        ->pluck('brand');

    return view('mapping.parameter-selection',
        compact('locations', 'campusTypes', 'brands')
    );
}
public function getFinalParameters(Request $request)
{dd($request->all());
    // Get Parameters Directly
    $parameters = QmSheetParameter::with('qm_sheet_sub_parameter')

        ->when($request->location, function ($query) use ($request) {
            $query->where('location', $request->location);
        })

        ->when($request->campus_type, function ($query) use ($request) {
            $query->where('campus_type', $request->campus_type);
        })

        ->when($request->pillar, function ($query) use ($request) {
            $query->where('pillar', $request->pillar);
        })

        ->get();

    // No Parameters Found
    if ($parameters->isEmpty()) {

        return response()->json([

            'status' => false,

            'message' => 'No parameters found',

            'data' => []

        ]);
    }

    return response()->json([

        'status' => true,

        'message' => 'Parameters fetched successfully',

        'parameters' => $parameters

    ]);
}

}