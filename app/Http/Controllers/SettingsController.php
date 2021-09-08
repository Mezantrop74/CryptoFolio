<?php

namespace App\Http\Controllers;

use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function updateUser(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required|string|min:6',
            'confirm_password' => 'required|string|same:new_password',
        ]);

        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return redirect()->back()->with('error', __('Old password is invalid.'));
        }

        auth()->user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->back()->with('success', __('Password was successfully changed.'));
    }

    public function updateView(Request $request)
    {
        $this->validate($request, [
            'lang' => 'required|string', Rule::in(config('app.locales')),
            'color_scheme' => 'required|string|in:light,dark',
        ]);

        auth()->user()->update([
            'lang' => $request->lang,
            'color_scheme' => $request->color_scheme,
        ]);

        return redirect()->back();
    }

    public function toggleColorScheme(Request $request)
    {
        $this->validate($request, [
            'toggle_colorscheme' => 'required|string',
        ]);
        auth()->user()->update([
            'color_scheme' => auth()->user()->color_scheme == 'dark' ? 'light' : 'dark',
        ]);
        return redirect()->back();
    }
}
