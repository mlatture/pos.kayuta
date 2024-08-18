<?php

namespace App\Http\Controllers;

use App\Models\ProductVendor;
use Illuminate\Http\Request;

class ProductVendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin_has_permission:'.config('constants.role_modules.list_product_vendors.value'))->only(['index']);
        $this->middleware('admin_has_permission:'.config('constants.role_modules.create_product_vendors.value'))->only(['create','store']);
        $this->middleware('admin_has_permission:'.config('constants.role_modules.edit_product_vendors.value'))->only(['edit','update']);
        $this->middleware('admin_has_permission:'.config('constants.role_modules.delete_product_vendors.value'))->only(['destroy']);
    }

    public function index(){
        $productVendorsQuery = ProductVendor::query();
        if(auth()->user()->organization_id && auth()->user()->admin_role_id != 1) {
            $productVendorsQuery->where('organization_id',auth()->user()->organization_id);
        }
        $data['productVendors'] = $productVendorsQuery->get();
        return view('product-vendors.index',$data);
    }

    public function create(){
        return view('product-vendors.create');
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required',
            'address_1' => 'required',
            'address_2' => 'nullable',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'country' => 'required',
            'contact_name' => 'required',
            'email' => 'required|email|unique:product_vendors,email',
            'work_phone' => 'nullable',
            'mobile_phone' => 'nullable',
            'fax' => 'nullable',
            'notes' => 'nullable'
        ]);
        $validated['organization_id'] = auth()->user()->organization_id;
        ProductVendor::create($validated);
        return redirect(route('product-vendors.index'))->with('success','Product Vendor created successfully');
    }

    public function edit(ProductVendor $productVendor){
        if(auth()->user()->organization_id == $productVendor->organization_id || auth()->user()->admin_role_id == 1){
            return view('product-vendors.edit',compact('productVendor'));
        }
        abort(403);
    }

    public function update(Request $request, ProductVendor $productVendor){
        if(auth()->user()->organization_id == $productVendor->organization_id || auth()->user()->admin_role_id == 1){
            $validated = $request->validate([
                'name' => 'required',
                'address_1' => 'required',
                'address_2' => 'nullable',
                'city' => 'required',
                'state' => 'required',
                'zip' => 'required',
                'country' => 'required',
                'contact_name' => 'required',
                'email' => 'required|email',
                'work_phone' => 'nullable',
                'mobile_phone' => 'nullable',
                'fax' => 'nullable',
                'notes' => 'nullable'
            ]);
            $productVendor->update($validated);
            return redirect(route('product-vendors.index'))->with('success','Product Vendor updated successfully');
        }
        abort(403);
    }

    public function destroy(ProductVendor $productVendor){
        if(auth()->user()->organization_id == $productVendor->organization_id || auth()->user()->admin_role_id == 1){
            $productVendor->delete();
            return redirect(route('product-vendors.index'))->with('success','Product Vendor deleted successfully');
        }
        abort(403);
    }
}
