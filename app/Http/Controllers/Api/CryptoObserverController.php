<?php

namespace App\Http\Controllers\Api;

use App\Domain\Convertor\Str;
use App\Domain\CryptoObserver\Api\Stats;
use App\Domain\Exchange\Api\Profit;
use App\Http\Controllers\Controller;
use Brick\Money\Money;
use Illuminate\Http\Request;

class CryptoObserverController extends Controller
{
    public function observers(Request $request, Stats $statsService)
    {
        $this->validate($request, [
            'period' => 'nullable|string|in:1h,3h,12h,24h,week,month,3month,6month,12month',
        ]);

        return response()->json($statsService->observersStats(auth()->user(), $request->period));
    }

    public function observer(Request $request, Stats $statsService)
    {
        $this->validate($request, [
            'observer_id' => 'required|string',
            'period' => 'nullable|string|in:1h,3h,12h,24h,week,month,3month,6month,12month',
        ]);

        $observer = auth()->user()->cryptoObservers()->where('observer_id', $request->observer_id)->with(['crypto', 'wallets', 'wallets.crypto', 'watchOnlyWallets', 'watchOnlyWallets.crypto'])->firstOrFail();
        $stats = $statsService->observerStats($observer, $request->period);
        $stats['total'] = [
            'crypto' => Str::TrimZeroes((string)Money::of($stats['total']['crypto']->getAmount($observer->crypto->currency), $observer->crypto->currency)->getAmount()),
            'usd' => Str::TrimZeroes((string)Money::of($stats['total']['usd']->getAmount('USD'), 'USD')->formatWith(Str::formatter())),
            'wo_crypto' => Str::TrimZeroes((string)Money::of($stats['total']['wo_crypto']->getAmount($observer->crypto->currency), $observer->crypto->currency)->getAmount()),
            'wo_usd' => Str::TrimZeroes((string)Money::of($stats['total']['wo_usd']->getAmount('USD'), 'USD')->formatWith(Str::formatter())),

        ];
        $stats['rate'] = Str::Beautify($stats['rate']);
        $stats['change']['usd'] = Str::TrimZeroes((string)Money::of($stats['change']['usd']->getAmount('USD'), 'USD')->formatWith(Str::formatter()));
        $stats['change']['wo_usd'] = Str::TrimZeroes((string)Money::of($stats['change']['wo_usd']->getAmount('USD'), 'USD')->formatWith(Str::formatter()));
        return response()->json($stats);
    }

    public function exchangeProfits(Request $request, Profit $profitService)
    {
        $this->validate($request, [
            'observer_id' => 'required|string',
        ]);

        $response = [
            'errors' => [],
            'message' => "",
            'exchanges' => [],
        ];
        $observer = auth()->user()->cryptoObservers()->select(['id'])->where('observer_id', $request->observer_id)->firstOrFail();
        $exchanges = auth()->user()->exchanges()->where(function ($q) use ($observer) {
            $q->where('sender_crypto_observer_id', $observer->id);
            $q->OrWhere('receiver_crypto_observer_id', $observer->id);
        })->with(['senderRate.crypto', 'receiverRate.crypto'])->get();
        foreach ($exchanges as $e) {
            $response['exchanges'][$e->exchange_id] = $profitService->CalculateProfit($e);
        }

        return response()->json($response);
    }


}
