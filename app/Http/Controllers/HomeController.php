<?php

namespace App\Http\Controllers;

use App\Domain\Telegram\Parser\Parser;
use Enqueue\Redis\RedisConnectionFactory;
use GuzzleHttp\Client;
use Illuminate\Contracts\Support\Renderable;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     * @throws \Interop\Queue\Exception
     * @throws \Interop\Queue\Exception\InvalidDestinationException
     * @throws \Interop\Queue\Exception\InvalidMessageException
     */
    public function index()
    {
        return view('home');
    }
}
