<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Request;

class AppController extends Controller
{
    public function time(Request $request)
    {
        return response()->json(['time' => Carbon::now()->toIso8601String()]);
    }
}
