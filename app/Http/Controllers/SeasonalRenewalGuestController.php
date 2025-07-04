<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\SeasonalRenewal;

class SeasonalRenewalGuestController extends Controller
{
    //

    public function verifyLink(Request $request, User $user)
    {
        if (!$request->hasValidSignature()) {
            return redirect()->route('front.auth.login')->with('error', 'link was expired.');
        }

        $user = User::find($user);

        if (!$user) {
            return redirect()->route('front.auth.login')->with('error', 'User not found.');

        }

        Auth::guard('customer')->login($user);

        return redirect()->route('front.customer.profile')->with('success', 'Successfully logged in!');
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
