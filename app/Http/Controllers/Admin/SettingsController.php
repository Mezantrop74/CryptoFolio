<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Settings\ApiSettings;
use App\Http\Controllers\Controller;
use App\Models\Rate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function index(ApiSettings $apiSettings)
    {
        $lastRate = Rate::select('created_at')->latest()->first();
        return view('admin.settings.index', compact('lastRate'))->with([
            'apiSettings' => [
                'cmc_api_token' => Str::isUuid($apiSettings->cmc_api_token) ? substr($apiSettings->cmc_api_token, 0, 14) . str_repeat("*", strlen($apiSettings->cmc_api_token) - 14) : $apiSettings->cmc_api_token,
                'cryptoapis_api_token' => strlen($apiSettings->cryptoapis_api_token) > 10 ? substr($apiSettings->cryptoapis_api_token, 0, 10) . str_repeat("*", strlen($apiSettings->cryptoapis_api_token) - 10) : $apiSettings->cryptoapis_api_token,
            ]
        ]);
    }


    public function updateApi(Request $request, ApiSettings $apiSettings)
    {
        $this->validate($request, [
            'cmc_api_token' => 'required|string',
            'cryptoapis_api_token' => 'required|min:20|string',
        ]);

        if(!Str::contains($request->cmc_api_token, '*')) {
            if(!Str::isUuid($request->cmc_api_token)) {
                return redirect()->route('admin.settings.index')->with('error', __('Wrong CoinMarketCap API key format.'));
            }
            $apiSettings->cmc_api_token = $request->cmc_api_token;
        }
        if(!Str::contains($request->cryptoapis_api_token, '*')) {
            $apiSettings->cryptoapis_api_token = $request->cryptoapis_api_token;
        }
        $apiSettings->save();

        return redirect()->route('admin.settings.index')->with('success', __("Settings were successfully applied."));
    }
}
