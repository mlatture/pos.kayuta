<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RateTier;
use Illuminate\Support\Str;

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
            return redirect()->back()->with('error', 'Rate Tier not found');
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

        return redirect()->route('rate-tier.index')->with('success', 'Success, Rate Tier updated successfully.');
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

    public function addImage($id)
    {
        $rate_tiers = RateTier::find($id);

        return view('rate-tier.add-image')->with('rate_tiers', $rate_tiers);
    }

    public function uploadImage(Request $request, $id)
    {
        $rate_tier = RateTier::find($id);

        if (!$rate_tier) {
            return redirect()->back()->with('error', 'Rate Tier not found.');
        }

        $uploadedImages = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file->isValid()) {
                    $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

                    $image = app('image')->resize($file);

                    $path = $file->move(public_path('storage/rate_tiers'), $filename);
                    app('image')->save($image, $path);
                    $uploadedImages[] = $filename;
                }
            }
        }

        $existing = $rate_tier->images;

        if (is_string($existing)) {
            $existing = json_decode($existing, true);
        }

        $existing = is_array($existing) ? $existing : [];

        $allImages = array_merge($existing, $uploadedImages);

        $rate_tier->images = json_encode($allImages);

        $rate_tier->save();
        $rate_tier->refresh();
        return redirect()->back()->with('success', 'Images uploaded successfully!');
    }

    public function deleteImage($rate_tiersId, $filename)
    {
        $rate_tiers = RateTier::find($rate_tiersId);

        if (!$rate_tiers) {
            return response()->json(['success' => false, 'message' => 'Rate Tier not found.']);
        }

        $images = json_decode($rate_tiers->images, true);

        if (($key = array_search($filename, $images)) !== false) {
            unset($images[$key]);
        } else {
            return response()->json(['success' => false, 'message' => 'Image not found in list.']);
        }

        $rate_tiers->images = json_encode(array_values($images));
        $rate_tiers->save();

        $filePath = public_path('storage/rate_tiers/' . $filename);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return response()->json(['success' => true, 'message' => 'Image deleted successfully.']);
    }
}
