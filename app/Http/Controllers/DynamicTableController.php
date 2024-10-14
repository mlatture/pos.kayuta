<?php

namespace App\Http\Controllers;

use App\CPU\Helpers;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\WhitelistTable;
use App\Models\DictionaryTable;

class DynamicTableController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin_has_permission:' . config('constants.role_modules.whitelist_tables.value'));
    }

    public function whitelist(): Factory|View|Application
    {
        // Fetch all whitelisted tables from the database
        $whitelistsTables = WhitelistTable::latest()->get();
        $whitelistTableNames = $whitelistsTables->pluck('table_name')->toArray();
        $allTables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        $allTables = array_diff($allTables, ['dictionary_tables', 'whitelist_tables', 'oauth_refresh_tokens', 'oauth_access_tokens', 'oauth_auth_codes', 'oauth_clients', 'oauth_personal_access_clients']);
        $remainingTables = array_diff($allTables, $whitelistTableNames);
        $remainingTablesObject = [];
        foreach ($remainingTables as $table) {
            $remainingTablesObject[$table] = $table;
        }
        return view('dynamic-tables.whitelist.index')
            ->with('whitelists', $whitelistsTables)
            ->with('remainingTablesJson', json_encode($remainingTablesObject));
    }

    public function edit_table($table): Factory|View|Application
    {
        $whitelistedTable = WhitelistTable::where('table_name', $table)->first();
        if (!$whitelistedTable) {
            abort(403, 'Table not allowed');
        }

        return view('dynamic-tables.dictionary.index')
            ->with('columns', Schema::getColumnListing($table))
            ->with('dictionary', DictionaryTable::where('table_name', $table)->select('table_name', 'display_name', 'field_name', 'description', 'viewable', 'order', 'visibility', 'validation')->orderBy('order', 'ASC')->get()->keyBy('field_name')->toArray())
            ->with('table', $table);
    }

    public function update_table(Request $request, $table): RedirectResponse
    {
        try {
            $whitelistedTable = WhitelistTable::where('table_name', $table)->first();
            if (!$whitelistedTable) {
                abort(403, 'Table not allowed');
            }

            $dictionary = $request->input('dictionary');
            foreach ($dictionary as $original_column => $column_data) {
                DictionaryTable::updateOrInsert(
                    [
                        'table_name' => $table,
                        'field_name' => $original_column
                    ],
                    [
                        'display_name' => $column_data['display_name'],
                        'description' => $column_data['description'],
                        'validation' => $column_data['validation'],
                        'viewable' => array_key_exists('viewable', $column_data),
                        'visibility' => $column_data['visibility'] ?? 'all',
                    ]
                );
            }
            return redirect()
                ->route('admin.edit-table', $table)
                ->with('success', "{$table} table columns updated successfully.");
        } catch (\Exception $exception) {
            return redirect()
                ->route('admin.edit-table', $table)
                ->with('error', $exception->getMessage());
        }
    }


    public function dynamic_module_records($table): Factory|View|Application
    {
        $whitelistedTable = WhitelistTable::where('table_name', $table)->first();
        if (!$whitelistedTable) {
            abort(403, 'Table not allowed');
        }

        return view('dynamic-tables.module.listing')
            ->with('records', DB::table($table)->get()->toArray())
            ->with('columns', Schema::getColumnListing($table))
            ->with('dictionaryFieldsDesc', Helpers::getDictionaryFields($table, true))
            ->with('dictionaryFields', DictionaryTable::where('table_name', $table)->select('display_name', 'field_name', 'description', 'viewable', 'order', 'visibility')->get()->keyBy('field_name')->toArray())
            ->with('table', $table)
            ->with('formattedTable', ucwords(str_replace('_', ' ', $table)));
    }

    public function dynamic_module_create_form_data($table, $id = null): Factory|View|Application
    {
        if (!auth()->user()->hasPermission("update_{$table}")) {
            abort(403, 'Table not allowed');
        }

        return view('dynamic-tables.module.form')
            ->with('isEdit', isset($id))
            ->with('module', isset($id) ? 'Edit' : 'Create')
            ->with('moduleData', isset($id) ? DB::table($table)->where('id', $id)->first() : [])
            ->with('columns', Schema::getColumnListing($table))
            ->with('dictionaryFieldsDesc', Helpers::getDictionaryFields($table, true))
            ->with('dictionaryFields', DictionaryTable::where('table_name', $table)->select('display_name', 'field_name', 'description', 'viewable', 'order', 'visibility', 'validation')->orderBy('order', 'ASC')->get()->keyBy('field_name')->toArray())
            ->with('table', $table)
            ->with('formattedTable', ucwords(str_replace('_', ' ', $table)));
    }

    public function dynamic_module_store_form_data(Request $request, $table): RedirectResponse
    {
        try {
            DB::table($table)->insert($request->except('_token'));
            return redirect()
                ->route('admin.dynamic-module-records', $table)
                ->with('success', "{$table} data added successfully.");
        } catch (\Exception $exception) {
            return redirect()
                ->route('admin.dynamic-module-create-form-data', $table)
                ->with('error', $exception->getMessage());
        }
    }

    public function dynamic_module_update_form_data(Request $request, $table, $id): RedirectResponse
    {
        try {
            DB::table($table)->where('id', $id)->update($request->except(['_token', '_method']));
            return redirect()
                ->route('admin.dynamic-module-records', $table)
                ->with('success', "{$table} data updated successfully.");
        } catch (\Exception $exception) {
            return redirect()
                ->route('admin.dynamic-module-create-form-data', $table)
                ->with('error', $exception->getMessage());
        }
    }

    public function updateColumnOrder(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $orderData = $request->input('order');
            foreach ($orderData as $columnData) {
                DictionaryTable::where([
                    'field_name' => $columnData['column'],
                    'table_name' => $columnData['table_name']
                ])
                    ->update(['order' => $columnData['order']]);
            }
            return response()->json(['message' => 'Column ordered successfully.'], 200);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    public function add_table(): \Illuminate\Http\JsonResponse
    {
        try {
            $newTable = new WhitelistTable();
            $newTable->table_name = request()->selected_option;
            $newTable->save();

            return response()->json(['message' => "{$newTable->table_name} table added successfully."], 200);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    public function delete_table($table): \Illuminate\Http\JsonResponse
    {
        try {
            $tableName = ucwords(str_replace('_', ' ', $table));
            DictionaryTable::where('table_name', $table)->delete();
            WhitelistTable::where('table_name', $table)->delete();

            return response()->json(['message' => "{$tableName} deleted successfully."], 200);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

}
