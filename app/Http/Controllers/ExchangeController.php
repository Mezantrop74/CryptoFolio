<?php

namespace App\Http\Controllers;

use App\Models\CryptoObserver;
use App\Models\Exchange;
use App\Models\Rate;
use Brick\Math\RoundingMode;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Str;

class ExchangeController extends Controller
{
    public function index()
    {
        $observers = auth()->user()->cryptoObservers()->select(['observer_id', 'crypto_id'])
            ->with(['crypto' => function ($q) {
                $q->select(['id', 'name', 'symbol']);
            }])->get();

        $exchanges = auth()->user()->exchanges()
            ->with(['receiverCrypto',
                'senderCrypto',
                'receiverObserver',
                'senderObserver',
                'receiverWallet',
                'senderWallet'
            ])->latest()->paginate(25);
        return view('crypto.exchange.index', compact('observers', 'exchanges'));
    }

    public function show($exchange_id)
    {
        $exchange = auth()->user()->exchanges()->where('exchange_id', $exchange_id)
            ->with(['receiverCrypto',
                'senderCrypto',
                'receiverWallet',
                'senderWallet',
                'receiverRate',
                'senderRate',
            ])->firstOrFail();
        return view('crypto.exchange.show', compact('exchange'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'sender.amount' => 'required|numeric|gt:0|regex:/^\d*(\.\d{0,14})?$/',
            'receiver.amount' => 'required|numeric|gt:0|regex:/^\d*(\.\d{0,14})?$/',
            'commission' => 'nullable|numeric|gte:0|regex:/^-?\d*(\.\d{0,14})?$/',
            'exchange_id' => 'required|uuid',
            'note' => 'nullable|string',
        ]);


        DB::transaction(function () use ($request) {
            $exchange = auth()->user()->exchanges()
                ->where('exchange_id', $request->exchange_id)
                ->with(['senderWallet' => function ($q) {
                    $q->sharedLock();
                }, 'receiverWallet' => function ($q) {
                    $q->sharedLock();
                }])
                ->sharedLock()
                ->firstOrFail();


            $exchange->senderWallet->balance = $exchange->senderWallet->balance->plus($exchange->sender_amount, RoundingMode::DOWN)->minus($request->sender['amount'])->getAmount();
            $exchange->receiverWallet->balance = $exchange->receiverWallet->balance->minus($exchange->receiver_amount, RoundingMode::DOWN)->plus($request->receiver['amount'])->getAmount();
            if ($exchange->receiverWallet->balance->isNegative()) {
                throw ValidationException::withMessages([
                    'wallet_id' => sprintf(__('%s final balance must not be negative.'), $exchange->receiverWallet->name),
                ]);
            }
            $exchange->senderWallet->save();
            $exchange->receiverWallet->save();
            $exchange->update([
                'commission' => $request->commission,
                'sender_amount' => $request->sender['amount'],
                'receiver_amount' => $request->receiver['amount'],
                'note' => $request->note,
            ]);
        }, 5);
        return redirect()->back()->with('success', __('Exchange was successfully updated.'));
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'sender.crypto_id' => 'required|string|exists:crypto_observers,observer_id',
            'sender.wallet_id' => 'required|string|exists:wallets,wallet_id',
            'sender.amount' => 'required|numeric|gt:0|regex:/^\d*(\.\d{0,14})?$/',

            'receiver.crypto_id' => 'required|string|exists:crypto_observers,observer_id',
            'receiver.wallet_id' => 'required|string|exists:wallets,wallet_id',
            'receiver.amount' => 'required|numeric|gt:0|regex:/^\d*(\.\d{0,14})?$/',

            'commission' => 'nullable|numeric|gte:0|regex:/^-?\d*(\.\d{0,14})?$/',
            'note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $senderObserver = CryptoObserver::where([
                'user_id' => auth()->user()->id,
                'observer_id' => $request->sender['crypto_id']
            ])->with('crypto')->first();

            $receiverObserver = CryptoObserver::where([
                'user_id' => auth()->user()->id,
                'observer_id' => $request->receiver['crypto_id']
            ])->with('crypto')->first();

            $senderRate = Rate::where('crypto_id', $senderObserver->crypto_id)->latest()->first();
            $receiverRate = Rate::where('crypto_id', $receiverObserver->crypto_id)->latest()->first();
            if ($senderRate == null || $receiverRate == null) {
                throw ValidationException::withMessages([
                    'wallet_id' => __('No rates for this exchange pair. Wait.'),
                ]);
            }


            $senderWallet = $senderObserver->wallets()
                ->where('wallet_id', $request->sender['wallet_id'])
                ->lockForUpdate()
                ->first();

            $receiverWallet = $receiverObserver->wallets()
                ->where('wallet_id', $request->receiver['wallet_id'])
                ->lockForUpdate()
                ->first();

            if ($senderWallet == null || $receiverWallet == null) {
                throw ValidationException::withMessages([
                    'wallet_id' => __('Selected wallets do not belong to selected cryptos.'),
                ]);
            }

            if ($senderWallet->id == $receiverWallet->id) {
                throw ValidationException::withMessages([
                    'wallet_id' => __('Selected wallets can\'t be the same.'),
                ]);
            }

            $senderWallet->balance = $senderWallet->balance->minus($request->sender['amount'], RoundingMode::DOWN)->getAmount();
            if ($senderWallet->balance->isNegative()) {
                throw ValidationException::withMessages([
                    'wallet_id' => sprintf("%s final balance must not be negative.", $senderWallet->name),
                ]);
            }

            $receiverWallet->balance = $receiverWallet->balance->plus($request->receiver['amount'], RoundingMode::DOWN)->getAmount();
            $senderWallet->save();
            $receiverWallet->save();

            $exchange = new Exchange([
                'exchange_id' => Str::uuid(),
                'user_id' => auth()->user()->id,

                'sender_crypto_id' => $senderObserver->crypto_id,
                'sender_crypto_observer_id' => $senderObserver->id,
                'sender_rate_id' => $senderRate->id,
                'sender_amount' => $request->sender['amount'],
                'sender_wallet_id' => $senderWallet->id,

                'receiver_crypto_id' => $receiverObserver->crypto_id,
                'receiver_crypto_observer_id' => $receiverObserver->id,
                'receiver_rate_id' => $receiverRate->id,
                'receiver_amount' => $request->receiver['amount'],
                'receiver_wallet_id' => $receiverWallet->id,

                'commission' => $request->commission,
                'note' => $request->note,
            ]);
            $exchange->profit = 0;
            $exchange->save();
        }, 5);
        return redirect()->back()->with('success', __('Exchange was successfully finished.'));
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'exchange_id' => 'required|string'
        ]);
        DB::transaction(function () use ($request) {
            $exchange = auth()->user()->exchanges()
                ->where('exchange_id', $request->exchange_id)
                ->with(['senderWallet' => function ($q) {
                    $q->sharedLock();
                }, 'receiverWallet' => function ($q) {
                    $q->sharedLock();
                }])
                ->sharedLock()
                ->firstOrFail();

            $nextExchangesAfterDeletingExchange = Exchange::where('id', '>', $exchange->id)
                ->where(function ($q) use ($exchange) {
                    $q->whereIntegerInRaw('sender_wallet_id', [$exchange->sender_wallet_id, $exchange->receiver_wallet_id])
                        ->OrWhereIntegerInRaw('receiver_wallet_id', [$exchange->sender_wallet_id, $exchange->receiver_wallet_id]);
                })->sharedLock()->count();
            if ($nextExchangesAfterDeletingExchange > 0) {
                throw ValidationException::withMessages([
                    'wallet_id' => __('You can\'t delete exchange with child exchanges.')
                ]);
            }

            $exchange->senderWallet->balance = $exchange->senderWallet->balance->plus($exchange->sender_amount, RoundingMode::DOWN)->getAmount();

            $exchange->receiverWallet->balance = $exchange->receiverWallet->balance->minus($exchange->receiver_amount, RoundingMode::DOWN)->getAmount();
            if ($exchange->receiverWallet->balance->isNegative()) {
                throw ValidationException::withMessages([
                    'wallet_id' => sprintf(__('%s final balance must not be negative.'), $exchange->receiverWallet->name),
                ]);
            }
            $exchange->senderWallet->save();
            $exchange->receiverWallet->save();
            $exchange->delete();
        }, 5);

        return redirect()->back()->with('success', __('Exchange was successfully deleted.'));
    }
}
