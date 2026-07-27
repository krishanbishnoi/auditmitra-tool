<?php



namespace App\Http\Controllers;



use App\Model\City;

use App\Model\State;

use App\Model\Branchable;

use App\User;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Crypt;


use Illuminate\Support\Facades\DB;

use Validator;




class LocationController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {
       $regions = DB::table("regions")->get();

        // $users = User::role('Collection Manager')->get(['id', 'name']);

       
        return view('location.create1', compact('regions')

        );
    }

   public function cityView()
{
    $cities = DB::table('cities')
        ->join('states', 'cities.state_id', '=', 'states.id')
        ->select('cities.*', 'states.name as state_name')
        ->where('cities.status', 0)
        ->get();

    return view('location.city_view', compact('cities'));
}

     public function stateView()

    {
         $states = DB::table('states')
        ->join('regions', 'states.region_id', '=', 'regions.id')
        ->select('states.*', 'regions.name as region_name')
        ->orderBy('states.name')
        ->get();

    return view('location.state_view', compact('states'));

    }

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {
        $regions = DB::table("regions")->get();

        // $users = User::role('Collection Manager')->get(['id', 'name']);

       
        return view('location.create', compact('regions')

        );

    }

    
    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'region_id' => 'required',
        'state' => 'required',
        'city' => 'required|unique:cities,name,NULL,id,state_id,' . $request->state,
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $city = City::create([
        'name' => $request->city,
        'state_id' => $request->state,
    ]);

    if ($city) {
        return redirect()->route('location.city_view')->with('success', ['City created successfully.']);
    } else {
        return redirect()->back()->with('error', ['City creation failed.']);
    }
}


    



   public function update(Request $request)
{
    $validator = Validator::make($request->all(), [
        'region_id' => 'required|exists:regions,id',
        'state' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
                         ->withErrors($validator)
                         ->withInput();
    }

    // Duplicate check: same name under same region (case insensitive)
    $existing = State::where('region_id', $request->region_id)
                     ->whereRaw('LOWER(name) = ?', [strtolower($request->state)])
                     ->first();

    if ($existing) {
        return redirect()->back()
                         ->withErrors(['state' => 'This state already exists in the selected region.'])
                         ->withInput();
    }

    // Proceed to create new state
    $state = State::create([
        'name' => $request->state,
        'region_id' => $request->region_id,
    ]);

    if ($state) {
        return redirect()->route('location.state_view')
                         ->with('success', ['State created successfully.']);
    } else {
        return redirect()->back()
                         ->withErrors(['state' => 'State creation failed. Please try again.'])
                         ->withInput();
    }
}





public function state_edit($id)
{
    $state = State::findOrFail($id);
    $regions = DB::table('regions')->get();

    return view('location.state_edit', compact('state', 'regions'));
}
public function updateState(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'region_id' => 'required|exists:regions,id',
        'state' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $existing = State::where('region_id', $request->region_id)
                     ->whereRaw('LOWER(name) = ?', [strtolower($request->state)])
                     ->where('id', '!=', $id)
                     ->first();

    if ($existing) {
        return redirect()->back()->withErrors(['state' => 'This state already exists in the selected region.'])->withInput();
    }

    $state = State::findOrFail($id);
    $state->update([
        'name' => $request->state,
        'region_id' => $request->region_id,
    ]);

    return redirect()->route('location.state_view')->with('success', ['State updated successfully.']);
}




public function deleteCity($id)
{
    // Decrypt the ID (if encrypted using Crypt)
    $id = Crypt::decrypt($id);

    // Find the city by ID and delete it
    $city = City::findOrFail($id);

    // Delete the city
    $city->delete();

    // Redirect back with success message
    return redirect()->route('location.city_view')->with('success', ['City deleted successfully.']);
}
public function editCity($id)
{
    $id = Crypt::decrypt($id);

    $city = City::findOrFail($id);
    $states = State::all(); // Add this line to get all states

    return view('location.city_edit', compact('city', 'states')); // Pass both city and states
}
public function updateCity(Request $request, $id)
{
    // Decrypt the ID
    $id = Crypt::decrypt($id);

    // Find the city by ID
    $city = City::findOrFail($id);

    // Validate the input
    $request->validate([
        'state' => 'required',
        'city' => 'required|unique:cities,name,' . $id,
    ]);

    // Update the city
    $city->state_id = $request->state;
    $city->name = $request->city;
    $city->save();

    // Redirect back with success message
    return redirect()->route('location.city_view')->with('success', ['City updated successfully.']);
}





    /**

     * Remove the specified resource from storage.

     *

     * @param  int $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)
{
    $state = State::findOrFail($id);
    $state->delete();

    return redirect()->route('location.state_view')->with('success', ['State deleted successfully.']);
}






//For fetching states

    public function getStates($id)

    {

        $states = DB::table("states")

            ->where("region_id", $id)

            ->pluck("name", "id");

        return response()->json($states);

    }



//For fetching cities

    public function getCities($id)

    {

        $cities = DB::table("cities")

            ->where("state_id", $id)

            ->pluck("name", "id");

        return response()->json($cities);

    }

    public function excelDownloadBranch(){

        ini_set('memory_limit', '-1');

        ini_set('max_execution_time', 3000);

        return Excel::download(new BranchExport, 'Branch.xlsx');

    }

}

