<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Brick\Math\RoundingMode;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Nesk\Puphpeteer\Puppeteer;
use Nesk\Rialto\Data\JsFunction;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class WalletController extends Controller
{
    public function checkAmount(Request $request)
    {
        $this->validate($request, [
            'wallet_id' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'commission' => 'nullable|numeric|min:0',
        ]);

        $response = [
            'errors' => [],
            'message' => '',
        ];

        $wallet = Wallet::where(['wallet_id' => $request->wallet_id, 'user_id' => auth()->user()->id])->firstOrFail();
        $balance = $wallet->balance;
        $balance = $balance->minus($request->amount, RoundingMode::DOWN);
        if ($request->commission) {
            $balance = $balance->minus($request->commission, RoundingMode::DOWN);
        }
        if ($balance->getAmount()->isNegative()) {
            $response['errors'][] = $request->commission || $request->commission > 0 ? __('Amount and commission are bigger then wallet balance') : __('Amount is bigger then wallet balance');
        }
        return response()->json($response);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'wallet_id' => 'required|string|exists:wallets,wallet_id',
            'note' => 'nullable|string',
        ]);

        $wallet = auth()->user()->wallets()->withoutGlobalScope('not-watch-only')->where('wallet_id', $request->wallet_id)->firstOrFail();
        $wallet->note = $request->note ?? null;
        $wallet->save();
        return response()->json([
            'errors' => [],
            'message' => '',
            'data' => [
                'wallet_id' => $wallet->wallet_id,
                'note' => $wallet->note
            ],
        ]);
    }

    public function debank(Request $request)
    {
        $this->validate($request, [
            'wallet_id' => 'required|uuid'
        ]);
        $wallet = auth()->user()->wallets()->withoutGlobalScope('not-watch-only')->where('wallet_id', $request->wallet_id)->firstOrFail();
        try {
            $puppeteer = new Puppeteer([
                'executable_path' => config('puppeteer.node_path'),
                'read_timeout' => 10
            ]);
            $browser = $puppeteer->launch([
                'executablePath' => config('puppeteer.chromium_path')
            ]);

            $page = $browser->newPage(['timeout' => 2000]);
            $page->goto('https://debank.com/profile/' . $wallet->address);
            $page->waitForSelector('[title="Wallet"]');
            sleep(2);
            $balance = $page->querySelectorEval('body', JsFunction::createWithParameters(['node'])
                ->body("return document.querySelector('[title=\"Wallet\"]').parentElement.querySelector('div:nth-child(2)').textContent;"));
            $browser->close();
        } catch (\Exception $e) {
            Log::channel('cmdlog')->error($e);
        } finally {
            return response()->json([
                'wallet' => $wallet->address ?? __('Error'),
                'balance' => $balance ?? __('Error'),
                'link' => sprintf("https://debank.com/profile/%s", $wallet->address)
            ]);
        }

    }

    public function airdrop(Request $request)
    {
        $this->validate($request, [
            'wallet_id' => 'required|uuid'
        ]);
        $wallet = auth()->user()->wallets()->withoutGlobalScope('not-watch-only')->where('wallet_id', $request->wallet_id)->firstOrFail();

        $unclaimed = [];
        $links = [];
        $requests = [];

        $fullLinkSegments = ["", "/fe", "/bs", "/poaps"];
        $queryWalletSegments = ["crv", "o", "fl", "e", "en", "early-adopter-poap", "spork-dao"];

        $client = new Client();
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; rv:78.0) Gecko/20100101 Firefox/78.0',
            'Referer' => 'https://earni.fi/',
            'Accept' => '*/*',
        ];

        foreach ($fullLinkSegments as $segment) {
            $links[] = sprintf("https://earni.fi/api/claimable%s/%s", $segment, $wallet->address);
        }
        foreach ($queryWalletSegments as $segment) {
            $links[] = sprintf("https://earni.fi/api/claimable/%s?addresses=%s", $segment, $wallet->address);
        }

        foreach ($links as $link) {
            $requests[] = new \GuzzleHttp\Psr7\Request('GET', $link, $headers);
        }
        $responses = [];
        $pool = new Pool($client, $requests, [
            'concurrency' => 5,
            'fulfilled' => function (Response $response) use (&$responses) {
                try {
                    $resp = json_decode($response->getBody(), true);
                    $responses[] = $resp;
                } catch (\Exception $e) {
                }
            },
            'rejected' => function (RequestException $reason, $index) {
                // this is delivered each failed request
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();

        foreach ($responses as $response) {
            if (is_array($response)) {
                $searchResult = $this->findKey($response, 'unclaimed');
                if (is_array($searchResult) && count($searchResult) > 0) {
                    foreach($searchResult as &$result) {
                        if(is_array($result)) {
                            $result = implode(", ", $result);
                        }
                    }
                    $unclaimed = array_merge($unclaimed, $searchResult);
                }
            }
        }

        return response()->json([
            'wallet' => $wallet->address,
            'unclaimed' => $unclaimed,
            'link' => sprintf('https://earni.fi/?address=%s', $wallet->address)
        ]);
    }

    function findKey($array, $searchKey)
    {
        foreach (new RecursiveIteratorIterator(new RecursiveArrayIterator($array), RecursiveIteratorIterator::SELF_FIRST) as $iteratorKey => $iteratorValue) {
            if ($iteratorKey == $searchKey) {
                return $iteratorValue;
            }
        }
        return false;
    }
}
