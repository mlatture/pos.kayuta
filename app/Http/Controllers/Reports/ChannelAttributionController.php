<?php

namespace App\Http\Controllers\Reports; 

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB; 
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChannelAttributionController extends Controller 
{
    public function index(Request $req) 
    {
        $orgId = (int)($req->get('organization_id') ?? 0);
        $from = $req->date('from'); $to = $req->date('to');
        $q = DB::table('channel_bookings as cb')
                ->join('booking_channels as bc','bc.id','=','cb.booking_channel_id')
                ->select('bc.name as channel_name','cb.booking_channel_id',
                    DB::raw('COUNT(cb.id) as bookings'),
                    DB::raw('SUM(cb.fee_total) as channel_fees'),
                    DB::raw('SUM(cb.fee_total) as net_after_fees'))->when($orgId, fn($qq)=>$qq->where('cb.organization_id',$orgId))
                ->when($from, fn($qq) => $qq->whereDate('cb.created_at', '>=', $from))
                ->when($to, fn($qq) => $qq->whereDate('cb.created_at', '<=', $to))
                ->groupBy('bc.name','cb.booking_channel_id')
                ->orderBy('bookings','desc');
        
        $rows = $q->paginate(50);
        
        return view('reports.channel.index', ['rows' => $rows, 'filters' => $req->only(['organization_id','from','to'])]);
    }
    
    public function export(Request $req): StreamedResponse
{
    $orgId = (int) ($req->get('organization_id') ?? 0);
    $from  = $req->get('from');
    $to    = $req->get('to');

    $q = DB::table('channel_bookings as cb')
        ->join('booking_channels as bc', 'bc.id', '=', 'cb.booking_channel_id')
        ->select([
            'cb.created_at',
            'bc.name as channel_name',
            DB::raw('cb.booking_channel_id as channel_id'), // <-- fixed
            'cb.fee_total',
            // 'cb.utm_source',
            // 'cb.utm_medium',
            // 'cb.utm_campaign',
        ])
        ->when($orgId, fn ($qq) => $qq->where('cb.organization_id', $orgId))
        ->when($from,  fn ($qq) => $qq->whereDate('cb.created_at', '>=', $from))
        ->when($to,    fn ($qq) => $qq->whereDate('cb.created_at', '<=', $to))
        ->orderByDesc('cb.created_at');

    $filename = 'channel_attribution'
        . ($from ? "_from-$from" : '')
        . ($to   ? "_to-$to"     : '')
        . '.csv';

    return response()->streamDownload(function () use ($q) {
        $out = fopen('php://output', 'w');

        // headers
        fputcsv($out, [
            'created_at',
            'channel_name',
            'channel_id',
            'currency',
            'channel_fee',
            // 'utm_source','utm_medium','utm_campaign',
        ]);

        foreach ($q->cursor() as $r) {
            fputcsv($out, [
                \Illuminate\Support\Carbon::parse($r->created_at)->format('Y-m-d H:i:s'),
                $r->channel_name,
                $r->channel_id,
                $r->currency,
                $r->fee_total,
                // $r->utm_source, $r->utm_medium, $r->utm_campaign,
            ]);
        }

        fclose($out);
    }, $filename);
}
}