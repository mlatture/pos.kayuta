<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemLog;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SystemLogsController extends Controller
{
    public function index(Request $request)
    {
        // Return types for filter dropdown
        if ($request->get('get_types')) {
            $types = SystemLog::distinct()->pluck('transaction_type')->filter()->values();
            return response()->json(['types' => $types]);
        }

        if ($request->ajax()) {
            $logs = SystemLog::query();

            // Date range filter
            if ($request->has('date_range') && $request->date_range) {
                [$start, $end] = explode(' to ', $request->date_range);
                $logs->whereBetween('created_at', [Carbon::parse($start)->startOfDay(), Carbon::parse($end)->endOfDay()]);
            }

            // Type multi-select filter
            if ($request->has('types') && is_array($request->types)) {
                $logs->whereIn('transaction_type', $request->types);
            }

            return DataTables::of($logs->latest())
                ->addIndexColumn()
                ->editColumn('created_at', fn($log) => Carbon::parse($log->created_at)->format('F j, Y g:i A'))
                ->editColumn('before', fn($log) => $this->formatJsonToPre($log->before))
                ->editColumn('after', fn($log) => $this->formatJsonToPre($log->after))
                ->rawColumns(['before', 'after'])
                ->make(true);
        }

        return view('admin.logs.index');
    }

    private function formatJsonToPre($json)
    {
        $data = is_array($json) ? $json : json_decode($json ?? '[]', true);
        if (empty($data)) {
            return '-';
        }

        $lines = collect($data)->map(fn($val, $key) => "$key: " . (is_scalar($val) ? $val : json_encode($val)))->implode("\n");
        return '<pre>' . e($lines) . '</pre>';
    }
}
