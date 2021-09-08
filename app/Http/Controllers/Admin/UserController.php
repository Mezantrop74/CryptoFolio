<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function show($user_id)
    {
        $user = User::where('user_id', $user_id)->firstOrFail();
        return view('admin.users.show', compact('user'));
    }

    public function indexCreate()
    {
        return view('admin.users.create');
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'login' => 'required|string|max:255|unique:users',
            'jabber' => 'nullable|email',
            'note' => 'string|max:2048|nullable',
            'password' => 'string|min:8',
            'is_active' => 'nullable',
        ]);

        $user = new User([
            'user_id' => Str::uuid(),
            'login' => $request->login,
            'is_active' => (bool)$request->is_active,
            'password' => Hash::make($request->password),
            'note' => $request->note,
            'jabber' => $request->jabber,
            'api_token' => Str::uuid(),
        ]);
        $user->save();
        return redirect()->route('admin.users.show', ['user_id' => $user->user_id])->with('success', __("User was successfully created."));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'login' => 'required|string|max:2048|unique:users,login,' . $request->user_id . ',user_id',
            'jabber' => 'nullable|email',
            'user_id' => 'required|string|exists:users,user_id',
            'note' => 'string|max:2048|nullable',
            'is_active' => 'nullable',
        ]);

        $user = User::where('user_id', $request->user_id)->firstOrFail();
        $user->update([
            'login' => $request->login,
            'jabber' => $request->jabber,
            'note' => $request->note,
            'is_active' => (bool)$request->is_active,
        ]);

        return redirect()->route('admin.users.show', ['user_id' => $user->user_id])->with('success', __("User was successfully updated."));
    }

    public function password(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|string',
        ]);

        $user = User::where('user_id', $request->user_id)->firstOrFail();
        $password = Str::random(20);
        $user->update([
            'password' => Hash::make($password)
        ]);
        return redirect()->route('admin.users.show', ['user_id' => $user->user_id])->with('success', __("Password was successfully updated. New password: ") . $password);
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|string|exists:users,user_id',
        ]);

        $user = User::where('user_id', $request->user_id)->firstOrFail();
        $user->update([
            'is_active' => false,
            'deleted_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.users.index')->with('success', __("User was successfully deleted."));
    }
}
