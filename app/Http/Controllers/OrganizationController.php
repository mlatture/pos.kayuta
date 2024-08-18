<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index()
    {
        $data['organizations'] = Organization::get();
        return view('organization.index',$data);
    }
    public function create()
    {
        return view('organization.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required',
            'address_1' => 'required',
            'address_2' => 'nullable',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'country' => 'required',
            'status' => 'required|in:Active,Inactive'
        ]);
        Organization::create($validated);
        return redirect(route('organizations.index'))->with('success','Organization Added!');
    }

    public function edit($id)
    {
        $data['organization'] = Organization::findOrFail($id);
        return view('organization.edit',$data);
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'name' => 'required',
            'address_1' => 'required',
            'address_2' => 'nullable',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'country' => 'required',
            'status' => 'required|in:Active,Inactive'
        ]);
        Organization::findOrFail($id)->update($validated);
        return redirect(route('organizations.index'))->with('success','Organization Updated!');
    }

    public function destroy($id) {
        Organization::findOrFail($id)->delete();
        return redirect(route('organizations.index'))->with('success','Organization Deleted!');
    }
}
