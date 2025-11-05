<?php

namespace App\Http\Controllers; 

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB; 
use Illuminate\Http\Request; 
use App\Http\Requests\ChannelFeeUpsertRequest;

class ChannelFeesController extends Controller 
{
    public function edit(int $id) {
        $channel = DB::table('booking_channels')->find($id);
        abort_unless($channel, 404);
        
        $fee = DB::table('channel_fees')->where('booking_channel_id',$id)->where('is_active',1)->orderByDesc('id')->first();
        return view('settings.components.fees', compact('channel','fee'));
    }
    
    public function update(ChannelFeeUpsertRequest $req, int $id) 
    {
        $data = $req->validated();
        $exists = DB::table('channel_fees')->where('booking_channel_id',$id)->exists();
        DB::table('channel_fees')
            ->updateOrInsert(['booking_channel_id'=>$id,'is_active'=>1],
            [
                'name' => 'Default',
                'type' => $data['type'],
                'amount' => $data['amount'],
                'pass_to_customer' => false,
                'is_active' => (bool)($data['is_active'] ?? true),
                'valid_from' => $data['valid_from'] ?? null,
                'valid_to' => $data['valid_to'] ?? null,
                'updated_at' => now(), 'created_at' => now(),
            ]);
        return redirect()->route('admin.api_channels.fees.edit', $id)->with('status','Saved');
    }
}