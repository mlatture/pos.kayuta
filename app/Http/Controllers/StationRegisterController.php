<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StationRegisters;
class StationRegisterController extends Controller
{
    
    public function showStation()
    {
        $registers = StationRegisters::all();
        return view('cart.components.header', compact('registers'));
    }

    public function set(Request $request)
    {
       
        session([
            'current_register_id' => $request->register_id,
            'current_register_name' => $request->register_name,
        ]);

        return response()->json(['success' => true]);
    }

    public function create()
    {
        $registers = StationRegisters::count();

        $new = StationRegisters::create([
            'name' => 'Register ' . ($registers + 1),
        ]);
        return response()->json(['success' => true, 'new_register' => $new]);

    }

    public function getStation()
    {
        $stations = StationRegisters::all();


        $stationData = $stations->map(function($station){
            return  [
                'id' => $station->id,
                'name' => $station->name
            ];
        });


        return response()->json($stationData);
    }



}
