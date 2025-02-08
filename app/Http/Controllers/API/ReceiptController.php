<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function uploadReceiptLogo(Request $request) 
    {
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/receipt_logos', $filename);

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
