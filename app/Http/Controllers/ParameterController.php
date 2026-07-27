<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Parameter;
use App\SubParameter;

class ParameterController extends Controller
{
    public function create()
{
    $parameters = Parameter::withCount('subParameters')->latest()->get(); // 👈 Add this
    $parameters = Parameter::where('client_id', auth()->user()->client_id)->latest()->get();
    return view('parameters.create', compact('parameters'));
}

public function edit($id)
{
    $parameters = Parameter::latest()->get();
    $editParam = Parameter::findOrFail($id);
    return view('parameters.create', compact('parameters', 'editParam'));
}
 public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $parameter = Parameter::findOrFail($id);
        $parameter->update([
            'name' => $request->name,
        ]);

        return redirect()->route('parameters.create')->with('success', 'Parameter updated successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Parameter::create([
            'name' => $request->name,
            'client_id' => auth()->user()->client_id,
        ]);

        return back()->with('success', 'Parameter added successfully!');
    }
    public function destroy($id)
{
    $parameter = Parameter::findOrFail($id);
    $parameter->delete();

    return back()->with('success', 'Parameter deleted successfully.');
}

public function category_create()
{
    // $category = Category::withCount('subParameters')->latest()->get(); // 👈 Add this
    $category = Category::where('client_id', auth()->user()->client_id)->latest()->get();
    return view('parameters.categorycreate', compact('category'));
}

public function category_store(Request $request)
{    $request->validate([
        'category' => 'required|string|max:255',
        'weight'   => 'required|numeric|min:0',
    ]);

    Category::create([
        'category'  => $request->category,
        'weight'    => $request->weight,
        'client_id' => auth()->user()->client_id,
    ]);

    return back()->with('success', 'Category added successfully!');
}

public function category_edit($id)
{
    $category = Category::latest()->get();
    $editParam = Category::findOrFail($id);
    return view('parameters.categorycreate', compact('category', 'editParam'));
}
public function category_update(Request $request, $id)
{
    $request->validate([
        'category' => 'required|string|max:255',
        'weight'   => 'required|numeric|min:0',
    ]);

    $category = Category::where('id', $id)
        ->where('client_id', auth()->user()->client_id) // 🔐 secure update
        ->firstOrFail();

    $category->update([
        'category' => $request->category,
        'weight'   => $request->weight,
    ]);

    return redirect()->route('category.create')
        ->with('success', 'Category updated successfully.');
}

    

    public function createSubParameter()
{
    $parameters = Parameter::where('client_id', auth()->user()->client_id)->get(); // fetch all parameters
    return view('parameters.sub_parameter_form', compact('parameters'));
}
    public function storeSubParameter(Request $request)
{
    $request->validate([
        'parameter_id' => 'required|exists:parameters,id',
        'sub_parameters' => 'required|array|min:1',
        'sub_parameters.*' => 'required|string',
    ]);

    foreach ($request->sub_parameters as $name) {
        SubParameter::create([
            'parameter_id' => $request->parameter_id,
            'name' => $name,
            'client_id' => auth()->user()->client_id,
        ]);
    }
    $subParameters = SubParameter::with('parameter')->where('client_id', auth()->user()->client_id)->get(); 
    //  return view('parameters.sub_parameter_list', compact('subParameters'));
    $parameters = Parameter::where('client_id', auth()->user()->client_id)->get();
     return view('parameters.sub_parameter_form', compact('parameters'));
}

public function listSubParameters()
{
    $subParameters = SubParameter::with('parameter')->where('client_id', auth()->user()->client_id)->get(); // eager load the related parameter
    return view('parameters.sub_parameter_list', compact('subParameters'));
}

public function getSubParameters($parameter_id)
{
    // dd($parameter_id);
    $subParameters = SubParameter::where('parameter_id', $parameter_id)->get(['id', 'name']);

    return response()->json($subParameters);
}




}

