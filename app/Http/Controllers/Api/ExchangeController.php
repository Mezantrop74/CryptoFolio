<?php

namespace App\Http\Controllers\Api;

use App\Domain\Convertor\Str;
use App\Domain\Exchange\Api\Profit;
use App\Http\Controllers\Controller;
use App\Models\CryptoObserver;
use App\Models\Rate;
use App\Models\Wallet;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Http\Request;

class ExchangeController extends Controller
{
    public function Calculate(Request $request)
    {
        $this->validate($request, [
            'sender_wallet_id' => 'required|string',
            'sender_amount' => 'required|numeric|gt:0',
            'commission' => 'nullable|numeric',
            'receiver_observer_id' => 'nullable|uuid',
            'receiver_amount' => 'nullable|numeric',
        ]);

        $response = [
            'errors' => [],
            'message' => '',
            'receiver_amount' => null,
        ];

        $wallet = Wallet::where(['wallet_id' => $request->sender_wallet_id, 'user_id' => auth()->user()->id])->with('crypto')->firstOrFail();
        $balance = $wallet->balance;
        $balance = $balance->minus($request->sender_amount, RoundingMode::DOWN);
        $decPercentCommission = BigDecimal::of($request->commission ?? 0)->dividedBy(100, 8, RoundingMode::DOWN);
        if (!$decPercentCommission->isZero()) {
            $commission = BigDecimal::of($request->sender_amount)->multipliedBy($decPercentCommission);
            $balance = $wallet->balance->minus(BigDecimal::of($request->sender_amount)->plus($commission));
        }
        if ($balance->getAmount()->isNegative()) {
            $response['errors'][] = !$decPercentCommission->isZero() ? __('Amount and commission are bigger then wallet balance') : __('Amount is bigger then wallet balance');
        }

        if ($request->receiver_observer_id) {
            $receiverObserver = CryptoObserver::where('observer_id', $request->receiver_observer_id)->firstOrFail();
            $senderRate = Rate::where('crypto_id', $wallet->crypto_id)->latest()->first();
            $receiverRate = Rate::where('crypto_id', $receiverObserver->crypto_id)->latest()->first();
            $rate = BigDecimal::of($senderRate->rate)->dividedBy($receiverRate->rate, 14, RoundingMode::DOWN);
            $receiverAmount = $request->receiver_amount ? BigDecimal::of($request->receiver_amount) : BigDecimal::of($request->sender_amount)->multipliedBy($rate);
            $response['receiver_amount'] = Str::TrimZeroes($receiverAmount);
            if ($decPercentCommission) {
                $receiverAmount = $receiverAmount->minus(BigDecimal::of($request->sender_amount)->multipliedBy($rate)->multipliedBy($decPercentCommission));
            }
            $response['receiver_amount_with_commission'] = Str::TrimZeroes($receiverAmount);
        }
        return response()->json($response);
    }


    public function ProfitController(Request $request, Profit $profitService)
    {
        $this->validate($request, [
            'exchange_id' => 'required|string',
        ]);

        $exchange = auth()->user()->exchanges()->where('exchange_id', $request->exchange_id)->with(['senderRate.crypto', 'receiverRate.crypto'])->firstOrFail();
        return response()->json($profitService->CalculateProfit($exchange));
    }

    public function ProfitsController(Request $request, Profit $profitService)
    {
        $response = [
            'errors' => [],
            'message' => "",
            'exchanges' => [],
        ];
        $exchanges = auth()->user()->exchanges()->with(['senderRate.crypto', 'receiverRate.crypto'])->get();
        foreach ($exchanges as $e) {
            $response['exchanges'][$e->exchange_id] = $profitService->CalculateProfit($e);
        }

        return response()->json($response);
    }
}
