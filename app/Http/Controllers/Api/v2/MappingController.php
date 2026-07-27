<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MappingMaster;
use App\QmSheetParameter;
use App\QmSheetSubParameter;
use DB;
use App\Artifact;
use URL;

class MappingController extends Controller
{
    public function getMappingLocation()
    {
        // Locations
        $locations = MappingMaster::select('location')
            ->distinct()
            ->pluck('location');

        return response()->json([
            'status' => true,
            'message' => 'Dropdown data fetched successfully',
            'data' => [
                'locations' => $locations
            ]
        ]);
    }
    // public function getMappingDropdownData()
    // {
    //     $locations = MappingMaster::select('location')
    //         ->distinct()
    //         ->pluck('location');
    //     // Campus Types
    //     $campusTypes = MappingMaster::select('campus_type')
    //         ->distinct()
    //         ->pluck('campus_type');

    //     // Brands
    //     $brands = MappingMaster::whereNotNull('brand')
    //         ->select('brand')
    //         ->distinct()
    //         ->pluck('brand');

    //     $pillar = MappingMaster::whereNotNull('pillar')
    //         ->select('pillar')
    //         ->distinct()
    //         ->pluck('pillar');


    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Dropdown data fetched successfully',
    //         'data' => [
    //             'locations' => $locations,
    //             'campus_types' => $campusTypes,
    //             'brands' => $brands,
    //             'pillar' => $pillar,

    //         ]
    //     ]);
    // }

    public function getMappingDropdownData(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Fetch Unique Combinations
        |--------------------------------------------------------------------------
        */

        $combinations = DB::table('qm_sheet_parameters')

            ->where('qm_sheet_id', $request->qm_sheet_id)

            ->select(
                'location',
                'campus_type'
            )

            ->distinct()

            ->get();

        $finalData = [];

        foreach ($combinations as $row) {

            /*
        |--------------------------------------------------------------------------
        | Total Parameters
        |--------------------------------------------------------------------------
        */

            $totalParameters = DB::table('qm_sheet_parameters')

                ->where('qm_sheet_id', $request->qm_sheet_id)

                ->where('location', $row->location)

                ->where('campus_type', $row->campus_type)

                ->count();

            /*
        |--------------------------------------------------------------------------
        | Filled Parameters
        |--------------------------------------------------------------------------
        */

            $filledParameters = DB::table('audit_result_v2')

                ->join(
                    'qm_sheet_parameters',
                    'audit_result_v2.parameter_id',
                    '=',
                    'qm_sheet_parameters.id'
                )

                ->where('audit_result_v2.audit_id', $request->audit_id)

                ->where('qm_sheet_parameters.qm_sheet_id', $request->qm_sheet_id)

                ->where('qm_sheet_parameters.location', $row->location)

                ->where('qm_sheet_parameters.campus_type', $row->campus_type)

                ->distinct('audit_result_v2.parameter_id')

                ->count('audit_result_v2.parameter_id');

            $finalData[] = [

                'location' => $row->location,

                'campus_type' => $row->campus_type,

                'filled_count' => $filledParameters,

                'total_count' => $totalParameters,

                'progress' => $filledParameters . '/' . $totalParameters,

                'is_completed' => $filledParameters == $totalParameters,

                'action' => [

                    'type' => 'view',

                    'url' => url('view-mapping') .

                        '?qm_sheet_id=' . $request->qm_sheet_id .

                        '&audit_id=' . $request->audit_id .

                        '&location=' . $row->location .

                        '&campus_type=' . $row->campus_type
                ]
            ];
        }
        usort($finalData, function ($a, $b) {
    return ($a['is_completed'] ? 1 : 0) <=> ($b['is_completed'] ? 1 : 0);
});

        return response()->json([

            'status' => true,

            'message' => 'Dropdown data fetched successfully',

            'data' => $finalData
        ]);
    }

    public function viewMapping(Request $request)
    {
        $query = DB::table('qm_sheet_parameters')
            ->where('qm_sheet_id', $request->qm_sheet_id);


        if (!empty($request->location)) {
            $query->where('location', $request->location);
        }

        if (!empty($request->campus_type)) {
            $query->where('campus_type', $request->campus_type);
        }


        $data = $query
            ->select(
                'id',
                'parameter'
            )
            ->distinct()
            ->get()
            ->map(function ($row) use ($request) {

                $isFilled = DB::table('audit_result_v2')
                    ->where('audit_id', $request->audit_id)
                    ->where('parameter_id', $row->id)
                    ->exists();

                return [
                    'id' => $row->id,
                    'parameter' => $row->parameter,
                    'is_filled' => $isFilled,
                    'status' => $isFilled ? 'Filled' : 'Pending',
                    'action' => [
                        'type' => 'view',
                        'url' => url('view-parameter-details') .
                            '?id=' . $row->id .
                            '&audit_id=' . $request->audit_id
                    ]
                ];
            })
            ->sortBy(function ($item) {
                return $item['is_filled'] ? 1 : 0;
            })
            ->values();

        return response()->json([

            'status' => true,

            'message' => 'Parameter data fetched successfully',

            'data' => $data
        ]);
    }

