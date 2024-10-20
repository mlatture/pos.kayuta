<?php

namespace App\Http\Controllers;

use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Requests\CustomerStoreRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function __construct(){
        $this->middleware('admin_has_permission:'.config('constants.role_modules.list_customers.value'))->only(['index']);
        $this->middleware('admin_has_permission:'.config('constants.role_modules.create_customers.value'))->only(['create','store']);
        $this->middleware('admin_has_permission:'.config('constants.role_modules.edit_customers.value'))->only(['edit','update']);
        $this->middleware('admin_has_permission:'.config('constants.role_modules.delete_customers.value'))->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usersQuery = User::query();
        if(auth()->user()->organization_id) {
            $usersQuery->where('organization_id',auth()->user()->organization_id);
        }
        if (request()->wantsJson()) {
            return response(
                $usersQuery->get()
            );
        }
        $customers = $usersQuery->where('id', '!=', 0)->latest()->get();
        return view('customers.index')
            ->with('customers', $customers)
            ->with('dictionaryFields', Helpers::getDictionaryFields('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customers.create')
            ->with('dictionaryFields', Helpers::getDictionaryFields('customers'))
            ->with('dictionaryFieldsDesc', Helpers::getDictionaryFields('customers', true));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerStoreRequest $request)
    {
        $avatar_path = '';

        if ($request->hasFile('avatar')) {
            $file   =   $request->file('avatar');
            $avatar_path = ImageManager::upload('customers/', $file->getClientOriginalExtension(), $file);
        }
        $password = rand('00000000', '99999999');

        $customer = User::create([
            'organization_id' => auth()->user()->organization_id,
            'name'  =>  $request->first_name.' '.$request->last_name,
            'f_name' => $request->first_name,
            'l_name' => $request->last_name,
            'email' => $request->email,
            'password'  => bcrypt($password),
            'phone' => $request->phone ?? '',
            'street_address' => $request->address,
            'image' => $avatar_path,
            // 'user_id' => $request->user()->id,
        ]);
        if (isset($request->is_modal)){
            if (!$customer) {
                return response()->json(['status' => 'error', 'message' => 'Sorry, Something went wrong while creating customer.'], 200);
            }
            return response()->json(['status' => 'success', 'message' => 'Success, New customer has been added successfully!', 'data' => $customer], 200);
        }
        if (!$customer) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while creating customer.');
        }
        return redirect()->route('customers.index')->with('success', 'Success, New customer has been added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(User $customer)
    {
        if($customer->organization_id == auth()->user()->organization_id || auth()->user()->admin_role_id == 1) {
            return view('customers.edit', compact('customer'))
                ->with('dictionaryFields', Helpers::getDictionaryFields('customers'))
                ->with('dictionaryFieldsDesc', Helpers::getDictionaryFields('customers', true));
        }
        abort(403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $customer)
    {
        $customer->f_name = $request->first_name;
        $customer->l_name = $request->last_name;
        $customer->email = $request->email;
        $customer->phone = $request->phone ?? '';
        $customer->street_address = $request->address;

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $avatar_path = ImageManager::update('customers/', $customer->image, $file->getClientOriginalExtension(), $file);
            $customer->image = $avatar_path;
        }

        if (!$customer->save()) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while updating the customer.');
        }
        return redirect()->route('customers.index')->with('success', 'Success, The customer has been updated.');
    }

    public function destroy(User $customer)
    {
        if ($customer->image) {
            ImageManager::delete('customers/'.$customer->image);
        }

        $customer->delete();

       return response()->json([
           'success' => true
       ]);
    }

    public function customerInfo(Request $request)
    {
        $customer = Customer::where('email', $request->email)->first();

        if($customer){
            return response()->json([
                'success' => true,
                'info' => [
                    'fname' => $customer->first_name,
                    'lname' => $customer->last_name,
                    'con' => $customer->phone,
                    'address' => $customer->address,
                ],
            ]);
        } else {
            return response()->json([
                'success' => false,
                "message" => "No user found with this email."

            ]);
        }
    }
}
