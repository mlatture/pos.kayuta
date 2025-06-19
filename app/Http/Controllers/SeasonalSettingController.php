<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\RateTier;
use App\Models\SeasonalSetting;
class SeasonalSettingController extends Controller
{
    //

    public function index()
    {
        $setting = session('success') ? null : SeasonalSetting::latest()->first();

        $rateTiers = RateTier::distinct('tier')->pluck('tier')->toArray();
        return view('admin.seasonal.index', compact('setting', 'rateTiers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'default_rate' => 'nullable|numeric',
            'discount_percentage' => 'nullable|numeric',
            'renewal_deadline' => 'nullable|date',
            'deposit_amount' => 'nullable|numeric',
            'rate_tiers' => 'nullable|array',
        ]);

        $data['rate_tiers'] = $request->rate_tiers;
        SeasonalSetting::create($data);

        return redirect()->back()->with('success', 'Seasonal settings saved.');
    }
}
