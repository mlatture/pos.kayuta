<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\GuestFile;
use App\Models\SystemLog;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;


class DocumentController extends Controller
{
    public function index(User $user)
    {
        $files = GuestFile::where('customer_id', $user->id)->latest()->paginate(5);

        return view('admin.customers.documents', compact('user', 'files'));
    }

    public function destroy(Request $request, GuestFile $file)
    {
        $admin = $request->user();

        $protected = in_array($file->file_category, ['contracts', 'renewals', 'non_renewals']);
        if ($protected && !$admin->hasPermission('Delete Contracts')) {
            abort(403, 'You do not have permission to delete protected documents.');
        }

        $absPath = public_path('storage/' . $file->file_path);
        if (is_file($absPath)) {
            @unlink($absPath);
        }

        SystemLog::create([
            'transaction_type' => 'file_management',
            'sale_amount' => null,
            'status' => 'success',
            'payment_type' => null,
            'confirmation_number' => null,
            'customer_name' => optional($file->customer)->name,
            'customer_email' => optional($file->customer)->email,
            'user_id' => $admin->id,
            'description' => "Deleted guest file {$file->file_path} ({$file->file_category})",
            'before' => [
                'file_path' => $file->file_path,
                'file_name' => $file->name,
                'category' => $file->file_category,
                'customer_id' => $file->customer_id,
            ],
            'after' => null,
            'created_at' => now(),
        ]);

        $file->delete();

        return back()->with('success', 'File deleted.');
    }
}
