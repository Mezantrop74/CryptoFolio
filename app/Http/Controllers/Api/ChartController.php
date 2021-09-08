<?php

namespace App\Http\Controllers\Api;

use App\Domain\CryptoObserver\Api\Charts;
use App\Domain\Ticker\Tickers;
use App\Http\Controllers\Controller;
use App\Models\CryptoObserver;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function CryptoObserverRateChart(Request $request)
    {
        $this->validate($request, [
            'observer_id' => 'required|string|exists:crypto_observers,observer_id',
            'period' => 'required|string|in:1h,3h,12h,24h,week,month,3month,6month,12month',
            'wallet_type' => 'required|string|in:wo,nwo,all',
        ]);

        $observer = CryptoObserver::where([
            'user_id' => auth()->user()->id,
            'observer_id' => $request->observer_id,
        ])->with('crypto')->first();

        return response()->json(Charts::observerChart($observer, $request->period, $request->wallet_type));
    }

    public function CryptoObserversRateChart(Request $request)
    {
        $this->validate($request, [
            'period' => 'nullable|string|in:1h,3h,12h,24h,week,month,3month,6month,12month',
            'type' => 'required|in:token,crypto,all',
            'wallet_type' => 'required|string|in:wo,nwo,all',
        ]);

        $observers = auth()->user()->cryptoObservers()->whereHas('crypto', function ($q) use ($request) {
            $q->where('ticker_type', Tickers::CMC);
            switch ($request->type) {
                case 'crypto':
                    $q->whereNull('platform');
                    break;
                case 'token':
                    $q->whereNotNull('platform');
                    break;
                default:
                    break;
            }
        })->with('crypto')->get();
        return response()->json(Charts::ObserverCharts($observers, $request->period, $request->wallet_type));
    }

    public function ConvertChart(Request $request)
    {
        $this->validate($request, [
            'left_observer' => 'required|string',
            'right_observer' => 'required|string',
            'period' => 'nullable|string|in:1h,3h,12h,24h,week,month,3month,6month,12month',
        ]);

        $leftObserver = auth()->user()->cryptoObservers()->where('observer_id', $request->left_observer)->limit(1)->firstOrFail();
        $rightObserver = auth()->user()->cryptoObservers()->where('observer_id', $request->right_observer)->limit(1)->firstOrFail();
        return response()->json(Charts::ConvertChart($leftObserver, $rightObserver, $request->period));
    }
}
