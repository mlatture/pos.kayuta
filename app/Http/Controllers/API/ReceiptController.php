<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Receipt;

class ReceiptController extends Controller
{
    public function index()
    {
        $categories = Category::where('account_type', 'Expense')->get();
    
        $receipts = Receipt::with('category')->orderByDesc('created_at')->get();
    
        return view('receipts.index', compact('receipts', 'categories'));
    }
    
    public function uploadReceiptLogo(Request $request)
    {
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/storage/receipt_logos', $filename);

            return response()->json([
                'success' => true,
                'filename' => $filename,
            ]);
        }
        return response()->json([
            'success' => false,
        ]);
    }
}
