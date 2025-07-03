<?php

namespace App\Http\Controllers;

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
            
            $destinationPath = public_path('storage/receipt_logos');
    
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0775, true);
            }
    
            $file->move($destinationPath, $filename);
    
            return response()->json([
                'success' => true,
                'filename' => $filename,
            ]);
        }
    
        return response()->json([
            'success' => false,
            'message' => 'No file uploaded',
        ]);
    }
}
