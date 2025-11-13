<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;            // your customer model (type-hint)
use App\Models\Waiver;
use App\Models\WaiverEvent;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class AdminWaiverController extends Controller
{
    // Modal body (HTML) – sirf shell + table placeholder
    public function index(User $user, Request $request)
    {
        // yeh partial view modal ke andar load hoga (AJAX se)
        return view('admin.waivers.unattached-modal', [
            'user' => $user,
        ]);
    }

    // DataTables JSON (unattached waivers newest first + filters)
    public function data(User $user, Request $request)
    {
        $email = trim((string)$request->get('email', ''));
        $name  = trim((string)$request->get('name', ''));
        $from  = $request->get('date_from');
        $to    = $request->get('date_to');

        $q = Waiver::query()
            ->where('status', 'unattached')
            // ->whereNull('deleted_at')
            ->when($email !== '', fn($qq) => $qq->where('email', $email))
            // ->when($name !== '', fn($qq)  => $qq->where('name', 'like', "%{$name}%"))
            // ->when($from, fn($qq) => $qq->where('created_at', '>=', $from))
            // ->when($to,   fn($qq) => $qq->where('created_at', '<=', $to))
            ->latest();

// dd($q,'sadasda');

        return DataTables::of($q)
            ->addColumn('checkbox', fn ($w) =>
                '<input type="checkbox" class="row-check" value="'.$w->id.'">')
            ->addColumn('download', function ($w) {
                $url = URL::temporarySignedRoute('waivers.download', now()->addMinutes(15), ['waiver' => $w->id]);
                return '<a href="'.$url.'" target="_blank" class="btn btn-sm btn-link">PDF</a>';
            })
            ->addColumn('status_badge', fn ($w) => '<span class="badge bg-secondary">'.$w->status.'</span>')
            ->editColumn('created_at', fn ($w) => $w->created_at->format('Y-m-d H:i'))
            ->rawColumns(['checkbox','download','status_badge'])
            ->make(true);
    }

    // Bulk attach -> selected unattached waivers to this customer
    public function attach(User $user, Request $request)
    {
        $ids = (array)$request->input('waiver_ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Select at least one waiver.'], 422);
        }

        DB::transaction(function () use ($ids, $user) {
            $waivers = Waiver::whereIn('id', $ids)->lockForUpdate()->get();

            foreach ($waivers as $w) {
                // set customer + status
                $w->customer_id = $user->id;
                $w->status = 'attached';
                $w->save();

                // create Document row (Category: Waiver)
                Document::create([
                    'customer_id' => $user->id,
                    'category'    => 'Waiver',
                    'title'       => 'Liability Waiver',
                    'path'        => $w->pdf_path,
                    'meta'        => ['booking_id' => $w->booking_id, 'hash' => $w->doc_hash],
                ]);

                // audit log
                WaiverEvent::create([
                    'waiver_id' => $w->id,
                    'actor_id'  => auth()->id(), // adjust if 'auth:admin'
                    'event'     => 'attached',
                    'meta'      => null,
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Attached successfully.']);
    }

    // Bulk soft delete unattached waivers
    public function bulkDelete(Request $request)
    {
        $ids = (array)$request->input('waiver_ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Select at least one waiver.'], 422);
        }

        DB::transaction(function () use ($ids) {
            $waivers = Waiver::whereIn('id', $ids)->lockForUpdate()->get();

            foreach ($waivers as $w) {
                $w->status = 'deleted';
                $w->delete(); // soft delete
                WaiverEvent::create([
                    'waiver_id' => $w->id,
                    'actor_id'  => auth()->id(),
                    'event'     => 'deleted',
                    'meta'      => null,
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Soft deleted.']);
    }

    // Optional: direct download for admins (signed)
    public function download(Waiver $waiver)
    {
        return Storage::download($waiver->pdf_path);
    }
}
