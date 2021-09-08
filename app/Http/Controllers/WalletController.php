<?php

namespace App\Http\Controllers;

use App\Domain\Cryptoapis\Api;
use App\Models\CryptoObserver;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function create(Request $request)
    {
        $this->validate($request, [
            'observer_id' => 'required|uuid|exists:crypto_observers,observer_id',
            'name' => 'required|array',
            'balance' => 'nullable|array',
            'name.*' => 'nullable|string',
            'balance.*' => 'nullable|numeric|gte:0|regex:/^\d*(\.\d{0,14})?$/',
            'note.*' => 'nullable|string',
        ]);
        $observer = CryptoObserver::where(['observer_id' => $request->observer_id, 'user_id' => auth()->user()->id])->firstOrFail();
        foreach ($request->name as $k => $name) {
            if ($name == null) {
                continue;
            }
            $wallet = new Wallet([
                'wallet_id' => Str::uuid(),
                'user_id' => auth()->user()->id,
                'crypto_id' => $observer->crypto_id,
                'crypto_observer_id' => $observer->id,
                'note' => $request->note[$k],
                'name' => $name,
                'balance' => $request->balance[$k] ?? 0,
                'start_balance' => $request->balance[$k] ?? 0,
            ]);
            $wallet->save();
        }
        return redirect()->back()->with('success', __("Wallets were successfully added."));
    }

    public function createWatchOnly(Request $request)
    {
        $this->validate($request, [
            'observer_id' => 'required|uuid|exists:crypto_observers,observer_id',
            'name' => 'required|array',
            'address' => 'required|array',
            'name.*' => 'nullable|string',
            'address.*' => 'required|max:300',
            'note.*' => 'nullable|string',
        ]);
        $observer = CryptoObserver::where(['observer_id' => $request->observer_id, 'user_id' => auth()->user()->id])->with('crypto')->firstOrFail();

        $watchOnlySlugs = config('crypto.watch_only_slugs') ?? [];
        $watchOnlyAllowedCMCIds = array_keys($watchOnlySlugs);
        if (!in_array($observer->crypto->cmc_id, $watchOnlyAllowedCMCIds)) {
            return redirect()->back()->with('error', sprintf(__("You can't add Watch-Only wallet to this asset yet. Only allowed: %s."), implode(", ", config('crypto.watch_only_slugs'))));
        }
        $api = new Api();
        foreach ($request->name as $k => $name) {
            if ($name == null || $request->address[$k] == null) {
                continue;
            }
            if (!$api->isValidWallet($request->address[$k], $watchOnlySlugs[$observer->crypto->cmc_id])) {
                return redirect()->back()->with('error', __("Invalid wallet address."));
            }
            $wallet = new Wallet([
                'wallet_id' => Str::uuid(),
                'user_id' => auth()->user()->id,
                'crypto_id' => $observer->crypto_id,
                'crypto_observer_id' => $observer->id,
                'note' => $request->note[$k],
                'name' => $name,
                'address' => $request->address[$k],
                'watch_only' => true,
            ]);
            try {
                $wallet->balance = $api->getBalance($request->address[$k], $watchOnlySlugs[$observer->crypto->cmc_id]);
                $wallet->start_balance = $wallet->balance->getAmount();
            } catch (\Exception $e) {
                Log::error($e);
                $wallet->balance = 0;
                $wallet->start_balance = 0;
            }
            $wallet->save();
            sleep(1);
        }
        return redirect()->back()->with('success', __("Wallets were successfully added."));
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'wallet_id' => 'required|string',
        ]);

        $wallet = Wallet::withoutGlobalScope('not-watch-only')->where(['wallet_id' => $request->wallet_id, 'user_id' => auth()->user()->id])->with(['sentExchanges', 'receivedExchanges'])->firstOrFail();
        if ($wallet->sentExchanges->count() > 0 || $wallet->receivedExchanges->count() > 0) {
            return redirect()->back()->with('error', __('You can\'t delete wallet with exchanges.'));
        }
        $wallet->delete();
        return redirect()->back()->with('success', __('Wallet was successfully deleted.'));
    }
}
