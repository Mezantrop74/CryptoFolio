<?php

namespace App\Http\Controllers\Api;

use App\Domain\Convertor\Str;
use App\Http\Controllers\Controller;
use App\Models\CryptoObserver;
use App\Models\Rate;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use DB;
use Illuminate\Http\Request;

class CryptoController extends Controller
{
    public function CryptoObserverRateChart(Request $request)
    {
        $this->validate($request, [
            'observer_id' => 'required|string|exists:crypto_observers,observer_id',
            'period' => 'nullable|string',
        ]);

        $response = [
            'errors' => [],
            'message' => "",
            'rates' => [],
        ];

        $observer = CryptoObserver::where([
            'user_id' => auth()->user()->id,
            'observer_id' => $request->observer_id,
        ])->with('crypto')->first();

        $rates = Rate::select(['rate', 'created_at'])->where([
            'crypto_id' => $observer->crypto_id,
            'ticker_type' => $observer->ticker_type,
        ])->whereIntegerInRaw('id', Rate::selectRaw('MAX(id) as id')->where([
            'crypto_id' => $observer->crypto_id,
            'ticker_type' => $observer->ticker_type,
        ])->groupBy(DB::raw('Date(created_at)'))->pluck('id')->toArray())->pluck('rate', 'created_at');

        $observerBalance = $observer->wallets()->selectRaw('SUM(balance) as full_balance')->first()->toArray();
        $observerMoney = Money::of($observerBalance['full_balance'], $observer->crypto->currency);
        foreach ($rates as $date => $rate) {
            $response['rates'][$date] = Str::TrimZeroes($observerMoney->multipliedBy($rate, RoundingMode::DOWN)->getAmount());
        }
        return response()->json($response);
    }
}
