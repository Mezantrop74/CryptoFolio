<?php

namespace App\Http\Controllers;

use App\Domain\Ticker\Tickers;
use App\Models\Crypto;
use App\Models\CryptoObserver as Observer;
use App\Models\Rate;
use Illuminate\Http\Request;
use Str;

class CryptoObserverController extends Controller
{
    public function create(Request $request)
    {
        $this->validate($request, [
            'crypto_id' => 'required|integer|exists:cryptos,id',
        ]);
        if (auth()->user()->cryptoObservers()->where('crypto_id')->first()) {
            return redirect()->route('crypto.index')->with('error', __('Asset with this crypto already exists.'));
        }
        $obs = new Observer([
            'observer_id' => Str::uuid(),
            'crypto_id' => $request->crypto_id,
            'user_id' => auth()->user()->id,
            'ticker_type' => Tickers::default(),
        ]);
        $obs->save();
        return redirect()->route('crypto.index')->with('success', __('Asset was successfully added.'));
    }

    public function createCustom(Request $request)
    {
        $this->validate($request, [
            'ticker' => 'required|string|unique:cryptos,symbol',
        ]);

        $crypto = new Crypto([
            'cmc_id' => null,
            'name' => $request->ticker,
            'symbol' => $request->ticker,
            'slug' => '-',
            'ticker_type' => Tickers::CUSTOM
        ]);
        $crypto->save();

        $obs = new Observer([
            'observer_id' => Str::uuid(),
            'crypto_id' => $crypto->id,
            'user_id' => auth()->user()->id,
            'ticker_type' => 0,
        ]);
        $obs->save();
        return redirect()->route('crypto.observer.show', ['observer_id' => $obs->observer_id]);
    }

    public function show($observer_id)
    {
        $observer = Observer::where(['user_id' => auth()->user()->id, 'observer_id' => $observer_id])
            ->with(['crypto', 'wallets', 'watchOnlyWallets', 'notifications' => function ($q) {
                $q->orderByRaw('SIGN(trigger_percent) DESC, ABS(trigger_percent) DESC');
            }])->firstOrFail();
        $exchanges = auth()->user()->exchanges()->where('sender_crypto_observer_id', $observer->id)->orWhere('receiver_crypto_observer_id', $observer->id)
            ->with(['receiverCrypto',
                'senderCrypto',
                'receiverObserver',
                'senderObserver',
                'receiverWallet',
                'senderWallet'
            ])->latest()->paginate(6);
        $walletsSum = $observer->wallets()->sum('balance');
        $watchOnlyWallets = $observer->wallets()->withoutGlobalScope('not-watch-only')->watchOnly()->get();
        $watchOnlyAllowedCMCIds = array_keys(config('crypto.watch_only_slugs') ?? []);
        $rate = Rate::select('rate', 'created_at')->where('crypto_id', $observer->crypto_id)->orderByDesc('id')->limit(1)->first();
        return view('crypto.observers.show', compact('observer', 'walletsSum', 'rate', 'exchanges', 'watchOnlyWallets', 'watchOnlyAllowedCMCIds'));
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'observer_id' => 'required|string'
        ]);
        $observer = auth()->user()->cryptoObservers()->where('observer_id', $request->observer_id)->with(['wallets', 'sentExchanges', 'receivedExchanges'])->firstOrFail();
        if ($observer->wallets->count() > 0) {
            return redirect()->route('crypto.index')->with('error', __('You can\'t delete asset with active wallets.'));
        }

        if ($observer->sentExchanges->count() > 0 || $observer->receivedExchanges->count() > 0) {
            return redirect()->route('crypto.index')->with('error', __('You can\'t delete asset with exchanges.'));
        }

        $observer->notifications()->delete();
        $observer->delete();

        return redirect()->route('crypto.index')->with('success', __('Asset was successfully removed.'));
    }
}
