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
}
