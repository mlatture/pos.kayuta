<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WidgetController extends Controller
{
    public function todaysIncome()
    {
        $orders = $this->order->whereDate('created_at', date('Y-m-d'))->get();

        return view('home', compact('orders'));
    }
}
