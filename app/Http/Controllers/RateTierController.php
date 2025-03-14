<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RateTier;

class RateTierController extends Controller
{
    protected $rate_tiers;
    public function __construct(RateTier $rate_tiers)
    {
        $this->rate_tiers = $rate_tiers;
    }

    public function index()
    {
        $rate_tiers = $this->rate_tiers->all();

        return view('rate-tier.index', compact('rate_tiers'));
    }

    public function edit($id)
    {
        $rateTier = $this->rate_tiers->find($id);

        if (!$rateTier) {
            return redirect()->back()
                ->with('error', 'Rate Tier not found');
        }

        return view('rate-tier.edit', compact('rateTier'));
        
    }

    public function update(Request $request, $id)
    {
        $rateTier = $this->rate_tiers->find($id);
    
        if (!$rateTier) {
            return redirect()->back()->with('error', 'Rate Tier not found.');
        }
    
        $validatedData = $request->validate([
            'tier' => 'required|string|max:255',
            'flatrate' => 'required|integer',
            'minimumstay' => 'required|integer',
            'weeklyrate' => 'required|integer',
            'useflatrate' => 'sometimes|boolean',
            'usedynamic' => 'sometimes|boolean',
        ]);
    
        $validatedData['useflatrate'] = $validatedData['useflatrate'] ?? 0;
        $validatedData['usedynamic'] = $validatedData['usedynamic'] ?? 0;
    
        $rateTier->update($validatedData);
    
        return redirect()->route('rate-tier.index')
            ->with('success', 'Success, Rate Tier updated successfully.');
    }

    public function destroy(RateTier $rateTier)
    {
    

        if (!$rateTier) {
            return redirect()->back()->with('error', 'Rate Tier not found');

        }
        $rateTier->delete();
        
        return response()->json([
            'success' => true,
        ]);
    }
    
}
