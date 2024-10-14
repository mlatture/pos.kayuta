<?php

namespace App\Http\Controllers;

use App\Models\AdminRole;
use App\Models\WhitelistTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminRoleController extends Controller
{
    public function index()
    {
        $data['adminRoles'] = AdminRole::where('id', '!=', '1')->where('is_pos', true)->get();
        return view('admin-roles.index', $data);
    }

    public function create()
    {
        return view('admin-roles.create')
            ->with('tablesPermissions', $this->getTables());
    }

    public function store(Request $request)
    {
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
        return redirect(route('admin-roles.index'))->with('success', 'Admin Role added successfully');
    }

    public function edit($id)
    {
        $data['adminRole'] = AdminRole::where('id', '!=', '1')->findOrFail($id);
        return view('admin-roles.edit', $data)
            ->with('tablesPermissions', $this->getTables());
    }

    public function update(Request $request, $id)
    {
        if ($id == 1) {
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
        return redirect(route('admin-roles.index'))->with('success', 'Admin Role updated successfully');
    }

    public function destroy($id)
    {
        if ($id == 1) {
            abort(404);
        }
        AdminRole::findOrFail($id)->delete();
        return redirect(route('admin-roles.index'))->with('success', 'Admin Role deleted successfully');
    }

    public function getTables(): array
    {
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        $tables = array_diff($tables, ['dictionary_tables', 'whitelist_tables', 'oauth_refresh_tokens', 'oauth_access_tokens', 'oauth_auth_codes', 'oauth_clients', 'oauth_personal_access_clients']);
        $tablesPermissions = [];
        foreach ($tables as $table) {
            foreach (['read', 'update', 'delete'] as $action) {
                $permissionKey = "{$action}_{$table}";
                $tablesPermissions[$permissionKey] = [
                    'name' => ucfirst($action) . ' ' . ucfirst(str_replace('_', ' ', $table)),
                    'value' => $permissionKey,
                ];
            }
        }
        return $tablesPermissions;
    }
}
