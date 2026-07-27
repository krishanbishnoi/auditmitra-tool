<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Model\Products;
use App\Model\ProductUser;
use App\Model\Productattribute;
use App\User;
use App\Model\Branch;
use App\Model\Branchable;
use Validator;
use Illuminate\Support\Facades\Crypt;
use DB;
use App\Exports\ProductHierarchyExport;
use Maatwebsite\Excel\Facades\Excel;
use Auth; 

class ProductattributeController extends Controller
{

    public function index()
    {        
        $productattdata = Productattribute::with('productName')
        ->join('products', 'productattributes.product_id', '=', 'products.id')
        ->where('products.client_id', Auth::user()->id)
        ->orderBy('products.name', 'ASC')
        ->select('productattributes.*')
        ->get();
        return view('productattribute.list', compact('productattdata'));
    }


    public function create()
    {
        $products=Products::where('status',0)->where('products.client_id', Auth::user()->id)->get();
        return view('productattribute.create', compact('products'));
    }


    public function store(Request $request)
    {
        // Convert product attribute name to CamelCase (if necessary)
        $camelCaseText = ucwords($request->product_attribute_name); // Ensure it's applied to the right field

        // Validate the request
        $validated = $request->validate([
            'product_attribute_name' => 'required|string|max:255',  // Adjust validation rules as necessary
            'product_id' => 'required|exists:products,id',  // Make sure the product exists in the database
        ]);

        // Create the Product Attribute record
        $productAttribute = Productattribute::create([
            'product_attribute_name' => $camelCaseText, // Store CamelCase version
            'product_id' => $request->product_id,
            'type' => 0,
            'client_id' => Auth::user()->id,  // Adjust as necessary
        ]);

        // Check if the creation was successful
        if ($productAttribute) {
            return redirect()->route('productattribute.index')->with('success', 'Product Attributes created successfully.');
        } else {
            return redirect()->back()->with('error', 'Product Attributes creation failed.');
        }
    }


    public function edit($id)
    {      
        $data=Productattribute::find(Crypt::decrypt($id));
        $products=Products::where('status',0)->latest()->get();
        return view('productattribute.edit', compact('data', 'products'));
    }

    public function show($id)
    {
        $productattdata=Productattribute::find(Crypt::decrypt($id));
        //echo '<pre>'; print_r($productattdata); die();
        $products=Products::where('status',0)->latest()->get();
        return view('productattribute.showproductattributes', compact('productattdata', 'products'));
    }



    public function update(Request $request, $id)
    {
        $camelCaseText = ucwords($request->name);
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            // 'type' => 'required'
        ]);
  
        if ($validator->fails()) {
            return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();

        } else {

        $yard=Productattribute::where('id',Crypt::decrypt($id))->update(
            [
                'product_attribute_name'=>$request->product_attribute_name,
                'product_id'=>$request->product_id,
                'type'=>0
            ]
        );

        // echo '<pre>'; print_r($yard);

        if($yard){
            return redirect('productattribute')->with('success', ['Product Attributes updated successfully.']);
        }
        else{
            return redirect()->back()->with('error', ['Product Attributes updated unsuccessfully.']);

        }

        }

    }


    public function destroy($id)
    {
        $productattribute = Productattribute::findOrFail($id);
        $productattribute->delete();
        
        return redirect()->back()->with('success', 'Product Attributes deleted successfully.');
    } 


}

