<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GuestFile;
use App\Models\SystemLog;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class DocumentController extends Controller
{
    // Page shell; the table is server-side
    public function index(User $user)
    {
        return view('admin.customers.documents', compact('user'));
    }

    // Server-side DataTables endpoint (filters + paging + sorting)
    public function data(Request $request, User $user)
    {
        $category    = trim($request->get('category', ''));
        $showExpired = (bool)$request->boolean('show_expired', true);

        $q = GuestFile::where('customer_id', $user->id);

        if ($category !== '') {
            $q->where('file_category', $category);
        }

        if (!$showExpired) {
            $q->where(function ($qq) {
                $qq->whereNull('expiration_date')
                   ->orWhere('expiration_date', '>', now()->toDateString());
            });
        }

        $q->latest();

        return DataTables::of($q)
            ->addColumn('checkbox', fn ($f) => '<input type="checkbox" class="row-check" data-id="'.$f->id.'">')
            ->addColumn('category_badge', function ($f) {
                $name = ucwords(str_replace('_', ' ', $f->file_category));
                $map = [
                    'contracts'     => 'bg-primary',
                    'renewals'      => 'bg-info text-dark',
                    'non renewals'  => 'bg-secondary',
                    'waivers'       => 'bg-warning text-dark',
                    'vaccinations'  => 'bg-success',
                    'ids'           => 'bg-dark',
                ];
                $cls = $map[strtolower($name)] ?? 'bg-light text-dark';
                return '<span class="badge '.$cls.'">'.$name.'</span>';
            })
            ->addColumn('expires_h', function ($f) {
                if (!$f->expiration_date) return '—';
                $d = Carbon::parse($f->expiration_date);
                return $d->isPast()
                    ? '<span class="badge bg-warning text-dark">Expired '.$d->toDateString().'</span>'
                    : e($d->toDateString());
            })
            ->addColumn('added_h', fn ($f) => Carbon::parse($f->created_at)->toDateString()) // DATE only
            ->addColumn('actions', function ($f) {
                $openUrl   = asset('storage/'.$f->file_path);
                $deleteUrl = route('file.destroy', $f);

                $protected = in_array($f->file_category, ['contracts','renewals','non_renewals']);
                $canDelete = auth()->user()->hasPermission('Delete Contracts') || !$protected;

                $open = '<a href="'.$openUrl.'" target="_blank" class="btn btn-sm btn-outline-primary">
                           <i class="fas fa-up-right-from-square"></i> Open
                         </a>';

                $del = $canDelete
                    ? '<button type="button" class="btn btn-sm btn-outline-danger btn-single-del" data-url="'.$deleteUrl.'">
                         <i class="fas fa-trash"></i> Delete
                       </button>'
                    : '';

                return '<div class="btn-group">'.$open.$del.'</div>';
            })
            ->rawColumns(['checkbox','category_badge','expires_h','actions'])
            ->make(true);
    }

    // Single delete (kept your logic)
    public function destroy(Request $request, GuestFile $file)
    {
        $admin = $request->user();

        $protected = in_array($file->file_category, ['contracts', 'renewals', 'non_renewals']);
        if ($protected && !$admin->hasPermission('Delete Contracts')) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $absPath = public_path('storage/'.$file->file_path);
        if (is_file($absPath)) @unlink($absPath);

        SystemLog::create([
            'transaction_type' => 'file_management',
            'sale_amount'      => null,
            'status'           => 'success',
            'payment_type'     => null,
            'confirmation_number' => null,
            'customer_name'    => optional($file->customer)->name,
            'customer_email'   => optional($file->customer)->email,
            'user_id'          => $admin->id,
            'description'      => "Deleted guest file {$file->file_path} ({$file->file_category})",
            'before'           => [
                'file_path'   => $file->file_path,
                'file_name'   => $file->name,
                'category'    => $file->file_category,
                'customer_id' => $file->customer_id,
            ],
            'after'            => null,
            'created_at'       => now(),
        ]);

        $file->delete();

        return response()->json(['success' => true]);
    }

    // NEW: Bulk delete
    public function bulkDestroy(Request $request)
    {
        $ids = (array)$request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No files selected.'], 422);
        }

        $admin = $request->user();
        $files = GuestFile::whereIn('id', $ids)->get();

        $deleted = 0;
        $failed  = [];

        foreach ($files as $file) {
            $protected = in_array($file->file_category, ['contracts','renewals','non_renewals']);
            if ($protected && !$admin->hasPermission('Delete Contracts')) {
                $failed[$file->id] = 'Forbidden';
                continue;
            }

            $absPath = public_path('storage/'.$file->file_path);
            if (is_file($absPath)) @unlink($absPath);

            SystemLog::create([
                'transaction_type' => 'file_management',
                'sale_amount'      => null,
                'status'           => 'success',
                'payment_type'     => null,
                'confirmation_number' => null,
                'customer_name'    => optional($file->customer)->name,
                'customer_email'   => optional($file->customer)->email,
                'user_id'          => $admin->id,
                'description'      => "Bulk delete guest file {$file->file_path} ({$file->file_category})",
                'before'           => [
                    'file_path'   => $file->file_path,
                    'file_name'   => $file->name,
                    'category'    => $file->file_category,
                    'customer_id' => $file->customer_id,
                ],
                'after'            => null,
                'created_at'       => now(),
            ]);

            $file->delete();
            $deleted++;
        }

        return response()->json([
            'success' => true,
            'deleted_count' => $deleted,
            'failed' => $failed,
        ]);
    }
}
