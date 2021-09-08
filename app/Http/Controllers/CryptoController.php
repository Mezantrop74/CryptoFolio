<?php

namespace App\Http\Controllers;


use App\Models\Crypto;

class CryptoController extends Controller
{
    public function index()
    {
        $observers = auth()->user()->cryptoObservers()->with(['wallets', 'crypto'])->get();
        $observableCryptoIds = auth()->user()->cryptoObservers()->select('crypto_id')->pluck('crypto_id')->toArray();
        $notObservableCryptos = Crypto::select('id', 'name', 'symbol')->whereIntegerNotInRaw('id', $observableCryptoIds)->get();
        return view('crypto.index', compact('observers', 'notObservableCryptos'));
    }
}
