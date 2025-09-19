<?php

namespace App\Http\Controllers;

use App\Models\AdminRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminRoleController extends Controller
{
    public function index()
    {
        $data['adminRoles'] = AdminRole::where('is_pos', true)->get();

        
        return view('admin-roles.index', $data);
    }

    public function create()
    {
        return view('admin-roles.create')->with('tablesPermissions', $this->getTables());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:admin_roles,name',
            'module_access' => 'nullable|array',
            'status' => 'required|boolean'
        ]);

        $moduleAccess = $validated['module_access'] ?? [];
        $moduleAccess[] = $this->getDashboardModule();
        $moduleAccess = array_unique($moduleAccess);

        $validated['module_access'] = json_encode($moduleAccess);
        $validated['is_pos'] = true;

        AdminRole::create($validated);
        return redirect(route('admin-roles.index'))->with('success', 'Admin Role added successfully');
    }

    public function edit($id)
    {
        $data['adminRole'] = AdminRole::whereNotIn('id', [1])->findOrFail($id);
        return view('admin-roles.edit', $data)->with('tablesPermissions', $this->getTables());
    }

    public function update(Request $request, $id)
    {
        if ($id == 1) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:admin_roles,name,' . $id,
            'module_access' => 'nullable|array',
            'status' => 'required|boolean'
        ]);

        $moduleAccess = $validated['module_access'] ?? [];
        $moduleAccess[] = $this->getDashboardModule();
        $moduleAccess = array_unique($moduleAccess);

        $validated['module_access'] = json_encode($moduleAccess);
        $validated['is_pos'] = true;

        AdminRole::findOrFail($id)->update($validated);
        return redirect(route('admin-roles.index'))->with('success', 'Admin Role updated successfully');
    }

    public function destroy($id)
    {
        if ($id == 1) {
            return redirect()->back()->with('error', 'This role cannot be deleted.');
        }

        AdminRole::findOrFail($id)->delete();
        return redirect(route('admin-roles.index'))->with('success', 'Admin Role deleted successfully');
    }

    private function getDashboardModule()
    {
        return config('constants.role_modules.dashboard.value', 'dashboard');
    }

    public function getTables(): array
    {
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        $excludedTables = ['dictionary_tables', 'whitelist_tables', 'oauth_refresh_tokens', 'oauth_access_tokens', 'oauth_auth_codes', 'oauth_clients', 'oauth_personal_access_clients'];
        $tables = array_diff($tables, $excludedTables);

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
