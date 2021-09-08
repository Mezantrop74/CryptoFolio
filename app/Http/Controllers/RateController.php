<?php

namespace App\Http\Controllers;

use App\Domain\Ticker\Tickers;
use App\Models\Rate;
use Illuminate\Http\Request;

class RateController extends Controller
{
    public function create(Request $request)
    {
        $this->validate($request, [
            'observer_id' => 'required|uuid',
            'rate' => 'numeric|gt:0|regex:/^\d*(\.\d{0,14})?$/',
        ]);

        $obs = auth()->user()->cryptoObservers()->where('observer_id', $request->observer_id)->with('crypto')->firstOrFail();
        if ($obs->crypto->ticker_type == Tickers::CUSTOM) {
            $rate = new Rate([
                'crypto_id' => $obs->crypto_id,
                'rate' => $request->rate,
                'ticker_type' => Tickers::CUSTOM,
            ]);
            $rate->save();
        } else {
            return redirect()->back()->with('error', __("You can't update rate for listed crypto."));
        }

        return redirect()->back()->with('success', __('Rate was successfully updated.'));
    }
}