    public function viewParameterDetails(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

        $request->validate([

            'audit_id' => 'required|integer',

            'parameter_id' => 'required|integer'
        ]);

        try {

            /*
        |--------------------------------------------------------------------------
        | Parameter Details
        |--------------------------------------------------------------------------
        */

            $parameter = DB::table('qm_sheet_parameters')

                ->where('id', $request->parameter_id)

                ->where('qm_sheet_id', $request->qm_sheet_id)

                ->first();
            // dd($parameter);
            /*
        |--------------------------------------------------------------------------
        | Parameter Not Found
        |--------------------------------------------------------------------------
        */

            if (!$parameter) {

                return response()->json([

                    'status' => false,

                    'message' => 'Parameter not found'
                ], 404);
            }

            /*
        |--------------------------------------------------------------------------
        | Fetch Parameter Indexes
        |--------------------------------------------------------------------------
        */

            $indexes = DB::table('audit_result_v2')

                ->where('audit_id', $request->audit_id)

                ->where('parameter_id', $request->parameter_id)

                ->distinct()

                ->pluck('parameter_index');

            /*
        |--------------------------------------------------------------------------
        | Default Index
        |--------------------------------------------------------------------------
        */

            if ($indexes->count() == 0) {

                $indexes = collect([1]);
            }

            $finalData = [];

            /*
        |--------------------------------------------------------------------------
        | Loop Parameter Indexes
        |--------------------------------------------------------------------------
        */

            foreach ($indexes as $index) {

                /*
            |--------------------------------------------------------------------------
            | Parameter Tag
            |--------------------------------------------------------------------------
            */

                $parameterTag = DB::table('audit_result_v2')

                    ->where('audit_id', $request->audit_id)

                    ->where('parameter_id', $request->parameter_id)

                    ->where('parameter_index', $index)

                    ->value('parameter_tag');

                /*
            |--------------------------------------------------------------------------
            | Sub Parameters
            |--------------------------------------------------------------------------
            */

                $subParameters = DB::table('qm_sheet_sub_parameters')

                    ->where('qm_sheet_parameter_id', $request->parameter_id)

                    ->get()

                    ->map(function ($sub) use ($request, $index) {

                        /*
                    |--------------------------------------------------------------------------
                    | Filled Data
                    |--------------------------------------------------------------------------
                    */

                        $filledData = DB::table('audit_result_v2')

                            ->where('audit_id', $request->audit_id)

                            ->where('parameter_id', $request->parameter_id)

                            ->where('parameter_index', $index)

                            ->where('sub_parameter_id', $sub->id)

                            ->get()

                            ->map(function ($item) {

                                /*
                            |--------------------------------------------------------------------------
                            | Artifacts
                            |--------------------------------------------------------------------------
                            */

                                $artifacts = Artifact::where(
                                    'audit_id',
                                    $item->audit_id
                                )

                                    ->where(
                                        'parameter_id',
                                        $item->parameter_id
                                    )

                                    ->where(
                                        'parameter_index',
                                        $item->parameter_index
                                    )

                                    ->where(
                                        'sub_parameter_id',
                                        $item->sub_parameter_id
                                    )

                                    ->get();

                                /*
                            |--------------------------------------------------------------------------
                            | Artifact File URL
                            |--------------------------------------------------------------------------
                            */

                                foreach ($artifacts as $artifact) {

                                    $artifact->file =
                                        URL::to('/') .
                                        '/storage/app/' .
                                        $artifact->file;
                                }

                                /*
                            |--------------------------------------------------------------------------
                            | Attach Artifacts
                            |--------------------------------------------------------------------------
                            */

                                $item->artifacts = $artifacts;

                                return $item;
                            });

                        return [

                            'sub_parameter_details' => $sub,

                            'is_filled' => $filledData->count() > 0,

                            'filled_data' => $filledData
                        ];
                    });

                /*
            |--------------------------------------------------------------------------
            | Parameter Filled Status
            |--------------------------------------------------------------------------
            */

                $isFilled = $subParameters->contains(function ($item) {

                    return $item['is_filled'] == true;
                });

                /*
            |--------------------------------------------------------------------------
            | Final Data
            |--------------------------------------------------------------------------
            */

                $finalData[] = [

                    'id' => $parameter->id,

                    'parameter' => $parameter->parameter,

                    'parameter_tag' => $parameterTag,

                    'parameter_index' => $index,

                    'location' => $parameter->location,

                    'campus_type' => $parameter->campus_type,

                    'add_more' => $parameter->add_more,

                    'add_more_limit' => $parameter->add_more_limit,

                    'is_filled' => $isFilled,

                    'sub_parameters' => $subParameters
                ];
            }

            /*
        |--------------------------------------------------------------------------
        | Final Response
        |--------------------------------------------------------------------------
        */

            return response()->json([

                'status' => true,

                'message' => 'Parameter details fetched successfully',

                'data' => $finalData
            ]);
        } catch (\Exception $e) {

            return response()->json([

                'status' => false,

                'message' => $e->getMessage()
            ], 500);
        }
    }
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
        if ($request->campus_type == 'Brand') {
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
        if ($request->campus_type == 'Brand') {
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


    public function getFinalParameters(Request $request)
    {
        // dd($request->all());
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
