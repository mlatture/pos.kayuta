<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Addon;
use Exception;
class AddOnsController extends Controller
{
    protected $addons;

    public function __construct(Addon $addons)
    {
        $this->addons = $addons;
    }

    public function index()
    {
        $addons = $this->addons->all();

        return view('addons.index', compact('addons'));
    }

    public function edit($id)
    {
        $addon = $this->addons->find($id);
    
        if (!$addon) {
            return redirect()->route('addons.index')->with('error', 'Addon not found.');
        }
    
        return view('addons.edit', compact('addon'));
    }
    

    public function update(Request $request, $id)
    {
        $addon = $this->addons->find($id);
    
        if (!$addon) {
            return redirect()->back()->with('error', 'Addon not found.');
        }
    
        $validatedData = $request->validate([
            'addon_name' => 'required|string|max:255',
            'price' => 'required|integer',
            'addon_type' => 'required|string|max:255',
            'capacity' => 'required|integer',
        ]);
    
        $addon->update($validatedData);
    
        return redirect()->route('addons.index')->with('success', 'Success, Addon has been updated.');
    }

    public function destroy(Addon $addon)
    {
    

        if (!$addon) {
            return redirect()->back()->with('error', 'Addon not found');

        }
        $addon->delete();
        
        return response()->json([
            'success' => true,
        ]);
    }
    
}