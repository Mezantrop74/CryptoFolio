<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\CoinMarketCap\Api as CMCApi;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function apiUsage()
    {
        $resp = (new CMCApi)->_call('/v1/key/info', []);
        $cmcUsage = [
            'usage' => sprintf("%s/%s", $resp['data']['usage']['current_month']['credits_used'], $resp['data']['plan']['credit_limit_monthly'] ?? 'error')
        ];
        return response()->json($cmcUsage);
    }
}
