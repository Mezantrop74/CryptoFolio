<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JabberController extends Controller
{
    public function update(Request $request)
    {
        $this->validate($request, [
            'jabber' => 'nullable|string|email'
        ]);
        auth()->user()->update([
            'jabber' => $request->jabber
        ]);

        return redirect()->back()->with('success', __('Settings were successfully applied.'));
    }
}
