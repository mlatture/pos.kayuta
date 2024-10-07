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
    public function whitelist(): Factory|View|Application
    {
        return view('dynamic-tables.whitelist.index')
            ->with('whitelists', WhitelistTable::latest()->get());
    }

    public function edit_table($table): Factory|View|Application
    {
        $whitelistedTable = WhitelistTable::where('table_name', $table)->first();
        if (!$whitelistedTable) {
            abort(403, 'Table not allowed');
        }

        return view('dynamic-tables.dictionary.index')
            ->with('columns', Schema::getColumnListing($table))
            ->with('dictionary', DictionaryTable::where('table_name', $table)->select('display_name', 'field_name', 'description', 'viewable', 'order', 'visibility')->get()->keyBy('field_name')->toArray())
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
                        'order' => 1,
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


    public function dynamic_module_records($table): Factory|View|Application {
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

    public function dynamic_module_create_form_data($table, $id = null): Factory|View|Application {
        $whitelistedTable = WhitelistTable::where('table_name', $table)->first();
        if (!$whitelistedTable) {
            abort(403, 'Table not allowed');
        }

        return view('dynamic-tables.module.form')
            ->with('isEdit', isset($id))
            ->with('module', isset($id) ? 'Edit' : 'Create')
            ->with('moduleData', isset($id) ? DB::table($table)->where('id', $id)->first() : [])
            ->with('columns', Schema::getColumnListing($table))
            ->with('dictionaryFieldsDesc', Helpers::getDictionaryFields($table, true))
            ->with('dictionaryFields', DictionaryTable::where('table_name', $table)->select('display_name', 'field_name', 'description', 'viewable', 'order', 'visibility')->get()->keyBy('field_name')->toArray())
            ->with('table', $table)
            ->with('formattedTable', ucwords(str_replace('_', ' ', $table)));
    }

    public function dynamic_module_store_form_data(Request $request, $table): RedirectResponse {
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

    public function dynamic_module_update_form_data(Request $request, $table, $id): RedirectResponse {
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
}
