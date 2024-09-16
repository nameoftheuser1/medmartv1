<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            'email' => ['required', 'max:255'],
            'password' => ['required'],
        ];

        if ($request->email !== 'admin') {
            $rules['email'][] = 'email';
        }

        if (RateLimiter::tooManyAttempts('login:' . $request->ip(), 5)) {
            return back()->withErrors(['email' => 'Too many login attempts. Please try again later.']);
        }
        RateLimiter::hit('login:' . $request->ip());

        $fields = $request->validate($rules);

        if (Auth::attempt($fields, $request->remember)) {
            return redirect()->intended('/');
        } else {
            return back()->withErrors([
                'failed' => 'The provided credentials do not match our records.'
            ]);
        }
    }


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
