<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\SeasonalRenewal;

class SeasonalRenewalGuestController extends Controller
{
    //

    public function show(User $user)
    {
        $renewal = SeasonalRenewal::where('customer_id', $user->id)->firstOrFail();

        return view('seasonal.guest-show', compact('user', 'renewal'));
    }

    public function respond(Request $request, User $user)
    {
        $validated = $request->validate([
            'response' => 'required|in:accepted,declined',
        ]);

        $renewal = SeasonalRenewal::where('customer_id', $user->id)->firstOrFail();
        $renewal->update([
            'status' => $validated['response'],
            'response_date' => now(),
            'renewed' => $validated['response'] === 'accepted',
        ]);

        return view('seasonal.response-confirmation', [
            'status' => $validated['response'],
        ]);
    }
}
