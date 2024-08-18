<?php

namespace App\Http\Controllers;

use App\Models\AdminRole;
use Illuminate\Http\Request;

class AdminRoleController extends Controller
{
    public function index(){
        $data['adminRoles'] = AdminRole::where('id','!=','1')->where('is_pos',true)->get();
        return view('admin-roles.index',$data);
    }

    public function create(){
        return view('admin-roles.create');
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required',
            'module_access' => 'required|array|min:1',
            'status' => 'required|boolean'
        ]);
        $moduleAccess = $validated['module_access'];
        $moduleAccess[] = config('constants.role_modules.dashboard.value');
        $validated['module_access'] = $moduleAccess;
        $validated['is_pos'] = true;
        AdminRole::create($validated);
        return redirect(route('admin-roles.index'))->with('success','Admin Role added successfully');
    }

    public function edit($id){
        $data['adminRole'] = AdminRole::where('id','!=','1')->findOrFail($id);
        return view('admin-roles.edit',$data);
    }

    public function update(Request $request, $id){
        if($id == 1) {
            abort(404);
        }
        $validated = $request->validate([
            'name' => 'required',
            'module_access' => 'required|array|min:1',
            'status' => 'required|boolean'
        ]);
        $moduleAccess = $validated['module_access'];
        $moduleAccess[] = config('constants.role_modules.dashboard.value');
        $validated['module_access'] = $moduleAccess;
        $validated['is_pos'] = true;
        AdminRole::findOrFail($id)->update($validated);
        return redirect(route('admin-roles.index'))->with('success','Admin Role updated successfully');
    }

    public function destroy($id){
        if($id == 1) {
            abort(404);
        }
        AdminRole::findOrFail($id)->delete();
        return redirect(route('admin-roles.index'))->with('success','Admin Role deleted successfully');
    }
}
