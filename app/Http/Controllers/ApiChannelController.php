<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Support\ApiKeys;

class ApiChannelController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'property_id' => ['required','integer'],
            'channel_id'  => ['required','integer'],
            'code'        => ['required','string','max:32','alpha_dash','unique:booking_channels,code'],
            'name'        => ['required','string','max:255'],
            'sandbox'     => ['sometimes','boolean'],
            'rate_limit_per_minute'  => ['sometimes','integer','min:1'],
            'rate_burst_per_minute'  => ['sometimes','integer','min:1'],
        ]);

        $plain = ApiKeys::generate();
        $hash  = ApiKeys::hash($plain);

        $id = DB::table('booking_channels')->insertGetId([
        'property_id'=>1,'channel_id'=>1,'code'=>'TEST','name'=>'Test','api_key_hash'=>$hash,'status'=>'active',
        'rate_limit_per_minute'=>100,'rate_burst_per_minute'=>300,
        'created_at'=>now(),'updated_at'=>now()
    ]);

        DB::table('channel_system_logs')->insert([
            'admin_id'        => optional($request->user())->id,
            'transaction_type'=> 'key_create',
            'status'          => 'success',
            'payload_snippet' => json_encode(['booking_channel_id' => $id]),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return redirect()->to(route('admin.business-settings.index') . '#' . 'apiChannels')->with('created_key_plain', $plain);
    }

    public function rotate(Request $request, int $id)
    {
        $plain = ApiKeys::generate();
        $hash  = ApiKeys::hash($plain);

        DB::table('booking_channels')->where('id',$id)->update([
            'api_key_hash' => $hash,
            'updated_at' => now(),
        ]);

        Cache::forget("api:key:".$hash); // new value cached on first use
        DB::table('channel_system_logs')->insert([
            'admin_id'        => optional($request->user())->id,
            'transaction_type'=> 'key_rotate',
            'status'          => 'success',
            'payload_snippet' => json_encode(['booking_channel_id' => $id]),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return redirect()->to(route('admin.business-settings.index') . '#' . 'apiChannels')->with('rotated_key_plain', $plain);
    }

    public function revoke(Request $request, int $id)
    {
        $row = DB::table('booking_channels')->where('id',$id)->first();
        if ($row) {
            DB::table('booking_channels')->where('id',$id)->update([
                'status' => 'inactive',
                'updated_at' => now(),
            ]);
            Cache::forget("api:key:".$row->api_key_hash);
        }
        DB::table('channel_system_logs')->insert([
            'admin_id'        => optional($request->user())->id,
            'transaction_type'=> 'key_revoke',
            'status'          => 'success',
            'payload_snippet' => json_encode(['booking_channel_id' => $id]),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
        return redirect()->to(route('admin.business-settings.index') . '#' . 'apiChannels')->with('message','Key revoked.');
    }
}