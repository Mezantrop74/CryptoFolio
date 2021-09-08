<?php

namespace App\Http\Controllers\Admin;

use App\Domain\CoinMarketCap\RateFiller;
use App\Domain\Ticker\Tickers;
use App\Http\Controllers\Controller;
use App\Models\Rate;
use Illuminate\Http\Request;

class RateController extends Controller
{
    public function fill(Request $request)
    {
        RateFiller::fill();
        return redirect()->back()->with('success', __('Rate refresh was successfully finished.'));
    }
}
