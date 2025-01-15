<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class PDFController extends Controller
{
    public function generate_zOutPDF(Request $request)
    {
        $reportData = $request->input('reportData');
    
        $dateRangeFormatted = Carbon::parse($reportData['selectedDateRange'] ?? 'N/A')
            ->format('l, F j, Y');
    
        $title = "POS Z - Out Report ($dateRangeFormatted) (Station: " . 
        ($reportData['selectedStatName'] ?? 'N/A') . 
        ") (User: " . ($reportData['selectedUserName'] ?? 'N/A') . ")";
    
        $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $title) . '.pdf';    
        $data = [
            'title' => $title,
            'grossSales' => $reportData['grossSales'] ?? '0.00',
            'netSales' => $reportData['netSales'] ?? '0.00',
            'salesTax' => $reportData['salesTax'] ?? '0.00',
            'netTax' => $reportData['netTax'] ?? '0.00',
            'totalSales' => $reportData['totalSales'] ?? '0.00',
            'netTotalSales' => $reportData['netTotalSales'] ?? '0.00',
            'salesTranCount' => $reportData['salesTranCount'] ?? '0',
            'netTranCount' => $reportData['netTranCount'] ?? '0',
            'salesActivity' => $reportData['salesActivity'] ?? [],
            'paymentSummary' => $reportData['paymentSummary'] ?? [],
            'creditCardListing' => $reportData['creditCardListing'] ?? [],
            'userActivity' => $reportData['userActivity'] ?? [],
        ];
    
        $pdf = PDF::loadView('reports.components.z-out-report-pdf', $data)
        ->setPaper('A4', 'portrait');
    
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
