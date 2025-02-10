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

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50'
        ]);
    
        $registersCount = StationRegisters::count();
        $newRegisterName = $request->name . ' ' . ($registersCount + 1);
    
        $newRegister = StationRegisters::create([
            'name' => $newRegisterName,
        ]);
    
        return response()->json([
            'success' => true,
            'new_register' => $newRegister
        ]);
    }

    public function rename(Request $request) 
    {
        $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $register = StationRegisters::findOrFail($request->id);
        $register->name = $request->name;

        if($register->save()) {
            if (session('current_register_id') == $register->id) {
                session([
                    'current_register_name' => $request->name
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Register renamed successfully!',
                'new_name' => $register->name
            ]);

        }
       

        return response()->json([
            'success' => false,
            'message' => 'Failed to rename register. Please try again.'
        ], 500);
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
