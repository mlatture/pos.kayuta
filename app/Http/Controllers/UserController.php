<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request){
        $validated = $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required'
        ]);
        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);
        return redirect()->back()->with('success','User Added!');
    }

  
}
