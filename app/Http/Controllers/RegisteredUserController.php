<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisteredUserRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisteredUserRequest $request)
    {

        $user = User::create($request->validated());

        $oldSessionId = $request->session()->getId();

        $user->markEmailAsVerified();
        session(['guest_session_id' => $oldSessionId]);

        Auth::login($user);

        event(new Registered($user));

        return redirect()->route('verification.notice');
        // return redirect('/')->with('success', 'Your account has been created!');
    }
}
