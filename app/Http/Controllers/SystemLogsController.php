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
        if ($request->ajax()) {
            $logs = SystemLog::select(['id', 'transaction_type', 'sale_amount', 'status', 'payment_type', 'confirmation_number', 'customer_name', 'customer_email', 'user_id', 'description', 'before', 'after', 'created_at'])->latest();

            return DataTables::of($logs)
                ->addIndexColumn()
                ->editColumn('created_at', function ($log) {
                    return Carbon::parse($log->created_at)->format('F j, Y g:i A');
                })
                ->editColumn('before', function ($log) {
                    $before = is_array($log->before) ? $log->before : json_decode($log->before ?? '[]', true);
                    if (empty($before)) {
                        return '-';
                    }

                    $lines = collect($before)
                        ->map(function ($val, $key) {
                            return "$key: " . (is_scalar($val) ? $val : json_encode($val));
                        })
                        ->implode("\n");

                    return '<pre>' . e($lines) . '</pre>';
                })
                ->editColumn('after', function ($log) {
                    $after = is_array($log->after) ? $log->after : json_decode($log->after ?? '[]', true);
                    if (empty($after)) {
                        return '-';
                    }

                    $lines = collect($after)
                        ->map(function ($val, $key) {
                            return "$key: " . (is_scalar($val) ? $val : json_encode($val));
                        })
                        ->implode("\n");

                    return '<pre>' . e($lines) . '</pre>';
                })
                ->rawColumns(['before', 'after'])
                ->make(true);
        }

        return view('admin.logs.index');
    }
}
