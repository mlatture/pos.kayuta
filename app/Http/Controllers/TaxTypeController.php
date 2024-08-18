<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaxTypeRequest;
use App\Models\TaxType;
use Exception;
use Illuminate\Http\Request;

class TaxTypeController extends Controller
{
    public function __construct(){
        $this->middleware('admin_has_permission:'.config('constants.role_modules.list_tax_types.value'))->only(['index']);
        $this->middleware('admin_has_permission:'.config('constants.role_modules.create_tax_types.value'))->only(['create','store']);
        $this->middleware('admin_has_permission:'.config('constants.role_modules.edit_tax_types.value'))->only(['edit','update']);
        $this->middleware('admin_has_permission:'.config('constants.role_modules.delete_tax_types.value'))->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $taxType = TaxType::query();
        if ($request->search) {
            $taxType = $taxType->where('name', 'LIKE', "%{$request->search}%");
        }
        if(auth()->user()->organization_id) {
            $taxType->where('organization_id',auth()->user()->organization_id);
        }
        $taxType = $taxType->latest()->get();
        return view('tax-types.index')->with('taxTypes', $taxType);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tax-types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaxTypeRequest $request)
    {
        try {
            $taxType = TaxType::create([
                'organization_id' => auth()->user()->organization_id,
                'title'         =>  $request->title,
                // 'tax_type'      =>  $request->tax_type ?? '',
                'tax_type'      =>  'percentage',
                'tax'           =>  $request->tax ?? 0,
            ]);

            if (!$taxType) {
                return redirect()->back()->with('error', 'Sorry, Something went wrong while creating tax type.');
            }
            return redirect()->route('tax-types.index')->with('success', 'Success, New tax type has been added successfully!');

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TaxType  $taxType
     * @return \Illuminate\Http\Response
     */
    public function show(TaxType $taxType)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TaxType  $taxType
     * @return \Illuminate\Http\Response
     */
    public function edit(TaxType $taxType)
    {
        if (auth()->user()->organization_id == $taxType->organization_id || auth()->user()->admin_role_id == 1) {
            return view('tax-types.edit', compact('taxType'));
        }
        abort(403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TaxType  $taxType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TaxType $taxType)
    {
        try {
            $taxType->title      =   $request->title;
            // $taxType->tax_type      =   $request->tax_type;
            $taxType->tax           =   $request->tax;

            if (!$taxType->save()) {
                return redirect()->back()->with('error', 'Sorry, Something went wrong while updating tax type.');
            }
            return redirect()->route('tax-types.index')->with('success', 'Success, Tax Type has been updated.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TaxType  $taxType
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaxType $taxType)
    {
        $taxType->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
