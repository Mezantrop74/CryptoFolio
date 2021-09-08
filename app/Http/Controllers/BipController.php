<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BipController extends Controller
{
    public function index()
    {
        return view('crypto.bip_converter.index');
    }
}
